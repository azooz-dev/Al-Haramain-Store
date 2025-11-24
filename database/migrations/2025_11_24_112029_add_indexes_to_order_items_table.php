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
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('is_reviewed', 'idx_order_items_is_reviewed');
            $table->index(['orderable_type', 'orderable_id'], 'idx_order_items_orderable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_is_reviewed');
            $table->dropIndex('idx_order_items_orderable');
        });
    }
};
