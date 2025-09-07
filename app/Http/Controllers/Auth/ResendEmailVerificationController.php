<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ResendEmailVerificationService;

use function App\Helpers\showOne;

class ResendEmailVerificationController extends Controller
{

    public function __construct(private ResendEmailVerificationService $resendEmail) {}

    public function resend($userId)
    {
        $user = $this->resendEmail->resend($userId);

        return showOne($user, __("app.messages.auth.resend_verification_link"));
    }
}
