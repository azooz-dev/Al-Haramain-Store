<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Exceptions\Order\OrderException;
use App\Exceptions\User\UserException;
use App\Http\Resources\User\UserApiResource;
use App\Repositories\Interface\User\UserRepositoryInterface;

use function App\Helpers\errorResponse;

class UserService
{
  public function __construct(private UserRepositoryInterface $userRepository) {}

  public function updateUser(int $userId, array $data)
  {
    try {
      // $this->checkBuyerVerified($userId);
      $user = $this->userRepository->update($userId, $data);

      if ($user->isDirty('email')) {
        $user->verified = User::UNVERIFIED_USER;
        $user->save();
      }
      return new UserApiResource($user);
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function deleteUser(int $userId)
  {
    $user = $this->userRepository->delete($userId);

    if (!$user) {
      return errorResponse(__("app.messages.auth.user_undeleted"));
    }

    return new UserApiResource($user);
  }

  private function checkBuyerVerified(int $userId): void
  {
    $user = User::find($userId);
    if (!$user) {
      throw new OrderException(__('app.messages.order.user_not_found'), 404);
    }
    if (!$user->verified) {
      throw new OrderException(__('app.messages.order.user_not_verified'), 403);
    }
  }
}
