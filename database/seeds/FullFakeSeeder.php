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
class FullFakeSeeder extends Seeder
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
        factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('test'),
        ]);

        // Additional users.
        factory(User::class, 1)->create();

        // Clients, projects, and time.
        User::all()->each(function ($user) {
                // First-round client assignment
                $clients = factory(Client::class, 5)->make();

                $user->clients()->saveMany($clients->all());

                // Projects for the newly created clients
                $clients->each(function ($client) use ($user) {
                    $projects = factory(Project::class, 2)->make();
                    $client->projects()->saveMany($projects);

                    // Time entries for the newly created projects
                    $projects->each( function ($project) use ($user) {
                        $time = factory(Time::class, 5)->make([
                            'user_id' => $user->getKey(),
                        ]);

                        $project->time()->saveMany($time);
                    });

                    // Invoices for the newly created projects
                    $projects->each( function ($project) use ($user) {
                        $invoices = factory(Invoice::class, 2)->make();
                        $project->invoices()->saveMany($invoices);
                    });
                });
            }
        );

        Model::reguard();
    }

}
