<?php

use Faker\Generator as Faker;
use Carbon\Carbon;


$factory->define(App\Invoice::class, function (Faker $faker) {
    $randomTime = Carbon::createFromDate(
        null,
        $faker->numberBetween(1, 12),
        $faker->numberBetween(1, 31)
    );

    $mimeTypes = ['application/pdf', 'image/jpeg', null];

    return [
        'amount' => $faker->randomFloat(2, 50, 1000),
        'sent' => $randomTime->copy()->addDays(30),
        'due' => $randomTime->copy()->addDays(61),
        'paid' => $faker->optional()->dateTimeBetween('-1 year', '-1 day'),
        'name' => sprintf('%s Invoice', $faker->colorName()),
        'start' => $randomTime,
        'end' => $randomTime->copy()->addDays(30),
        'summary' => $faker->paragraph(),
    ];
});
