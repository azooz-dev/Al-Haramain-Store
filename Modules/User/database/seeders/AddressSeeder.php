<?php

namespace Modules\User\Database\Seeders;

use Modules\User\Entities\Address;
use Modules\User\Entities\User;
use Modules\User\Enums\AddressType;
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
            'label' => '456 Business District',
            'address_type' => AddressType::HOME,
            'street' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'postal_code' => '12345',
            'country' => 'USA',
            'is_default' => true,
        ]);
    }
}
