<?php

namespace App\Http\Controllers\Auth;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Events\User\UserRegistered;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

use Illuminate\Support\Facades\Hash;
use function App\Helpers\showMessage;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\errorResponse;
use function App\Helpers\showOne;

use App\Exceptions\User\VerificationEmailFailedException;

class EmailVerificationController extends Controller
{
    public function verify($id, Request $request)
    {
        $data = $request->validate(['code' => 'required|string']);

        // Find the user by ID
        $user = User::findOrFail($id);

        $cacheKey = "email_verification_code:user:{$user->id}";
        $hashed = Cache::get($cacheKey);


        if (! $hashed || !Hash::check($data['code'], $hashed)) {
            $exception = new VerificationEmailFailedException(__("app.messages.auth.expired_code"), 442);
            return errorResponse($exception->getMessage(), $exception->getCode());
        }

        if (!$user->isVerified()) {
            $user->forceFill(['email_verified_at' => now(), 'verified' => User::VERIFIED_USER])->save();
        }

        Cache::forget($cacheKey);

        $token = $user->createToken('personal_token')->plainTextToken;


        return showOne(['user' => $user, 'token' => $token], __("app.messages.auth.user_verified"));
    }

    public function resend($id)
    {
        $user = User::findOrFail($id);

        if ($user->isVerified()) {
            return errorResponse(__("app.messages.auth.already_verified"), 400);
        }

        UserRegistered::dispatch($user);

        return showMessage(__("app.messages.auth.resend_verification_link"));
    }
}
