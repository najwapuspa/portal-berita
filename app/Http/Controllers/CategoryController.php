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
        // Validasi slug ada di config
        $categories = config('categories');
        abort_unless(isset($categories[$slug]), 404);
        $cfg = $categories[$slug];

        // Cari kategori di database
        $category = Category::where('slug', $slug)->first();

        // Kalau kategori belum ada di DB, tampilkan halaman kosong
        if (! $category) {
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 9, 1, ['path' => $request->url()]
            );
            return view('articles.category', [
                'slug'      => $slug,
                'cfg'       => $cfg,
                'paginator' => $paginator,
                'total'     => 0,
            ]);
        }

        // Query artikel langsung dari DB — efisien, support pagination
        $paginator = Article::with(['category', 'user'])
            ->withCount('comments')
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(9)
            ->through(function ($a) {
                $img = $a->image;
                if ($img && ! Str::startsWith($img, ['http://', 'https://', '/'])) {
                    $img = asset('storage/' . $img);
                }
                return [
                    'title'    => $a->title,
                    'url'      => route('articles.show', $a->slug),
                    'cat'      => optional($a->category)->name ?? 'Berita',
                    'author'   => optional($a->user)->name ?? 'Admin',
                    'img'      => $img,
                    'date'     => $a->created_at->locale('id')->translatedFormat('j M Y'),
                    'comments' => $a->comments_count,
                    'excerpt'  => Str::limit(strip_tags($a->content), 130),
                ];
            });

        return view('articles.category', [
            'slug'      => $slug,
            'cfg'       => $cfg,
            'paginator' => $paginator,
            'total'     => $paginator->total(),
        ]);
    }
}
