<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_dapat_diakses_tanpa_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_homepage_menampilkan_berita_publik(): void
    {
        Article::factory()->published()->create([
            'title' => 'Berita Tes Tampil di Homepage',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Berita Tes Tampil di Homepage');
    }

    public function test_homepage_tidak_menampilkan_berita_draft(): void
    {
        Article::factory()->draft()->create([
            'title' => 'Berita Draft Rahasia',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Berita Draft Rahasia');
    }

    public function test_pencarian_berita_dapat_diakses(): void
    {
        $response = $this->get('/cari?q=ekonomi');
        $response->assertStatus(200);
    }

    public function test_pencarian_menemukan_berita_berdasarkan_judul(): void
    {
        Article::factory()->published()->create([
            'title' => 'Ekonomi Indonesia Tumbuh Pesat',
        ]);

        $response = $this->get('/cari?q=Ekonomi');
        $response->assertStatus(200);
        $response->assertSee('Ekonomi Indonesia Tumbuh Pesat');
    }

    public function test_pencarian_dengan_hasil_kosong_tetap_berfungsi(): void
    {
        $response = $this->get('/cari?q=katakunciyangtidakada12345');
        $response->assertStatus(200);
    }

    public function test_halaman_tentang_kami_dapat_diakses(): void
    {
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
    }

    public function test_halaman_daftar_berita_dapat_diakses(): void
    {
        $response = $this->get('/berita');
        $response->assertStatus(200);
    }

    public function test_pagination_berita_berfungsi(): void
    {
        Article::factory()->count(15)->published()->create();

        $response = $this->get('/berita');
        $response->assertStatus(200);
    }
}
