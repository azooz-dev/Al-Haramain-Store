<?php

namespace Modules\Auth\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;

/**
 * TC-AUT-001: Successful Registration
 * TC-AUT-002: Registration - Duplicate Email
 * TC-AUT-003: Registration - Invalid Email Format
 * TC-AUT-004: Registration - Missing Required Fields
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Event::fake();
    }

    public function test_successful_registration_creates_user(): void
    {
        // Arrange
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        // Act
        $response = $this->postJson('/api/register', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'verified' => false,
        ]);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        // Arrange
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        // Act
        $response = $this->postJson('/api/register', $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_invalid_email_format(): void
    {
        // Arrange
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        // Act
        $response = $this->postJson('/api/register', $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_missing_required_fields(): void
    {
        // Arrange
        $data = [
            'first_name' => 'John',
            // Missing email, password, etc.
        ];

        // Act
        $response = $this->postJson('/api/register', $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}

