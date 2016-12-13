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

        // Additional users.
        factory(User::class, $maxUserId)->create();

        // Clients, projects, and time.
        $clientAccumulator = new Collection();
        User::all()->each(
            function ($user) use ($faker, &$clientAccumulator) {
                // First-round client assignment
                $clientsPerUser = 5;
                $clients = factory(Client::class, $clientsPerUser)->make();
                $user->clients()->saveMany($clients->all());

                // Projects for the newly created clients
                $clients->each(
                    function ($client) use ($faker, $user) {
                        $projectsPerClient = 4;
                        $projects = factory(Project::class, $projectsPerClient)->make();
                        $client->projects()->saveMany($projects->all());

                        // Time entries for the newly created projects
                        $projects->each(
                            function ($project) use ($faker, $user) {
                                $time = factory(Time::class, 20)->make([
                                    'user_id' => $user->getKey(),
                                ]);
                                $project->time()->saveMany($time->all());
                            }
                        );
                    }
                );

                // Second-round client assignment: add user to other users' clients.
                if ($clientAccumulator->isEmpty() === false) {
                    $user->clients()->saveMany($clientAccumulator->random($clientsPerUser));
                }
                $clientAccumulator = $clientAccumulator->merge($clients);
            }
        );

        // Invoices
        Project::all()->each(
            function ($project) use ($faker) {
                $invoices = factory(Invoice::class, 5)->make();
                $project->invoices()->saveMany($invoices->all());
            }
        );

        Model::reguard();
    }

}
