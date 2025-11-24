<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_translations', function (Blueprint $table) {
            $table->index('name', 'idx_product_translations_name');
            $table->index('local', 'idx_product_translations_local');
        });

        // Add fulltext index using raw SQL (MySQL/MariaDB)
        DB::statement('ALTER TABLE product_translations ADD FULLTEXT INDEX idx_product_translations_name_description_fulltext (name, description)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_translations', function (Blueprint $table) {
            $table->dropIndex('idx_product_translations_name');
            $table->dropIndex('idx_product_translations_local');
        });

        DB::statement('ALTER TABLE product_translations DROP INDEX idx_product_translations_name_description_fulltext');
    }
};
