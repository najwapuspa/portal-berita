<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        // 1. Cari kategori di database — sumber kebenaran utama
        $category = Category::where('slug', $slug)->first();

        // Jika tidak ada di DB → 404
        if (! $category) {
            abort(404, 'Kategori tidak ditemukan.');
        }

        // 2. Ambil config tampilan (warna, nama display) — boleh fallback jika tidak ada di config
        $configCategories = config('categories', []);
        $cfg = $configCategories[$slug] ?? [
            'name'  => $category->name,
            'color' => '#1e8fd6',
        ];
        $cfg['display_name'] = app()->getLocale() === 'en'
            ? ($cfg['name_en'] ?? $category->display_name)
            : $category->name;

        // 3. Query artikel — langsung ke DB, efisien, support pagination
        $paginator = Article::with(['category', 'user'])
            ->withCount('comments')
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->through(function ($a) use ($slug, $cfg) {
                return [
                    'title'     => $a->display_title,
                    'url'       => route('articles.show', $a->slug),
                    'cat'       => optional($a->category)->display_name ?? $cfg['display_name'],
                    'catSlug'   => optional($a->category)->slug ?? $slug,
                    'author'    => optional($a->user)->name ?? 'Admin',
                    'img'       => $a->image_url,          // accessor — selalu ada fallback
                    'date'      => $a->formatted_date,
                    'comments'  => $a->comments_count,
                    'excerpt'   => $a->display_excerpt,
                    'read_time' => $a->read_time_minutes,
                    'source'    => $a->source_name,
                ];
            });

        return view('articles.category', [
            'slug'      => $slug,
            'cfg'       => $cfg,
            'category'  => $category,
            'paginator' => $paginator,
            'total'     => $paginator->total(),
        ]);
    }
}
