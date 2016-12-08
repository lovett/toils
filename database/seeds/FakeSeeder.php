<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Project;
use App\Time;
use App\User;
use App\Invoice;
use Faker\Factory as FakerFactory;


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

        // User 1 has predicatable credentials so that it can be used
        // during development.
        User::updateOrCreate(
            ['name' => 'test'],
            [
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]
        );

        // Additional users with random credentials
        factory(User::class, $maxUserId)->create();

        // Clients
        factory(Client::class, 20)->create()->each(
            function ($client) use ($faker) {
                $userId = $this->randomUserId();
                $client->users()->attach([1, $userId]);
            }
        );

        // Projects
        Client::all()->each(
            function ($client) use ($faker) {
                $projects = factory(Project::class, $faker->numberBetween(1, 2))->make();
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

    /**
     * Return a random user.
     *
     * @return User
     */
    public function randomUserId()
    {
        return User::select('id')
            ->where('id', '>', 1)
            ->orderByRaw('RANDOM()')
            ->limit(1)
            ->first()->id;
    }
}
