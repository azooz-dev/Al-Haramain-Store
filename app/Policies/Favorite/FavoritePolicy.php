<?php

namespace App\Policies\Favorite;

use App\Models\User\User;
use App\Models\Admin\Admin;
use App\Models\Favorite\Favorite;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoritePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('view_any_favorite');
    }

    /**
     * Determine whether the admin can view the model.
     */
    public function view(Admin $admin, Favorite $favorite): bool
    {
        return $admin->can('view_favorite');
    }

    /**
     * Determine whether the admin can create models.
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_favorite');
    }

    /**
     * Determine whether the admin can update the model.
     */
    public function update(Admin $admin, Favorite $favorite): bool
    {
        return $admin->can('update_favorite');
    }

    /**
     * Determine whether the admin can delete the model.
     */
    public function delete(User $user, Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }

    /**
     * Determine whether the admin can bulk delete.
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_favorite');
    }

    /**
     * Determine whether the admin can permanently delete.
     */
    public function forceDelete(Admin $admin, Favorite $favorite): bool
    {
        return $admin->can('force_delete_favorite');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_favorite');
    }

    /**
     * Determine whether the admin can restore.
     */
    public function restore(Admin $admin, Favorite $favorite): bool
    {
        return $admin->can('restore_favorite');
    }

    /**
     * Determine whether the admin can bulk restore.
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_favorite');
    }

    /**
     * Determine whether the admin can replicate.
     */
    public function replicate(Admin $admin, Favorite $favorite): bool
    {
        return $admin->can('replicate_favorite');
    }

    /**
     * Determine whether the admin can reorder.
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_favorite');
    }
}
