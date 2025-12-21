<?php

namespace Modules\Auth\Contracts;

interface ResetPasswordServiceInterface
{
    /**
     * Reset user password
     */
    public function resetPassword(array $data);
}

