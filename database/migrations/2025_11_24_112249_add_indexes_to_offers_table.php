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
        Schema::table('offers', function (Blueprint $table) {
            $table->index('status', 'idx_offers_status');
            $table->index(['start_date', 'end_date', 'status'], 'idx_offers_dates_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex('idx_offers_status');
            $table->dropIndex('idx_offers_dates_status');
        });
    }
};
