<?php

namespace Tests\Support\Helpers;

use Modules\User\Entities\User;
use Laravel\Sanctum\Sanctum;

/**
 * Common test helper functions
 */
class TestHelpers
{
    /**
     * Create and authenticate a verified user for testing
     */
    public static function createAndAuthenticateVerifiedUser(array $attributes = []): User
    {
        $user = User::factory()
            ->verified()
            ->create($attributes);
        
        Sanctum::actingAs($user, ['*']);
        
        return $user;
    }

    /**
     * Create and authenticate an unverified user for testing
     */
    public static function createAndAuthenticateUnverifiedUser(array $attributes = []): User
    {
        $user = User::factory()
            ->unverified()
            ->create($attributes);
        
        Sanctum::actingAs($user, ['*']);
        
        return $user;
    }
}

