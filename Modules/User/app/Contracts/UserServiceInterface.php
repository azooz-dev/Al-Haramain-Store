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

    /**
     * Check if user is verified
     *
     * @param int $userId
     * @return bool
     */
    public function isUserVerified(int $userId): bool;

    /**
     * Mark user as verified and return UserApiResource
     *
     * @param int $userId
     * @return mixed UserApiResource instance
     */
    public function markUserAsVerified(int $userId);

    /**
     * Get UserApiResource for a user
     *
     * @param int $userId
     * @return mixed UserApiResource instance
     */
    public function getUserApiResource(int $userId);
}

