<?php

namespace Modules\Auth\Services;

use Modules\Auth\Contracts\EmailVerificationServiceInterface;
use Modules\User\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;

use Modules\User\Exceptions\VerificationEmailFailedException;
use Modules\Auth\Repositories\Interface\EmailVerificationRepositoryInterface;

class EmailVerificationService implements EmailVerificationServiceInterface
{
  public function __construct(
    private EmailVerificationRepositoryInterface $emailVerification,
    private UserServiceInterface $userService
  ) {}

  public function verify(array $data)
  {
    $user = $this->emailVerification->findUserByEmail($data['email']);

    $cacheKey = "email_verification_code:user:{$user->id}";
    $hashed = Cache::get($cacheKey);


    if (! $hashed || !Hash::check($data['code'], $hashed)) {
      $exception = new VerificationEmailFailedException(__("app.messages.auth.expired_code"), 442);
      return errorResponse($exception->getMessage(), $exception->getCode());
    }

    // Use UserServiceInterface instead of direct entity access
    if (!$this->userService->isUserVerified($user->id)) {
      $userResource = $this->userService->markUserAsVerified($user->id);
    } else {
      // User already verified, get the resource without updating
      $userResource = $this->userService->getUserApiResource($user->id);
    }

    if (!$userResource) {
      return errorResponse(__("app.messages.auth.user_not_found"), 404);
    }

    Cache::forget($cacheKey);

    request()->session()->regenerate();

    $token = $userResource->createToken('personal_token')->plainTextToken;

    return ['user' => $userResource, 'token' => $token];
  }
}
