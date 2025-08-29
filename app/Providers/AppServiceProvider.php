<?php

namespace App\Providers;

use App\Models\Product\Product;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\Product\ProductRepository;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Eloquent\Offer\OfferTranslationRepository;

use App\Repositories\interface\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Product\ProductTranslationRepository;
use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;
use App\Repositories\interface\Offer\OfferTranslationRepositoryInterface;
use App\Repositories\interface\Product\ProductTranslationRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
