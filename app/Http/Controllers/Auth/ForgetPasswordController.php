<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ForgetPasswordService;
use Illuminate\Http\Request;

use function App\Helpers\showMessage;

class ForgetPasswordController extends Controller
{
    public function __construct(private ForgetPasswordService $forgetPassword) {}

    public function forget(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $response = $this->forgetPassword->forgetPassword($data['email']);

        return showMessage($response, 200);
    }
}
