<?php

namespace Modules\Order\Tests\Feature;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-ORD-004: Order Creation - Unauthenticated User
 * TC-ORD-017: Customer Cannot View Other's Orders
 */
class OrderAuthorizationTest extends TestCase
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
     * TC-ORD-004: Order Creation - Unauthenticated User
     */
    public function test_unauthenticated_user_cannot_create_order(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10]);

        $orderData = $builder->buildOrderData();

        // Act - Make request without authentication
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(401);
        $this->assertDatabaseMissing('orders', [
            'user_id' => $builder->getUser()->id,
        ]);
    }

    /**
     * TC-ORD-017: Customer Cannot View Other's Orders
     */
    public function test_customer_cannot_view_other_users_orders(): void
    {
        // Arrange
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();

        $order = Order::factory()->create(['user_id' => $user1->id]);

        // Act - User2 tries to view User1's order
        $response = $this->actingAs($user2, 'api')
            ->getJson("/api/orders/{$order->id}");

        // Assert
        $response->assertStatus(403);
    }

    public function test_customer_can_view_own_order(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/orders/{$order->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $order->id);
    }
}

