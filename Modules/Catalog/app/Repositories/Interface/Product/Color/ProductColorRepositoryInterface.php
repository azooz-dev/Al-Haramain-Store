<?php

namespace Modules\Catalog\Repositories\Interface\Product\Color;

interface ProductColorRepositoryInterface
{
  public function colorBelongsToProduct(int $productId, int $colorId): bool;
}


