<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Client::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'active' => $faker->boolean(50),
        'name' => $faker->company(),
        'contact_name' => $faker->name(),
        'contact_email' => $faker->safeEmail(),
        'address1' => $faker->streetAddress(),
        'address2' => $faker->secondaryAddress(),
        'city' => $faker->city(),
        'locality' => $faker->stateAbbr(),
        'postal_code' => $faker->postcode(),
        'phone' => $faker->phoneNumber(),
    ];
});