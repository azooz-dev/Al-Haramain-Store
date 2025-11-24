<?php

namespace App\Observers\User;

use App\Models\User\User;
use App\Services\Dashboard\DashboardCacheHelper;
use App\Services\Cache\CacheService;
use App\Events\Auth\UserRegistered;

class UserObserver
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        UserRegistered::dispatch($user);

        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
        $this->cacheService->flush(['dashboard', 'customers']);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('email')) {
            UserRegistered::dispatch($user);
        }
    }
}
