<?php

namespace Modules\User\Contracts;

interface UserAddressServiceInterface
{
    /**
     * Get all addresses for a user
     */
    public function getAllUserAddresses(int $userId);

    /**
     * Store a new address for a user
     */
    public function storeUserAddress(array $data, int $userId);

    /**
     * Update an existing address for a user
     */
    public function updateUserAddress(array $data, int $userId, int $addressId);

    /**
     * Delete an address for a user
     */
    public function deleteUserAddress(int $userId, int $addressId);
}

