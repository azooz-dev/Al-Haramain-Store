<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\ResetPasswordService;

use function App\Helpers\showMessage;

class ResetPasswordController extends Controller
{
    public function __construct(private ResetPasswordService $resetPasswordService) {}

    public function reset(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $result = $this->resetPasswordService->resetPassword($data);

        // Ensure we have the expected array structure
        if (is_array($result) && isset($result['message']) && isset($result['code'])) {
            return showMessage($result['message'], $result['code']);
        }

        // Fallback if the result is not in expected format
        return showMessage('Password reset failed', 422);
    }
}
