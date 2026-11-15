<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // PUBLIK — halaman kategori untuk pengunjung
    // ─────────────────────────────────────────────────────────────

    public function show(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->first();

        if (! $category) {
            abort(404, 'Kategori tidak ditemukan.');
        }

        $configCategories = config('categories', []);
        $cfg = $configCategories[$slug] ?? [
            'name'  => $category->name,
            'color' => '#1e8fd6',
        ];
        $cfg['display_name'] = app()->getLocale() === 'en'
            ? ($cfg['name_en'] ?? $category->display_name)
            : $category->name;

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
                    'img'       => $a->image_url,
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

    // ─────────────────────────────────────────────────────────────
    // ADMIN — CRUD Kategori
    // ─────────────────────────────────────────────────────────────

    /** Daftar semua kategori */
    public function adminIndex(Request $request)
    {
        $kategori = Category::withCount('articles')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('kategori'));
    }

    /** Form tambah kategori */
    public function create()
    {
        return view('admin.categories.form');
    }

    /** Simpan kategori baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'slug'        => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
            'name.max'      => 'Nama kategori maksimal 100 karakter.',
        ]);

        // Auto-generate slug jika kosong
        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($data['name']);

        // Pastikan slug unik
        $baseSlug = $slug;
        $counter  = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        Category::create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$data['name']}\" berhasil ditambahkan.");
    }

    /** Form edit kategori */
    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    /** Simpan perubahan kategori */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $category->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? $category->description,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    /** Hapus kategori */
    public function destroy(Category $category)
    {
        $jumlahArtikel = $category->articles()->count();

        if ($jumlahArtikel > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori \"{$category->name}\" tidak dapat dihapus karena memiliki {$jumlahArtikel} artikel. Pindahkan atau hapus artikel terlebih dahulu.");
        }

        $nama = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }
}

