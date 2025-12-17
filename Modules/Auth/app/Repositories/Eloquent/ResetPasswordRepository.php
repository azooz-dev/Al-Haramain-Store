<?php

namespace Modules\Auth\Repositories\Eloquent;

use App\Repositories\Interface\Auth\ResetPasswordRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordRepository implements ResetPasswordRepositoryInterface
{
  public function reset(array $data)
  {
    return Password::reset([
      'email' => $data['email'],
      'password' => $data['password'],
      'password_confirmation' => $data['password_confirmation'],
      'token' => $data['token']
    ], function ($user, $password) {
      $user->forceFill([
        'password' => Hash::make($password)
      ])->save();

      $user->tokens()->delete();
    });
  }
}
