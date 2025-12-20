<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Entities\User;
use Modules\User\Repositories\Interface\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
  public function findById(int $userId): ?User
  {
    return User::find($userId);
  }

  public function update(int $userId, array $data): User
  {
    $user = User::findOrFail($userId);
    $user->update($data);
    return $user;
  }

  public function delete(int $userId): User
  {
    $user = User::findOrFail($userId);
    $user->delete();
    return $user;
  }
}
