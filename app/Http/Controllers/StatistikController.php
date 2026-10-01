<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Kunjungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatistikController extends Controller
{
    public function index()
    {
        $hari    = 30;
        $peta    = Kunjungan::where('tanggal', '>=', now()->subDays($hari - 1)->toDateString())
            ->pluck('jumlah', 'tanggal');

        $kunjungan = collect(range($hari - 1, 0))->map(fn ($i) => [
            'tanggal' => now()->subDays($i)->toDateString(),
            'label'   => now()->subDays($i)->translatedFormat('j M'),
            'jumlah'  => (int) ($peta[now()->subDays($i)->toDateString()] ?? 0),
        ]);

        $totalKunjungan = $kunjungan->sum('jumlah');
        $totalMingguIni = $kunjungan->take(-7)->sum('jumlah');
        $totalMingguLalu = $kunjungan->slice(count($kunjungan) - 14, 7)->sum('jumlah');
        $trenMinggu = $totalMingguLalu > 0
            ? round(($totalMingguIni - $totalMingguLalu) / $totalMingguLalu * 100, 1)
            : null;

        // Views per kategori
        $viewsPerKat = Category::withSum('articles', 'views')
            ->withCount('articles')
            ->orderByDesc('articles_sum_views')
            ->get();

        // Berita terpopuler
        $artikelPopuler = Article::with('category')
            ->orderByDesc('views')
            ->take(10)
            ->get();

        // Pencarian populer — dummy realistis berbasis judul artikel
        $pencarianPopuler = Article::where('status', 'published')
            ->orderByDesc('views')
            ->take(8)
            ->get()
            ->map(fn ($a) => [
                'kata'   => explode(' ', $a->title)[0] . ' ' . (explode(' ', $a->title)[1] ?? ''),
                'jumlah' => (int) ($a->views * 0.12),
            ]);

        // Statistik umum
        $stats = [
            'total_artikel'    => Article::count(),
            'published'        => Article::where('status', 'published')->count(),
            'draft'            => Article::where('status', 'draft')->count(),
            'total_user'       => User::count(),
            'total_komentar'   => Comment::count(),
            'total_views'      => (int) Article::sum('views'),
            'kunjungan_hari'   => (int) ($peta[now()->toDateString()] ?? 0),
            'kunjungan_minggu' => $totalMingguIni,
            'kunjungan_bulan'  => $totalKunjungan,
        ];

        return view('admin.statistik', compact(
            'kunjungan',
            'totalKunjungan',
            'trenMinggu',
            'viewsPerKat',
            'artikelPopuler',
            'pencarianPopuler',
            'stats'
        ));
    }
}
