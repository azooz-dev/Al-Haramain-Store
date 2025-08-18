<?php

namespace App\Providers;

use App\Repositories\interface\Category\CategoryRepositoryInterface;
use App\Repositories\interface\Category\CategoryTranslationRepositoryInterface;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
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
        $this->app->singleton(LanguageSwitch::class, function ($app) {
            return new LanguageSwitch();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
