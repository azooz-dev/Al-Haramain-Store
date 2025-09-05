<?php

namespace App\Providers;

use App\Models\Offer\Offer;
use App\Observers\Offer\OfferObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Product\ProductColorImage;
use App\Observers\Product\ProductColorImageObserver;

use App\Repositories\Eloquent\Order\OrderRepository;
use App\Repositories\Eloquent\Product\ProductRepository;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Eloquent\Offer\OfferTranslationRepository;
use App\Repositories\Eloquent\Order\OrderItem\OrderItemRepository;
use App\Repositories\interface\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Product\ProductTranslationRepository;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;
use App\Repositories\Eloquent\Product\Variant\ProductVariantRepository;
use App\Repositories\interface\Offer\OfferTranslationRepositoryInterface;
use App\Repositories\interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Repositories\interface\Product\ProductTranslationRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use App\Repositories\interface\Product\Variant\ProductVariantRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryTranslationRepositoryInterface::class, CategoryTranslationRepository::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductTranslationRepositoryInterface::class, ProductTranslationRepository::class);

        $this->app->bind(OfferTranslationRepositoryInterface::class, OfferTranslationRepository::class);

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(ProductVariantRepositoryInterface::class, ProductVariantRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Offer::observe(OfferObserver::class);
        ProductColorImage::observe(ProductColorImageObserver::class);
    }
}
