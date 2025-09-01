<?php

namespace Database\Seeders\Coupon;

use App\Models\User\User;
use App\Models\Coupon\Coupon;
use Illuminate\Database\Seeder;
use App\Models\Coupon\CouponUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CouponUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample coupon users using factory
        CouponUser::factory(80)->create();

        // Create specific coupon user for testing
        CouponUser::create([
            'coupon_id' => Coupon::first()->id,
            'user_id' => User::first()->id,
            'times_used' => 0,
        ]);
    }
}
