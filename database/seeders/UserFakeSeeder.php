<?php

// phpcs:disable Squiz.Commenting.ClassComment.Missing

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Project;
use App\Models\Time;
use App\Models\User;
use App\Models\Invoice;
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
