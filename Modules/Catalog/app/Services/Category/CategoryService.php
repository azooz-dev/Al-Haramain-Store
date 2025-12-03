<?php

namespace Modules\Catalog\Services\Category;

use Modules\Catalog\Entities\Category\Category;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Category\CategoryApiResource;
use Modules\Catalog\Repositories\Interface\Category\CategoryRepositoryInterface;

class CategoryService
{
  public function __construct(
    private CategoryRepositoryInterface $categoryRepository,
    private CategoryTranslationService $translationService
  ) {}

  public function getCategories()
  {
    $categories = $this->categoryRepository->getAllCategories();

    return CategoryApiResource::collection($categories);
  }

  public function findCategoryById(int $id)
  {
    $category = $this->categoryRepository->findById($id);

    return new CategoryApiResource($category);
  }

  public function createCategory(array $data, array $translationData): Category
  {
    // Generate unique slug from English name if not provided
    if (empty($data['slug']) && !empty($translationData['en']['name'])) {
      $data['slug'] = $this->translationService->generateSlugFromName($translationData['en']['name']);
    }

    // Create category via repository
    $category = $this->categoryRepository->create($data);

    // Save translations via CategoryTranslationService
    $this->translationService->saveTranslations($category, $translationData);

    // Return category with translations loaded
    return $category->fresh(['translations', 'products']);
  }

  public function updateCategory(int $id, array $data, array $translationData): Category
  {
    $category = $this->categoryRepository->findByIdWithTranslations($id);

    // Check if English name changed
    $currentEnglishName = $this->translationService->getTranslatedName($category, 'en');
    $newEnglishName = $translationData['en']['name'] ?? null;

    // If name changed, regenerate slug
    if ($newEnglishName && $newEnglishName !== $currentEnglishName) {
      $data['slug'] = $this->translationService->generateSlugFromName($newEnglishName);
    }

    // Update category via repository
    $category = $this->categoryRepository->update($id, $data);

    // Update translations via CategoryTranslationService
    $this->translationService->saveTranslations($category, $translationData);

    // Return updated category with translations loaded
    return $category->fresh(['translations', 'products']);
  }

  public function deleteCategory(int $id): bool
  {
    return $this->categoryRepository->delete($id);
  }

  public function getCategoriesCount(): int
  {
    return $this->categoryRepository->count();
  }

  public function getQueryBuilder(): Builder
  {
    return $this->categoryRepository->getQueryBuilder();
  }

  public function getProductCount(Category $category): int
  {
    // If products_count is already loaded via withCount, use it
    if (isset($category->products_count)) {
      return (int) $category->products_count;
    }

    // Otherwise, use relationship count
    return $category->products()->count();
  }
}

