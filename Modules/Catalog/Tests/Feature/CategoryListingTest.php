<?php

namespace Modules\Catalog\Tests\Feature;

use Tests\TestCase;
use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Category\CategoryTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-CAT-011: List Categories - Public Access
 * TC-CAT-012: Category Display with Translations
 */
class CategoryListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_categories(): void
    {
        // Arrange
        $categories = Category::factory()->count(3)->create();

        // Create translations for each category
        foreach ($categories as $category) {
            CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'local' => 'en',
            ]);
            CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'local' => 'ar',
            ]);
        }

        // Act
        $response = $this->getJson('/api/categories');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'identifier',
                        'slug',
                        'en' => [
                            'title',
                            'details',
                        ],
                        'ar' => [
                            'title',
                            'details',
                        ],
                    ],
                ],
            ],
            'message',
            'status',
        ]);
    }

    public function test_categories_returned_with_translations(): void
    {
        // Arrange
        $category = Category::factory()->create();
        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'local' => 'en',
            'name' => 'Test Category',
        ]);
        CategoryTranslation::factory()->create([
            'category_id' => $category->id,
            'local' => 'ar',
            'name' => 'فئة الاختبار',
        ]);

        // Act
        $response = $this->withHeader('Accept-Language', 'en')
            ->getJson('/api/categories');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Test Category'], 'data.*.en');
    }
}
