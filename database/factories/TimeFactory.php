<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Time::class, function (Faker $faker) {
    $fakeStart = $faker->dateTimeBetween('-1 year', '-1 day');

    return [
        'start' => Carbon::createFromTimeStamp($fakeStart->getTimestamp()),
        'minutes' => $faker->numberBetween(0, 180),
        'estimatedDuration' => $faker->numberBetween(1, 480),
        'summary' => $faker->paragraph(),
    ];
});
