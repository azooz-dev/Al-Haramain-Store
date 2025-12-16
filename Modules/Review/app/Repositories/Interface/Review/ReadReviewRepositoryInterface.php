<?php

namespace Modules\Review\Repositories\Interface\Review;

use Modules\Review\Entities\Review\Review;
use Illuminate\Support\Collection;

interface ReadReviewRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): Review;

    public function count(): int;

    public function countByStatus(string $status): int;
}


