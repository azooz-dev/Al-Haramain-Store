<?php

namespace Modules\Auth\Services;

use Modules\Auth\Contracts\AuthServiceInterface;
use Modules\Auth\Events\UserRegistered;
use function App\Helpers\showMessage;
use Modules\User\Exceptions\UserException;

use function App\Helpers\errorResponse;
use Modules\User\app\Http\Resources\UserApiResource;
use Modules\Auth\Repositories\Interface\AuthRepositoryInterface;

class AuthService implements AuthServiceInterface
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
      return ['user' => $user];
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function logout()
  {
    $user = request()->user();

    if ($user) {
      // Delete the current Sanctum access token
      if ($user->currentAccessToken()) {
        $user->currentAccessToken()->delete();
      }
      // Also delete all tokens for this user to ensure complete logout
      $user->tokens()->delete();
    }

    if ($this->authRepository->logout()) {
      // Invalidate the session completely
      if (request()->hasSession()) {
        request()->session()->invalidate();
        // Regenerate CSRF token
        request()->session()->regenerateToken();
      }

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
