<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Project;
use App\Time;
use App\User;
use App\Invoice;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Collection;


/**
 * Populate the database with fake data.
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
        $faker = FakerFactory::create();
        $maxUserId = 10;

        Model::unguard();

        // A user with predicatable credentials that can be used during development.
        User::updateOrCreate(
            ['name' => 'test'],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]
        );

        // Users.
        factory(User::class, $maxUserId)->create();

        // Clients.
        $accumulator = new Collection();
        User::all()->each(
            function ($user) use (&$accumulator) {
                $clientsPerUser = 5;
                $clients = factory(Client::class, $clientsPerUser)->make();
                $user->clients()->saveMany($clients->all());

                if ($accumulator->isEmpty() === false) {
                    $user->clients()->saveMany($accumulator->random($clientsPerUser));
                }
                $accumulator = $accumulator->merge($clients);
            }
        );

        // Projects
        $accumulator = new Collection();
        Client::all()->each(
            function ($client) use (&$accumulator) {
                $projectsPerClient = 4;
                $projects = factory(Project::class, $projectsPerClient)->make();
                $client->projects()->saveMany($projects->all());
            }
        );

        // Time
        Project::all()->each(
            function ($project) use ($faker, $maxUserId) {
                $time = factory(Time::class, 20)->make([
                    'user_id' => $faker->numberBetween(1, $maxUserId),
                ]);
                $project->time()->saveMany($time->all());
            }
        );

        // Invoices
        Project::all()->each(
            function ($project) use ($faker, $maxUserId) {
                $invoices = factory(Invoice::class, 5)->make();
                $project->invoices()->saveMany($invoices->all());
            }
        );

        Model::reguard();
    }

}
