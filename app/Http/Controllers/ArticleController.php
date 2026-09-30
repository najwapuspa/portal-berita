<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'articles'   => Article::where('status', 'published')->count(),
            'categories' => Category::count(),
            'comments'   => Comment::count(),
            'authors'    => User::count(),
        ];

        $categories = Category::withCount([
            'articles' => fn ($q) => $q->where('status', 'published'),
        ])->get();

        $query = Article::with(['category', 'user'])
            ->withCount('comments')
            ->where('status', 'published');

        if ($request->filled('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->kategori));
        }

        $articles = $query->latest()->get();

        return view('articles.index', compact('articles', 'stats', 'categories'));
    }

    public function show($slug)
    {
        $article = Article::with(['category', 'user', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('articles.show', compact('article'));
    }
}