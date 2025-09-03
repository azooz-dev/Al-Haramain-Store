<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Services\Category\CategoryService;

use function App\Helpers\successResponse;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->getCategories();

        return successResponse($categories, 'Categories', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = $this->categoryService->findCategoryById($id);

        return successResponse($category, 'Category', 201);
    }
}
