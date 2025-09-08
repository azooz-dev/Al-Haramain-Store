<?php

namespace App\Listeners\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Auth\ResendVerificationEmail;
use App\Notifications\VerificationCodeNotification;
use App\Exceptions\User\VerificationEmailFailedException;

class ResendVerificationEmailListener implements ShouldQueue
{
  /**
   * Create the event listener.
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   */
  public function handle(ResendVerificationEmail $event): void
  {
    $user = $event->user;

    // For resend, we don't check if user is verified - we always send a new code
    $cacheKey = "email_verification_code:user:{$user->id}";

    // Always generate a new code and override any existing one
    $code = (string) random_int(100000, 999999);

    Cache::put($cacheKey, Hash::make($code), now()->addMinutes(15));

    $user->notify(new VerificationCodeNotification($code));
  }

  public function failed(ResendVerificationEmail $event, \Throwable $e)
  {
    $exception = new VerificationEmailFailedException($e->getMessage(), $e->getCode());
    return errorResponse($exception->getMessage(), $exception->getCode());
  }
}
