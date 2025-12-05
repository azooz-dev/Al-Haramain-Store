<?php

namespace App\Repositories\Eloquent\Auth;

use Modules\User\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;

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

    Auth::guard('web')->login($user);

    return $user;
  }

  public function logout(): bool
  {
    Auth::guard('web')->logout();
    return true;
  }

  public function user()
  {
    return Auth::user();
  }
}
