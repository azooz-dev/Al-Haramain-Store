<?php

namespace Modules\Offer\Tests\Feature;

use Tests\TestCase;
use Modules\Offer\Entities\Offer\Offer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-OFF-001: List Active Offers
 * TC-OFF-002: List Offers - Expired Not Shown
 * TC-OFF-003: List Offers - Inactive Not Shown
 */
class OfferListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_active_offers(): void
    {
        // Arrange
        Offer::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(7),
        ]);
        Offer::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(5),
        ]);

        // Act
        $response = $this->getJson('/api/offers');

        // Assert
        $response->assertStatus(200);
        $offers = $response->json('data');
        $this->assertCount(2, $offers);
    }

    public function test_excludes_expired_offers(): void
    {
        // Arrange
        Offer::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->subDays(1), // Expired
        ]);
        Offer::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(7), // Active
        ]);

        // Act
        $response = $this->getJson('/api/offers');

        // Assert
        $response->assertStatus(200);
        $offers = $response->json('data');
        $this->assertCount(1, $offers);
    }

    public function test_excludes_inactive_offers(): void
    {
        // Arrange
        Offer::factory()->create([
            'status' => 'inactive',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(7),
        ]);
        Offer::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(7),
        ]);

        // Act
        $response = $this->getJson('/api/offers');

        // Assert
        $response->assertStatus(200);
        $offers = $response->json('data');
        $this->assertCount(1, $offers);
        $this->assertEquals('active', $offers[0]['status']);
    }
}

