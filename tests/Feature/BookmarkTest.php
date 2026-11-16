<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_menambah_bookmark_baru(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);

        $this->assertDatabaseHas('article_bookmarks', [
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_mencegah_duplikasi_bookmark(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        Bookmark::create([
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);

        // Kedua insert harus gagal karena unique constraint
        $this->expectException(\Illuminate\Database\QueryException::class);

        Bookmark::create([
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_menghapus_bookmark(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);

        $bookmark->delete();

        $this->assertDatabaseMissing('article_bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    public function test_satu_user_dapat_bookmark_banyak_artikel_berbeda(): void
    {
        $user = User::factory()->create();
        $a1   = Article::factory()->create();
        $a2   = Article::factory()->create();
        $a3   = Article::factory()->create();

        Bookmark::create(['user_id' => $user->id, 'article_id' => $a1->id]);
        Bookmark::create(['user_id' => $user->id, 'article_id' => $a2->id]);
        Bookmark::create(['user_id' => $user->id, 'article_id' => $a3->id]);

        $this->assertDatabaseCount('article_bookmarks', 3);
    }
}
