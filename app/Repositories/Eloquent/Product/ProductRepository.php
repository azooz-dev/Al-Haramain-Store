<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use App\Models\Order\Order;
use App\Repositories\Interface\Product\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
  public function getAllProducts()
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->get();
  }

  public function findById(int $id): ?Product
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->findOrFail($id);
  }

  public function findByIdWithTranslations(int $id): ?Product
  {
    return Product::with('translations')->findOrFail($id);
  }

  public function searchByName(string $search): Collection
  {
    return Product::whereHas('translations', function ($query) use ($search) {
      $query->where('name', 'like', "%{$search}%");
    })->with('translations')->get();
  }

  public function slugExists(string $slug): bool
  {
    return Product::where('slug', $slug)->exists();
  }

  public function create(array $data): Product
  {
    return Product::create($data);
  }

  public function update(int $id, array $data): Product
  {
    $product = Product::findOrFail($id);
    $product->update($data);
    return $product->fresh(['translations', 'colors.images', 'variants', 'categories.translations']);
  }

  public function delete(int $id): bool
  {
    $product = Product::findOrFail($id);
    return $product->delete();
  }

  public function count(): int
  {
    return Product::count();
  }

  public function getQueryBuilder(): Builder
  {
    return Product::query()
      ->withoutGlobalScopes([SoftDeletingScope::class])
      ->with(['translations', 'colors.images', 'variants', 'categories.translations'])
      ->withCount(['colors', 'variants', 'images', 'categories']);
  }

  // Widget-specific methods
  public function getLowStockProductsCount(int $threshold = 10): int
  {
    return Product::where('quantity', '<=', $threshold)->count();
  }

  public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection
  {
    return Product::query()
      ->select([
        'products.*',
        DB::raw('SUM(order_items.quantity) as total_sold'),
        DB::raw('SUM(order_items.quantity * order_items.total_price) as total_revenue'),
        DB::raw('COUNT(DISTINCT orders.id) as order_count')
      ])
      ->join('order_items', function ($join) {
        $join->on('products.id', '=', 'order_items.orderable_id')
          ->where('order_items.orderable_type', '=', Product::class);
      })
      ->join('orders', 'order_items.order_id', '=', 'orders.id')
      ->where('orders.status', '!=', Order::CANCELLED)
      ->where('orders.status', '!=', Order::REFUNDED)
      ->whereBetween('orders.created_at', [$start, $end])
      ->groupBy('products.id')
      ->orderByDesc('total_sold')
      ->limit($limit)
      ->with(['translations', 'colors', 'variants'])
      ->get();
  }
}
