<?php

namespace Modules\Catalog\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Modules\Catalog\Services\Category\CategoryService;
use function App\Helpers\showAll;
use function App\Helpers\showOne;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->getCategories();

        return showAll($categories, 'Categories', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = $this->categoryService->findCategoryById($id);

        return showOne($category, 'Category', 201);
    }
}


