<?php

namespace App\Repositories\Interface\Category;

use App\Models\Category\Category;
use Illuminate\Support\Collection;

interface ReadCategoryRepositoryInterface
{
    public function getAllCategories(): ?Collection;

    public function findById(int $id): Category;

    public function findByIdWithTranslations(int $id): Category;

    public function searchByName(string $search): Collection;

    public function slugExists(string $slug): bool;

    public function count(): int;
}


