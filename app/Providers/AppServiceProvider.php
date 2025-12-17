<?php

namespace App\Providers;


use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Product\Product;
use App\Models\Favorite\Favorite;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Gate;



use Illuminate\Support\Facades\Event;


use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Entities\Product\ProductColorImage;
use App\Policies\Favorite\FavoritePolicy;
use App\Events\Auth\ResendVerificationEmail;

use App\Listeners\Auth\SendVerificationEmail;
use App\Events\Auth\PasswordResetTokenCreated;
use App\Listeners\Auth\SendPasswordResetEmail;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Listeners\Auth\ResendVerificationEmailListener;
use App\Repositories\Eloquent\Admin\AdminRepository;
use App\Repositories\Eloquent\Favorite\FavoriteRepository;

use App\Repositories\Eloquent\Auth\ResetPasswordRepository;
use App\Repositories\Eloquent\Auth\ForgetPasswordRepository;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;
use App\Repositories\Eloquent\Auth\EmailVerificationRepository;
use App\Repositories\Interface\Admin\AdminRepositoryInterface;
use App\Repositories\Interface\Favorite\FavoriteRepositoryInterface;

use App\Repositories\Eloquent\Auth\ResendEmailVerificationRepository;
use App\Repositories\Interface\Auth\ResetPasswordRepositoryInterface;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;
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

        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);



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
