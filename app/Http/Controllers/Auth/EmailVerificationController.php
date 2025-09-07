<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function App\Helpers\showOne;

use App\Services\Auth\EmailVerificationService;

class EmailVerificationController extends Controller
{
    public function __construct(private EmailVerificationService $emailVerificationService) {}
    public function verify($userId, Request $request)
    {
        $data = $request->validate(['code' => 'required|string']);

        $userData = $this->emailVerificationService->verify(['code' => $data['code'], 'user_id' => $userId]);

        return showOne($userData, __("app.messages.auth.user_verified"));
    }
}
