<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_dapat_mengirim_komentar(): void
    {
        $user    = User::factory()->create(['role' => 'user']);
        $article = Article::factory()->published()->create([
            'slug' => 'artikel-tes-komentar',
        ]);

        $response = $this->actingAs($user)->post("/berita/{$article->slug}/komentar", [
            'body' => 'Ini komentar tes yang valid.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id'    => $user->id,
            'body'       => 'Ini komentar tes yang valid.',
        ]);
    }

    public function test_tamu_tidak_dapat_mengirim_komentar(): void
    {
        $article = Article::factory()->published()->create([
            'slug' => 'artikel-tes-komentar-tamu',
        ]);

        $response = $this->post("/berita/{$article->slug}/komentar", [
            'body' => 'Komentar dari tamu.',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'body' => 'Komentar dari tamu.',
        ]);
    }

    public function test_komentar_validasi_body_wajib(): void
    {
        $user    = User::factory()->create(['role' => 'user']);
        $article = Article::factory()->published()->create([
            'slug' => 'artikel-tes-komentar-validasi',
        ]);

        $response = $this->actingAs($user)->post("/berita/{$article->slug}/komentar", [
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_admin_dapat_melihat_halaman_moderasi_komentar(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/komentar');
        $response->assertStatus(200);
    }

    public function test_user_biasa_tidak_dapat_melihat_moderasi_komentar(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/komentar');
        $response->assertStatus(403);
    }

    public function test_admin_dapat_menghapus_komentar(): void
    {
        $admin   = User::factory()->create(['role' => 'admin']);
        $user    = User::factory()->create();
        $article = Article::factory()->create();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id'    => $user->id,
            'body'       => 'Komentar yang akan dihapus admin.',
        ]);

        $response = $this->actingAs($admin)->delete("/komentar/{$comment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }
}
