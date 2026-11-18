<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleValidationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function validArticleData(array $overrides = []): array
    {
        return array_merge([
            'title'       => 'Berita Tes Valid Lengkap',
            'category_id' => Category::factory()->create()->id,
            'content'     => 'Ini adalah isi berita tes yang cukup panjang untuk validasi minimum.',
            'status'      => 'published',
        ], $overrides);
    }

    /**
     * image_url dengan skema javascript: harus ditolak.
     */
    public function test_image_url_skema_javascript_ditolak(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'image_url' => 'javascript:alert(1)',
        ]));

        $response->assertSessionHasErrors('image_url');
    }

    /**
     * image_url dengan skema data: harus ditolak.
     */
    public function test_image_url_skema_data_ditolak(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'image_url' => 'data:text/html,<script>alert(1)</script>',
        ]));

        $response->assertSessionHasErrors('image_url');
    }

    /**
     * image_url dengan skema ftp: harus ditolak.
     */
    public function test_image_url_skema_ftp_ditolak(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'image_url' => 'ftp://malicious.example.com/image.jpg',
        ]));

        $response->assertSessionHasErrors('image_url');
    }

    /**
     * image_url dengan https:// yang sah harus diterima.
     */
    public function test_image_url_https_sah_diterima(): void
    {
        $admin = $this->createAdmin();
        $cat   = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => 'Berita dengan URL Gambar HTTPS',
            'category_id' => $cat->id,
            'content'     => 'Isi berita tes yang cukup panjang untuk validasi.',
            'status'      => 'published',
            'image_url'   => 'https://example.com/photo.jpg',
        ]);

        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('articles', [
            'title' => 'Berita dengan URL Gambar HTTPS',
            'image' => 'https://example.com/photo.jpg',
        ]);
    }

    /**
     * image_url dengan http:// yang sah harus diterima.
     */
    public function test_image_url_http_sah_diterima(): void
    {
        $admin = $this->createAdmin();
        $cat   = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => 'Berita dengan URL Gambar HTTP',
            'category_id' => $cat->id,
            'content'     => 'Isi berita tes yang cukup panjang untuk validasi.',
            'status'      => 'published',
            'image_url'   => 'http://example.com/photo.jpg',
        ]);

        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('articles', [
            'title' => 'Berita dengan URL Gambar HTTP',
            'image' => 'http://example.com/photo.jpg',
        ]);
    }

    /**
     * Judul minimal 3 karakter.
     */
    public function test_judul_minimal_3_karakter(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'title' => 'AB',
        ]));

        $response->assertSessionHasErrors('title');
    }

    /**
     * Konten minimal 10 karakter.
     */
    public function test_konten_minimal_10_karakter(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'content' => 'Pendek',
        ]));

        $response->assertSessionHasErrors('content');
    }

    /**
     * Status tidak valid harus ditolak.
     */
    public function test_status_tidak_valid_ditolak(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'status' => 'archived',
        ]));

        $response->assertSessionHasErrors('status');
    }

    /**
     * Kategori yang tidak ada di database harus ditolak.
     */
    public function test_kategori_tidak_ada_ditolak(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/berita', $this->validArticleData([
            'category_id' => 99999,
        ]));

        $response->assertSessionHasErrors('category_id');
    }

    /**
     * Validasi update juga menolak skema berbahaya.
     */
    public function test_update_menolak_image_url_berbahaya(): void
    {
        $admin   = $this->createAdmin();
        $cat     = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $cat->id]);

        $response = $this->actingAs($admin)->put("/admin/berita/{$article->id}", [
            'title'       => 'Judul Update Tes',
            'category_id' => $cat->id,
            'content'     => 'Isi berita yang sudah diperbarui dengan cukup panjang.',
            'status'      => 'published',
            'image_url'   => 'javascript:alert(document.cookie)',
        ]);

        $response->assertSessionHasErrors('image_url');
    }
}
