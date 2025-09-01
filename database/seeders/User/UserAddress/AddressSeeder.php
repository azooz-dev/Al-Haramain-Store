<?php

namespace Database\Seeders\User\UserAddress;

use App\Models\User\UserAddresses\Address;
use App\Models\User\User;  // Add this import
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample addresses using factory
        Address::factory(100)->create();

        // Create specific address for testing
        Address::create([
            'user_id' => User::first()->id,
            'full_name' => 'John Doe',
            'phone' => '1234567890',
            'street' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'postal_code' => '12345',
            'country' => 'USA',
            'is_default' => true,
        ]);
    }
}
