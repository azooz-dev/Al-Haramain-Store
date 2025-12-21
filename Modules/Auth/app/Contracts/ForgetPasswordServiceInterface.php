<?php

namespace Modules\Auth\Contracts;

interface ForgetPasswordServiceInterface
{
    /**
     * Send password reset token
     */
    public function forgetPassword(string $email);
}

