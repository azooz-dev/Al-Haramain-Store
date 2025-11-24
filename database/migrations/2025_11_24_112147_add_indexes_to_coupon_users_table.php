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
        Schema::table('coupon_users', function (Blueprint $table) {
            $table->index(['coupon_id', 'user_id'], 'idx_coupon_users_composite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_users', function (Blueprint $table) {
            $table->dropIndex('idx_coupon_users_composite');
        });
    }
};
