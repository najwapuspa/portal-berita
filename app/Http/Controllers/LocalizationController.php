<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    /**
     * Ubah bahasa aktif aplikasi (ID / EN) dan simpan ke session + cookie.
     */
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, ['id', 'en'], true)) {
            session(['locale' => $locale]);
            cookie()->queue(cookie()->forever('app_locale', $locale));
        }

        // Kembali ke halaman sebelumnya dengan aman tanpa redirect loop
        $previousUrl = url()->previous();
        if (empty($previousUrl) || str_contains($previousUrl, '/lang/')) {
            $previousUrl = route('articles.index');
        }

        return redirect()->to($previousUrl);
    }
}

