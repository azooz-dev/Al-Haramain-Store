<?php

namespace Database\Seeders\Role;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
    }
}
