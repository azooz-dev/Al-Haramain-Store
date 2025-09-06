<?php

namespace App\Services\Category;

use App\Http\Resources\Category\CategoryApiResource;
use App\Repositories\Interface\Category\CategoryRepositoryInterface;

class CategoryService
{
  public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

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
}
