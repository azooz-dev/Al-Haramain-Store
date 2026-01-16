<?php

namespace Modules\Auth\tests\Unit\Services;

use Tests\TestCase;
use Modules\Auth\Services\ResetPasswordService;
use Modules\Auth\Repositories\Interface\ResetPasswordRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Mockery;

/**
 * TC-AUT-009: Password Reset Request
 * TC-AUT-010: Password Reset - Valid Token
 * TC-AUT-011: Password Reset - Invalid Token
 */
class ResetPasswordServiceTest extends TestCase
{
  private ResetPasswordService $service;
  private $resetPasswordRepositoryMock;

  protected function setUp(): void
  {
    parent::setUp();

    $this->resetPasswordRepositoryMock = Mockery::mock(ResetPasswordRepositoryInterface::class);
    $this->service = new ResetPasswordService($this->resetPasswordRepositoryMock);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  public function test_resets_password_successfully(): void
  {
    // Arrange
    $data = [
      'email' => 'test@example.com',
      'token' => 'valid_token',
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ];

    $this->resetPasswordRepositoryMock
      ->shouldReceive('reset')
      ->with($data)
      ->once()
      ->andReturn(Password::PASSWORD_RESET);

    // Act
    $result = $this->service->resetPassword($data);

    // Assert
    $this->assertIsArray($result);
    $this->assertEquals(200, $result['code']);
    $this->assertArrayHasKey('message', $result);
  }

  public function test_reset_password_fails_with_invalid_token(): void
  {
    // Arrange
    $data = [
      'email' => 'test@example.com',
      'token' => 'invalid_token',
      'password' => 'newpassword123',
    ];

    $this->resetPasswordRepositoryMock
      ->shouldReceive('reset')
      ->with($data)
      ->once()
      ->andReturn(Password::INVALID_TOKEN);

    // Act
    $result = $this->service->resetPassword($data);

    // Assert
    $this->assertIsArray($result);
    $this->assertEquals(422, $result['code']);
  }
}
