<?php

namespace Modules\User\Repositories\Interface;

use Modules\User\Entities\User;

interface WriteUserRepositoryInterface
{
    public function update(int $userId, array $data): User;

    public function delete(int $userId): User;
}


