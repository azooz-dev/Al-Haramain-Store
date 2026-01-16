<?php

namespace Modules\User\tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\User\Entities\Address;

/**
 * TC-USR-004: Create Address
 * TC-USR-005: First Address - Auto Default
 * TC-USR-006: Set New Default Address
 * TC-USR-007: List Own Addresses
 * TC-USR-008: Delete Address
 * TC-USR-009: Delete Default Address - Denied
 */
class AddressManagementTest extends TestCase
{

    public function test_user_can_create_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        $data = [
            'street' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'postal_code' => '12345',
            'address_type' => 'Home',
        ];

        // Act
        $response = $this->actingAs($user, 'web')
            ->postJson("/api/users/{$user->id}/addresses", $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => '123 Main St',
        ]);
    }

    public function test_first_address_is_set_as_default(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        $data = [
            'street' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'postal_code' => '12345',
            'address_type' => 'Home',
        ];

        // Act
        $response = $this->actingAs($user, 'web')
            ->postJson("/api/users/{$user->id}/addresses", $data);

        // Assert
        $response->assertStatus(201);
        $address = Address::where('user_id', $user->id)->first();
        $this->assertTrue($address->is_default);
    }

    public function test_user_can_list_own_addresses(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        Address::factory()->count(3)->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'web')
            ->getJson("/api/users/{$user->id}/addresses");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data.data');
    }

    public function test_user_can_set_new_default_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $address1 = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $address2 = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $data = ['is_default' => true];

        // Act
        $response = $this->actingAs($user, 'web')
            ->putJson("/api/users/{$user->id}/addresses/{$address2->id}", $data);

        // Assert
        $response->assertStatus(200);
        $address1->refresh();
        $address2->refresh();
        $this->assertFalse($address1->is_default);
        $this->assertTrue($address2->is_default);
    }

    public function test_user_can_delete_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        // Act
        $response = $this->actingAs($user, 'web')
            ->deleteJson("/api/users/{$user->id}/addresses/{$address->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_user_cannot_delete_default_address(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        // Act
        $response = $this->actingAs($user, 'web')
            ->deleteJson("/api/users/{$user->id}/addresses/{$address->id}");

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }
}
