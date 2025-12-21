<?php

namespace Modules\Auth\Contracts;

interface EmailVerificationServiceInterface
{
    /**
     * Verify user email with verification code
     */
    public function verify(array $data);
}

