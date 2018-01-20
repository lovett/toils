<?php

use Faker\Generator as Faker;

$factory->define(App\Client::class, function (Faker $faker) {
    return [
        'active' => $faker->boolean(50),
        'name' => $faker->company(),
        'contactName' => $faker->name(),
        'contactEmail' => $faker->safeEmail(),
        'address1' => $faker->streetAddress(),
        'address2' => $faker->secondaryAddress(),
        'city' => $faker->city(),
        'locality' => $faker->stateAbbr(),
        'postalCode' => $faker->postcode(),
        'phone' => $faker->phoneNumber(),
    ];
});
