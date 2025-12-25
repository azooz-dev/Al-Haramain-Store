<?php

namespace Modules\User\Tests\Unit\Services;

use Tests\TestCase;
use Modules\User\Services\UserAddressService;
use Modules\User\Repositories\Interface\UserAddressRepositoryInterface;
use Modules\User\Contracts\UserServiceInterface;
use Modules\User\Entities\Address;
use Modules\User\Entities\User;
use Modules\User\app\Http\Resources\UserAddresses\AddressApiResource;
use Mockery;

/**
 * TC-USR-004: Create Address
 * TC-USR-005: First Address - Auto Default
 * TC-USR-006: Set New Default Address
 * TC-USR-007: List Own Addresses
 * TC-USR-008: Delete Address
 * TC-USR-009: Delete Default Address - Denied
 */
class AddressServiceTest extends TestCase
{
    private UserAddressService $service;
    private $addressRepositoryMock;
    private $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addressRepositoryMock = Mockery::mock(UserAddressRepositoryInterface::class);
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->service = new UserAddressService($this->addressRepositoryMock, $this->userServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_stores_address_successfully(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);
        $data = [
            'street' => '123 Main St',
            'city' => 'City',
            'country' => 'Country',
        ];

        $address = Address::factory()->make(['id' => 1]);

        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->addressRepositoryMock
            ->shouldReceive('storeUserAddress')
            ->with($data, 1)
            ->once()
            ->andReturn($address);

        // Act
        $result = $this->service->storeUserAddress($data, 1);

        // Assert
        $this->assertInstanceOf(AddressApiResource::class, $result);
    }

    public function test_gets_all_user_addresses(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);
        $addresses = Address::factory()->count(3)->make();

        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->addressRepositoryMock
            ->shouldReceive('getAllUserAddresses')
            ->with(1)
            ->once()
            ->andReturn($addresses);

        // Act
        $result = $this->service->getAllUserAddresses(1);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_updates_user_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);
        $addressId = 1;
        $data = ['street' => 'Updated Street'];

        $updatedAddress = Address::factory()->make(['id' => $addressId]);

        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->addressRepositoryMock
            ->shouldReceive('updateUserAddress')
            ->with($data, 1, $addressId)
            ->once()
            ->andReturn($updatedAddress);

        // Act
        $result = $this->service->updateUserAddress($data, 1, $addressId);

        // Assert
        $this->assertInstanceOf(AddressApiResource::class, $result);
    }

    public function test_deletes_user_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->make(['id' => 1]);
        $addressId = 1;
        // Explicitly set is_default to false to ensure the deletion proceeds
        $address = Address::factory()->make(['id' => $addressId, 'user_id' => 1, 'is_default' => false]);

        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->addressRepositoryMock
            ->shouldReceive('getAddressById')
            ->with($addressId)
            ->once()
            ->andReturn($address);

        $this->addressRepositoryMock
            ->shouldReceive('deleteUserAddress')
            ->with(1, $addressId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->deleteUserAddress(1, $addressId);

        // Assert
        // deleteUserAddress returns JsonResponse from showMessage()
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $resultData = $result->getData(true);
        $this->assertArrayHasKey('message', $resultData);
    }
}
