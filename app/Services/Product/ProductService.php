<?php

namespace App\Services\Product;

use App\Exceptions\Product\ProductException;
use App\Http\Resources\Product\ProductApiResource;
use App\Repositories\interface\Product\ProductRepositoryInterface;
use function App\Helpers\errorResponse;

class ProductService
{
  public function __construct(private ProductRepositoryInterface $productRepository) {}

  public function getProducts()
  {
    try {
      $products = $this->productRepository->getAllProducts();

      return ProductApiResource::collection($products);
    } catch (ProductException $e) {
      return errorResponse($e->getMessage(), 500);
    }
  }

  public function findProductById(int $id)
  {
    try {
      $product = $this->productRepository->findById($id);

      return new ProductApiResource($product);
    } catch (ProductException $e) {
      return errorResponse($e->getMessage(), 500);
    }
  }
}
