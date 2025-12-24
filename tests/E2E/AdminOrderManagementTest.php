<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E-04: Admin Order Management
 * 
 * Tests the complete flow for admin order management:
 * 1. Admin views orders list
 * 2. Admin views order details
 * 3. Admin updates order status
 * 4. Order status changes are reflected
 */
class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_order_management_flow(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Step 1: Admin views orders list
        $ordersResponse = $this->actingAs($admin, 'admin')
            ->getJson('/api/admin/orders');

        $ordersResponse->assertStatus(200);
        $this->assertGreaterThan(0, count($ordersResponse->json('data')));

        // Step 2: Admin views order details
        $orderResponse = $this->actingAs($admin, 'admin')
            ->getJson("/api/admin/orders/{$order->id}");

        $orderResponse->assertStatus(200);
        $this->assertEquals($order->id, $orderResponse->json('data.id'));

        // Step 3: Admin updates order status
        $updateData = [
            'status' => 'processing',
        ];

        $updateResponse = $this->actingAs($admin, 'admin')
            ->putJson("/api/admin/orders/{$order->id}", $updateData);

        $updateResponse->assertStatus(200);

        // Step 4: Verify order status was updated
        $order->refresh();
        $this->assertEquals('processing', $order->status->value);
    }
}

