<?php

namespace Modules\Auth\tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * TC-AUT-008: Successful Email Verification
 * TC-AUT-009: Email Verification - Invalid Code
 * TC-AUT-010: Resend Verification - Rate Limit
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_successful_email_verification(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();
        $code = '123456';
        $hashedCode = Hash::make($code);
        
        $cacheKey = "email_verification_code:user:{$user->id}";
        Cache::put($cacheKey, $hashedCode, 600);

        $data = [
            'email' => $user->email,
            'code' => $code,
        ];

        // Act
        $response = $this->postJson('/api/users/email/verify-code', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'token',
            ],
        ]);

        $user->refresh();
        $this->assertTrue($user->verified);
    }

    public function test_email_verification_fails_with_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();
        $code = '123456';
        $hashedCode = Hash::make($code);
        
        $cacheKey = "email_verification_code:user:{$user->id}";
        Cache::put($cacheKey, $hashedCode, 600);

        $data = [
            'email' => $user->email,
            'code' => 'wrong_code',
        ];

        // Act
        $response = $this->postJson('/api/users/email/verify-code', $data);

        // Assert
        $response->assertStatus(442);
    }

    public function test_resend_verification_code_respects_rate_limit(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();

        // Act - Try to resend 4 times
        $responses = [];
        for ($i = 0; $i < 4; $i++) {
            $response = $this->postJson('/api/users/email/resend-code', [
                'email' => $user->email,
            ]);
            $responses[] = $response->status();
        }

        // Assert - First 3 should succeed, 4th should be rate limited
        $this->assertEquals(200, $responses[0]);
        $this->assertEquals(200, $responses[1]);
        $this->assertEquals(200, $responses[2]);
        $this->assertEquals(429, $responses[3]);
    }
}

