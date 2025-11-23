<?php

namespace App\Observers\User;

use App\Models\User\User;
use App\Services\Dashboard\DashboardCacheHelper;
use App\Events\Auth\UserRegistered;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        UserRegistered::dispatch($user);

        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
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
