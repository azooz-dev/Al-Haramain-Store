<?php

namespace App\Services\Auth;

use App\Models\User\User;
use App\Events\Auth\UserRegistered;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;

use App\Exceptions\User\VerificationEmailFailedException;
use App\Http\Resources\User\UserApiResource;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;

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
      $user->forceFill(['email_verified_at' => now(), 'verified' => User::VERIFIED_USER])->save();
    }

    Cache::forget($cacheKey);

    $token = $user->createToken('personal_token')->plainTextToken;

    $user = new UserApiResource($user);

    return ['user' => $user, 'token' => $token];
  }
}
