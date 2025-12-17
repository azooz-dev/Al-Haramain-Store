<?php

namespace Modules\Auth\Repositories\Interface;

interface EmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $email);
}
