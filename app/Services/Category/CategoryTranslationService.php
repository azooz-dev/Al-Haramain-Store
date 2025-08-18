<?php

namespace App\Services\Category;

use App\Models\Category\Category;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryTranslationService
{
  public function __construct(
    private CategoryRepositoryInterface $categoryRepository,
    private CategoryTranslationRepositoryInterface $translationRepository
  ) {}

  public function getTranslationsForCategory(Category $category): Collection
  {
    return $this->translationRepository->getTranslationsForCategory($category);
  }

  public function getTranslatedName(Category $category, ?string $locale = null): string
  {
    $locale = $locale ?: app()->getLocale();
    $translation = $this->translationRepository->getTranslationByLocale($category, $locale);

    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($category, 'en');
    }

    return $translation?->name ?? '';
  }

  public function getTranslatedDescription(Category $category, ?string $locale = null): string
  {
    $locale = $locale ?: app()->getLocale();
    $translation = $this->translationRepository->getTranslationByLocale($category, $locale);

    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($category, 'en');
    }

    return $translation?->description ?? '';
  }

  public function saveTranslations(Category $category, array $translationData): void
  {
    foreach (['en', 'ar'] as $locale) {
      $payload = $translationData[$locale] ?? [];

      if (!empty($payload['name']) || !empty($payload['description'])) {
        $this->translationRepository->updateOrCreateTranslation($category, $locale, $payload);
      }
    }
  }


  public function getFormData(Category $category): array
  {
    $enTranslation = $this->translationRepository->getTranslationByLocale($category, 'en');
    $arTranslation = $this->translationRepository->getTranslationByLocale($category, 'ar');

    return [
      'slug' => $category->slug,
      'image' => $category->image,
      'en' => [
        'name' => $enTranslation?->name ?? '',
        'description' => $enTranslation?->description ?? '',
      ],
      'ar' => [
        'name' => $arTranslation?->name ?? '',
        'description' => $arTranslation?->description ?? '',
      ],
    ];
  }

  public function findCategoryWithTranslations(int $id): ?Category
  {
    return $this->categoryRepository->findByIdWithTranslations($id);
  }

  public function searchCategoriesByName(string $search): Collection
  {
    return $this->categoryRepository->searchByName($search);
  }

  /**
   * Generate a unique slug from translation data
   */
  public function generateSlugFromName(string $categoryName): string
  {
    return $this->generateUniqueSlug($categoryName);
  }

  /**
   * Generate a unique slug from a name
   */
  public function generateUniqueSlug(string $name): string
  {
    $baseSlug = Str::slug($name, '-');
    $slug = $baseSlug;
    $counter = 1;

    while ($this->slugExists($slug)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  /**
   * Check if a slug already exists
   */
  private function slugExists(string $slug): bool
  {
    return $this->categoryRepository->slugExists($slug);
  }

  /**
   * Generate slug for existing category (for updates)
   */
  public function generateSlugForUpdate(string $name, int $categoryId): string
  {
    $baseSlug = Str::slug($name, '-');
    $slug = $baseSlug;
    $counter = 1;

    while ($this->slugExistsExcluding($slug, $categoryId)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  /**
   * Check if slug exists excluding a specific category ID
   */
  private function slugExistsExcluding(string $slug, int $excludeId): bool
  {
    return $this->categoryRepository->slugExistsExcluding($slug, $excludeId);
  }
}
