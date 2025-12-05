<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Modules\User\Entities\User;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Email Verification Tests
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private EmailVerificationService $verificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $session = $this->app['session']->driver('array');
        $session->start();
        $this->app['request']->setLaravelSession($session);
        $this->verificationService = app(EmailVerificationService::class);
    }

    /**
     * Test email verification succeeds with valid code
     */
    public function test_email_verification_succeeds_with_valid_code(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create([
            'email' => 'test@example.com',
        ]);

        $code = '123456';
        $hashedCode = Hash::make($code);
        $cacheKey = "email_verification_code:user:{$user->id}";
        Cache::put($cacheKey, $hashedCode, now()->addMinutes(10));

        $verificationData = [
            'email' => 'test@example.com',
            'code' => $code,
        ];

        // Act
        $result = $this->verificationService->verify($verificationData);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $user->refresh();
        $this->assertEquals(User::VERIFIED_USER, $user->verified);
        $this->assertNotNull($user->email_verified_at);
    }

    /**
     * Test email verification fails with invalid code
     */
    public function test_email_verification_fails_with_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create([
            'email' => 'test@example.com',
        ]);

        $code = '123456';
        $hashedCode = Hash::make($code);
        $cacheKey = "email_verification_code:user:{$user->id}";
        Cache::put($cacheKey, $hashedCode, now()->addMinutes(10));

        $verificationData = [
            'email' => 'test@example.com',
            'code' => 'wrong_code',
        ];

        // Act
        $result = $this->verificationService->verify($verificationData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
    }
}
