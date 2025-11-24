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
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug', 'idx_products_slug');
            $table->index('sku', 'idx_products_sku');
            $table->index('quantity', 'idx_products_quantity');
            $table->index('deleted_at', 'idx_products_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_slug');
            $table->dropIndex('idx_products_sku');
            $table->dropIndex('idx_products_quantity');
            $table->dropIndex('idx_products_deleted_at');
        });
    }
};
