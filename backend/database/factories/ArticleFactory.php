<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'status' => 'draft',
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'views_count' => 0,
            'likes_count' => 0,
            'reading_time' => 5,
            'is_featured' => false,
            'published_at' => null,
        ];
    }
}
