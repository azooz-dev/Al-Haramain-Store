<?php

namespace App\Repositories\Interface\Category;

use App\Models\Category\Category;

interface WriteCategoryRepositoryInterface
{
    public function create(array $data): Category;

    public function update(int $id, array $data): Category;

    public function delete(int $id): bool;
}


