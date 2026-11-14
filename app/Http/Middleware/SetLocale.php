<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Prioritas penentuan locale:
     * 1. Session (pengguna sudah pernah memilih / tombol bahasa ditekan)
     * 2. Cookie permanen (preferensi sesi sebelumnya)
     * 3. Deteksi otomatis dari Accept-Language header browser
     * 4. Default 'id' (Bahasa Indonesia)
     *
     * Tombol bahasa manual tidak lagi ditampilkan di UI, namun route
     * /lang/{locale} tetap berfungsi agar preferensi manual via URL masih
     * dapat digunakan jika dibutuhkan di masa depan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil dari session
        $locale = session('locale');

        // 2. Fallback ke cookie
        if (! $locale) {
            $locale = $request->cookie('app_locale');
        }

        // 3. Deteksi otomatis dari Accept-Language browser
        if (! $locale) {
            $locale = $this->detectFromBrowser($request);
        }

        // Validasi — hanya 'id' dan 'en' yang didukung
        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    /**
     * Deteksi bahasa dari header Accept-Language browser.
     * Contoh Accept-Language: "en-US,en;q=0.9,id;q=0.8"
     * Kembalikan 'en' jika browser utama adalah English,
     * kembalikan 'id' jika Indonesian, atau null jika tidak dikenali.
     */
    private function detectFromBrowser(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language', '');

        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header: ambil bahasa dengan q tertinggi
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $part) {
            $part = trim($part);
            if (preg_match('/^([a-zA-Z\-]+)(?:;q=(\d+(?:\.\d+)?))?$/', $part, $m)) {
                $tag = strtolower($m[1]);
                $q   = isset($m[2]) ? (float) $m[2] : 1.0;
                $languages[$tag] = $q;
            }
        }

        // Urutkan berdasarkan q-value tertinggi
        arsort($languages);

        // Pemetaan: awalan bahasa → locale yang didukung
        foreach (array_keys($languages) as $tag) {
            $primary = explode('-', $tag)[0]; // 'en-US' → 'en', 'id-ID' → 'id'
            if ($primary === 'id') return 'id';
            if ($primary === 'en') return 'en';
        }

        return null; // tidak dikenali → fallback ke default
    }
}
