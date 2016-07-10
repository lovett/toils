<?php

use Illuminate\Database\Seeder;
use App\Time;

/**
 * Seed the times table with factory models
 */
class TimeTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Time::class, 100)->create();
    }
}
