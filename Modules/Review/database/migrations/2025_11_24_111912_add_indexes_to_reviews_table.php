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
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('status', 'idx_reviews_status');
            $table->index('rating', 'idx_reviews_rating');
            $table->index('created_at', 'idx_reviews_created_at');
            $table->index(['status', 'created_at'], 'idx_reviews_status_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_status');
            $table->dropIndex('idx_reviews_rating');
            $table->dropIndex('idx_reviews_created_at');
            $table->dropIndex('idx_reviews_status_created_at');
        });
    }
};
