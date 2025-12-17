<?php

namespace Modules\Favorite\Database\Factories\Favorite;

use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Favorite\Entities\Favorite\Favorite>
 */
class FavoriteFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Favorite::class;
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
