<?php

namespace Modules\Admin\Tests\Unit\Policies;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Policies\AdminPolicy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-ADM-004: Admin Without Permission - Access Denied
 * TC-ADM-005: Super Admin - Full Access
 */
class AdminPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AdminPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new AdminPolicy();
        
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'view_any_admin', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'create_admin', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'update_admin', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'delete_admin', 'guard_name' => 'admin']);
    }

    public function test_super_admin_can_view_any_admin(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $admin->assignRole('super_admin');

        // Act
        $result = $this->policy->viewAny($admin);

        // Assert
        $this->assertTrue($result);
    }

    public function test_admin_without_permission_cannot_view_any(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        // No role assigned

        // Act
        $result = $this->policy->viewAny($admin);

        // Assert
        $this->assertFalse($result);
    }

    public function test_admin_with_permission_can_create(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $admin->givePermissionTo('create_admin');

        // Act
        $result = $this->policy->create($admin);

        // Assert
        $this->assertTrue($result);
    }

    public function test_admin_without_permission_cannot_create(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        // No permission assigned

        // Act
        $result = $this->policy->create($admin);

        // Assert
        $this->assertFalse($result);
    }
}
