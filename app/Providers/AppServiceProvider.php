<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Application-level service bindings
        // Module-specific bindings are handled by their respective ServiceProviders
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Application-level bootstrapping
        // Module-specific observers and policies are registered by their respective ServiceProviders
    }
}
