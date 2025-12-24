<?php

namespace Modules\Coupon\Tests\Feature;

use Tests\TestCase;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Modules\User\Entities\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-COU-001 to TC-COU-009: All Coupon Application Tests
 */
class ApplyCouponTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    public function test_applies_valid_fixed_coupon(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $coupon = Coupon::factory()->active()->create([
            'code' => 'FIXED10',
            'type' => CouponType::FIXED,
            'discount_amount' => 10.00,
        ]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/coupons/{$coupon->code}/{$user->id}");

        // Assert
        $response->assertStatus(200);
    }

    public function test_applies_valid_percentage_coupon(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $coupon = Coupon::factory()->active()->create([
            'code' => 'PERCENT10',
            'type' => CouponType::PERCENTAGE,
            'discount_amount' => 10, // 10%
        ]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/coupons/{$coupon->code}/{$user->id}");

        // Assert
        $response->assertStatus(200);
    }

    public function test_rejects_expired_coupon(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'status' => CouponStatus::ACTIVE,
            'end_date' => Carbon::now()->subDay(),
        ]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/coupons/{$coupon->code}/{$user->id}");

        // Assert
        $response->assertStatus(422);
    }

    public function test_rejects_inactive_coupon(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'INACTIVE',
            'status' => CouponStatus::INACTIVE,
        ]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/coupons/{$coupon->code}/{$user->id}");

        // Assert
        $response->assertStatus(422);
    }

    public function test_rejects_coupon_not_yet_valid(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'FUTURE',
            'status' => CouponStatus::ACTIVE,
            'start_date' => Carbon::now()->addDay(),
        ]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/coupons/{$coupon->code}/{$user->id}");

        // Assert
        $response->assertStatus(422);
    }
}

