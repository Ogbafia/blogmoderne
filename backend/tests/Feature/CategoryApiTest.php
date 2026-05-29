<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    }

    public function test_only_admin_can_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'reader']);
        
        $payload = ['name' => 'Tech', 'color' => '#ff0000'];

        // Guest
        $this->postJson('/api/admin/categories', $payload)->assertStatus(401);

        // Standard user
        $this->actingAs($user)->postJson('/api/admin/categories', $payload)->assertStatus(403);

        // Admin
        $this->actingAs($admin)->postJson('/api/admin/categories', $payload)
             ->assertStatus(201)
             ->assertJsonPath('name', 'Tech');
             
        $this->assertDatabaseHas('categories', ['name' => 'Tech']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)->putJson("/api/admin/categories/{$category->id}", [
            'name' => 'New Name'
        ])->assertStatus(200);

        $this->assertDatabaseHas('categories', ['name' => 'New Name']);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($admin)->deleteJson("/api/admin/categories/{$category->id}")
             ->assertStatus(204);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
