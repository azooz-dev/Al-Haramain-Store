<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
  public function findById(int $id): ?Category
  {
    return Category::find($id);
  }

  public function findByIdWithTranslations(int $id): ?Category
  {
    return Category::with('translations')->find($id);
  }

  public function getAllWithTranslations(): Collection
  {
    return Category::with('translations')->get();
  }

  public function create(array $data): Category
  {
    return Category::create($data);
  }

  public function update(Category $category, array $data): bool
  {
    return $category->update($data);
  }

  public function delete(Category $category): bool
  {
    return $category->delete();
  }

  public function searchByName(string $search): Collection
  {
    return Category::whereHas('translations', function ($query) use ($search) {
      $query->where('name', 'like', "%{$search}%");
    })->with('translations')->get();
  }

  public function slugExists(string $slug): bool
  {
    return Category::where('slug', $slug)->exists();
  }

  public function slugExistsExcluding(string $slug, int $excludeId): bool
  {
    return Category::where('slug', $slug)->where('id', '!=', $excludeId)->exists();
  }
}
