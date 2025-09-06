<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Services\Auth\AuthService;
use function App\Helpers\showOne;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(UserStoreRequest $request)
    {
        $data = $request->validated();

        $data = $this->authService->register($data);
        return showOne($data, 201);
    }


    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->login($data);

        return showOne($user, 'user', 200);
    }

    public function logout()
    {
        return $this->authService->logout();
    }
}
