<?php

namespace Database\Seeders\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
