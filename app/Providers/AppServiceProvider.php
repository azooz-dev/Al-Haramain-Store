<?php

namespace App\Providers;

use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Models\Review\Review;
use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Product\Product;
use App\Models\Favorite\Favorite;
use App\Models\Offer\OfferProduct;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Gate;
use App\Policies\Review\ReviewPolicy;


use Illuminate\Support\Facades\Event;


use App\Observers\Offer\OfferObserver;
use App\Observers\Order\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Entities\Product\ProductColorImage;
use App\Policies\Favorite\FavoritePolicy;
use App\Events\Auth\ResendVerificationEmail;
use App\Observers\Review\ReviewObserver;
use App\Listeners\Auth\SendVerificationEmail;
use App\Observers\Offer\OfferProductObserver;
use App\Events\Auth\PasswordResetTokenCreated;
use App\Listeners\Auth\SendPasswordResetEmail;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Repositories\Eloquent\Offer\OfferRepository;
use App\Repositories\Eloquent\Order\OrderRepository;
use App\Repositories\Eloquent\Coupon\CouponRepository;
use App\Listeners\Auth\ResendVerificationEmailListener;
use App\Repositories\Eloquent\Payment\PaymentRepository;
use App\Repositories\Eloquent\Admin\AdminRepository;
use App\Repositories\Eloquent\Favorite\FavoriteRepository;
use App\Repositories\Eloquent\Review\ReviewRepository;
use App\Repositories\Eloquent\Auth\ResetPasswordRepository;
use App\Repositories\Eloquent\Auth\ForgetPasswordRepository;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;
use App\Repositories\Interface\Offer\OfferRepositoryInterface;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Eloquent\Auth\EmailVerificationRepository;
use App\Repositories\Eloquent\Offer\OfferTranslationRepository;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;
use App\Repositories\Eloquent\Order\OrderItem\OrderItemRepository;
use App\Repositories\Interface\Payment\PaymentRepositoryInterface;
use App\Repositories\Interface\Admin\AdminRepositoryInterface;
use App\Repositories\Interface\Favorite\FavoriteRepositoryInterface;
use App\Repositories\Interface\Review\ReviewRepositoryInterface;
use App\Repositories\Eloquent\Auth\ResendEmailVerificationRepository;
use App\Repositories\Interface\Auth\ResetPasswordRepositoryInterface;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;
use App\Repositories\Interface\Offer\OfferTranslationRepositoryInterface;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;
use App\Repositories\Eloquent\Analytics\UserAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\OrderAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\ProductAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\ReviewAnalyticsRepository;
use App\Repositories\Eloquent\Analytics\CategoryAnalyticsRepository;
use App\Repositories\Interface\Analytics\UserAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\OrderAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\ProductAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\ReviewAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\CategoryAnalyticsRepositoryInterface;
use App\Services\Payment\Processors\CashOnDeliveryProcessor;
use App\Services\Payment\Processors\StripePaymentProcessor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository Bindings
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);

        $this->app->bind(OfferTranslationRepositoryInterface::class, OfferTranslationRepository::class);

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
        $this->app->bind(UserOrderItemReviewRepositoryInterface::class, UserOrderItemReviewRepository::class);

        $this->app->bind(OfferRepositoryInterface::class, OfferRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

        $this->app->bind(EmailVerificationRepositoryInterface::class, EmailVerificationRepository::class);
        $this->app->bind(ResendEmailVerificationRepositoryInterface::class, ResendEmailVerificationRepository::class);
        $this->app->bind(ForgetPasswordRepositoryInterface::class, ForgetPasswordRepository::class);
        $this->app->bind(ResetPasswordRepositoryInterface::class, ResetPasswordRepository::class);

        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);

        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        // Register Analytics Repository Bindings
        $this->app->bind(UserAnalyticsRepositoryInterface::class, UserAnalyticsRepository::class);
        $this->app->bind(OrderAnalyticsRepositoryInterface::class, OrderAnalyticsRepository::class);
        $this->app->bind(ProductAnalyticsRepositoryInterface::class, ProductAnalyticsRepository::class);
        $this->app->bind(ReviewAnalyticsRepositoryInterface::class, ReviewAnalyticsRepository::class);
        $this->app->bind(CategoryAnalyticsRepositoryInterface::class, CategoryAnalyticsRepository::class);

        // Register Payment Processors as Singletons
        $this->app->singleton(CashOnDeliveryProcessor::class);
        $this->app->singleton(StripePaymentProcessor::class);
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
        Review::observe(ReviewObserver::class);

        // Register Event Listeners
        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(PasswordResetTokenCreated::class, SendPasswordResetEmail::class);
        Event::listen(ResendVerificationEmail::class, ResendVerificationEmailListener::class);

        // Register Policies
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(Favorite::class, FavoritePolicy::class);
    }
}
