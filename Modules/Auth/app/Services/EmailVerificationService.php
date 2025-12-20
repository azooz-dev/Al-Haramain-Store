<?php

namespace Modules\Auth\Services;

use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;

use Modules\User\Exceptions\VerificationEmailFailedException;
use Modules\User\app\Http\Resources\UserApiResource;
use Modules\Auth\Repositories\Interface\EmailVerificationRepositoryInterface;

class EmailVerificationService
{
  public function __construct(private EmailVerificationRepositoryInterface $emailVerification) {}

  public function verify(array $data)
  {
    $user = $this->emailVerification->findUserByEmail($data['email']);

    $cacheKey = "email_verification_code:user:{$user->id}";
    $hashed = Cache::get($cacheKey);


    if (! $hashed || !Hash::check($data['code'], $hashed)) {
      $exception = new VerificationEmailFailedException(__("app.messages.auth.expired_code"), 442);
      return errorResponse($exception->getMessage(), $exception->getCode());
    }

    if (!$user->isVerified()) {
      $user->forceFill(['email_verified_at' => now(), 'verified' => true])->save();
    }

    Cache::forget($cacheKey);

    request()->session()->regenerate();

    $user = new UserApiResource($user);

    $token = $user->createToken('personal_token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
  }
}
