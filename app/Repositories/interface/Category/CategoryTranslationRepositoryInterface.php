<?php

namespace App\Repositories\interface\Category;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Illuminate\Support\Collection;

interface CategoryTranslationRepositoryInterface
{
  public function getTranslationsForCategory(Category $category): Collection;

  public function getTranslationByLocale(Category $category, string $locale): ?CategoryTranslation;

  public function saveTranslation(Category $category, string $locale, array $data): CategoryTranslation;

  public function updateOrCreateTranslation(Category $category, string $locale, array $data): CategoryTranslation;

  public function deleteTranslationsForCategory(Category $category): bool;

  public function searchTranslations(string $search, string $field = 'name'): Collection;
}
