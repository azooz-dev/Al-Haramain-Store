<?php

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
            $table->foreignId('coupon_id')->constrained('coupons')->nullable();
            $table->string('order_number');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit']);
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded']);
            $table->index('coupon_id');
            $table->index(['user_id', 'coupon_id']);
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
