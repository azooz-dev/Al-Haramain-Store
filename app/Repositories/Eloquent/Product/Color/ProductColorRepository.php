<?php

namespace App\Repositories\Eloquent\Product\Color;

use App\Models\Product\ProductColor;
use App\Repositories\Interface\Product\Color\ProductColorRepositoryInterface;

class ProductColorRepository implements ProductColorRepositoryInterface
{
  public function colorBelongsToProduct(int $productId, int $colorId): bool
  {
    return ProductColor::where('product_id', $productId)->where('id', $colorId)->exists();
  }
}
