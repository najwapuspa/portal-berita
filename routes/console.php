<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 |--------------------------------------------------------------------------
 | Laravel Scheduler — Pembaruan Berita Otomatis
 |--------------------------------------------------------------------------
 |
 | Jadwal pengambilan berita RSS setiap 2 jam.
 | Untuk menjalankan scheduler:
 |   - Windows (lokal): lihat instruksi Task Scheduler di README.
 |   - Linux/Mac (produksi): tambahkan cron berikut:
 |     * * * * * cd /path/to/portal-berita && php artisan schedule:run >> /dev/null 2>&1
 |
 | Test manual: php artisan news:fetch
 | Test scheduler tanpa jadwal: php artisan schedule:run --verbose
 |
 */
Schedule::command('news:fetch')
    ->everyTwoHours()
    ->withoutOverlapping()           // Cegah job berjalan bersamaan
    ->runInBackground()              // Jalan di background, tidak memblokir scheduler
    ->appendOutputTo(storage_path('logs/news-fetch.log')); // Log output ke file terpisah
