<?php

namespace Modules\User\Database\Seeders;

use Modules\User\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users using factory
        User::factory(50)->create();

        // Create specific user for testing
        User::create([
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
