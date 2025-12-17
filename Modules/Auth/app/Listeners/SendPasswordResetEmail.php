<?php

namespace Modules\Auth\Listeners;

use Modules\Auth\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Auth\Events\PasswordResetTokenCreated;

class SendPasswordResetEmail
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
    public function handle(PasswordResetTokenCreated $event): void
    {
        $user = $event->user;
        $token = $event->token;

        $frontendUrl = config('app.frontend_url') ?? env('FRONTEND_URL', env('APP_URL'));
        $resultUrl = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        Mail::to($user->email)->queue(new ResetPasswordMail($user, $token, $resultUrl));
    }
}
