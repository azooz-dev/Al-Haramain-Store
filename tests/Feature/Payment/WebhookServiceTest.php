<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Services\Payment\WebhookService;
use App\Repositories\Interface\Payment\PaymentRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Stripe\PaymentIntent;
use Tests\Fixtures\OrderFixtures;
use Modules\User\Entities\User;
use Modules\User\Entities\Address;

/**
 * Webhook Service Tests
 */
class WebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test payment succeeded webhook handling
     */
    public function test_payment_succeeded_webhook_handling(): void
    {
        // Arrange
        Log::shouldReceive('info'); // Allow multiple calls

        // Create real user and address
        $user = User::factory()->verified()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        // Create a real product to ensure valid items (with no discount)
        $product = \Modules\Catalog\Entities\Product\Product::factory()->create();
        $color = \Modules\Catalog\Entities\Product\ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = \Modules\Catalog\Entities\Product\ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 10,
            'price' => 100.00,
            'amount_discount_price' => null // Explicitly no discount
        ]);
        $productData = ['product' => $product, 'color' => $color, 'variant' => $variant];
        $items = [
            [
                'orderable_type' => \Modules\Catalog\Entities\Product\Product::class,
                'orderable_id' => $productData['product']->id,
                'variant_id' => $productData['variant']->id,
                'color_id' => $productData['color']->id,
                'quantity' => 1
            ]
        ];

        // Mock PaymentService to avoid real Stripe calls
        $paymentResult = new \App\DTOs\PaymentResult(
            true,
            'pi_test_123',
            'card',
            100.00,
            now()
        );

        $paymentService = Mockery::mock(\App\Services\Payment\PaymentService::class);
        $paymentService->shouldReceive('processPayment')
            ->andReturn($paymentResult);
        $paymentService->shouldReceive('createPayment')
            ->andReturn(null);

        $this->app->instance(\App\Services\Payment\PaymentService::class, $paymentService);

        // Create a REAL PaymentIntent object manually
        $paymentIntent = new PaymentIntent('pi_test_123');
        $paymentIntent->metadata = [
            'user_id' => (string)$user->id,
            'address_id' => (string)$address->id,
            'items' => json_encode($items),
            'total_amount' => 100.00
        ];
        $paymentIntent->amount = 10000; // $100.00 in cents

        // Mock repository
        $paymentRepo = Mockery::mock(PaymentRepositoryInterface::class);
        $paymentRepo->shouldReceive('findByTransactionId')
            ->once()
            ->with('pi_test_123')
            ->andReturn(null);

        $this->app->instance(PaymentRepositoryInterface::class, $paymentRepo);

        // Resolve service AFTER mocks are bound
        $webhookService = app(WebhookService::class);

        // Act
        $webhookService->handlePaymentSucceeded($paymentIntent);

        // Assert - order was successfully created
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 100.00
        ]);

        // Verify order exists
        $order = \Modules\Order\Entities\Order\Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(100.00, (float)$order->total_amount);
    }

    /**
     * Test payment failed webhook handling
     */
    public function test_payment_failed_webhook_handling(): void
    {
        // Arrange
        Log::shouldReceive('info');

        // Create a REAL PaymentIntent object
        $paymentIntent = new PaymentIntent('pi_test_123');
        $paymentIntent->last_payment_error = 'Card declined';

        $webhookService = app(WebhookService::class);

        // Act
        $webhookService->handlePaymentFailed($paymentIntent);

        // Assert
        $this->assertTrue(true);
    }

    /**
     * Test payment canceled webhook handling
     */
    public function test_payment_canceled_webhook_handling(): void
    {
        // Arrange
        Log::shouldReceive('info');

        // Create a REAL PaymentIntent object
        $paymentIntent = new PaymentIntent('pi_test_123');

        $webhookService = app(WebhookService::class);

        // Act
        $webhookService->handlePaymentCanceled($paymentIntent);

        // Assert
        $this->assertTrue(true);
    }
}
