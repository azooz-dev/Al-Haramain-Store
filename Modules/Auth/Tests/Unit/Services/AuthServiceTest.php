<?php

namespace Modules\Auth\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Repositories\Interface\AuthRepositoryInterface;
use Modules\User\Entities\User;
use Modules\User\Exceptions\UserException;
use Mockery;

/**
 * TC-AUT-001: User Registration - Valid Data
 * TC-AUT-002: User Registration - Duplicate Email
 * TC-AUT-003: User Login - Valid Credentials
 * TC-AUT-004: User Login - Invalid Credentials
 * TC-AUT-005: User Login - Unverified Email
 */
class AuthServiceTest extends TestCase
{
    private AuthService $service;
    private $authRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authRepositoryMock = Mockery::mock(AuthRepositoryInterface::class);
        $this->service = new AuthService($this->authRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_registers_user_successfully(): void
    {
        // Arrange
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $user = User::factory()->make(['id' => 1]);

        $this->authRepositoryMock
            ->shouldReceive('register')
            ->with($data)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->register($data);

        // Assert
        $this->assertNotNull($result);
    }

    public function test_registration_handles_exception(): void
    {
        // Arrange
        $data = ['email' => 'duplicate@example.com'];
        
        $this->authRepositoryMock
            ->shouldReceive('register')
            ->with($data)
            ->once()
            ->andThrow(new UserException('Email already exists', 422));

        // Act
        $result = $this->service->register($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
    }

    public function test_login_returns_user_and_token_when_verified(): void
    {
        // Arrange
        $data = ['email' => 'test@example.com', 'password' => 'password123'];
        $user = User::factory()->verified()->make(['id' => 1]);

        $this->authRepositoryMock
            ->shouldReceive('login')
            ->with($data)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->login($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
    }

    public function test_login_returns_error_when_unverified(): void
    {
        // Arrange
        $data = ['email' => 'test@example.com', 'password' => 'password123'];
        $user = User::factory()->unverified()->make(['id' => 1]);

        $this->authRepositoryMock
            ->shouldReceive('login')
            ->with($data)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->login($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
    }
}
