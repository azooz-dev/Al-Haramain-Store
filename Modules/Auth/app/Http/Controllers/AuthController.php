<?php

namespace Modules\Auth\Http\Controllers;

use function App\Helpers\showOne;
use Modules\Auth\Contracts\AuthServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\User\app\Http\Requests\UserLoginRequest;
use Modules\User\app\Http\Requests\UserStoreRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AuthServiceInterface $authService) {}

    public function register(UserStoreRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->register($data);
        
        // If it's an error response, return it directly
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }
        
        return showOne($result, 'User', 201);
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
