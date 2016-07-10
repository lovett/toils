<?php

use Illuminate\Database\Seeder;

/**
 * Seed the  table with factory models
 */
class ClientsTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Client::class, 100)->create();
    }
}
