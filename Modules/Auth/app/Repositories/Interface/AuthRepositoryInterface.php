<?php


namespace App\Repositories\Interface\Auth;

use Modules\User\Entities\User;


interface AuthRepositoryInterface
{
  public function register(array $data): User;

  public function login(array $data): ?User;

  public function logout(): bool;

  public function user();
}
