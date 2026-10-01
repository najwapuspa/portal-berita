<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /** /dashboard → arahkan sesuai role */
    public function index(Request $request)
    {
        return $request->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    /** Dashboard ADMIN */
    public function admin()
    {
        $seminggu = now()->subWeek();

        // Kunjungan 7 hari
        $peta = Kunjungan::where('tanggal', '>=', now()->subDays(6)->toDateString())
            ->pluck('jumlah', 'tanggal');

        $kunjungan7 = collect(range(6, 0))->map(fn ($i) => [
            'tanggal' => now()->subDays($i),
            'jumlah'  => (int) ($peta[now()->subDays($i)->toDateString()] ?? 0),
        ]);

        // Kunjungan 30 hari untuk grafik garis
        $peta30 = Kunjungan::where('tanggal', '>=', now()->subDays(29)->toDateString())
            ->pluck('jumlah', 'tanggal');

        $kunjungan30 = collect(range(29, 0))->map(fn ($i) => [
            'tanggal' => now()->subDays($i)->toDateString(),
            'jumlah'  => (int) ($peta30[now()->subDays($i)->toDateString()] ?? 0),
        ]);

        $hariIni = $kunjungan7->last()['jumlah'];
        $kemarin = $kunjungan7->get(5)['jumlah'];
        $selisih = $kemarin > 0 ? round(($hariIni - $kemarin) / $kemarin * 100, 1) : null;

        // Views per kategori
        $viewsPerKat = Category::withSum('articles', 'views')
            ->orderByDesc('articles_sum_views')
            ->get()
            ->map(fn ($c) => ['name' => $c->name, 'views' => (int) $c->articles_sum_views]);

        return view('dashboard.admin', [
            'total' => [
                'artikel'  => Article::count(),
                'user'     => User::count(),
                'komentar' => Comment::count(),
                'views'    => (int) Article::sum('views'),
            ],
            'baru' => [
                'artikel'  => Article::where('created_at', '>=', $seminggu)->count(),
                'user'     => User::where('created_at', '>=', $seminggu)->count(),
                'komentar' => Comment::where('created_at', '>=', $seminggu)->count(),
            ],
            'kunjungan'      => $kunjungan7,
            'kunjungan30'    => $kunjungan30,
            'hariIni'        => $hariIni,
            'selisih'        => $selisih,
            'viewsPerKat'    => $viewsPerKat,
            'kategori'       => Category::withCount('articles')->orderByDesc('articles_count')->get(),
            'artikelTerbaru' => Article::with('category')->latest()->take(5)->get(),
            'artikelPopuler' => Article::with('category')->orderByDesc('views')->take(5)->get(),
            'userTerbaru'    => User::latest()->take(5)->get(),
        ]);
    }

    /** Dashboard USER/PEMBACA */
    public function user(Request $request)
    {
        $user = $request->user();

        $artikelTerbaru = Article::with('category')
            ->where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        $artikelPopuler = Article::with('category')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('dashboard.user', [
            'user'           => $user,
            'jumlahKomentar' => Comment::where('user_id', $user->id)->count(),
            'artikelTerbaru' => $artikelTerbaru,
            'artikelPopuler' => $artikelPopuler,
            'kategori'       => Category::orderBy('name')->get(),
        ]);
    }
}
