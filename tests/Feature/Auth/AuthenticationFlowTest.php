<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Enterprise-grade Authentication Tests
 */
class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        // Manually set the session on the request for request() helper to work
        // This is needed when calling services directly (not through HTTP)
        $session = $this->app['session']->driver('array');
        $session->start();
        $this->app['request']->setLaravelSession($session);

        $this->authService = app(AuthService::class);
    }


    /**
     * Test user registration succeeds with valid data
     */
    public function test_user_registration_succeeds_with_valid_data(): void
    {
        // Arrange
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'password' => 'SecurePassword123!',
        ];

        // Act
        $result = $this->authService->register($userData);

        // Assert
        $this->assertNotInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertInstanceOf(\App\Http\Resources\User\UserApiResource::class, $result);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'verified' => User::UNVERIFIED_USER,
        ]);
    }

    /**
     * Test user login succeeds with verified account and correct credentials
     */
    public function test_user_login_succeeds_with_verified_account_and_correct_credentials(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'verified@example.com',
            'password' => 'password123',
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        // Initialize session before calling login
        $this->withSession([]);

        // Act
        $result = $this->authService->login($loginData);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertNotEmpty($result['token']);
        $this->assertInstanceOf(\App\Http\Resources\User\UserApiResource::class, $result['user']);
    }

    /**
     * Test user login fails with unverified account
     */
    public function test_user_login_fails_with_unverified_account(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ];

        // Act
        $result = $this->authService->login($loginData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals(403, $result->getStatusCode());
    }

    /**
     * Test user login fails with invalid credentials
     */
    public function test_user_login_fails_with_invalid_credentials(): void
    {
        // Arrange
        User::factory()->verified()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Act
        $result = $this->authService->login($loginData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertEquals(401, $result->getStatusCode());
    }

    /**
     * Test user login fails with non-existent email
     */
    public function test_user_login_fails_with_nonexistent_email(): void
    {
        // Arrange
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'anypassword',
        ];

        // Act
        $result = $this->authService->login($loginData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertEquals(401, $result->getStatusCode());
    }
}
