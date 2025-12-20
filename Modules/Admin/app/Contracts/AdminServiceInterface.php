<?php

namespace Modules\Admin\Contracts;

use Modules\Admin\Entities\Admin;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface AdminServiceInterface
{
    /**
     * Create a new admin with password hashing and role assignment
     */
    public function createAdmin(array $data, ?array $roleIds = null): Admin;

    /**
     * Update an existing admin with optional role synchronization
     */
    public function updateAdmin(int $id, array $data, ?array $roleIds = null): Admin;

    /**
     * Delete an admin
     */
    public function deleteAdmin(int $id): bool;

    /**
     * Get all admins
     */
    public function getAllAdmins(): Collection;

    /**
     * Get admin by ID
     */
    public function getAdminById(int $id): Admin;

    /**
     * Get total count of admins
     */
    public function getAdminsCount(): int;

    /**
     * Sync roles for an admin
     */
    public function syncRoles(Admin $admin, array $roleIds): void;

    /**
     * Get query builder for custom queries
     */
    public function getQueryBuilder(): Builder;
}

