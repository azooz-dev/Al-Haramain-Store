<?php

namespace Modules\Admin\Tests\Unit\Entities;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $admin = Admin::factory()->create(['verified' => true, 'email_verified_at' => now()]);
        $canAccess = $admin->canAccessPanel(Mockery::mock(\Filament\Panel::class));
        $this->assertTrue($canAccess);
    }

    public function test_admin_cannot_access_panel_when_unverified(): void
    {
        $admin = Admin::factory()->create(['verified' => false]);
        $canAccess = $admin->canAccessPanel(Mockery::mock(\Filament\Panel::class));
        $this->assertFalse($canAccess);
    }
}
