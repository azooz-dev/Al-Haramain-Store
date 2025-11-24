<?php

namespace App\Repositories\Interface\Offer;

use App\Models\Offer\Offer;

interface WriteOfferRepositoryInterface
{
    public function create(array $data): Offer;

    public function update(int $id, array $data): Offer;

    public function delete(int $id): bool;
}


