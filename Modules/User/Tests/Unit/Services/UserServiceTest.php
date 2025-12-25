<?php

namespace Modules\User\Tests\Unit\Services;

use Tests\TestCase;
use Modules\User\Services\UserService;
use Modules\User\Repositories\Interface\UserRepositoryInterface;
use Modules\User\Entities\User;
use Modules\User\Exceptions\UserException;
use Mockery;

/**
 * TC-USR-001: View Own Profile
 * TC-USR-002: Update Own Profile
 * TC-USR-003: Update Other User's Profile - Denied
 */
class UserServiceTest extends TestCase
{
    private UserService $service;
    private $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_updates_user_profile_successfully(): void
    {
        // Arrange
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->verified = true;
        $data = ['first_name' => 'Updated Name'];

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with(1, $data)
            ->once()
            ->andReturn($user);

        // Mock wasChanged to return false (email not changed)
        $user->shouldReceive('wasChanged')
            ->with('email')
            ->once()
            ->andReturn(false);

        // Act
        $result = $this->service->updateUser(1, $data);

        // Assert
        $this->assertInstanceOf(\Modules\User\app\Http\Resources\UserApiResource::class, $result);
    }

    public function test_marks_user_unverified_when_email_changes(): void
    {
        // Arrange
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->verified = true;
        $data = ['email' => 'newemail@example.com'];

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with(1, $data)
            ->once()
            ->andReturn($user);

        // Mock wasChanged to return true for email
        $user->shouldReceive('wasChanged')
            ->with('email')
            ->once()
            ->andReturn(true);
        
        // Mock save method
        $user->shouldReceive('save')
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->updateUser(1, $data);

        // Assert
        $this->assertNotNull($result);
        $this->assertInstanceOf(\Modules\User\app\Http\Resources\UserApiResource::class, $result);
    }

    public function test_checks_user_verification_status(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->isUserVerified(1);

        // Assert
        $this->assertTrue($result);
    }
}

