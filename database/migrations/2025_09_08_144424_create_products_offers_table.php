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
        Schema::create('products_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('product_color_id')->constrained('product_colors')->onDelete('cascade');
            $table->decimal('variant_price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            // Ensure unique combination of offer, product, variant, and color
            $table->unique(['offer_id', 'product_id', 'product_variant_id', 'product_color_id'], 'unique_offer_product_variant_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_offers');
    }
};
