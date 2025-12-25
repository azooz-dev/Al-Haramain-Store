<?php

namespace Modules\Order\Tests\Feature;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\User\Entities\User;

/**
 * TC-ORD-016: Customer Views Own Orders
 */
class OrderHistoryTest extends TestCase
{

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

        // Verify database state before API call
        $totalOrders = Order::count();
        $userOrderCount = Order::where('user_id', $user->id)->count();
        $this->assertEquals(5, $totalOrders, "Should have 5 orders total, got: {$totalOrders}");
        $this->assertEquals(3, $userOrderCount, "User should have 3 orders, got: {$userOrderCount}");

        // Act - Use actingAs for better authentication in tests
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders');

        // Assert
        $response->assertStatus(200);

        // Verify only user's orders are returned
        $responseData = $response->json('data');

        // The response might be a collection or array - handle both
        if (is_array($responseData) && isset($responseData['data'])) {
            $responseData = $responseData['data'];
        }

        // Extract order IDs from response - the identifier field should contain the order ID
        $returnedOrderIds = [];
        foreach ($responseData as $orderData) {
            // Try different possible field names
            $orderId = $orderData['identifier'] ?? $orderData['id'] ?? null;
            if ($orderId) {
                $returnedOrderIds[] = (int)$orderId;
            }
        }

        // If no identifiers found, the API might be returning all orders without filtering
        // In that case, verify the API filters correctly by checking user_id in the response
        if (empty($returnedOrderIds)) {
            // Check if orders have user information in the response
            $userOrdersInResponse = 0;
            foreach ($responseData as $orderData) {
                // Check if customer/user info is in response
                $customerId = $orderData['customer']['identifier'] ?? $orderData['customer']['id'] ?? $orderData['user_id'] ?? null;
                if ($customerId && (int)$customerId === $user->id) {
                    $userOrdersInResponse++;
                }
            }
            $this->assertEquals(3, $userOrdersInResponse, 'API should return exactly 3 orders for authenticated user, but found: ' . $userOrdersInResponse . ' orders belonging to user in response. Total returned: ' . count($responseData));
        } else {
            // Get the actual orders from database
            $returnedOrders = Order::whereIn('id', $returnedOrderIds)->get();

            // Verify all returned orders belong to the authenticated user
            foreach ($returnedOrders as $order) {
                $this->assertEquals($user->id, $order->user_id, "Order {$order->id} belongs to user {$order->user_id}, but authenticated user is {$user->id}. API should only return orders for authenticated user.");
            }

            // Verify we got exactly 3 orders for our user (the ones we created)
            $userOrderIds = $userOrders->pluck('id')->toArray();
            $returnedUserOrderIds = $returnedOrders->where('user_id', $user->id)->pluck('id')->toArray();

            $this->assertCount(3, $returnedUserOrderIds, 'Expected exactly 3 orders for the user, but got: ' . count($returnedUserOrderIds) . '. Created order IDs: ' . implode(', ', $userOrderIds) . '. Returned order IDs: ' . implode(', ', $returnedUserOrderIds) . '. Total returned: ' . count($responseData));

            // Also verify the API only returns orders for the authenticated user
            $this->assertCount(3, $responseData, 'API should return exactly 3 orders for authenticated user, but returned: ' . count($responseData));
        }
    }

    public function test_returns_empty_list_when_user_has_no_orders(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        // Act - Use actingAs for better authentication in tests
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/orders');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonPath('data', []);
    }
}
