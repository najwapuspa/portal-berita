<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatKunjungan
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get') && $request->is('/', 'berita/*', 'kategori/*')) {
            DB::table('kunjungan')->upsert(
                [['tanggal' => today()->toDateString(), 'jumlah' => 1]],
                ['tanggal'],
                ['jumlah' => DB::raw('kunjungan.jumlah + 1')]
            );
        }
        return $next($request);
    }
}