<?php

namespace Modules\Auth\Services;

use App\Repositories\Interface\Auth\ResetPasswordRepositoryInterface;
use Illuminate\Support\Facades\Password;

class ResetPasswordService
{
  public function __construct(private ResetPasswordRepositoryInterface $resetPasswordRepository) {}

  public function resetPassword(array $data)
  {
    $status = $this->resetPasswordRepository->reset($data);

    return $status === Password::PASSWORD_RESET ? ['message' => __($status), 'code' => 200] : ['message' => __($status), 'code' => 422];
  }
}
