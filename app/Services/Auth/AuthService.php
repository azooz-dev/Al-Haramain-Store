<?php

namespace App\Services\Auth;

use App\Exceptions\User\UserException;
use App\Http\Resources\User\UserResource;
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

      return new UserResource($user);
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function login(array $data)
  {
    try {
      $user = $this->authRepository->login($data);

      if (!$user->isVerified()) {
        return errorResponse(__("app.messages.auth.unverified"), 403);
      }

      $user = new UserResource($user);

      $token = $user->createToken('personal_token')->plainTextToken;
      return ['user' => $user, 'token' => $token];
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function logout()
  {
    if ($this->authRepository->logout()) {
      return showMessage(__("app.messages.auth.logged_out"), 200);
    }

    return showMessage(__("app.messages.auth.failed_logged_out"), 500);
  }

  private function handleAuth($userModel)
  {
    if (!$userModel) return;
  }
}
