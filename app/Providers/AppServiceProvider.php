<?php

namespace App\Providers;


use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Support\Facades\Gate;
use Modules\Admin\Repositories\Interface\AdminRepositoryInterface;
use Modules\Admin\Repositories\Eloquent\AdminRepository;
use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\Favorite\Policies\Favorite\FavoritePolicy;



use Illuminate\Support\Facades\Event;


use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Entities\Product\ProductColorImage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers

        // Register Policies

        Gate::policy(Favorite::class, FavoritePolicy::class);
    }
}
