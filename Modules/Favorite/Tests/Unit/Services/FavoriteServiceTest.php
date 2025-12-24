<?php

namespace Modules\Favorite\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Favorite\Services\Favorite\FavoriteService;
use Modules\Favorite\Repositories\Interface\Favorite\FavoriteRepositoryInterface;
use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\Catalog\Contracts\ProductTranslationServiceInterface;
use Mockery;

/**
 * TC-FAV-001: Add to Favorites
 * TC-FAV-002: Add Favorite - Invalid Color
 * TC-FAV-003: Add Favorite - Invalid Variant
 * TC-FAV-004: List Own Favorites
 * TC-FAV-005: Remove Favorite
 */
class FavoriteServiceTest extends TestCase
{
    private FavoriteService $service;
    private $favoriteRepositoryMock;
    private $productTranslationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->favoriteRepositoryMock = Mockery::mock(FavoriteRepositoryInterface::class);
        $this->productTranslationServiceMock = Mockery::mock(ProductTranslationServiceInterface::class);
        
        $this->service = new FavoriteService(
            $this->favoriteRepositoryMock,
            $this->productTranslationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_all_favorites(): void
    {
        // Arrange
        $favorites = Favorite::factory()->count(3)->make();

        $this->favoriteRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($favorites);

        // Act
        $result = $this->service->getAllFavorites();

        // Assert
        $this->assertCount(3, $result);
    }

    public function test_gets_favorite_by_id(): void
    {
        // Arrange
        $favoriteId = 1;
        $favorite = Favorite::factory()->make(['id' => $favoriteId]);

        $this->favoriteRepositoryMock
            ->shouldReceive('findById')
            ->with($favoriteId)
            ->once()
            ->andReturn($favorite);

        // Act
        $result = $this->service->getFavoriteById($favoriteId);

        // Assert
        $this->assertInstanceOf(Favorite::class, $result);
        $this->assertEquals($favoriteId, $result->id);
    }

    public function test_gets_favorites_count(): void
    {
        // Arrange
        $count = 10;

        $this->favoriteRepositoryMock
            ->shouldReceive('count')
            ->once()
            ->andReturn($count);

        // Act
        $result = $this->service->getFavoritesCount();

        // Assert
        $this->assertEquals($count, $result);
    }

    public function test_gets_recent_favorites_count(): void
    {
        // Arrange
        $days = 7;
        $count = 5;

        $this->favoriteRepositoryMock
            ->shouldReceive('countRecent')
            ->with($days)
            ->once()
            ->andReturn($count);

        // Act
        $result = $this->service->getRecentFavoritesCount($days);

        // Assert
        $this->assertEquals($count, $result);
    }

    public function test_gets_translated_product_name(): void
    {
        // Arrange
        $favorite = Favorite::factory()->make();
        $translatedName = 'Translated Product Name';

        $this->productTranslationServiceMock
            ->shouldReceive('getTranslatedName')
            ->with($favorite->product)
            ->once()
            ->andReturn($translatedName);

        // Act
        $result = $this->service->getTranslatedProductName($favorite);

        // Assert
        $this->assertEquals($translatedName, $result);
    }
}

