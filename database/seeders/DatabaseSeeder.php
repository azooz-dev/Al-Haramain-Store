<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Role\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Offer\OfferSeeder;
use Database\Seeders\Order\OrderSeeder;
use Database\Seeders\Coupon\CouponSeeder;
use Database\Seeders\Review\ReviewSeeder;
use Database\Seeders\Order\OrderItemSeeder;
use Database\Seeders\Payment\PaymentSeeder;
use Database\Seeders\Coupon\CouponUserSeeder;
use Database\Seeders\Favorite\FavoriteSeeder;
use Database\Seeders\Permission\PermissionSeeder;
use Database\Seeders\Offer\OfferTranslationSeeder;
use Modules\Catalog\Database\Seeders\CatalogDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserDatabaseSeeder::class, // User module seeders
            // PermissionSeeder::class,
            // RoleSeeder::class,
            // AdminSeeder::class,
            // CatalogDatabaseSeeder::class, // Catalog module seeders (Category, Product, etc.)
            OfferSeeder::class,
            OfferTranslationSeeder::class,
            // CouponSeeder::class,
            // CouponUserSeeder::class,
            // OrderSeeder::class,
            // OrderItemSeeder::class,
            // PaymentSeeder::class,
            // ReviewSeeder::class,
            FavoriteSeeder::class,
        ]);
    }
}
