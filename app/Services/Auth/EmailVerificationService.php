<?php

namespace App\Services\Auth;

use App\Models\User\User;
use App\Events\User\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;

use App\Exceptions\User\VerificationEmailFailedException;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;

class EmailVerificationService
{
  public function __construct(private EmailVerificationRepositoryInterface $emailVerification) {}

  public function verify(array $data)
  {
    $user = $this->emailVerification->findUserById($data['user_id']);

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

    return ['user' => $user, 'token' => $token];
  }


  public function resend(int $userId)
  {
    $user = $this->emailVerification->findUserById($userId);

    if ($user->isVerified()) {
      return errorResponse(__("app.messages.auth.already_verified"), 400);
    }

    UserRegistered::dispatch($user);

    return $user;
  }
}
