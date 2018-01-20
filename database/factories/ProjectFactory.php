<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'name' => sprintf(
            '%s %s',
            $faker->colorName(),
            $faker->randomDigit()
        ),
        'active' => $faker->boolean(75),
        'billable' => $faker->boolean(90),
        'taxDeducted' => $faker->boolean(20),
    ];
});
