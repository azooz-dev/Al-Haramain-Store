<?php

namespace App\Repositories\Eloquent\User\UserAddresses;

use App\Models\User\UserAddresses\Address;
use App\Repositories\Interface\User\UserAddresses\UserAddressRepositoryInterface;

class UserAddressRepository implements UserAddressRepositoryInterface
{
  public function getAllUserAddresses(int $userId)
  {
    return Address::where('user_id', $userId)->get();
  }

  public function storeUserAddress(array $data, int $userId)
  {
    return Address::create([
      'user_id' => $userId,
      "address_type" => $data['address_type'],
      "label" => $data['label'],
      "street" => $data['street'],
      "city" => $data['city'],
      "state" => $data['state'],
      "postal_code" => $data['postal_code'],
      "country" => $data['country'],
      "is_default" => $data['is_default']
    ]);
  }

  public function updateUserAddress(array $data, int $userId, $addressId)
  {
    return Address::where("id", $addressId)
      ->where('user_id', $userId)
      ->update($data);
  }

  public function deleteUserAddress(int $userId, int $addressId)
  {
    return Address::where("id", $addressId)
      ->where('user_id', $userId)
      ->delete();
  }
}
