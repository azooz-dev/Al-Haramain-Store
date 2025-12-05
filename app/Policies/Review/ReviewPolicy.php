<?php

namespace App\Policies\Review;

use Modules\User\Entities\User;
use App\Models\Admin\Admin;
use App\Models\Review\Review;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('view_any_review');
    }

    /**
     * Determine whether the admin can view the model.
     */
    public function view(Admin $admin, Review $review): bool
    {
        return $admin->can('view_review');
    }

    /**
     * Determine whether the admin can create models.
     */
    public function create(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->id === $user->id;
    }

    /**
     * Determine whether the admin can update the model.
     */
    public function update(Admin $admin, Review $review): bool
    {
        return $admin->can('update_review');
    }

    /**
     * Determine whether the admin can delete the model.
     */
    public function delete(Admin $admin, Review $review): bool
    {
        return $admin->can('delete_review');
    }

    /**
     * Determine whether the admin can bulk delete.
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_review');
    }

    /**
     * Determine whether the admin can permanently delete.
     */
    public function forceDelete(Admin $admin, Review $review): bool
    {
        return $admin->can('force_delete_review');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_review');
    }

    /**
     * Determine whether the admin can restore.
     */
    public function restore(Admin $admin, Review $review): bool
    {
        return $admin->can('restore_review');
    }

    /**
     * Determine whether the admin can bulk restore.
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_review');
    }

    /**
     * Determine whether the admin can replicate.
     */
    public function replicate(Admin $admin, Review $review): bool
    {
        return $admin->can('replicate_review');
    }

    /**
     * Determine whether the admin can reorder.
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_review');
    }
}
