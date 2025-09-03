<?php

namespace App\Services\Product;

use App\Http\Resources\Product\ProductApiResource;
use App\Repositories\interface\Product\ProductRepositoryInterface;

class ProductService
{
  public function __construct(private ProductRepositoryInterface $productRepository) {}

  public function getProducts()
  {
    return ProductApiResource::collection($this->productRepository->getAllProducts());
  }

  public function findProductById(int $id)
  {
    return new ProductApiResource($this->productRepository->findById($id));
  }
}
