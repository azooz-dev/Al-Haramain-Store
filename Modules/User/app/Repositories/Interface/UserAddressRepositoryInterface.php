<?php

namespace Modules\User\Repositories\Interface;

interface UserAddressRepositoryInterface
{
  public function getAllUserAddresses(int $userId);

  public function storeUserAddress(array $data, int $userId);

  public function updateUserAddress(array $data, int $userId, int $addressId);

  public function deleteUserAddress(int $userId, int $addressId);
}
