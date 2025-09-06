<?php


namespace App\Repositories\Interface\Auth;

use App\Models\User\User;


interface AuthRepositoryInterface
{
  public function register(array $data): User;

  public function login(array $data): ?User;

  public function logout(): bool;
}
