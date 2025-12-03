<?php

namespace Modules\Catalog\Repositories\Interface\Category;

use Modules\Catalog\Entities\Category\Category;

interface WriteCategoryRepositoryInterface
{
    public function create(array $data): Category;

    public function update(int $id, array $data): Category;

    public function delete(int $id): bool;
}


