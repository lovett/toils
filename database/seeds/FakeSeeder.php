<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Project;
use App\Time;
use App\User;

/**
 * Populate the database with fake data
 */
class FakeSeeder extends Seeder
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

        factory(Client::class, 100)->create();
        factory(Project::class, 100)->create();
        factory(Time::class, 100)->create();
        factory(User::class, 10)->create();
        Model::reguard();
    }
}
