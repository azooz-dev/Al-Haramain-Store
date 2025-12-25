<?php

namespace Modules\User\Services;

use Modules\Order\Exceptions\Order\OrderException;
use Modules\User\Contracts\UserAddressServiceInterface;
use Modules\User\Exceptions\UserAddressException;
use Modules\User\app\Http\Resources\UserAddresses\AddressApiResource;
use Modules\User\Repositories\Interface\UserAddressRepositoryInterface;
use Modules\User\Contracts\UserServiceInterface;

use function App\Helpers\errorResponse;
use function App\Helpers\showMessage;

class UserAddressService implements UserAddressServiceInterface
{
  public function __construct(
    private UserAddressRepositoryInterface $userAddressRepository,
    private UserServiceInterface $userService
  ) {}

  public function getAllUserAddresses(int $userId)
  {
    try {
      $this->checkUserVerified($userId);

      $addresses = $this->userAddressRepository->getAllUserAddresses($userId);

      return AddressApiResource::collection($addresses);
    } catch (UserAddressException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function storeUserAddress(array $data, int $userId)
  {
    try {
      $this->checkUserVerified($userId);
      $address = $this->userAddressRepository->storeUserAddress($data, $userId);

      return new AddressApiResource($address);
    } catch (UserAddressException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function updateUserAddress(array $data, int $userId, int $addressId)
  {
    try {
      $this->checkUserVerified($userId);

      $updatedAddress = $this->userAddressRepository->updateUserAddress($data, $userId, $addressId);

      return new AddressApiResource($updatedAddress);
    } catch (UserAddressException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function deleteUserAddress(int $userId, int $addressId)
  {
    try {
      $this->checkUserVerified($userId);

      // Check if address is default before deletion
      $address = $this->userAddressRepository->getAddressById($addressId);
      if ($address && $address->is_default) {
        return errorResponse(__("app.messages.user_address.cannot_delete_default"), 422);
      }

      if ($this->userAddressRepository->deleteUserAddress($userId, $addressId)) {
        return showMessage(__("app.messages.user_address.user_address_deleted"), 200);
      }

      return showMessage(__("app.messages.user_address.user_address_not_deleted"), 500);
    } catch (UserAddressException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  private function checkUserVerified(int $userId): void
  {
    $user = $this->userService->findUserById($userId);
    if (!$user) {
      throw new OrderException(__('app.messages.order.user_not_found'), 404);
    }
    if (!$user->verified) {
      throw new OrderException(__('app.messages.order.user_not_verified'), 403);
    }
  }
}
