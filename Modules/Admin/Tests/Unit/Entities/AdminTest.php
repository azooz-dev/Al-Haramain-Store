<?php

namespace Modules\Admin\Tests\Unit\Entities;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Filament\Panel;

/**
 * TC-ADM-001: Admin Login - Verified Account
 * TC-ADM-007: Customer Access Admin Panel
 */
class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
    }

    public function test_admin_can_access_panel_when_verified(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'verified' => true,
            'email_verified_at' => now(),
        ]);

        // Act
        $canAccess = $admin->canAccessPanel(Mockery::mock(Panel::class));

        // Assert
        $this->assertTrue($canAccess);
    }

    public function test_admin_cannot_access_panel_when_unverified(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'verified' => false,
        ]);

        // Act
        $canAccess = $admin->canAccessPanel(Mockery::mock(Panel::class));

        // Assert
        $this->assertFalse($canAccess);
    }

    public function test_admin_cannot_access_panel_when_email_unverified(): void
    {
        // Arrange
        $admin = Admin::factory()->create([
            'verified' => true,
            'email_verified_at' => null,
        ]);

        // Act
        $canAccess = $admin->canAccessPanel(Mockery::mock(Panel::class));

        // Assert
        $this->assertFalse($canAccess);
    }
}
