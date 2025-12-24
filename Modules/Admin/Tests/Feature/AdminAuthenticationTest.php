<?php

namespace Modules\Admin\Tests\Feature;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * TC-ADM-001: Admin Login - Verified Account
 * TC-ADM-002: Admin Login - Unverified Email
 * TC-ADM-003: Admin Login - Unverified Account
 */
class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
    }

    public function test_verified_admin_can_login(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'verified' => true,
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(302); // Redirect after login
    }

    public function test_unverified_admin_cannot_login(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'verified' => false,
        ]);

        // Act
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_admin_with_unverified_email_cannot_login(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'verified' => true,
            'email_verified_at' => null,
        ]);

        // Act
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(403);
    }
}

