<?php

namespace Modules\User\Policies;

use Modules\User\Entities\User;
use Modules\User\Entities\Address;

class AddressPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }
}
