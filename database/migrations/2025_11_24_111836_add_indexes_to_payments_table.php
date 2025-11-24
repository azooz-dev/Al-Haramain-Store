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
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'idx_payments_status');
            $table->unique('transaction_id', 'idx_payments_transaction_id_unique');
            $table->index('paid_at', 'idx_payments_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status');
            $table->dropUnique('idx_payments_transaction_id_unique');
            $table->dropIndex('idx_payments_paid_at');
        });
    }
};
