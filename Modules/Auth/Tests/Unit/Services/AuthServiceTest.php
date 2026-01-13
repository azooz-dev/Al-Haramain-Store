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
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            $resultData = $result->getData(true);
            $this->assertIsArray($resultData);
            $this->assertArrayHasKey('message', $resultData);
        } else {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('message', $result);
        }
    }

    public function test_login_returns_user_and_token_when_verified(): void
    {
        // Arrange
        $data = ['email' => 'test@example.com', 'password' => 'password123'];
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->verified = true;
        $user->shouldReceive('tokens')->andReturn(collect());
        $user->shouldReceive('addresses')->andReturn(collect());

        $this->authRepositoryMock
            ->shouldReceive('login')
            ->with($data)
            ->once()
            ->andReturn($user);

        // Mock session to avoid "Session store not set" error
        $sessionMock = \Mockery::mock(\Illuminate\Session\Store::class);
        $sessionMock->shouldReceive('regenerate')->once();

        $requestMock = \Mockery::mock(\Illuminate\Http\Request::class);
        $requestMock->shouldReceive('is')->with('api/*')->andReturn(false);
        $requestMock->shouldReceive('session')->andReturn($sessionMock);
        $requestMock->shouldReceive('user')->andReturn($user);
        $requestMock->shouldReceive('setUserResolver')->andReturnSelf();

        // Swap the request instance
        $this->app->instance('request', $requestMock);

        // Mock User model's createToken method
        // JsonResource delegates method calls to the underlying resource via __call
        // So when UserApiResource->createToken() is called, it calls $user->createToken()
        $tokenMock = (object)['plainTextToken' => 'test-token'];
        $user->shouldReceive('createToken')
            ->with('personal_token')
            ->once()
            ->andReturn($tokenMock);

        // Mock isVerified to return true without database access
        $user->shouldReceive('isVerified')
            ->andReturn(true);

        // Act
        $result = $this->service->login($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals('test-token', $result['token']);
    }

    public function test_login_returns_error_when_unverified(): void
    {
        // Arrange - Fake events to prevent actual dispatch
        \Illuminate\Support\Facades\Event::fake();

        $data = ['email' => 'test@example.com', 'password' => 'password123'];
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->verified = false;

        $this->authRepositoryMock
            ->shouldReceive('login')
            ->with($data)
            ->once()
            ->andReturn($user);

        // Mock isVerified to return false
        $user->shouldReceive('isVerified')
            ->andReturn(false);

        // Act
        $result = $this->service->login($data);

        // Assert
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            $resultData = $result->getData(true);
            $this->assertIsArray($resultData);
            $this->assertArrayHasKey('message', $resultData);
        } else {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('message', $result);
        }

        // Optionally assert that the event was dispatched
        \Illuminate\Support\Facades\Event::assertDispatched(\Modules\Auth\Events\UserRegistered::class);
    }
}
