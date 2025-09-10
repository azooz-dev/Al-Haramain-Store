<?php

namespace App\Services\Auth;

use App\Exceptions\User\UserException;
use App\Http\Resources\User\UserApiResource;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;

use function App\Helpers\errorResponse;
use function App\Helpers\showMessage;

class AuthService
{
  public function __construct(private AuthRepositoryInterface $authRepository) {}

  public function register(array $data)
  {
    try {
      $user = $this->authRepository->register($data);

      return new UserApiResource($user);
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function login(array $data)
  {
    try {
      $user = $this->authRepository->login($data);

      if (!$user) {
        return showMessage(__("auth.failed"), 401);
      }

      if (!$user->isVerified()) {
        return errorResponse(__("app.messages.auth.unverified"), 403);
      }

      request()->session()->regenerate();

      $user = new UserApiResource($user);

      $token = $user->createToken('personal_token')->plainTextToken;
      return ['user' => $user, 'token' => $token];
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function logout()
  {
    if ($this->authRepository->logout()) {
      request()->session()->invalidate();
      request()->session()->regenerateToken();

      return showMessage(__("app.messages.auth.logged_out"), 200);
    }

    return showMessage(__("app.messages.auth.failed_logged_out"), 500);
  }
}
