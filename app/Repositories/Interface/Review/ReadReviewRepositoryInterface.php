<?php

namespace App\Repositories\Interface\Review;

use App\Models\Review\Review;
use Illuminate\Support\Collection;

interface ReadReviewRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): Review;

    public function count(): int;

    public function countByStatus(string $status): int;
}


