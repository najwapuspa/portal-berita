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
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil dari session
        // 2. Fallback ke cookie permanen (agar bertahan setelah logout/login)
        // 3. Default ke 'id' (Bahasa Indonesia)
        $locale = session('locale', $request->cookie('app_locale', 'id'));

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}

