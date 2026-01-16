<?php

namespace Modules\Auth\tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * TC-AUT-005: Successful Login
 * TC-AUT-006: Login - Invalid Password
 * TC-AUT-007: Login - Non-Existent User
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_returns_token(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/login', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'token',
            ],
        ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ];

        // Act
        $response = $this->postJson('/api/login', $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_login_fails_with_non_existent_user(): void
    {
        // Arrange
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/login', $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_login_fails_with_unverified_user(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/login', $data);

        // Assert
        $response->assertStatus(403);
    }
}

