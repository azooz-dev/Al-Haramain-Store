<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Role\RoleSeeder;
use Database\Seeders\Permission\PermissionSeeder;
use Modules\Admin\Database\Seeders\AdminDatabaseSeeder;
use Modules\Catalog\Database\Seeders\CatalogDatabaseSeeder;
use Modules\Offer\Database\Seeders\OfferDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;
use Modules\Coupon\Database\Seeders\CouponDatabaseSeeder;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Payment\Database\Seeders\PaymentDatabaseSeeder;
use Modules\Review\Database\Seeders\ReviewDatabaseSeeder;
use Modules\Favorite\Database\Seeders\FavoriteDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core seeders
            PermissionSeeder::class,
            RoleSeeder::class,

            // Module seeders - Order matters for foreign key constraints
            UserDatabaseSeeder::class,
            AdminDatabaseSeeder::class,
            CatalogDatabaseSeeder::class, // Categories, Products, Variants, Colors, Images, Translations
            OfferDatabaseSeeder::class,   // Offers, Offer Translations, Offer Products
            CouponDatabaseSeeder::class,
            OrderDatabaseSeeder::class,
            PaymentDatabaseSeeder::class,
            ReviewDatabaseSeeder::class,
            FavoriteDatabaseSeeder::class,
        ]);
    }
}
