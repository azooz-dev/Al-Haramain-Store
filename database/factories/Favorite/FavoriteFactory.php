<?php

namespace Database\Factories\Favorite;

use App\Models\Favorite\Favorite;
use App\Models\Product\Product;
use App\Models\Product\ProductColor;
use App\Models\Product\ProductVariant;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite\Favorite>
 */
class FavoriteFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'product_id' => Product::factory(),
      'color_id' => ProductColor::factory(),
      'variant_id' => ProductVariant::factory()
    ];
  }
}
