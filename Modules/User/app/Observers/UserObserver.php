<?php

namespace Modules\User\Observers;

use Modules\User\Entities\User;
use Modules\User\Events\UserCreated;
use Modules\Auth\Events\UserRegistered;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Dispatch UserRegistered event for Auth module
        UserRegistered::dispatch($user);

        // Dispatch UserCreated event for Analytics module
        UserCreated::dispatch($user);
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
