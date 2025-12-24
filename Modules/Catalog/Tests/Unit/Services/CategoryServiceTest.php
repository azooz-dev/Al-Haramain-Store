<?php

namespace Modules\Catalog\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Catalog\Services\Category\CategoryService;
use Modules\Catalog\Repositories\Interface\Category\CategoryRepositoryInterface;
use Modules\Catalog\Contracts\CategoryTranslationServiceInterface;
use Modules\Catalog\Entities\Category\Category;
use Mockery;

/**
 * TC-CAT-011: List Categories - Public Access
 * TC-CAT-012: Category Display with Translations
 */
class CategoryServiceTest extends TestCase
{
    private CategoryService $service;
    private $categoryRepositoryMock;
    private $translationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->categoryRepositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
        $this->translationServiceMock = Mockery::mock(CategoryTranslationServiceInterface::class);
        
        $this->service = new CategoryService(
            $this->categoryRepositoryMock,
            $this->translationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_all_categories(): void
    {
        // Arrange
        $categories = collect([
            Category::factory()->make(['id' => 1]),
            Category::factory()->make(['id' => 2]),
        ]);

        $this->categoryRepositoryMock
            ->shouldReceive('getAllCategories')
            ->once()
            ->andReturn($categories);

        // Act
        $result = $this->service->getCategories();

        // Assert
        $this->assertNotNull($result);
    }

    public function test_finds_category_by_id(): void
    {
        // Arrange
        $category = Category::factory()->make(['id' => 1]);

        $this->categoryRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($category);

        // Act
        $result = $this->service->findCategoryById(1);

        // Assert
        $this->assertNotNull($result);
    }

    public function test_creates_category_with_auto_generated_slug(): void
    {
        // Arrange
        $data = [];
        $translationData = [
            'en' => ['name' => 'Test Category'],
            'ar' => ['name' => 'فئة الاختبار'],
        ];

        $category = Category::factory()->make(['id' => 1, 'slug' => 'test-category']);

        $this->translationServiceMock
            ->shouldReceive('generateSlugFromName')
            ->with('Test Category')
            ->once()
            ->andReturn('test-category');

        $this->categoryRepositoryMock
            ->shouldReceive('create')
            ->with(['slug' => 'test-category'])
            ->once()
            ->andReturn($category);

        $this->translationServiceMock
            ->shouldReceive('saveTranslations')
            ->with($category, $translationData)
            ->once();

        // Act
        $result = $this->service->createCategory($data, $translationData);

        // Assert
        $this->assertInstanceOf(Category::class, $result);
    }
}

