<?php

use App\User;
use App\Client;
use App\Time;
use App\Project;
use Faker\Generator as FakerGenerator;

$factory->define(
    User::class,
    function (FakerGenerator $faker) {
        return [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt(str_random(10)),
            'remember_token' => str_random(10),
        ];
    }
);

$factory->define(
    Client::class,
    function (FakerGenerator $faker) {
        return [
            'user_id' => 1,
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
    }
);

$factory->define(
    Project::class,
    function (FakerGenerator $faker) {
        return [
            'user_id' => 1,
            'client_id' => 1,
            'name' => sprintf(
                '%s %s',
                'Project',
                $faker->colorName()
            ),
            'active' => $faker->boolean(),
            'billable' => $faker->boolean(),
            'taxDeducted' => $faker->boolean(),
        ];
    }
);

$factory->define(
    Time::class,
    function (FakerGenerator $faker) {
        $randomHours = $faker->numberBetween(1, 8);

        $randomMinutes = $faker->numberBetween(0, 180);

        $start = $faker->dateTimeBetween('-10 years', '-1 day');

        return [
            'user_id' => 1,
            'start' => $start,
            'minutes' => $randomMinutes,
            'estimatedDuration' => $faker->numberBetween(1, 480),
            'summary' => $faker->paragraph(),
            'project_id' => 1,
        ];
    }
);
