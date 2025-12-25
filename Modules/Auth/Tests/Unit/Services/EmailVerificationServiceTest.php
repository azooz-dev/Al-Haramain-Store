<?php

namespace Modules\Auth\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Auth\Services\EmailVerificationService;
use Modules\Auth\Repositories\Interface\EmailVerificationRepositoryInterface;
use Modules\User\Contracts\UserServiceInterface;
use Modules\User\Entities\User;
use Modules\User\Exceptions\VerificationEmailFailedException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Mockery;

/**
 * TC-AUT-006: Email Verification - Valid Code
 * TC-AUT-007: Email Verification - Invalid Code
 * TC-AUT-008: Email Verification - Expired Code
 */
class EmailVerificationServiceTest extends TestCase
{
    private EmailVerificationService $service;
    private $emailVerificationRepositoryMock;
    private $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->emailVerificationRepositoryMock = Mockery::mock(EmailVerificationRepositoryInterface::class);
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->service = new EmailVerificationService(
            $this->emailVerificationRepositoryMock,
            $this->userServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Cache::flush();
        parent::tearDown();
    }

    public function test_verifies_email_with_valid_code(): void
    {
        // Arrange
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->verified = false;
        $code = '123456';
        $hashedCode = Hash::make($code);
        
        Cache::put("email_verification_code:user:1", $hashedCode, now()->addMinutes(10));

        $this->emailVerificationRepositoryMock
            ->shouldReceive('findUserByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->userServiceMock
            ->shouldReceive('isUserVerified')
            ->with(1)
            ->once()
            ->andReturn(false);

        // Mock UserApiResource - it will be returned by markUserAsVerified
        // The service will call createToken on it, which delegates to the underlying user
        // Create a real UserApiResource with the mocked user
        $user->setRelation('addresses', collect());
        $userApiResource = new \Modules\User\app\Http\Resources\UserApiResource($user);
        
        $tokenMock = (object)['plainTextToken' => 'test-token'];
        $user->shouldReceive('createToken')
            ->with('personal_token')
            ->once()
            ->andReturn($tokenMock);
        
        $this->userServiceMock
            ->shouldReceive('markUserAsVerified')
            ->with(1)
            ->once()
            ->andReturn($userApiResource);

        // Mock session
        $sessionMock = \Mockery::mock(\Illuminate\Session\Store::class);
        $sessionMock->shouldReceive('regenerate')->once();
        
        $requestMock = \Mockery::mock(\Illuminate\Http\Request::class);
        $requestMock->shouldReceive('session')->andReturn($sessionMock);
        $requestMock->shouldReceive('user')->andReturn($userApiResource);
        $requestMock->shouldReceive('setUserResolver')->andReturnSelf();
        
        $this->app->instance('request', $requestMock);

        // Act
        $result = $this->service->verify([
            'email' => 'test@example.com',
            'code' => $code,
        ]);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
    }

    public function test_verification_fails_with_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->make(['id' => 1, 'email' => 'test@example.com']);
        $hashedCode = Hash::make('correct_code');
        
        Cache::put("email_verification_code:user:1", $hashedCode, now()->addMinutes(10));

        $this->emailVerificationRepositoryMock
            ->shouldReceive('findUserByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->verify([
            'email' => 'test@example.com',
            'code' => 'wrong_code',
        ]);

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
}
