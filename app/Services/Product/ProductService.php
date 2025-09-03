<?php

namespace App\Services\Product;

use App\Http\Resources\Product\ProductApiResource;
use App\Repositories\interface\Product\ProductRepositoryInterface;

class ProductService
{
  public function __construct(private ProductRepositoryInterface $productRepository) {}

  public function getProducts()
  {
    $products = $this->productRepository->getAllProducts();

    return ProductApiResource::collection($products);
  }

  public function findProductById(int $id)
  {
    $product = $this->productRepository->findById($id);

    return new ProductApiResource($product);
  }
}
