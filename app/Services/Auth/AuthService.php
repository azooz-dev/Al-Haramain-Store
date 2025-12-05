<?php

namespace App\Services\Auth;

use App\Events\Auth\UserRegistered;
use function App\Helpers\showMessage;
use Modules\User\Exceptions\UserException;

use function App\Helpers\errorResponse;
use App\Http\Resources\User\UserApiResource;
use App\Repositories\Interface\Auth\AuthRepositoryInterface;

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
        UserRegistered::dispatch($user);
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
      // Delete the Sanctum access token
      request()->user()->currentAccessToken()->delete();

      // Invalidate the session completely
      request()->session()->invalidate();

      // Regenerate CSRF token
      request()->session()->regenerateToken();

      $response = showMessage(__("app.messages.auth.logged_out"), 200);

      // Remove cookies from browser
      $response->headers->clearCookie('XSRF-TOKEN');
      $response->headers->clearCookie('laravel-session', '/', request()->getHost(), false, true);

      return $response;
    }

    return showMessage(__("app.messages.auth.failed_logged_out"), 500);
  }

  public function user()
  {
    try {
      $user = $this->authRepository->user();

      if (!$user) {
        return errorResponse(__("app.messages.auth.user_not_found"), 404);
      }

      return new UserApiResource($user);
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}
