<?php

namespace Modules\Catalog\Database\Seeders\Category;

use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Category\CategoryTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample category translations using factory
        CategoryTranslation::factory(40)->create();

        // Create specific category translations for testing
        CategoryTranslation::create([
            'category_id' => Category::first()->id,
            'name' => 'Electronics',
            'local' => 'en',
            'description' => 'Electronics description',
        ]);

        CategoryTranslation::create([
            'category_id' => Category::first()->id,
            'name' => 'الكهربائية',
            'local' => 'ar',
            'description' => 'الكهربائية description',
        ]);
    }
}

