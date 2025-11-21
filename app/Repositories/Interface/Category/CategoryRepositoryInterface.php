<?php

namespace App\Repositories\Interface\Category;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
  public function getAllCategories(): ?Collection;

  public function findById(int $id): Category;

  public function findByIdWithTranslations(int $id): Category;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;

  public function create(array $data): Category;

  public function update(int $id, array $data): Category;

  public function delete(int $id): bool;

  public function count(): int;

  public function getQueryBuilder(): Builder;
}
