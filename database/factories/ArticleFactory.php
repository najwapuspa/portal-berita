<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        return [
            'category_id' => Category::factory(),
            'user_id'      => User::factory(),
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . Str::random(5),
            'content'      => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'image'        => null,
            'status'       => 'published',
            'views'        => 0,
        ];
    }

    /** Artikel dengan status draft */
    public function draft(): static
    {
        return $this->state(fn (array $a) => ['status' => 'draft']);
    }

    /** Artikel dengan status published */
    public function published(): static
    {
        return $this->state(fn (array $a) => ['status' => 'published']);
    }
}
