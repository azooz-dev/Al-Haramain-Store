<?php

namespace App\Observers\User;

use App\Events\Auth\UserRegistered;
use App\Models\User\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        UserRegistered::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty()) {
            UserRegistered::dispatch($user);
        }
    }
}
