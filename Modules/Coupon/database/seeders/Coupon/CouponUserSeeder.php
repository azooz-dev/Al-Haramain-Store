<?php

namespace Modules\Coupon\Database\Seeders\Coupon;

use Modules\User\Entities\User;
use Modules\Coupon\Entities\Coupon\Coupon;
use Illuminate\Database\Seeder;
use Modules\Coupon\Entities\Coupon\CouponUser;
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
