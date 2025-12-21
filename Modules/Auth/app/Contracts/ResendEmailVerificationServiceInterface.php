<?php

namespace Modules\Auth\Contracts;

interface ResendEmailVerificationServiceInterface
{
    /**
     * Resend email verification code
     */
    public function resend(string $userEmail);
}

