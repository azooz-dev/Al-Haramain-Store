<?php

namespace Modules\Auth\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * TC-AUT-011: Request Password Reset
 * TC-AUT-012: Reset Password with Valid Token
 * TC-AUT-013: Reset Password - Invalid Token
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_password_reset_sends_email(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Act
        $response = $this->postJson('/api/forget-password', [
            'email' => 'user@example.com',
        ]);

        // Assert
        $response->assertStatus(200);
    }

    public function test_reset_password_with_valid_token(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
            'verified' => true,
        ]);

        $token = Password::createToken($user);

        $data = [
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $response = $this->postJson('/api/reset-password', $data);

        // Assert
        $response->assertStatus(200);

        // Verify user can login with new password
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'newpassword123',
        ]);

        $loginResponse->assertStatus(200);
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $data = [
            'email' => 'user@example.com',
            'token' => 'invalid_token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $response = $this->postJson('/api/reset-password', $data);

        // Assert
        $response->assertStatus(422);
    }
}
