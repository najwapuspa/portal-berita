<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $categories = Category::all();

        $titles = [
            'Pemerintah Umumkan Kebijakan Baru Tahun Ini',
            'Harga Bahan Pokok Stabil Menjelang Akhir Bulan',
            'Tim Nasional Menang Dramatis di Laga Terakhir',
            'Perkembangan Kecerdasan Buatan Semakin Pesat',
            'Startup Lokal Raih Pendanaan Baru',
            'Pemilu Daerah Digelar dengan Lancar',
        ];

        foreach ($titles as $index => $title) {
            $article = Article::create([
                'category_id' => $categories[$index % $categories->count()]->id,
                'user_id' => $user->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => 'Ini adalah isi contoh untuk berita "' . $title . '". Konten ini hanya data percobaan untuk portal berita.',
                'status' => $index % 3 === 2 ? 'draft' : 'published',
            ]);

            Comment::create([
                'article_id' => $article->id,
                'user_id' => $user->id,
                'body' => 'Komentar contoh untuk berita ini.',
            ]);
        }
    }
}