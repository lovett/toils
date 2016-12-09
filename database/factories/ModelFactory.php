<?php

use App\User;
use App\Client;
use App\Time;
use App\Project;
use App\Invoice;
use Faker\Generator as FakerGenerator;
use Carbon\Carbon;

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
    }
);

$factory->define(
    Time::class,
    function (FakerGenerator $faker) {
        $start = $faker->dateTimeBetween('-1 year', '-1 day');

        return [
            'start' => $start,
            'minutes' => $faker->numberBetween(0, 180),
            'estimatedDuration' => $faker->numberBetween(1, 480),
            'summary' => $faker->paragraph(),
        ];
    }
);

$factory->define(
    Invoice::class,
    function (FakerGenerator $faker) {
        $randomProject = Project::orderByRaw('RANDOM()')
                       ->limit(1)
                       ->first();

        $randomTime = $randomProject->time()
                    ->select('start')
                    ->orderByRaw('RANDOM()')
                    ->limit(1)
                    ->first()
                    ->start
                    ->startOfDay();

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
            'project_id' => $randomProject->id,
            'start' => $randomTime,
            'end' => $randomTime->copy()->addDays(30),
            'summary' => $faker->paragraph(),
            'receiptType' => array_rand($mimeTypes),
            'receiptSize' => $faker->numberBetween(500, 100 * 1024),
        ];
    }
);
