<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Category\CategorySeeder;
use Database\Seeders\Category\CategoryTranslationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            CategoryTranslationSeeder::class,
        ]);
    }
}
