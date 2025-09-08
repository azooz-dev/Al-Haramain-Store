<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User\User;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
  public function register(array $data): User
  {
    return User::create([
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'password' => $data['password'],
      'verification_token' => User::generatedTokenString(),
    ]);
  }

  public function login(array $data): ?User
  {
    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      return null;
    }

    return $user;
  }

  public function logout(): bool
  {
    return request()->user()->currentAccessToken()->delete();
  }
}
