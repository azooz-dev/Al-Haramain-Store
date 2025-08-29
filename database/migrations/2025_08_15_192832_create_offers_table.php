<?php

use App\Models\Offer\Offer;
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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->enum('discount_type', [Offer::FIXED, Offer::PERCENTAGE]);
            $table->decimal('discount_amount', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->enum('status', [Offer::ACTIVE, Offer::INACTIVE])->default(Offer::ACTIVE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
