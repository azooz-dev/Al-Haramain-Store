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
        $user->verified = false;
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
    return $this->userRepository->findById($userId);
  }

  public function isUserVerified(int $userId): bool
  {
    $user = $this->userRepository->findById($userId);
    return $user && $user->isVerified();
  }

  public function markUserAsVerified(int $userId)
  {
    $user = $this->userRepository->findById($userId);
    
    if (!$user) {
      return null;
    }

    if (!$user->isVerified()) {
      $user->forceFill(['email_verified_at' => now(), 'verified' => true])->save();
    }

    return new UserApiResource($user);
  }

  public function getUserApiResource(int $userId)
  {
    $user = $this->userRepository->findById($userId);
    
    if (!$user) {
      return null;
    }

    return new UserApiResource($user);
  }

  private function checkBuyerVerified(int $userId): void
  {
    $user = $this->userRepository->findById($userId);
    if (!$user) {
      throw new OrderException(__('app.messages.order.user_not_found'), 404);
    }
    if (!$user->verified) {
      throw new OrderException(__('app.messages.order.user_not_verified'), 403);
    }
  }
}
