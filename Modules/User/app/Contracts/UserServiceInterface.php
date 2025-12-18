<?php

namespace Modules\User\Contracts;

use Modules\User\Entities\User;

interface UserServiceInterface
{
    /**
     * Find user by ID
     *
     * @param int $userId
     * @return User|null
     */
    public function findUserById(int $userId): ?User;
}

