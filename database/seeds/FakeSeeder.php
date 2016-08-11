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

        // A default user with predicatable credentials for use during
        // development
        User::updateOrCreate(
            [
                'name' => 'test'
            ],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]
        );

        // Additional users with random credentials for use in table
        // relations but not much else
        factory(User::class, 10)->create();

        factory(Client::class, 20)->create()->each(
            function ($client) {
                $userId = $this->randomUserId();
                $client->users()->attach([1, $userId]);
            }
        );

        factory(Project::class, 50)->create()->each(
            function ($project) {
                $userId = $this->randomUserId();
                $project->users()->attach([1, $userId]);
            }
        );

        factory(Time::class, 1000)->create();
        Model::reguard();
    }

    /**
     * Return a random user
     *
     * @return User
     */
    public function randomUserId()
    {
        return User::select('id')
            ->where('id', '>', 1)
            ->orderByRaw('RANDOM()')
            ->limit(1)
            ->first();
    }
}
