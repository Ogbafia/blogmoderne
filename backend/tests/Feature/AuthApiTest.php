<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'role', 'avatar'],
            ])
            ->assertJsonPath('user.email', 'jean@example.com')
            ->assertJsonPath('user.role', 'reader');

        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'role' => 'reader',
            'is_active' => true,
        ]);
    }

    public function test_user_can_login_fetch_profile_update_profile_and_logout(): void
    {
        User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $login = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'Password123',
        ]);

        $token = $login->assertOk()
            ->assertJsonStructure(['message', 'token', 'user'])
            ->assertJsonPath('user.role', 'admin')
            ->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'admin@example.com');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/auth/profile', [
                'name' => 'Admin Updated',
                'bio' => 'Bio mise à jour',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Admin Updated')
            ->assertJsonPath('data.bio', 'Bio mise à jour');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_guest_cannot_access_current_user_endpoint(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'blocked@example.com',
            'password' => Hash::make('Password123'),
            'is_active' => false,
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'blocked@example.com',
            'password' => 'Password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Votre compte a été désactivé.');
    }
}
