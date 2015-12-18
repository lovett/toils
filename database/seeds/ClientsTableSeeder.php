<?php

use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert([
            'user_id' => 1,
            'name' => 'Test Client 1',
            'active' => 1,
            'contact_name' => 'John Smith',
            'contact_email' => 'jsmith@example.com',
            'address1' => '123 Fake Street',
            'address2' => 'Suite 999',
            'city' => 'Fakeville',
            'locality' => 'CA',
            'postal_code' => '90210',
            'phone' => '555-555-5555',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        DB::table('clients')->insert([
            'user_id' => 1,
            'name' => 'Test Client 2',
            'active' => 1,
            'contact_name' => 'Jane Doe',
            'contact_email' => 'jdoe@example.com',
            'address1' => 'Rue de Fakeville',
            'city' => 'Nowhere',
            'locality' => 'NV',
            'postal_code' => '12345',
            'phone' => '555-867-5309',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);
        DB::table('clients')->insert([
            'user_id' => 1,
            'name' => 'Inactive Test Client 1',
            'active' => 0,
            'contact_name' => 'Larry Jones',
            'contact_email' => 'ljones@example.com',
            'address1' => '1 Whatever Way',
            'city' => 'Imaginationland',
            'locality' => 'FL',
            'postal_code' => '13579',
            'phone' => '555-333-2222',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);
    }
}
