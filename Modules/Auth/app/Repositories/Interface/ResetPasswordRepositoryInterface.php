<?php

namespace Modules\Auth\Repositories\Interface;

interface ResetPasswordRepositoryInterface
{
  public function reset(array $data);
}
