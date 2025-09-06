<?php

namespace App\Providers;

use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Observers\Offer\OfferObserver;
use App\Observers\Order\OrderObserver;
use Illuminate\Support\ServiceProvider;

use App\Models\Product\ProductColorImage;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Observers\Product\ProductColorImageObserver;
use App\Repositories\Eloquent\Offer\OfferRepository;
use App\Repositories\Eloquent\Order\OrderRepository;
use App\Repositories\Eloquent\Product\ProductRepository;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Eloquent\Offer\OfferTranslationRepository;
use App\Repositories\Eloquent\Order\OrderItem\OrderItemRepository;
use App\Repositories\Interface\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Product\ProductTranslationRepository;
use App\Repositories\Interface\Category\CategoryRepositoryInterface;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;
use App\Repositories\Eloquent\Product\Variant\ProductVariantRepository;
use App\Repositories\Interface\Offer\OfferTranslationRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Repositories\Interface\Product\ProductTranslationRepositoryInterface;
use App\Repositories\Interface\Category\CategoryTranslationRepositoryInterface;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;
use App\Repositories\Eloquent\User\Order\Product\Review\UserOrderProductReviewRepository;
use App\Repositories\Interface\User\Order\Product\Review\UserOrderProductReviewRepositoryInterface;

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
        $this->app->bind(UserOrderProductReviewRepositoryInterface::class, UserOrderProductReviewRepository::class);

        $this->app->bind(OfferRepositoryInterface::class, OfferRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Offer::observe(OfferObserver::class);
        Order::observe(OrderObserver::class);
        ProductColorImage::observe(ProductColorImageObserver::class);
    }
}
