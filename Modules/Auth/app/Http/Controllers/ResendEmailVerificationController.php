<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Contracts\ResendEmailVerificationServiceInterface;
use Illuminate\Http\Request;

use function App\Helpers\showOne;

class ResendEmailVerificationController extends Controller
{

    public function __construct(private ResendEmailVerificationServiceInterface $resendEmail) {}

    public function resend(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $user = $this->resendEmail->resend($data['email']);

        return showOne($user, __("app.messages.auth.resend_verification_code"));
    }
}
