<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use function App\Helpers\showOne;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request)
    {
        $data = $request->all();

        $data = $this->authService->register($data);
        return showOne($data, 201);
    }


    public function login(Request $request)
    {
        $data = $request->input(['email', 'password']);

        $user = $this->authService->login($data);

        return showOne($user, 'user', 200);
    }

    public function logout()
    {
        return $this->authService->logout();
    }
}
