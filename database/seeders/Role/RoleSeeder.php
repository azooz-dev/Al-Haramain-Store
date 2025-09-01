<?php

namespace Database\Seeders\Role;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super_admin role
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);

        // Get all permissions and assign them to super_admin role
        $permissions = Permission::where('guard_name', 'admin')->get();
        $superAdminRole->syncPermissions($permissions);

        $this->command->info('Super admin role created with all permissions!');
        $this->command->info('Total permissions assigned: ' . $permissions->count());
    }
}
