<?php

namespace Modules\Order\tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\ValidateBuyerStep;
use Modules\Order\Exceptions\Order\OrderException;
use Modules\User\Entities\User;
use Modules\User\Contracts\UserServiceInterface;
use Mockery;

/**
 * TC-ORD-004: Order Creation - Unauthenticated User
 */
class ValidateBuyerStepTest extends TestCase
{
    private ValidateBuyerStep $step;
    private $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->step = new ValidateBuyerStep($this->userServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_exception_when_buyer_not_found(): void
    {
        // Arrange
        $data = ['user_id' => 999];
        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(404);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }

    public function test_throws_exception_when_buyer_not_verified(): void
    {
        // Arrange
        $user = User::factory()->unverified()->make(['id' => 1]);
        $data = ['user_id' => 1];
        
        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(403);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }

    public function test_passes_when_buyer_is_verified(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);
        $data = ['user_id' => 1];
        
        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals($data, $result);
    }
}

