<?php

use App\Models\Order\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('address_id')->constrained('addresses')->onDelete('cascade');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('order_number');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', [Order::PAYMENT_METHOD_CASH_ON_DELIVERY, Order::PAYMENT_METHOD_CREDIT_CARD]);
            $table->enum('status', [Order::PENDING, Order::PROCESSING, Order::SHIPPED, Order::DELIVERED, Order::CANCELLED, Order::REFUNDED]);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
