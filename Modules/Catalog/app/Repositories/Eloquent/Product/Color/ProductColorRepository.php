<?php

namespace Modules\Catalog\Repositories\Eloquent\Product\Color;

use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Repositories\Interface\Product\Color\ProductColorRepositoryInterface;

class ProductColorRepository implements ProductColorRepositoryInterface
{
  public function colorBelongsToProduct(int $productId, int $colorId): bool
  {
    return ProductColor::where('product_id', $productId)->where('id', $colorId)->exists();
  }
}


