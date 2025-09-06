<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use App\Repositories\Interface\Category\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
  public function getAllCategories(): ?Collection
  {
    return Category::with('translations')->get();
  }

  public function findById(int $id): Category
  {
    return Category::findOrFail($id);
  }

  public function findByIdWithTranslations(int $id): Category
  {
    return Category::with('translations')->findOrFail($id);
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
}
