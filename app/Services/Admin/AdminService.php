<?php

namespace App\Services\Admin;

use App\Models\Admin\Admin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Exceptions\Admin\AdminException;
use App\Repositories\Interface\Admin\AdminRepositoryInterface;

class AdminService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {}

    /**
     * Create a new admin with password hashing and role assignment
     *
     * @param array $data
     * @param array|null $roleIds
     * @return Admin
     * @throws AdminException
     */
    public function createAdmin(array $data, ?array $roleIds = null): Admin
    {
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Remove password_confirmation if present (not needed in database)
            unset($data['password_confirmation']);

            // Create admin
            $admin = $this->adminRepository->create($data);

            // Sync roles if provided
            if ($roleIds !== null && !empty($roleIds)) {
                $this->syncRoles($admin, $roleIds);
            }

            return $admin->fresh(['roles']);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage(), 500);
        }
    }

    /**
     * Update an existing admin with optional role synchronization
     *
     * @param int $id
     * @param array $data
     * @param array|null $roleIds
     * @return Admin
     * @throws AdminException
     */
    public function updateAdmin(int $id, array $data, ?array $roleIds = null): Admin
    {
        try {
            // Hash password if provided and not empty
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                // Remove password from data if empty (don't update it)
                unset($data['password']);
            }

            // Remove password_confirmation if present
            unset($data['password_confirmation']);

            // Update admin
            $admin = $this->adminRepository->update($id, $data);

            // Sync roles if provided
            if ($roleIds !== null) {
                $this->syncRoles($admin, $roleIds);
            }

            return $admin->fresh(['roles']);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage(), 500);
        }
    }

    /**
     * Delete an admin
     *
     * @param int $id
     * @return bool
     * @throws AdminException
     */
    public function deleteAdmin(int $id): bool
    {
        try {
            return $this->adminRepository->delete($id);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage(), 500);
        }
    }

    /**
     * Get all admins
     *
     * @return Collection
     */
    public function getAllAdmins(): Collection
    {
        return $this->adminRepository->getAll();
    }

    /**
     * Get admin by ID
     *
     * @param int $id
     * @return Admin
     */
    public function getAdminById(int $id): Admin
    {
        return $this->adminRepository->findById($id);
    }

    /**
     * Get total count of admins
     *
     * @return int
     */
    public function getAdminsCount(): int
    {
        return $this->adminRepository->count();
    }

    /**
     * Sync roles for an admin
     * Filament relationship fields return role IDs, so we convert them to role names
     * for Spatie Permission's syncRoles method
     *
     * @param Admin $admin
     * @param array $roleIds Array of role IDs from Filament form
     * @return void
     */
    public function syncRoles(Admin $admin, array $roleIds): void
    {
        // Handle empty array - remove all roles
        if (empty($roleIds)) {
            $admin->syncRoles([]);
            return;
        }

        // Convert role IDs to role names for Spatie Permission
        // Spatie's syncRoles works best with role names
        $roleNames = Role::whereIn('id', $roleIds)
            ->pluck('name')
            ->toArray();

        // Sync roles using role names
        $admin->syncRoles($roleNames);
    }

    /**
     * Get query builder for custom queries
     * Useful for Filament's getEloquentQuery() method
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQueryBuilder(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->adminRepository->getQueryBuilder();
    }
}
