<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ResendEmailVerificationService;
use Illuminate\Http\Request;

use function App\Helpers\showOne;

class ResendEmailVerificationController extends Controller
{

    public function __construct(private ResendEmailVerificationService $resendEmail) {}

    public function resend(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $user = $this->resendEmail->resend($data['email']);

        return showOne($user, __("app.messages.auth.resend_verification_code"));
    }
}
