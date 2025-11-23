<?php

namespace App\Repositories\Eloquent\Category;

use App\Models\Category\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Order\Order;
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

  public function create(array $data): Category
  {
    return Category::create($data);
  }

  public function update(int $id, array $data): Category
  {
    $category = Category::findOrFail($id);
    $category->update($data);
    return $category->fresh(['translations', 'products']);
  }

  public function delete(int $id): bool
  {
    $category = Category::findOrFail($id);
    return $category->delete();
  }

  public function count(): int
  {
    return Category::count();
  }

  public function getQueryBuilder(): Builder
  {
    return Category::query()
      ->with(['translations', 'products'])
      ->withCount(['products']);
  }

  // Widget-specific methods
  public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?Category
  {
    $topCategoryId = DB::table('categories')
      ->join('category_product', 'categories.id', '=', 'category_product.category_id')
      ->join('order_items', function ($join) {
        $join->on('category_product.product_id', '=', 'order_items.orderable_id')
          ->where('order_items.orderable_type', '=', \App\Models\Product\Product::class);
      })
      ->join('orders', 'order_items.order_id', '=', 'orders.id')
      ->where('orders.status', '!=', Order::CANCELLED)
      ->where('orders.status', '!=', Order::REFUNDED)
      ->whereBetween('orders.created_at', [$start, $end])
      ->select('categories.id', DB::raw('SUM(order_items.quantity * order_items.total_price) as revenue'))
      ->groupBy('categories.id')
      ->orderByDesc('revenue')
      ->value('categories.id');

    if (!$topCategoryId) {
      return null;
    }

    return Category::with('translations')->find($topCategoryId);
  }
}
