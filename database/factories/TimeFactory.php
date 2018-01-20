<?php

use Faker\Generator as Faker;

$factory->define(App\Time::class, function (Faker $faker) {
    return [
        'start' => $faker->dateTimeBetween('-1 year', '-1 day'),
        'minutes' => $faker->numberBetween(0, 180),
        'estimatedDuration' => $faker->numberBetween(1, 480),
        'summary' => $faker->paragraph(),
    ];
});
