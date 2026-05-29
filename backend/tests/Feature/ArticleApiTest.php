<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_published_articles(): void
    {
        Article::factory()->create(['title' => 'Published Article', 'status' => 'published', 'published_at' => now()]);
        Article::factory()->create(['title' => 'Draft Article', 'status' => 'draft']);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Published Article'])
                 ->assertJsonMissing(['title' => 'Draft Article']);
    }

    public function test_author_can_create_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $category = Category::factory()->create();

        $payload = [
            'title' => 'My new article',
            'content' => 'Content here',
            'category_id' => $category->id
        ];

        $response = $this->actingAs($author)->postJson('/api/articles', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', [
            'title' => 'My new article',
            'author_id' => $author->id,
            'status' => 'draft'
        ]);
    }

    public function test_admin_can_publish_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Article::factory()->create(['status' => 'draft']);

        $this->actingAs($admin)->postJson("/api/admin/articles/{$article->id}/publish")
             ->assertStatus(200);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'status' => 'published'
        ]);
    }
}
