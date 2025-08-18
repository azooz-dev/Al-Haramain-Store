<?php

namespace App\Repositories\interface\Category;

use App\Models\Category\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
  public function findById(int $id): ?Category;

  public function findByIdWithTranslations(int $id): ?Category;

  public function getAllWithTranslations(): Collection;

  public function create(array $data): Category;

  public function update(Category $category, array $data): bool;

  public function delete(Category $category): bool;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;

  public function slugExistsExcluding(string $slug, int $excludeId): bool;
}
