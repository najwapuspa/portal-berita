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
        $q = trim((string) $request->query('q', ''));
        $items = collect();
        $catSlug = null;

        if ($q !== '') {
            $needle = Str::lower($q);
            $words = array_values(array_filter(preg_split('/\s+/u', $needle)));

            // Kalau kata kunci = nama kategori, tampilkan tautan ke halaman kategori
            foreach (config('categories') as $slug => $c) {
                if ($slug === Str::slug($q) || Str::lower($c['name']) === $needle) {
                    $catSlug = $slug;
                    break;
                }
            }

            $query = Article::query();
            if (method_exists(Article::class, 'category')) {
                $query->with('category');
            }
            if (method_exists(Article::class, 'comments')) {
                $query->withCount('comments');
            }

            $items = $query->latest()->get()
                ->map(fn ($a) => $this->present($a))
                ->map(function ($x) use ($needle, $words) {
                    // Semua kata harus ada di judul, kategori, atau isi berita
                    foreach ($words as $w) {
                        if (! Str::contains($x['search'], $w)) {
                            $x['rank'] = 9;
                            return $x;
                        }
                    }
                    // Urutan: cocok di judul, lalu kategori, lalu isi
                    if (Str::contains(Str::lower($x['title']), $needle)) {
                        $x['rank'] = 0;
                    } elseif (Str::contains(Str::lower($x['cat']), $needle)) {
                        $x['rank'] = 1;
                    } else {
                        $x['rank'] = 2;
                    }
                    return $x;
                })
                ->filter(fn ($x) => $x['rank'] < 9)
                ->sortBy('rank')
                ->values();
        }

        $perPage = 8;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('articles.search', [
            'q'         => $q,
            'catSlug'   => $catSlug,
            'paginator' => $paginator,
            'total'     => $items->count(),
        ]);
    }

    private function present($a): array
    {
        $str = fn ($v) => is_string($v) && $v !== '' ? $v : null;

        $cat = $str(data_get($a, 'category.name')) ?? $str(data_get($a, 'category')) ?? 'Berita';
        $author = $str(data_get($a, 'author.name')) ?? $str(data_get($a, 'author')) ?? $str(data_get($a, 'user.name')) ?? 'Admin';
        $img = $str(data_get($a, 'image')) ?? $str(data_get($a, 'thumbnail')) ?? $str(data_get($a, 'cover')) ?? $str(data_get($a, 'image_url'));
        if ($img && ! Str::startsWith($img, ['http://', 'https://', '/'])) {
            $img = asset('storage/' . $img);
        }
        $slug = data_get($a, 'slug');
        $title = $str(data_get($a, 'title')) ?? 'Tanpa judul';
        $body = $str(data_get($a, 'excerpt')) ?? $str(data_get($a, 'summary')) ?? $str(data_get($a, 'body')) ?? $str(data_get($a, 'content')) ?? '';
        $plain = strip_tags($body);

        return [
            'title'    => $title,
            'url'      => $slug ? route('articles.show', $slug) : '#',
            'cat'      => $cat,
            'author'   => $author,
            'img'      => $img,
            'date'     => Carbon::parse(data_get($a, 'published_at') ?? data_get($a, 'created_at') ?? now())->locale('id')->translatedFormat('j M Y'),
            'comments' => (int) data_get($a, 'comments_count', 0),
            'excerpt'  => Str::limit($plain, 150),
            'search'   => Str::lower($title . ' ' . $cat . ' ' . $plain),
        ];
    }
}