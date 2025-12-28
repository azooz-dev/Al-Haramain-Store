<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login;
use Filament\Facades\Filament;
use Modules\Admin\Entities\Admin;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Hash;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class AdminLogin extends Login
{
  /**
   * Override the authenticate method to handle email verification.
   * This provides a clear message when an admin hasn't verified their email,
   * instead of the generic "credentials do not match" message.
   */
  public function authenticate(): ?LoginResponse
  {
    try {
      $this->rateLimit(5);
    } catch (TooManyRequestsException $exception) {
      $this->getRateLimitedNotification($exception)?->send();

      return null;
    }

    $data = $this->form->getState();

    // First, check if the admin exists with these credentials
    $admin = Admin::where('email', $data['email'])->first();

    if ($admin) {
      // Check password manually to distinguish between wrong password and unverified email
      if (! Hash::check($data['password'], $admin->password)) {
        $this->throwFailureValidationException();
      }

      // Admin exists with correct password - now check verification status
      // Show error if email is NOT verified OR account is NOT approved
      if (!$admin->verified) {
        throw ValidationException::withMessages([
          'data.email' => __('auth.email_not_verified'),
        ]);
      }
    }

    // If we get here, either admin doesn't exist or all checks passed
    // Let Filament handle the actual authentication
    if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
      $this->throwFailureValidationException();
    }

    $user = Filament::auth()->user();

    // Double-check canAccessPanel (handles any edge cases)
    if (
      ($user instanceof \Filament\Models\Contracts\FilamentUser) &&
      (! $user->canAccessPanel(Filament::getCurrentPanel()))
    ) {
      Filament::auth()->logout();

      $this->throwFailureValidationException();
    }

    session()->regenerate();

    return app(LoginResponse::class);
  }

  /**
   * Throw the standard failure validation exception.
   */
  protected function throwFailureValidationException(): never
  {
    throw ValidationException::withMessages([
      'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
    ]);
  }
}
