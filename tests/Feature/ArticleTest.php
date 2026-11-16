<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_berita_dapat_diakses(): void
    {
        $response = $this->get('/berita');
        $response->assertStatus(200);
    }

    public function test_detail_berita_dapat_diakses(): void
    {
        $article = Article::factory()->create([
            'status' => 'published',
            'slug'   => 'berita-tes-detail',
        ]);

        $response = $this->get('/berita/berita-tes-detail');
        $response->assertStatus(200);
    }

    public function test_berita_draft_tidak_tampil_di_publik(): void
    {
        Article::factory()->draft()->create([
            'slug' => 'berita-draft-tersembunyi',
        ]);

        $response = $this->get('/berita/berita-draft-tersembunyi');
        $response->assertStatus(404);
    }

    public function test_admin_dapat_melihat_daftar_berita(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/berita');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_membuka_form_tambah_berita(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/berita/tambah');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_menambah_berita(): void
    {
        $admin  = User::factory()->create(['role' => 'admin']);
        $cat    = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => 'Berita Tes Penambahan',
            'category_id' => $cat->id,
            'content'     => 'Ini adalah isi berita tes yang cukup panjang untuk validasi.',
            'status'      => 'published',
        ]);

        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('articles', [
            'title' => 'Berita Tes Penambahan',
        ]);
    }

    public function test_tambah_berita_validasi_judul_wajib(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat   = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => '',
            'category_id' => $cat->id,
            'content'     => 'Isi berita.',
            'status'      => 'published',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_tambah_berita_validasi_kategori_wajib(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => 'Berita Tanpa Kategori',
            'category_id' => '',
            'content'     => 'Isi berita.',
            'status'      => 'published',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_tambah_berita_validasi_konten_wajib(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat   = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/berita', [
            'title'       => 'Berita Tanpa Konten',
            'category_id' => $cat->id,
            'content'     => '',
            'status'      => 'published',
        ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_admin_dapat_membuka_form_edit_berita(): void
    {
        $admin   = User::factory()->create(['role' => 'admin']);
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/berita/{$article->id}/edit");
        $response->assertStatus(200);
    }

    public function test_admin_dapat_mengubah_berita(): void
    {
        $admin   = User::factory()->create(['role' => 'admin']);
        $cat     = Category::factory()->create();
        $article = Article::factory()->create([
            'title'       => 'Judul Lama',
            'category_id' => $cat->id,
        ]);

        $response = $this->actingAs($admin)->put("/admin/berita/{$article->id}", [
            'title'       => 'Judul Baru Setelah Edit',
            'category_id' => $cat->id,
            'content'     => 'Konten berita yang sudah diperbarui.',
            'status'      => 'published',
        ]);

        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseHas('articles', [
            'id'    => $article->id,
            'title' => 'Judul Baru Setelah Edit',
        ]);
    }

    public function test_admin_dapat_menghapus_berita(): void
    {
        $admin   = User::factory()->create(['role' => 'admin']);
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/berita/{$article->id}");

        $response->assertRedirect('/admin/berita');
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }
}
