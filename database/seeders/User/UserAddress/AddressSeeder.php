<?php

namespace Database\Seeders\User\UserAddress;

use App\Models\User\UserAddresses\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'user_id' => 1,
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
