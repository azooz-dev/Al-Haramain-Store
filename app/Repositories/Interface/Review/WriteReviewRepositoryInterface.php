<?php

namespace App\Repositories\Interface\Review;

use App\Models\Review\Review;

interface WriteReviewRepositoryInterface
{
    public function update(int $id, array $data): Review;

    public function delete(int $id): bool;

    public function updateStatus(int $id, string $status): Review;

    public function bulkUpdateStatus(array $ids, string $status): int;
}


