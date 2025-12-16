<?php

namespace Modules\Coupon\Database\Seeders\Coupon;

use Modules\Coupon\Entities\Coupon\Coupon;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample coupons using factory
        Coupon::factory(30)->create();

        // Create specific coupon for testing
        Coupon::create([
            'code' => '1234567890',
            'name' => 'Coupon 1',
            'type' => collect([Coupon::FIXED, Coupon::PERCENTAGE])->random(),
            'discount_amount' => 100,
            'usage_limit' => 100,
            'usage_limit_per_user' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => collect([Coupon::ACTIVE, Coupon::INACTIVE])->random(),
        ]);
    }
}
