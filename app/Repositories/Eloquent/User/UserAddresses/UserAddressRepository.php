<?php

namespace App\Repositories\Eloquent\User\UserAddresses;

use App\Models\User\UserAddresses\Address;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interface\User\UserAddresses\UserAddressRepositoryInterface;

class UserAddressRepository implements UserAddressRepositoryInterface
{
  public function getAllUserAddresses(int $userId)
  {
    return Address::where('user_id', $userId)->get();
  }

  public function storeUserAddress(array $data, int $userId)
  {
    return DB::transaction(function () use ($data, $userId) {
      // If this address is being set as default, unset any existing default address
      if (isset($data['is_default']) && $data['is_default']) {
        $this->unsetDefaultAddresses($userId);
      }

      return Address::create([
        'user_id' => $userId,
        "address_type" => $data['address_type'],
        "label" => $data['label'],
        "street" => $data['street'],
        "city" => $data['city'],
        "state" => $data['state'],
        "postal_code" => $data['postal_code'],
        "country" => $data['country'],
        "is_default" => $data['is_default'] ?? false
      ]);
    });
  }

  public function updateUserAddress(array $data, int $userId, $addressId)
  {
    return DB::transaction(function () use ($data, $userId, $addressId) {
      // If this address is being set as default, unset any existing default address
      if (isset($data['is_default']) && $data['is_default']) {
        $this->unsetDefaultAddresses($userId, $addressId);
      }

      $address = Address::where("id", $addressId)
        ->where('user_id', $userId)
        ->first();

      $address->update($data);

      return $address;
    });
  }

  public function deleteUserAddress(int $userId, int $addressId)
  {
    return DB::transaction(function () use ($userId, $addressId) {
      // Check if the address being deleted is the default address
      $address = Address::where("id", $addressId)
        ->where('user_id', $userId)
        ->first();

      if ($address && $address->is_default) {
        // If deleting the default address, set the first remaining address as default
        $remainingAddress = Address::where('user_id', $userId)
          ->where('id', '!=', $addressId)
          ->first();

        if ($remainingAddress) {
          $remainingAddress->update(['is_default' => true]);
        }
      }

      return Address::where("id", $addressId)
        ->where('user_id', $userId)
        ->delete();
    });
  }

  /**
   * Unset all default addresses for a user, optionally excluding a specific address
   *
   * @param int $userId
   * @param int|null $excludeAddressId
   * @return void
   */
  private function unsetDefaultAddresses(int $userId, ?int $excludeAddressId = null): void
  {
    $query = Address::where('user_id', $userId)
      ->where('is_default', true);

    if ($excludeAddressId) {
      $query->where('id', '!=', $excludeAddressId);
    }

    $query->update(['is_default' => false]);
  }
}
