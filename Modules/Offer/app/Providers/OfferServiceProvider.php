<?php

namespace Modules\Offer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class OfferServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Offer';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'offer';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->registerRepositories();
        $this->registerObservers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerRepositories();
    }

    /**
     * Register repository bindings.
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\Offer\Repositories\Interface\Offer\OfferRepositoryInterface::class,
            \Modules\Offer\Repositories\Eloquent\Offer\OfferRepository::class
        );
        $this->app->bind(
            \Modules\Offer\Repositories\Interface\Offer\OfferTranslationRepositoryInterface::class,
            \Modules\Offer\Repositories\Eloquent\Offer\OfferTranslationRepository::class
        );

        // Register OfferServiceInterface binding
        $this->app->bind(
            \Modules\Offer\Contracts\OfferServiceInterface::class,
            \Modules\Offer\Services\Offer\OfferService::class
        );

        // Register OfferTranslationServiceInterface binding
        $this->app->bind(
            \Modules\Offer\Contracts\OfferTranslationServiceInterface::class,
            \Modules\Offer\Services\Offer\OfferTranslationService::class
        );
    }

    /**
     * Register observers.
     */
    protected function registerObservers(): void
    {
        \Modules\Offer\Entities\Offer\Offer::observe(
            \Modules\Offer\Observers\Offer\OfferObserver::class
        );
        \Modules\Offer\Entities\Offer\OfferProduct::observe(
            \Modules\Offer\Observers\Offer\OfferProductObserver::class
        );
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
