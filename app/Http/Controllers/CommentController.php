<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Simpan komentar baru dari user yang login.
     * Dipanggil via POST /berita/{slug}/komentar
     */
    public function store(Request $request, string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'body.required' => 'Komentar tidak boleh kosong.',
            'body.min'      => 'Komentar minimal 3 karakter.',
            'body.max'      => 'Komentar maksimal 2000 karakter.',
        ]);

        Comment::create([
            'article_id' => $article->id,
            'user_id'    => auth()->id(),
            'body'       => strip_tags($request->body), // cegah XSS
        ]);

        return back()
            ->with('comment_success', 'Komentar berhasil dikirim.')
            ->withFragment('komentar');
    }

    /**
     * Hapus komentar — hanya admin.
     * Dipanggil via DELETE /komentar/{comment}
     */
    public function destroy(Comment $comment)
    {
        // Guard: hanya admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus komentar.');
        }

        $comment->delete();

        // Jika dari halaman admin comments, kembali ke sana
        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Halaman admin: daftar semua komentar.
     * GET /admin/comments
     */
    public function adminIndex(Request $request)
    {
        $query = Comment::with(['user', 'article'])
            ->latest();

        // Filter opsional berdasarkan pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('body', 'like', '%' . $q . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%'))
                    ->orWhereHas('article', fn ($a) => $a->where('title', 'like', '%' . $q . '%'));
            });
        }

        $comments = $query->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }
}
