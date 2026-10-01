<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    // ── Publik ──────────────────────────────────────────────────────

    /** Beranda: daftar artikel published */
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

        $articles       = $query->latest()->get();
        $artikelPopuler = Article::where('status', 'published')
            ->orderByDesc('views')
            ->take(4)
            ->get();
        $trending       = Article::where('status', 'published')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('articles.index', compact('articles', 'stats', 'categories', 'artikelPopuler', 'trending'));
    }

    /** Detail berita — tambah views */
    public function show($slug)
    {
        $article = Article::with(['category', 'user', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment views
        $article->increment('views');

        // Catat kunjungan harian
        \Illuminate\Support\Facades\DB::table('kunjungan')->upsert(
            [['tanggal' => today()->toDateString(), 'jumlah' => 1]],
            ['tanggal'],
            ['jumlah' => \Illuminate\Support\Facades\DB::raw('kunjungan.jumlah + 1')]
        );

        $terkait = Article::with('category')
            ->where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('views')
            ->take(4)
            ->get();

        return view('articles.show', compact('article', 'terkait'));
    }

    // ── Admin CRUD ───────────────────────────────────────────────────

    /** Admin: daftar semua artikel */
    public function adminIndex(Request $request)
    {
        $query = Article::with(['category', 'user'])->withCount('comments');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    /** Admin: form tambah */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.articles.form', compact('categories'));
    }

    /** Admin: simpan artikel baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content'     => ['required', 'string'],
            'image'       => ['nullable', 'url', 'max:500'],
            'status'      => ['required', 'in:draft,published'],
        ], [
            'title.required'       => 'Judul wajib diisi.',
            'category_id.required' => 'Pilih kategori.',
            'content.required'     => 'Isi artikel wajib diisi.',
        ]);

        $data['slug']    = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id'] = auth()->id();
        $data['views']   = 0;

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /** Admin: form edit */
    public function edit(Article $article)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.articles.form', compact('article', 'categories'));
    }

    /** Admin: simpan perubahan */
    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content'     => ['required', 'string'],
            'image'       => ['nullable', 'url', 'max:500'],
            'status'      => ['required', 'in:draft,published'],
        ]);

        // Update slug hanya jika judul berubah
        if ($article->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /** Admin: hapus artikel */
    public function destroy(Article $article)
    {
        $article->comments()->delete();
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    /** Admin: toggle publish/draft */
    public function toggleStatus(Article $article)
    {
        $article->update([
            'status' => $article->status === 'published' ? 'draft' : 'published',
        ]);

        return back()->with('success', 'Status berita diperbarui.');
    }
}
