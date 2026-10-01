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
            ['name' => 'Politik',   'description' => 'Berita seputar politik dan pemerintahan'],
            ['name' => 'Ekonomi',   'description' => 'Berita ekonomi, bisnis, dan keuangan'],
            ['name' => 'Olahraga',  'description' => 'Berita olahraga terkini'],
            ['name' => 'Teknologi', 'description' => 'Berita teknologi, inovasi, dan gadget'],
            ['name' => 'Opini',     'description' => 'Kolom opini dan analisis mendalam'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name'        => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
