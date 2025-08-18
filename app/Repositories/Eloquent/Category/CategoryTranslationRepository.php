<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryTranslationRepository implements CategoryTranslationRepositoryInterface
{
  public function getTranslationsForCategory(Category $category): Collection
  {
    return $category->translations;
  }

  public function getTranslationByLocale(Category $category, string $locale): ?CategoryTranslation
  {
    return $category->translations()->where('local', $locale)->first();
  }

  public function saveTranslation(Category $category, string $locale, array $data): CategoryTranslation
  {
    return $category->translations()->create([
      'local' => $locale,
      'name' => $data['name'] ?? '',
      'description' => $data['description'] ?? '',
    ]);
  }

  public function updateOrCreateTranslation(Category $category, string $locale, array $data): CategoryTranslation
  {
    return CategoryTranslation::updateOrCreate(
      [
        'category_id' => $category->id,
        'local' => $locale,
      ],
      [
        'name' => $data['name'] ?? '',
        'description' => $data['description'] ?? '',
      ]
    );
  }

  public function deleteTranslationsForCategory(Category $category): bool
  {
    return $category->translations()->delete();
  }

  public function searchTranslations(string $search, string $field = 'name'): Collection
  {
    return CategoryTranslation::where($field, 'like', "%{$search}%")->get();
  }
}
