<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Auth\Services\ResetPasswordService;
use Modules\Auth\Services\ForgetPasswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Event;

/**
 * Password Reset Tests
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private ResetPasswordService $resetPasswordService;
    private ForgetPasswordService $forgetPasswordService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetPasswordService = app(ResetPasswordService::class);
        $this->forgetPasswordService = app(ForgetPasswordService::class);
    }

    /**
     * Test password reset request succeeds
     */
    public function test_password_reset_request_succeeds(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'test@example.com',
        ]);

        Event::fake();

        // Act
        $result = $this->forgetPasswordService->forgetPassword('test@example.com');

        // Assert
        $this->assertIsString($result);
        Event::assertDispatched(\Modules\Auth\Events\PasswordResetTokenCreated::class);
    }

    /**
     * Test password reset succeeds with valid token
     */
    public function test_password_reset_succeeds_with_valid_token(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        // Create a password reset token
        $token = Password::broker()->createToken($user);

        $resetData = [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $result = $this->resetPasswordService->resetPassword($resetData);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(200, $result['code']);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    /**
     * Test password reset fails with invalid token
     */
    public function test_password_reset_fails_with_invalid_token(): void
    {
        // Arrange
        $user = User::factory()->verified()->create([
            'email' => 'test@example.com',
        ]);

        $resetData = [
            'email' => 'test@example.com',
            'token' => 'invalid_token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $result = $this->resetPasswordService->resetPassword($resetData);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(422, $result['code']);
    }
}
