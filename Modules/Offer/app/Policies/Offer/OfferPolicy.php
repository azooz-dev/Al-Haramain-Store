<?php

namespace Modules\Offer\Policies\Offer;

use App\Models\Admin\Admin;
use Modules\Offer\Entities\Offer\Offer;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('view_any_offer');
    }

    /**
     * Determine whether the admin can view the model.
     */
    public function view(Admin $admin, Offer $offer): bool
    {
        return $admin->can('view_offer');
    }

    /**
     * Determine whether the admin can create models.
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_offer');
    }

    /**
     * Determine whether the admin can update the model.
     */
    public function update(Admin $admin, Offer $offer): bool
    {
        return $admin->can('update_offer');
    }

    /**
     * Determine whether the admin can delete the model.
     */
    public function delete(Admin $admin, Offer $offer): bool
    {
        return $admin->can('delete_offer');
    }

    /**
     * Determine whether the admin can bulk delete.
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_offer');
    }

    /**
     * Determine whether the admin can permanently delete.
     */
    public function forceDelete(Admin $admin, Offer $offer): bool
    {
        return $admin->can('force_delete_offer');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_offer');
    }

    /**
     * Determine whether the admin can restore.
     */
    public function restore(Admin $admin, Offer $offer): bool
    {
        return $admin->can('restore_offer');
    }

    /**
     * Determine whether the admin can bulk restore.
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_offer');
    }

    /**
     * Determine whether the admin can replicate.
     */
    public function replicate(Admin $admin, Offer $offer): bool
    {
        return $admin->can('replicate_offer');
    }

    /**
     * Determine whether the admin can reorder.
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_offer');
    }
}
