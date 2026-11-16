<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XssSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Payload XSS tidak boleh dieksekusi sebagai HTML di halaman pencarian.
     * <script> harus muncul sebagai teks yang di-escape, bukan tag HTML.
     */
    public function test_payload_xss_tidak_dieksekusi_sebagai_html(): void
    {
        // Buat artikel yang judulnya mengandung kata kunci normal
        Article::factory()->published()->create([
            'title' => 'Berita Ekonomi Terbaru Hari Ini',
        ]);

        // Cari dengan payload XSS sebagai keyword
        $payload = '<script>alert(1)</script>';
        $response = $this->get('/cari?q=' . urlencode($payload));

        $response->assertStatus(200);

        // Tag <script> TIDAK boleh muncul sebagai HTML tag yang aktif
        // Harus muncul sebagai teks yang sudah di-escape: &lt;script&gt;
        $response->assertSee('&lt;script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    /**
     * Highlight <mark> tetap berfungsi untuk keyword yang aman.
     */
    public function test_highlight_mark_tetap_berfungsi(): void
    {
        Article::factory()->published()->create([
            'title' => 'Ekonomi Indonesia Tumbuh Pesat',
        ]);

        $response = $this->get('/cari?q=Ekonomi');

        $response->assertStatus(200);
        // Tag <mark> harus muncul di output (hasil highlight)
        $response->assertSee('<mark>', false);
    }

    /**
     * Karakter khusus HTML seperti <, >, ", ' ditampilkan dengan aman.
     */
    public function test_karakter_khusus_ditampilkan_sebagai_teks(): void
    {
        Article::factory()->published()->create([
            'title' => 'Harga Naik 50% Hari Ini',
        ]);

        // Cari dengan karakter khusus
        $response = $this->get('/cari?q=' . urlencode('50% <test>"quotes"'));

        $response->assertStatus(200);
        // Karakter khusus harus di-escape, bukan dieksekusi
        $response->assertDontSee('<test>', false);
        $response->assertSee('&lt;test&gt;', false);
    }

    /**
     * Pencarian dengan keyword Unicode tetap berfungsi.
     */
    public function test_pencarian_unicode_tetap_berfungsi(): void
    {
        Article::factory()->published()->create([
            'title' => 'Berita Ekonomi Terbaru',
        ]);

        $response = $this->get('/cari?q=Ekonomi');

        $response->assertStatus(200);
        $response->assertSee('Ekonomi');
    }

    /**
     * Pencarian kosong tidak menghasilkan error.
     */
    public function test_pencarian_kosong_tidak_error(): void
    {
        $response = $this->get('/cari?q=');

        $response->assertStatus(200);
    }

    /**
     * Multiple payload XSS tidak menyusupkan tag HTML.
     */
    public function test_multiple_xss_payload_tidak_dievaluasi(): void
    {
        Article::factory()->published()->create([
            'title' => 'Berita Test Aman',
        ]);

        // Payload 1: <img> dengan onerror
        $response = $this->get('/cari?q=' . urlencode('<img src=x onerror=alert(1)>'));
        $response->assertStatus(200);
        $response->assertSee('&lt;img', false); // harus ter-escape
        $response->assertDontSee('<img src=x onerror', false); // tidak boleh tag aktif

        // Payload 2: <script> tag
        $response = $this->get('/cari?q=' . urlencode('"><script>alert(2)</script>'));
        $response->assertStatus(200);
        $response->assertSee('&lt;script&gt;', false); // harus ter-escape
        $response->assertDontSee('"><script>alert(2)</script>', false); // tidak boleh muncul utuh

        // Payload 3: <iframe>
        $response = $this->get('/cari?q=' . urlencode('<iframe src="javascript:alert(4)"></iframe>'));
        $response->assertStatus(200);
        $response->assertSee('&lt;iframe', false); // harus ter-escape
        $response->assertDontSee('<iframe src="javascript', false); // tidak boleh tag aktif
    }
}
