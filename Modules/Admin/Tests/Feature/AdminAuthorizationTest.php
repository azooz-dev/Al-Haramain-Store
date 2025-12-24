<?php

namespace Modules\Admin\Tests\Feature;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-ADM-004: Admin Without Permission - Access Denied
 * TC-ADM-005: Super Admin - Full Access
 * TC-ADM-006: Widget Permission Check
 * TC-ADM-007: Customer Access Admin Panel
 */
class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'limited_admin', 'guard_name' => 'admin']);

        Permission::firstOrCreate(['name' => 'view_any_order', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'create_product', 'guard_name' => 'admin']);
        Permission::firstOrCreate(['name' => 'widget_revenue_overview_widget', 'guard_name' => 'admin']);
    }

    public function test_super_admin_has_full_access(): void
    {
        // Arrange
        $superAdmin = Admin::factory()->create([
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // Act
        $this->actingAs($superAdmin, 'admin');

        // Assert
        $this->assertTrue($superAdmin->can('view_any_order'));
        $this->assertTrue($superAdmin->can('create_product'));
    }

    public function test_limited_admin_without_permission_denied(): void
    {
        // Arrange
        $limitedAdmin = Admin::factory()->create([
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        $limitedAdmin->assignRole('limited_admin');
        // No permissions assigned

        // Act
        $this->actingAs($limitedAdmin, 'admin');

        // Assert
        $this->assertFalse($limitedAdmin->can('create_product'));
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        // Act
        $response = $this->actingAs($user, 'web')
            ->get('/admin');

        // Assert
        $response->assertStatus(302); // Redirect to admin login
    }
}
