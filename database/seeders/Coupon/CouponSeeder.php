<?php

namespace Database\Seeders\Coupon;

use App\Models\Coupon\Coupon;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'code' => '1234567890',
            'name' => 'Coupon 1',
            'type' => Random::element([Coupon::FIXED, Coupon::PERCENTAGE]),
            'discount_amount' => 100,
            'usage_limit' => 100,
            'usage_limit_per_user' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => Random::element([Coupon::ACTIVE, Coupon::INACTIVE]),
        ]);
    }
}
