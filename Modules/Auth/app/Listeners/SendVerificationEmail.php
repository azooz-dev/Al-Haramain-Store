<?php

namespace Modules\Auth\Listeners;

use Modules\Auth\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\Auth\Notifications\VerificationCodeNotification;
use Modules\User\Exceptions\VerificationEmailFailedException;

class SendVerificationEmail implements ShouldQueue
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
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        if ($user->isVerified()) {
            return;
        }

        $cacheKey = "email_verification_code:user:{$user->id}";
        if (Cache::has($cacheKey)) {
            return;
        }

        $code = (string) random_int(100000, 999999);

        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(15));

        $user->notify(new VerificationCodeNotification($code));
    }

    public function failed(UserRegistered $event, \Throwable $e)
    {
        $exception = new VerificationEmailFailedException($e->getMessage(), $e->getCode());
        return errorResponse($exception->getMessage(), $exception->getCode());
    }
}
