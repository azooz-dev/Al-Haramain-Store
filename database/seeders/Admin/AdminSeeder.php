<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::firstOrCreate([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
