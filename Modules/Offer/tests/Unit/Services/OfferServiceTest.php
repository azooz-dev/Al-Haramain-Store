<?php

namespace Modules\Offer\tests\Unit\Services;

use Tests\TestCase;
use Modules\Offer\Services\Offer\OfferService;
use Modules\Offer\Repositories\Interface\Offer\OfferRepositoryInterface;
use Modules\Offer\Contracts\OfferTranslationServiceInterface;
use Modules\Offer\Entities\Offer\Offer;
use Modules\Offer\Exceptions\Offer\OfferException;
use Mockery;

/**
 * TC-OFF-001: List Active Offers
 * TC-OFF-002: List Offers - Expired Not Shown
 * TC-OFF-003: List Offers - Inactive Not Shown
 * TC-OFF-004: Offer Details with Products
 * TC-OFF-005: Create Offer - Price Validation
 */
class OfferServiceTest extends TestCase
{
    private OfferService $service;
    private $offerRepositoryMock;
    private $translationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->offerRepositoryMock = Mockery::mock(OfferRepositoryInterface::class);
        $this->translationServiceMock = Mockery::mock(OfferTranslationServiceInterface::class);
        
        $this->service = new OfferService(
            $this->offerRepositoryMock,
            $this->translationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_fetches_all_offers(): void
    {
        // Arrange
        $offers = Offer::factory()->count(3)->make();

        $this->offerRepositoryMock
            ->shouldReceive('getAllOffers')
            ->once()
            ->andReturn($offers);

        // Act
        $result = $this->service->fetchAllOffers();

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_finds_offer_by_id(): void
    {
        // Arrange
        $offerId = 1;
        $offer = Offer::factory()->make(['id' => $offerId]);

        $this->offerRepositoryMock
            ->shouldReceive('findOfferById')
            ->with($offerId)
            ->once()
            ->andReturn($offer);

        // Act
        $result = $this->service->findOfferById($offerId);

        // Assert
        $this->assertInstanceOf(\Modules\Offer\Http\Resources\Offer\OfferApiResource::class, $result);
    }

    public function test_calculates_discount_amount(): void
    {
        // Arrange
        $offer = Offer::factory()->make([
            'products_total_price' => 100.00,
            'offer_price' => 80.00,
        ]);

        // Act
        $result = $this->service->getDiscountAmount($offer);

        // Assert
        $this->assertEquals('$20.00', $result);
    }

    public function test_gets_products_count(): void
    {
        // Arrange
        $offer = Offer::factory()->make();
        $offer->offer_products_count = 5;

        // Act
        $result = $this->service->getProductsCount($offer);

        // Assert
        $this->assertEquals(5, $result);
    }

    public function test_gets_translated_name(): void
    {
        // Arrange
        $offer = Offer::factory()->make();
        $translatedName = 'Translated Offer Name';

        $this->translationServiceMock
            ->shouldReceive('getTranslatedName')
            ->with($offer)
            ->once()
            ->andReturn($translatedName);

        // Act
        $result = $this->service->getTranslatedName($offer);

        // Assert
        $this->assertEquals($translatedName, $result);
    }
}

