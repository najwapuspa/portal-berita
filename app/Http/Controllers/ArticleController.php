<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // PUBLIK
    // ─────────────────────────────────────────────────────────────

    /** Beranda — daftar artikel published dengan pagination */
    public function index(Request $request)
    {
        $categories = Category::withCount([
            'articles' => fn ($q) => $q->where('status', 'published'),
        ])->get();

        $query = Article::with(['category', 'user'])
            ->withCount('comments')
            ->where('status', 'published');

        if ($request->filled('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->kategori));
        }

        // Paginate — 12 per halaman untuk homepage
        $articles = $query->latest()->paginate(12)->withQueryString();

        $artikelPopuler = Article::where('status', 'published')
            ->orderByDesc('views')
            ->take(6)
            ->get();

        $trending = Article::where('status', 'published')
            ->orderByDesc('views')
            ->take(8)
            ->get();

        // Berita terbaru per kategori untuk section tambahan
        $beritaPerKategori = collect(config('categories'))->map(function ($cfg, $slug) {
            return [
                'slug'   => $slug,
                'name'   => $cfg['name'],
                'color'  => $cfg['color'],
                'items'  => Article::with(['category', 'user'])
                    ->whereHas('category', fn ($q) => $q->where('slug', $slug))
                    ->where('status', 'published')
                    ->latest()
                    ->take(4)
                    ->get(),
            ];
        })->filter(fn ($item) => $item['items']->count() > 0)->values();

        // Pilihan editor (top 6 views dari artikel yang bukan 4 teratas)
        $pilihanEditor = Article::with('category')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->skip(4)->take(6)
            ->get();

        return view('articles.index', compact(
            'articles', 'categories', 'artikelPopuler', 'trending',
            'beritaPerKategori', 'pilihanEditor'
        ));
    }

    /** Detail berita — views counter dengan session throttle */
    public function show(Request $request, string $slug)
    {
        $article = Article::with(['category', 'user', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // ── Views throttle: satu kenaikan per artikel per session (24 jam) ──
        $sessionKey = 'viewed_article_' . $article->id;
        if (! $request->session()->has($sessionKey)) {
            $article->increment('views');
            $request->session()->put($sessionKey, true);

            // Catat kunjungan harian di tabel kunjungan
            DB::table('kunjungan')->upsert(
                [['tanggal' => today()->toDateString(), 'jumlah' => 1]],
                ['tanggal'],
                ['jumlah' => DB::raw('kunjungan.jumlah + 1')]
            );
        }

        // ── Berita terkait (sama kategori, bukan artikel ini) ──
        $terkait = Article::with('category')
            ->where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('views')
            ->take(4)
            ->get();

        // ── Navigasi prev/next (berdasarkan waktu publish) ──
        $prevArticle = Article::where('status', 'published')
            ->where('created_at', '<', $article->created_at)
            ->orderByDesc('created_at')
            ->first(['id', 'title', 'slug']);

        $nextArticle = Article::where('status', 'published')
            ->where('created_at', '>', $article->created_at)
            ->orderBy('created_at')
            ->first(['id', 'title', 'slug']);

        return view('articles.show', compact(
            'article', 'terkait', 'prevArticle', 'nextArticle'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN CRUD
    // ─────────────────────────────────────────────────────────────

    /** Admin: daftar semua artikel dengan filter */
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

    /** Admin: form tambah artikel */
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
            'status'      => ['required', 'in:draft,published'],
            // Gambar: bisa upload file ATAU URL
            'image_file'  => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'image_url'   => ['nullable', 'url', 'max:500'],
        ], [
            'title.required'       => 'Judul wajib diisi.',
            'category_id.required' => 'Pilih kategori.',
            'content.required'     => 'Isi artikel wajib diisi.',
            'image_file.image'     => 'File harus berupa gambar.',
            'image_file.mimes'     => 'Gambar harus berformat JPEG, PNG, WebP, atau GIF.',
            'image_file.max'       => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Tentukan nilai kolom image
        $imagePath = $this->resolveImagePath($request, null);

        $data['slug']    = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id'] = auth()->id();
        $data['views']   = 0;
        $data['image']   = $imagePath;

        // Hapus field helper yang tidak ada di tabel
        unset($data['image_file'], $data['image_url']);

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /** Admin: form edit artikel */
    public function edit(Article $article)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.articles.form', compact('article', 'categories'));
    }

    /** Admin: simpan perubahan artikel */
    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content'     => ['required', 'string'],
            'status'      => ['required', 'in:draft,published'],
            'image_file'  => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'image_url'   => ['nullable', 'url', 'max:500'],
        ], [
            'image_file.image' => 'File harus berupa gambar.',
            'image_file.mimes' => 'Gambar harus berformat JPEG, PNG, WebP, atau GIF.',
            'image_file.max'   => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Update slug hanya jika judul berubah
        if ($article->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        }

        $newImage = $this->resolveImagePath($request, $article->image);
        $data['image'] = $newImage;

        // Hapus gambar lama dari storage jika diganti dengan file baru
        if ($request->hasFile('image_file') && $article->image) {
            $this->deleteStorageImage($article->image);
        }

        unset($data['image_file'], $data['image_url']);

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /** Admin: hapus artikel */
    public function destroy(Article $article)
    {
        // Hapus gambar dari storage jika ada
        $this->deleteStorageImage($article->image);

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

    // ─────────────────────────────────────────────────────────────
    // HELPER PRIVATE
    // ─────────────────────────────────────────────────────────────

    /**
     * Tentukan path/URL gambar:
     * 1. Jika ada file upload baru → simpan ke storage, kembalikan path relatif
     * 2. Jika ada image_url diisi → gunakan URL tersebut
     * 3. Jika tidak ada input baru → pertahankan gambar lama ($existing)
     */
    private function resolveImagePath(Request $request, ?string $existing): ?string
    {
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('articles', 'public');
            return $path; // disimpan relatif, akses via asset('storage/...')
        }

        $url = trim((string) $request->input('image_url', ''));
        if ($url !== '') {
            return $url;
        }

        return $existing;
    }

    /**
     * Hapus file gambar dari storage jika path-nya relatif (bukan URL eksternal).
     */
    private function deleteStorageImage(?string $image): void
    {
        if (! $image) return;
        // Hanya hapus jika bukan URL eksternal
        if (Str::startsWith($image, ['http://', 'https://'])) return;
        Storage::disk('public')->delete($image);
    }
}
