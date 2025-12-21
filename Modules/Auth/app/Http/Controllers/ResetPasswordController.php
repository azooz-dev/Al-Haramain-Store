<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Contracts\ResetPasswordServiceInterface;

use function App\Helpers\showMessage;

class ResetPasswordController extends Controller
{
    public function __construct(private ResetPasswordServiceInterface $resetPasswordService) {}

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
