<?php

namespace App\Console\Commands;

use App\Services\RssNewsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * FetchNewsCommand
 *
 * Jalankan manual:   php artisan news:fetch
 * Dry-run:           php artisan news:fetch --dry-run
 * Satu sumber saja:  php artisan news:fetch --source=CNN
 */
class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch
        {--dry-run  : Simulasi tanpa menyimpan ke database}
        {--source=  : Filter berdasarkan nama sumber (opsional)}
        {--verbose  : Tampilkan detail per sumber}';

    protected $description = 'Ambil berita terbaru dari RSS feed (CNN Indonesia, Republika, ANTARA, CNBC, Tempo, Sindonews)';

    public function handle(RssNewsService $service): int
    {
        $isDryRun = $this->option('dry-run');
        $start    = microtime(true);

        $this->line('');
        $this->line('╔══════════════════════════════════════════════╗');
        $this->line('║       NEWS X PAPER — RSS Fetcher             ║');
        $this->line('╚══════════════════════════════════════════════╝');
        $this->line('  Waktu  : ' . now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') . ' WIB');
        $this->line('  Mode   : ' . ($isDryRun ? 'DRY-RUN (tidak ada data disimpan)' : 'LIVE'));
        $this->line('');

        if ($isDryRun) {
            $this->warn('  ⚠  Dry-run aktif — tidak ada data yang disimpan.');
            $this->line('');
            return Command::SUCCESS;
        }

        try {
            $stats   = $service->fetchAll();
            $elapsed = round(microtime(true) - $start, 2);

            // Ringkasan per sumber
            $this->line('  HASIL PER SUMBER:');
            $rows = [];
            foreach ($stats['per_source'] as $src => $s) {
                $rows[] = [
                    $src,
                    $s['inserted'],
                    $s['skipped'],
                    $s['errors'],
                ];
            }
            $this->table(['Sumber', 'Baru', 'Dilewati', 'Error'], $rows);

            // Ringkasan total
            $this->line('');
            $this->table(
                ['Metrik', 'Nilai'],
                [
                    ['Total artikel baru', $stats['inserted']],
                    ['Total dilewati (duplikat/invalid)', $stats['skipped']],
                    ['Feed gagal diakses', $stats['errors']],
                    ['Waktu eksekusi', $elapsed . ' detik'],
                ]
            );

            if ($stats['inserted'] > 0) {
                $this->info("  ✅  {$stats['inserted']} berita baru berhasil diimpor.");
            } else {
                $this->line("  ℹ   Tidak ada berita baru (semua sudah ada atau feed tidak memiliki item baru).");
            }

            if ($stats['errors'] > 0) {
                $this->warn("  ⚠   {$stats['errors']} feed gagal diakses. Periksa storage/logs/laravel.log.");
            }

            $this->line('');

            Log::info('news:fetch selesai', [
                'inserted' => $stats['inserted'],
                'skipped'  => $stats['skipped'],
                'errors'   => $stats['errors'],
                'elapsed'  => $elapsed,
                'run_at'   => now()->toIso8601String(),
            ]);

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("  ❌  Error tidak terduga: " . $e->getMessage());
            Log::error('news:fetch gagal total', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
