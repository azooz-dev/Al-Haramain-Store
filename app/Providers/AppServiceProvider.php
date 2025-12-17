<?php

namespace App\Providers;


use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Support\Facades\Gate;



use Illuminate\Support\Facades\Event;


use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Entities\Product\ProductColorImage;
use App\Repositories\Eloquent\Analytics\UserAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\OrderAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\ProductAnalyticsRepository;

use App\Repositories\Eloquent\Analytics\CategoryAnalyticsRepository;
use App\Repositories\Interface\Analytics\UserAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\OrderAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\ProductAnalyticsRepositoryInterface;

use App\Repositories\Interface\Analytics\CategoryAnalyticsRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);

        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

        $this->app->bind(EmailVerificationRepositoryInterface::class, EmailVerificationRepository::class);
        $this->app->bind(ResendEmailVerificationRepositoryInterface::class, ResendEmailVerificationRepository::class);
        $this->app->bind(ForgetPasswordRepositoryInterface::class, ForgetPasswordRepository::class);
        $this->app->bind(ResetPasswordRepositoryInterface::class, ResetPasswordRepository::class);




        // Register Analytics Repository Bindings
        $this->app->bind(UserAnalyticsRepositoryInterface::class, UserAnalyticsRepository::class);
        $this->app->bind(OrderAnalyticsRepositoryInterface::class, OrderAnalyticsRepository::class);
        $this->app->bind(ProductAnalyticsRepositoryInterface::class, ProductAnalyticsRepository::class);

        $this->app->bind(CategoryAnalyticsRepositoryInterface::class, CategoryAnalyticsRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers


        // Register Event Listeners
        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(PasswordResetTokenCreated::class, SendPasswordResetEmail::class);
        Event::listen(ResendVerificationEmail::class, ResendVerificationEmailListener::class);

        // Register Policies

        Gate::policy(Favorite::class, FavoritePolicy::class);
    }
}
