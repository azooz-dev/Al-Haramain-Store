<?php

namespace Modules\Auth\Contracts;

interface AuthServiceInterface
{
    /**
     * Register a new user
     */
    public function register(array $data);

    /**
     * Login a user
     */
    public function login(array $data);

    /**
     * Logout the current user
     */
    public function logout();

    /**
     * Get the current authenticated user
     */
    public function user();
}

