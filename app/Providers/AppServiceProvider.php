<?php

namespace App\Providers;

use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;

use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
