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
            'email' => $faker->safeEmail,
            'password' => bcrypt(str_random(10)),
            'remember_token' => str_random(10),
        ];
    }
);

$factory->define(
    Client::class,
    function (FakerGenerator $faker) {
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
    }
);

$factory->define(
    Project::class,
    function (FakerGenerator $faker) {
        $randomClient = Client::select('id')
                      ->orderByRaw('RANDOM()')
                      ->limit(1)
                      ->first();

        return [
            'client_id' => $randomClient->id,
            'name' => sprintf(
                '%s %s %d',
                'Project',
                $faker->colorName(),
                $faker->randomDigit()
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

        $randomProject = Project::select('id')
                       ->orderByRaw('RANDOM()')
                       ->limit(1)
                       ->first();

        $randomUser = User::select('id')
                    ->orderByRaw('RANDOM()')
                    ->limit(1)
                    ->first();

        $start = $faker->dateTimeBetween('-10 years', '-1 day');

        return [
            'user_id' => $randomUser->id,
            'start' => $start,
            'minutes' => $randomMinutes,
            'estimatedDuration' => $faker->numberBetween(1, 480),
            'summary' => $faker->paragraph(),
            'project_id' => $randomProject->id,
        ];
    }
);
