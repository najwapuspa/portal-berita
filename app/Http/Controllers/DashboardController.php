<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // /dashboard -> arahkan sesuai peran
    public function index(Request $request)
    {
        return $request->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    // Dashboard ADMIN
    public function admin()
    {
        $seminggu = now()->subWeek();

        $peta = Kunjungan::where('tanggal', '>=', now()->subDays(6)->toDateString())
            ->pluck('jumlah', 'tanggal');

        $kunjungan = collect(range(6, 0))->map(fn ($i) => [
            'tanggal' => now()->subDays($i),
            'jumlah'  => (int) ($peta[now()->subDays($i)->toDateString()] ?? 0),
        ]);

        $hariIni = $kunjungan->last()['jumlah'];
        $kemarin = $kunjungan->get(5)['jumlah'];
        $selisih = $kemarin > 0 ? round(($hariIni - $kemarin) / $kemarin * 100, 1) : null;

        return view('dashboard.admin', [
            'total' => [
                'artikel'  => Article::count(),
                'user'     => User::count(),
                'komentar' => Comment::count(),
            ],
            'baru' => [
                'artikel'  => Article::where('created_at', '>=', $seminggu)->count(),
                'user'     => User::where('created_at', '>=', $seminggu)->count(),
                'komentar' => Comment::where('created_at', '>=', $seminggu)->count(),
            ],
            'kunjungan'      => $kunjungan,
            'hariIni'        => $hariIni,
            'selisih'        => $selisih,
            'kategori'       => Category::withCount('articles')->orderByDesc('articles_count')->get(),
            'artikelTerbaru' => Article::with('category')->latest()->take(5)->get(),
            'artikelPopuler' => Article::orderByDesc('views')->take(5)->get(),
            'userTerbaru'    => User::latest()->take(5)->get(),
        ]);
    }

    // Dashboard PEMBACA
    public function user(Request $request)
    {
        $user = $request->user();

        return view('dashboard.user', [
            'user'           => $user,
            'jumlahKomentar' => Comment::where('user_id', $user->id)->count(),
            'artikelTerbaru' => Article::with('category')->latest()->take(6)->get(),
            'kategori'       => Category::orderBy('name')->get(),
        ]);
    }
}