<?php

namespace App\Repositories\Eloquent\User;

use App\Models\User\User;
use App\Repositories\Interface\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
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
