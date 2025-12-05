<?php

namespace Tests\Unit\Order\Pipeline;

use Tests\TestCase;
use Modules\User\Entities\User;
use App\Services\Order\Pipeline\ValidateBuyerStep;
use App\Exceptions\Order\OrderException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for ValidateBuyerStep
 */
class ValidateBuyerStepTest extends TestCase
{
    use RefreshDatabase;

    private ValidateBuyerStep $step;

    protected function setUp(): void
    {
        parent::setUp();
        $this->step = app(ValidateBuyerStep::class);
    }

    /**
     * Test buyer validation succeeds with verified user
     */
    public function test_buyer_validation_succeeds_with_verified_user(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $data = ['user_id' => $user->id];

        // Act & Assert - Should not throw exception
        $result = $this->step->handle($data, fn($data) => $data);

        $this->assertIsArray($result);
        $this->assertEquals($user->id, $result['user_id']);
    }

    /**
     * Test buyer validation fails when user not found
     */
    public function test_buyer_validation_fails_when_user_not_found(): void
    {
        // Arrange
        $data = ['user_id' => 99999]; // Non-existent user

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(404);

        $this->step->handle($data, fn($data) => $data);
    }

    /**
     * Test buyer validation fails when user is not verified
     */
    public function test_buyer_validation_fails_when_user_is_not_verified(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();
        $data = ['user_id' => $user->id];

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(403);

        $this->step->handle($data, fn($data) => $data);
    }
}
