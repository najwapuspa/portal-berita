<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $categories = config('categories');
        abort_unless(isset($categories[$slug]), 404);
        $cfg = $categories[$slug];

        $query = Article::query();
        if (method_exists(Article::class, 'category')) {
            $query->with('category');
        }
        if (method_exists(Article::class, 'comments')) {
            $query->withCount('comments');
        }

        // Ambil artikel, ubah ke format seragam, lalu saring sesuai kategori
        $items = $query->latest()->get()
            ->map(fn ($a) => $this->present($a))
            ->filter(fn ($x) => Str::slug($x['cat']) === $slug)
            ->values();

        $perPage = 9;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return view('articles.category', [
            'slug'      => $slug,
            'cfg'       => $cfg,
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
        $body = $str(data_get($a, 'excerpt')) ?? $str(data_get($a, 'summary')) ?? $str(data_get($a, 'body')) ?? $str(data_get($a, 'content')) ?? '';

        return [
            'title'    => $str(data_get($a, 'title')) ?? 'Tanpa judul',
            'url'      => $slug ? route('articles.show', $slug) : '#',
            'cat'      => $cat,
            'author'   => $author,
            'img'      => $img,
            'date'     => Carbon::parse(data_get($a, 'published_at') ?? data_get($a, 'created_at') ?? now())->locale('id')->translatedFormat('j M Y'),
            'comments' => (int) data_get($a, 'comments_count', 0),
            'excerpt'  => Str::limit(strip_tags($body), 130),
        ];
    }
}