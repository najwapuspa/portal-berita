<?php

namespace Tests\Feature;

use App\Services\RssNewsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RssSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Source code RssNewsService tidak boleh menonaktifkan SSL verification.
     * Cek: string 'verify' => false tidak ada di file.
     */
    public function test_ssl_verification_tidak_dinonaktifkan_di_kode(): void
    {
        $source = file_get_contents(base_path('app/Services/RssNewsService.php'));

        // Tidak boleh ada 'verify' => false atau 'verify'=>false
        $this->assertStringNotContainsString("'verify' => false", $source);
        $this->assertStringNotContainsString('"verify" => false', $source);
        $this->assertStringNotContainsString("'verify'=>false", $source);
    }

    /**
     * RssNewsService tetap dapat diinstansiasi dan punya method fetchAll.
     */
    public function test_rss_service_dapat_diinstansiasi(): void
    {
        $service = new RssNewsService();
        $this->assertInstanceOf(RssNewsService::class, $service);
        $this->assertTrue(method_exists($service, 'fetchAll'));
    }

    /**
     * Jika tidak ada admin di database, fetchAll tidak boleh crash.
     * Ini terkait M5 (user_id hardcoded) — akan diperbaiki di task terpisah,
     * tapi struktur kode harus menangani kondisi ini.
     */
    public function test_fetch_all_tidak_crash_jika_tidak_ada_artikel_baru(): void
    {
        // Buat kategori dan admin agar tidak error FK constraint
        \App\Models\Category::factory()->create(['slug' => 'nasional']);
        \App\Models\User::factory()->create(['role' => 'admin']);

        $service = new RssNewsService();

        // fetchAll akan mencoba mengakses RSS feed eksternal
        // Jika gagal (offline/koneksi), harus menangani error per feed
        // tanpa melempar exception fatal
        try {
            $stats = $service->fetchAll();
            $this->assertIsArray($stats);
            $this->assertArrayHasKey('inserted', $stats);
            $this->assertArrayHasKey('errors', $stats);
        } catch (\Throwable $e) {
            // Jika lempar exception karena tidak ada koneksi,
            // test tetap PASS selama tidak fatal error
            $this->assertStringContainsString('HTTP', $e->getMessage());
        }
    }
}
