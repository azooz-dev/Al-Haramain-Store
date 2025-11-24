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
        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'color_id', 'variant_id'], 'idx_favorites_unique');
            $table->index('created_at', 'idx_favorites_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('idx_favorites_unique');
            $table->dropIndex('idx_favorites_created_at');
        });
    }
};
