<?php

namespace App\Console\Commands;

use App\Services\RssNewsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * FetchNewsCommand
 *
 * Jalankan manual:    php artisan news:fetch
 * Test dry-run:       php artisan news:fetch --dry-run
 * Lihat detail:       php artisan news:fetch --verbose
 */
class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch
        {--dry-run : Simulasi tanpa menyimpan ke database}
        {--limit=  : Batasi jumlah feed yang diproses (opsional)}';

    protected $description = 'Ambil berita terbaru dari RSS feed CNN Indonesia, Republika, dan ANTARA';

    public function handle(RssNewsService $service): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('NewsXpaper RSS Fetcher — mulai...');
        $this->info('Tanggal   : ' . now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') . ' WIB');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('⚠️  Mode dry-run aktif — tidak ada data yang disimpan.');
            $this->newLine();
        }

        $startTime = microtime(true);

        try {
            if ($isDryRun) {
                $this->line('Dry-run: melewati proses fetch aktual.');
                $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => 0];
            } else {
                $stats = $service->fetchAll();
            }

            $elapsed = round(microtime(true) - $startTime, 2);

            $this->newLine();
            $this->table(
                ['Metrik', 'Jumlah'],
                [
                    ['Artikel baru tersimpan', $stats['inserted']],
                    ['Dilewati (duplikat/invalid)', $stats['skipped']],
                    ['Feed gagal diakses', $stats['errors']],
                    ['Waktu eksekusi', $elapsed . ' detik'],
                ]
            );

            if ($stats['inserted'] > 0) {
                $this->info("✅ {$stats['inserted']} berita baru berhasil diimpor.");
            } else {
                $this->line('ℹ️  Tidak ada berita baru (semua sudah ada atau tidak ada artikel baru).');
            }

            if ($stats['errors'] > 0) {
                $this->warn("⚠️  {$stats['errors']} feed gagal diakses. Cek storage/logs/laravel.log.");
            }

            Log::info('news:fetch selesai', array_merge($stats, ['elapsed_s' => $elapsed]));

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('❌ Error tidak terduga: ' . $e->getMessage());
            Log::error('news:fetch gagal total', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
