<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Populate the database with factory-generated test data
 */
class FactorySeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::updateOrCreate(
            [
                'name' => 'test'
            ],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]
        );

        $this->call(UsersTableSeeder::class);
        $this->call(ClientsTableSeeder::class);
        $this->call(TimeTableSeeder::class);

        Model::reguard();
    }
}
