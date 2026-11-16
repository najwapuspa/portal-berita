<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_melihat_daftar_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/kategori');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_menambah_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/kategori', [
            'name'        => 'Kategori Tes Baru',
            'description' => 'Deskripsi kategori tes',
        ]);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'name' => 'Kategori Tes Baru',
        ]);
    }

    public function test_tambah_kategori_validasi_nama_wajib(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/kategori', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_dapat_mengubah_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat   = Category::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/kategori/{$cat->id}", [
            'name'        => 'Nama Baru',
            'description' => 'Deskripsi baru',
        ]);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'id'   => $cat->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_admin_tidak_dapat_menghapus_kategori_yang_memiliki_artikel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat   = Category::factory()->create();
        Article::factory()->create(['category_id' => $cat->id]);

        $response = $this->actingAs($admin)->delete("/admin/kategori/{$cat->id}");

        $response->assertRedirect('/admin/kategori');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_admin_dapat_menghapus_kategori_kosong(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat   = Category::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/kategori/{$cat->id}");

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_halaman_kategori_publik_dapat_diakses(): void
    {
        $cat = Category::factory()->create(['slug' => 'tes-kategori']);

        $response = $this->get('/kategori/tes-kategori');
        $response->assertStatus(200);
    }

    public function test_halaman_kategori_tidak_ada_menghasilkan_404(): void
    {
        $response = $this->get('/kategori/tidak-ada');
        $response->assertStatus(404);
    }
}
