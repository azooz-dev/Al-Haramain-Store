<?php

namespace Modules\Admin\Repositories\Eloquent;

use Modules\Admin\Entities\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Modules\Admin\Repositories\Interface\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
  /**
   * Get all admins with roles eager loaded
   *
   * @return Collection
   */
  public function getAll(): Collection
  {
    return Admin::with('roles')->get();
  }

  /**
   * Find admin by ID with roles eager loaded
   *
   * @param int $id
   * @return Admin
   */
  public function findById(int $id): Admin
  {
    return Admin::with('roles')->findOrFail($id);
  }

  /**
   * Create a new admin
   *
   * @param array $data
   * @return Admin
   */
  public function create(array $data): Admin
  {
    return Admin::create($data);
  }

  /**
   * Update an existing admin
   *
   * @param int $id
   * @param array $data
   * @return Admin
   */
  public function update(int $id, array $data): Admin
  {
    $admin = Admin::findOrFail($id);
    $admin->update($data);
    return $admin->fresh(['roles']);
  }

  /**
   * Delete an admin
   *
   * @param int $id
   * @return bool
   */
  public function delete(int $id): bool
  {
    $admin = Admin::findOrFail($id);
    return $admin->delete();
  }

  /**
   * Get total count of admins
   *
   * @return int
   */
  public function count(): int
  {
    return Admin::count();
  }

  /**
   * Find admin by email
   *
   * @param string $email
   * @return Admin|null
   */
  public function findByEmail(string $email): ?Admin
  {
    return Admin::where('email', $email)->with('roles')->first();
  }

  /**
   * Get admins with roles eager loaded
   *
   * @return Collection
   */
  public function getWithRoles(): Collection
  {
    return Admin::with('roles')->get();
  }

  /**
   * Get query builder for custom queries
   * Useful for Filament's getEloquentQuery() method
   *
   * @return Builder
   */
  public function getQueryBuilder(): Builder
  {
    return Admin::query()->with('roles')->withCount('roles');
  }
}
