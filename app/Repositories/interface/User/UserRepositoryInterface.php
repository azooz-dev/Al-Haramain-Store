<?php


namespace App\Repositories\Interface\User;

use App\Models\User\User;

interface UserRepositoryInterface
{
  public function update(int $userId, array $data): User;

  public function delete(int $userId): User;
}
