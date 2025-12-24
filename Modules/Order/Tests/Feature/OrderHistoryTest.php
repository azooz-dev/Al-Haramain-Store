<?php

namespace Modules\Order\Tests\Feature;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-ORD-016: Customer Views Own Orders
 */
class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    /**
     * TC-ORD-016: Customer Views Own Orders
     */
    public function test_customer_can_view_own_orders(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $otherUser = User::factory()->verified()->create();

        // Create 3 orders for the user
        $userOrders = Order::factory()->count(3)->create(['user_id' => $user->id]);
        
        // Create 2 orders for another user
        Order::factory()->count(2)->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson('/api/orders');

        // Assert
        $response->assertStatus(200);
        
        // Verify only user's orders are returned
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);
        
        foreach ($responseData as $orderData) {
            $this->assertEquals($user->id, $orderData['user_id']);
        }
    }

    public function test_returns_empty_list_when_user_has_no_orders(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson('/api/orders');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data', []);
    }
}

