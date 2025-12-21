<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Auth';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'auth';

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
        $this->registerEventListeners();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->registerRepositories();
        $this->registerServices();
    }

    /**
     * Register service bindings.
     */
    protected function registerServices(): void
    {
        $this->app->bind(
            \Modules\Auth\Contracts\AuthServiceInterface::class,
            \Modules\Auth\Services\AuthService::class
        );
        $this->app->bind(
            \Modules\Auth\Contracts\ResetPasswordServiceInterface::class,
            \Modules\Auth\Services\ResetPasswordService::class
        );
        $this->app->bind(
            \Modules\Auth\Contracts\ForgetPasswordServiceInterface::class,
            \Modules\Auth\Services\ForgetPasswordService::class
        );
        $this->app->bind(
            \Modules\Auth\Contracts\EmailVerificationServiceInterface::class,
            \Modules\Auth\Services\EmailVerificationService::class
        );
        $this->app->bind(
            \Modules\Auth\Contracts\ResendEmailVerificationServiceInterface::class,
            \Modules\Auth\Services\ResendEmailVerificationService::class
        );
    }

    /**
     * Register repository bindings.
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\Auth\Repositories\Interface\AuthRepositoryInterface::class,
            \Modules\Auth\Repositories\Eloquent\AuthRepository::class
        );
        $this->app->bind(
            \Modules\Auth\Repositories\Interface\EmailVerificationRepositoryInterface::class,
            \Modules\Auth\Repositories\Eloquent\EmailVerificationRepository::class
        );
        $this->app->bind(
            \Modules\Auth\Repositories\Interface\ForgetPasswordRepositoryInterface::class,
            \Modules\Auth\Repositories\Eloquent\ForgetPasswordRepository::class
        );
        $this->app->bind(
            \Modules\Auth\Repositories\Interface\ResendEmailVerificationRepositoryInterface::class,
            \Modules\Auth\Repositories\Eloquent\ResendEmailVerificationRepository::class
        );
        $this->app->bind(
            \Modules\Auth\Repositories\Interface\ResetPasswordRepositoryInterface::class,
            \Modules\Auth\Repositories\Eloquent\ResetPasswordRepository::class
        );
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \Modules\Auth\Events\UserRegistered::class,
            \Modules\Auth\Listeners\SendVerificationEmail::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \Modules\Auth\Events\PasswordResetTokenCreated::class,
            \Modules\Auth\Listeners\SendPasswordResetEmail::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \Modules\Auth\Events\ResendVerificationEmail::class,
            \Modules\Auth\Listeners\ResendVerificationEmailListener::class
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower.'.php'),
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
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
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

    /**
     * Get the publishable view paths.
     *
     * @return array
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
