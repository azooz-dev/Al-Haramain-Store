<?php

namespace Modules\Catalog\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Catalog\Services\Product\ProductService;
use Modules\Catalog\Repositories\Interface\Product\ProductRepositoryInterface;
use Modules\Catalog\Repositories\Interface\Product\Color\ProductColorRepositoryInterface;
use Modules\Catalog\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;
use Modules\Catalog\Contracts\ProductTranslationServiceInterface;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Exceptions\Product\ProductException;
use Mockery;

/**
 * TC-CAT-001: Create Product with All Attributes
 * TC-CAT-002: Create Product - Duplicate SKU
 * TC-CAT-003: Slug Auto-Generation
 * TC-CAT-004: Product Soft Delete
 */
class ProductServiceTest extends TestCase
{
    private ProductService $service;
    private $productRepositoryMock;
    private $productColorRepositoryMock;
    private $productVariantRepositoryMock;
    private $translationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productColorRepositoryMock = Mockery::mock(ProductColorRepositoryInterface::class);
        $this->productVariantRepositoryMock = Mockery::mock(ProductVariantRepositoryInterface::class);
        $this->translationServiceMock = Mockery::mock(ProductTranslationServiceInterface::class);
        
        $this->service = new ProductService(
            $this->productRepositoryMock,
            $this->productColorRepositoryMock,
            $this->productVariantRepositoryMock,
            $this->translationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_product_with_auto_generated_slug(): void
    {
        // Arrange
        $data = ['sku' => 'TEST-001'];
        $translationData = [
            'en' => ['name' => 'Test Product Name'],
            'ar' => ['name' => 'اسم المنتج'],
        ];

        $product = Product::factory()->make(['id' => 1, 'slug' => 'test-product-name']);
        $productMock = Mockery::mock($product)->makePartial();
        $productMock->shouldReceive('fresh')
            ->with(['translations', 'colors.images', 'variants', 'categories.translations'])
            ->once()
            ->andReturn($product);

        $this->translationServiceMock
            ->shouldReceive('generateSlugFromName')
            ->with('Test Product Name')
            ->once()
            ->andReturn('test-product-name');

        $this->productRepositoryMock
            ->shouldReceive('create')
            ->with(array_merge($data, ['slug' => 'test-product-name']))
            ->once()
            ->andReturn($productMock);

        $this->translationServiceMock
            ->shouldReceive('saveTranslation')
            ->with($productMock, $translationData)
            ->once();

        // Act
        $result = $this->service->createProduct($data, $translationData);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
    }

    public function test_checks_color_belongs_to_product(): void
    {
        // Arrange
        $productId = 1;
        $colorId = 2;

        $this->productColorRepositoryMock
            ->shouldReceive('colorBelongsToProduct')
            ->with($productId, $colorId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->checkColorBelongsToProduct($productId, $colorId);

        // Assert
        $this->assertTrue($result);
    }

    public function test_checks_variant_belongs_to_product_and_color(): void
    {
        // Arrange
        $productId = 1;
        $colorId = 2;
        $variantId = 3;

        $this->productVariantRepositoryMock
            ->shouldReceive('variantBelongsToProductAndColor')
            ->with($productId, $colorId, $variantId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->checkVariantBelongsToProductAndColor($productId, $colorId, $variantId);

        // Assert
        $this->assertTrue($result);
    }

    public function test_deletes_product(): void
    {
        // Arrange
        $productId = 1;

        $this->productRepositoryMock
            ->shouldReceive('delete')
            ->with($productId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->deleteProduct($productId);

        // Assert
        $this->assertTrue($result);
    }
}

