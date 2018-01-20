<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Project;
use App\Time;
use App\User;
use App\Invoice;
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
        Model::unguard();

        // A user with predicatable credentials for use during development.
        User::updateOrCreate(
            ['name' => 'test'],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]
        );

        // Additional users.
        factory(User::class, 3)->create();

        // Clients, projects, and time.
        $clientAccumulator = new Collection();
        User::all()->each(
            function ($user) use (&$clientAccumulator) {
                // First-round client assignment
                $clientsPerUser = 5;
                $clients = factory(Client::class, $clientsPerUser)->make();

                $user->clients()->saveMany($clients->all());

                // Projects for the newly created clients
                $clients->each(function ($client) use ($user) {
                    $projectsPerClient = 4;
                    $projects = factory(Project::class, $projectsPerClient)->make();
                    $client->projects()->saveMany($projects->all());

                    // Time entries for the newly created projects
                    $projects->each( function ($project) use ($user) {
                        $timeEntriesPerProject = 20;
                        $time = factory(Time::class, $timeEntriesPerProject)->make([
                            'user_id' => $user->getKey(),
                        ]);

                        $project->time()->saveMany($time->all());
                    });

                    // Invoices for the newly created projects
                    $projects->each( function ($project) use ($user) {
                        $invoicesPerProject = 3;
                        $invoices = factory(Invoice::class, $invoicesPerProject)->make();
                        $project->invoices()->saveMany($invoices->all());
                    });
                });

                // // Second-round client assignment: add user to other users' clients.
                // if ($clientAccumulator->isEmpty() === false) {
                //     $user->clients()->saveMany($clientAccumulator->random($clientsPerUser));
                // }
                $clientAccumulator = $clientAccumulator->merge($clients);
            }
        );

        Model::reguard();
    }

}
