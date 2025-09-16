<?php

namespace App\Http\Controllers\Auth;

use function App\Helpers\showOne;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UserLoginRequest;

use App\Http\Requests\User\UserStoreRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

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

    public function user()
    {
        $this->authorize('view', Auth::user());
        return $this->authService->user();
    }
}
