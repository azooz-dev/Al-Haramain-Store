<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\Order\Exceptions\Order\OrderException;
use Modules\User\Exceptions\UserException;
use Modules\User\app\Http\Resources\UserApiResource;
use Modules\User\Repositories\Interface\UserRepositoryInterface;
use Modules\User\Contracts\UserServiceInterface;

use function App\Helpers\errorResponse;

class UserService implements UserServiceInterface
{
  public function __construct(private UserRepositoryInterface $userRepository) {}

  public function updateUser(int $userId, array $data)
  {
    try {
      $this->checkBuyerVerified($userId);
      $user = $this->userRepository->update($userId, $data);

      if ($user->wasChanged('email')) {
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

  public function findUserById(int $userId): ?User
  {
    return User::find($userId);
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
