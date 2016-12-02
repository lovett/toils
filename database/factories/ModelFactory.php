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
            'name' => sprintf('%s %s', $faker->company(), $faker->randomNumber),
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
