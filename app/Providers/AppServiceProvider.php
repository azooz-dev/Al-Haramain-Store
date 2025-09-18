<?php

namespace App\Providers;

use App\Models\User\User;
use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Models\Category\Category;
use App\Models\Offer\OfferProduct;
use App\Policies\User\UserPolicy;
use App\Events\Auth\UserRegistered;
use App\Observers\User\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Observers\Offer\OfferObserver;
use App\Observers\Offer\OfferProductObserver;


use App\Observers\Order\OrderObserver;


use Illuminate\Support\ServiceProvider;
use App\Models\Product\ProductColorImage;
use App\Models\User\UserAddresses\Address;
use App\Events\Auth\ResendVerificationEmail;
use App\Policies\User\Address\AddressPolicy;
use App\Listeners\Auth\SendVerificationEmail;
use App\Events\Auth\PasswordResetTokenCreated;
use App\Listeners\Auth\SendPasswordResetEmail;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Repositories\Eloquent\User\UserRepository;
use App\Observers\Product\ProductColorImageObserver;
use App\Repositories\Eloquent\Offer\OfferRepository;
use App\Repositories\Eloquent\Order\OrderRepository;
use App\Repositories\Eloquent\Coupon\CouponRepository;
use App\Listeners\Auth\ResendVerificationEmailListener;
use App\Observers\Category\CategoryObserver;
use App\Repositories\Eloquent\Product\ProductRepository;
use App\Repositories\Eloquent\Category\CategoryRepository;
use App\Repositories\Eloquent\User\UserFavoriteRepository;
use App\Repositories\Eloquent\Auth\ResetPasswordRepository;
use App\Repositories\Eloquent\Auth\ForgetPasswordRepository;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;
use App\Repositories\Interface\User\UserRepositoryInterface;
use App\Repositories\Eloquent\User\Order\UserOrderRepository;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Eloquent\Auth\EmailVerificationRepository;
use App\Repositories\Eloquent\Offer\OfferTranslationRepository;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;
use App\Repositories\Eloquent\Order\OrderItem\OrderItemRepository;
use App\Repositories\Interface\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Product\ProductTranslationRepository;
use App\Repositories\Interface\Category\CategoryRepositoryInterface;
use App\Repositories\Interface\User\UserFavoriteRepositoryInterface;
use App\Repositories\Eloquent\Auth\ResendEmailVerificationRepository;
use App\Repositories\Eloquent\Category\CategoryTranslationRepository;
use App\Repositories\Interface\Auth\ResetPasswordRepositoryInterface;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;
use App\Repositories\Eloquent\Product\Variant\ProductVariantRepository;
use App\Repositories\Eloquent\User\UserAddresses\UserAddressRepository;
use App\Repositories\Interface\User\Order\UserOrderRepositoryInterface;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;
use App\Repositories\Interface\Offer\OfferTranslationRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Repositories\Interface\Product\ProductTranslationRepositoryInterface;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;
use App\Repositories\Interface\Category\CategoryTranslationRepositoryInterface;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;
use App\Repositories\Interface\User\UserAddresses\UserAddressRepositoryInterface;
use App\Repositories\Eloquent\User\Product\Favorite\UserProductFavoriteRepository;
use App\Repositories\Eloquent\User\Order\Product\Review\UserOrderProductReviewRepository;
use App\Repositories\Interface\User\Product\Favorite\UserProductFavoriteRepositoryInterface;
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

        $this->app->bind(EmailVerificationRepositoryInterface::class, EmailVerificationRepository::class);
        $this->app->bind(ResendEmailVerificationRepositoryInterface::class, ResendEmailVerificationRepository::class);
        $this->app->bind(ForgetPasswordRepositoryInterface::class, ForgetPasswordRepository::class);
        $this->app->bind(ResetPasswordRepositoryInterface::class, ResetPasswordRepository::class);

        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserOrderRepositoryInterface::class, UserOrderRepository::class);
        $this->app->bind(UserProductFavoriteRepositoryInterface::class, UserProductFavoriteRepository::class);
        $this->app->bind(UserFavoriteRepositoryInterface::class, UserFavoriteRepository::class);
        $this->app->bind(UserAddressRepositoryInterface::class, UserAddressRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers
        Offer::observe(OfferObserver::class);
        OfferProduct::observe(OfferProductObserver::class);
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        ProductColorImage::observe(ProductColorImageObserver::class);
        Category::observe(CategoryObserver::class);

        // Register Event Listeners
        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(PasswordResetTokenCreated::class, SendPasswordResetEmail::class);
        Event::listen(ResendVerificationEmail::class, ResendVerificationEmailListener::class);

        // Register Policies
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
