<?php

namespace App\Repositories\Interface\Admin;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface AdminRepositoryInterface
{
  /**
   * Get all admins with roles eager loaded
   *
   * @return Collection
   */
  public function getAll(): Collection;

  /**
   * Find admin by ID with roles eager loaded
   *
   * @param int $id
   * @return Admin
   */
  public function findById(int $id): Admin;

  /**
   * Create a new admin
   *
   * @param array $data
   * @return Admin
   */
  public function create(array $data): Admin;

  /**
   * Update an existing admin
   *
   * @param int $id
   * @param array $data
   * @return Admin
   */
  public function update(int $id, array $data): Admin;

  /**
   * Delete an admin
   *
   * @param int $id
   * @return bool
   */
  public function delete(int $id): bool;

  /**
   * Get total count of admins
   *
   * @return int
   */
  public function count(): int;

  /**
   * Find admin by email
   *
   * @param string $email
   * @return Admin|null
   */
  public function findByEmail(string $email): ?Admin;

  /**
   * Get admins with roles eager loaded
   *
   * @return Collection
   */
  public function getWithRoles(): Collection;

  /**
   * Get query builder for custom queries
   * Useful for Filament's getEloquentQuery() method
   *
   * @return Builder
   */
  public function getQueryBuilder(): Builder;
}
