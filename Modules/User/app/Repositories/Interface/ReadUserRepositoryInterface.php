<?php

namespace Modules\User\Repositories\Interface;

use Modules\User\Entities\User;

interface ReadUserRepositoryInterface
{
    /**
     * Find user by ID
     *
     * @param int $userId
     * @return User|null
     */
    public function findById(int $userId): ?User;
}


