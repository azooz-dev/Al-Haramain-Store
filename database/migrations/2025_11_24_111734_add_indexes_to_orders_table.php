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
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created_at');
            $table->unique('order_number', 'idx_orders_order_number_unique');
            $table->index('payment_method', 'idx_orders_payment_method');
            $table->index(['status', 'created_at'], 'idx_orders_status_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
            $table->dropUnique('idx_orders_order_number_unique');
            $table->dropIndex('idx_orders_payment_method');
            $table->dropIndex('idx_orders_status_created_at');
        });
    }
};
