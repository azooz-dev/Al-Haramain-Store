<?php

namespace Modules\Coupon\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Coupon\Database\Seeders\Coupon\CouponSeeder;

class CouponDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CouponSeeder::class,
        ]);
    }
}
