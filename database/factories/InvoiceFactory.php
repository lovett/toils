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
        'number' => $faker->numberBetween(1000, 9000),
        'amount' => $faker->randomFloat(2, 50, 1000),
        'sent' => $randomTime->copy()->addDays(30),
        'due' => $randomTime->copy()->addDays(61),
        'paid' => array_rand([
            null,
            $randomTime->copy()->addDays($faker->numberBetween(62, 90)),
        ]),
        'name' => sprintf('%s Invoice', $faker->colorName()),
        'start' => $randomTime,
        'end' => $randomTime->copy()->addDays(30),
        'summary' => $faker->paragraph(),
        'receiptType' => array_rand($mimeTypes),
        'receiptSize' => $faker->numberBetween(500, 100 * 1024),
    ];
});
