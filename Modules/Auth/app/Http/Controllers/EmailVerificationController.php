<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function App\Helpers\showOne;

use App\Services\Auth\EmailVerificationService;

class EmailVerificationController extends Controller
{
    public function __construct(private EmailVerificationService $emailVerificationService) {}
    public function verify(Request $request)
    {
        $data = $request->validate(['code' => 'required|string', "email" => "required|email"]);

        $userData = $this->emailVerificationService->verify($data);

        return showOne($userData, __("app.messages.auth.user_verified"));
    }
}
