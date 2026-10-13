<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Nasional',      'description' => 'Berita nasional dan dalam negeri terkini'],
            ['name' => 'Internasional', 'description' => 'Berita dunia dan internasional terkini'],
            ['name' => 'Politik',       'description' => 'Berita seputar politik dan pemerintahan'],
            ['name' => 'Ekonomi',       'description' => 'Berita ekonomi, bisnis, dan keuangan'],
            ['name' => 'Teknologi',     'description' => 'Berita teknologi, inovasi, dan gadget'],
            ['name' => 'Pendidikan',    'description' => 'Berita dunia pendidikan dan akademik'],
            ['name' => 'Kesehatan',     'description' => 'Berita kesehatan, medis, dan gaya hidup sehat'],
            ['name' => 'Olahraga',      'description' => 'Berita olahraga terkini'],
            ['name' => 'Hiburan',       'description' => 'Berita hiburan, film, musik, dan selebriti'],
            ['name' => 'Gaya Hidup',    'description' => 'Tren gaya hidup, kuliner, dan perjalanan'],
            ['name' => 'Lingkungan',    'description' => 'Berita lingkungan hidup dan perubahan iklim'],
            ['name' => 'Opini',         'description' => 'Kolom opini dan analisis mendalam'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
