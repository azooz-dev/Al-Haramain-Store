<?php

namespace Modules\Auth\Repositories\Interface;

interface ForgetPasswordRepositoryInterface
{
  public function forget(string $email);
}
