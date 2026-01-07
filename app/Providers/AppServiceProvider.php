<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use App\Filament\Pages\Auth\AdminLogin;

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
        // Force HTTPS in production (Render terminates TLS at their load balancer)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register custom Livewire components for Filament
        Livewire::component('app.filament.pages.auth.admin-login', AdminLogin::class);

        // Application-level bootstrapping
        // Module-specific observers and policies are registered by their respective ServiceProviders
    }
}
