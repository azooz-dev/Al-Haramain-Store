<?php

namespace App\Services\Product;

use function App\Helpers\errorResponse;
use App\Exceptions\Product\ProductException;
use App\Http\Resources\Product\ProductApiResource;
use App\Repositories\Interface\Product\ProductRepositoryInterface;
use App\Repositories\Interface\Product\Color\ProductColorRepositoryInterface;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductService
{
  public function __construct(
    private ProductRepositoryInterface $productRepository,
    private ProductColorRepositoryInterface $productColorRepository,
    private ProductVariantRepositoryInterface $productVariantRepository
  ) {}

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
      $product = $this->getProductById($id);

      return new ProductApiResource($product);
    } catch (ProductException $e) {
      return errorResponse($e->getMessage(), 500);
    }
  }

  public function getProductById(int $id)
  {
    return $this->productRepository->findById($id);
  }

  public function checkColorBelongsToProduct(int $productId, int $colorId)
  {
    return $this->productColorRepository->colorBelongsToProduct($productId, $colorId);
  }

  public function checkVariantBelongsToProductAndColor(int $productId, int $colorId, int $variantId)
  {
    return $this->productVariantRepository->variantBelongsToProductAndColor($productId, $colorId, $variantId);
  }
}
