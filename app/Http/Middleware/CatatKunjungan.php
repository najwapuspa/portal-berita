<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CatatKunjungan
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get') && $request->is('/', 'berita/*', 'kategori/*')) {
            // Periksa keberadaan tabel sebelum operasi — aman untuk testing (SQLite :memory:)
            // dan production. Tidak menyembunyikan error DB lain yang sesungguhnya.
            if (Schema::hasTable('kunjungan')) {
                DB::table('kunjungan')->upsert(
                    [['tanggal' => today()->toDateString(), 'jumlah' => 1]],
                    ['tanggal'],
                    ['jumlah' => DB::raw('kunjungan.jumlah + 1')]
                );
            }
        }
        return $next($request);
    }
}
