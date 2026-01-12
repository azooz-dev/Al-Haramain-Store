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

      if (!request()->is('api/*')) {
        request()->session()->regenerate();
      }

      $token = $user->createToken('personal_token')->plainTextToken;

      $user = new UserApiResource($user);
      return ['user' => $user, 'token' => $token];
    } catch (UserException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function logout()
  {
    $user = request()->user();

    if ($user) {
      $token = $user->currentAccessToken();
      if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
        $token->delete();
      }
    }

    if ($this->authRepository->logout()) {
      $response = showMessage(__("app.messages.auth.logged_out"), 200);

      // Only invalidate session for Web requests, NOT API
      if (!request()->is('api/*')) {
          // Invalidate the session completely
          if (request()->hasSession()) {
            request()->session()->invalidate();
          }

          // Remove cookies from browser
          $response->headers->clearCookie('XSRF-TOKEN');
          $response->headers->clearCookie('laravel-session', '/', request()->getHost(), false, true);
      }

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
