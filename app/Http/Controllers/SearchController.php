<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->query('q', ''));
        $sort    = $request->query('sort', 'relevan'); // relevan | terbaru | terpopuler
        $katFilter = $request->query('kat', '');
        $items   = collect();
        $catSlug = null;

        if ($q !== '') {
            $needle = Str::lower($q);
            $words  = array_values(array_filter(preg_split('/\s+/u', $needle)));

            // Deteksi apakah kata kunci = nama kategori
            foreach (config('categories') as $slug => $c) {
                if ($slug === Str::slug($q) || Str::lower($c['name']) === $needle) {
                    $catSlug = $slug;
                    break;
                }
            }

            $query = Article::with('category')->withCount('comments')
                ->where('status', 'published');

            $items = $query->get()
                ->map(fn ($a) => $this->present($a))
                ->map(function ($x) use ($needle, $words) {
                    foreach ($words as $w) {
                        if (! Str::contains($x['search'], $w)) {
                            return array_merge($x, ['rank' => 9, 'score' => 0]);
                        }
                    }
                    $score = 0;
                    if (Str::contains(Str::lower($x['title']), $needle))     { $rank = 0; $score = 100; }
                    elseif (Str::contains(Str::lower($x['cat']), $needle))   { $rank = 1; $score = 70; }
                    elseif (Str::contains(Str::lower($x['excerpt']), $needle))  { $rank = 2; $score = 40; }
                    else                                                      { $rank = 3; $score = 20; }
                    return array_merge($x, ['rank' => $rank, 'score' => $score]);
                })
                ->filter(fn ($x) => $x['rank'] < 9);

            // Filter kategori tambahan
            if ($katFilter !== '') {
                $items = $items->filter(fn ($x) => Str::slug($x['cat']) === $katFilter);
            }

            // Urutan
            $items = match ($sort) {
                'terbaru'   => $items->sortByDesc('date_raw'),
                'terpopuler'=> $items->sortByDesc('views'),
                default     => $items->sortBy('rank'),
            };

            $items = $items->values();
        }

        $perPage  = 8;
        $page     = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Berita populer untuk empty state
        $popular = Article::with('category')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->take(4)
            ->get();

        return view('articles.search', [
            'q'         => $q,
            'catSlug'   => $catSlug,
            'paginator' => $paginator,
            'total'     => $items->count(),
            'sort'      => $sort,
            'katFilter' => $katFilter,
            'popular'   => $popular,
        ]);
    }

    /** JSON autosuggest — maks 5 saran */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $needle = Str::lower($q);

        // Saran dari judul artikel
        $articles = Article::where('status', 'published')
            ->where('title', 'like', '%' . $q . '%')
            ->orderByDesc('views')
            ->take(5)
            ->get(['title', 'slug'])
            ->map(fn ($a) => ['type' => 'berita', 'label' => $a->title, 'slug' => $a->slug]);

        // Saran dari kategori
        $cats = collect(config('categories'))
            ->filter(fn ($c, $s) => Str::contains(Str::lower($c['name']), $needle))
            ->map(fn ($c, $s) => ['type' => 'kategori', 'label' => $c['name'], 'slug' => $s]);

        $results = $cats->values()->merge($articles)->take(5)->values();

        return response()->json($results);
    }

    private function present($a): array
    {
        $str = fn ($v) => is_string($v) && $v !== '' ? $v : null;

        $cat    = $str(data_get($a, 'category.name')) ?? 'Berita';
        $author = $str(data_get($a, 'user.name')) ?? 'Admin';
        $img    = $str(data_get($a, 'image'));
        if ($img && ! Str::startsWith($img, ['http://', 'https://', '/'])) {
            $img = asset('storage/' . $img);
        }
        $slug  = data_get($a, 'slug');
        $body  = strip_tags($str(data_get($a, 'content')) ?? '');
        $dt    = data_get($a, 'created_at') ?? now();

        return [
            'title'    => $str(data_get($a, 'title')) ?? 'Tanpa judul',
            'url'      => $slug ? route('articles.show', $slug) : '#',
            'cat'      => $cat,
            'author'   => $author,
            'img'      => $img,
            'date'     => Carbon::parse($dt)->locale('id')->translatedFormat('j M Y'),
            'date_raw' => Carbon::parse($dt)->timestamp,
            'views'    => (int) data_get($a, 'views', 0),
            'comments' => (int) data_get($a, 'comments_count', 0),
            'excerpt'  => Str::limit($body, 160),
            'search'   => Str::lower(data_get($a, 'title') . ' ' . $cat . ' ' . $author . ' ' . $body),
        ];
    }
}
