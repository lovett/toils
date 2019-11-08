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
 * Populate the database with a single fake user and no other data.
 *
 * This simulates a new user's experience.
 */
class UserFakeSeeder extends Seeder
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

        Model::reguard();
    }

}
