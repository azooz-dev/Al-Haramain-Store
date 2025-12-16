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
        Schema::table('coupons', function (Blueprint $table) {
            $table->unique('code', 'idx_coupons_code_unique');
            $table->index('status', 'idx_coupons_status');
            $table->index(['start_date', 'end_date', 'status'], 'idx_coupons_dates_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropUnique('idx_coupons_code_unique');
            $table->dropIndex('idx_coupons_status');
            $table->dropIndex('idx_coupons_dates_status');
        });
    }
};
