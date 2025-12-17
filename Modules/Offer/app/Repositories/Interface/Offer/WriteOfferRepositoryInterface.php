<?php

namespace Modules\Offer\Repositories\Interface\Offer;

use Modules\Offer\Entities\Offer\Offer;

interface WriteOfferRepositoryInterface
{
    public function create(array $data): Offer;

    public function update(int $id, array $data): Offer;

    public function delete(int $id): bool;
}


