<?php

use Illuminate\Database\Seeder;
use App\User;

/**
 * Seed the users table with factory models
 */
class UsersTableSeeder extends Seeder
{


    /**
     * Create users via factory model
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 10)->create();
    }
}
