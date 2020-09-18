<?php

// phpcs:disable Squiz.Commenting

namespace Database\Factories;

use App\Models\Time;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Time::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fakeStart = $this->faker->dateTimeBetween('-1 year', '-1 day');

        return [
            'start' => Carbon::createFromTimeStamp($fakeStart->getTimestamp()),
            'minutes' => $this->faker->numberBetween(0, 180),
            'estimatedDuration' => $this->faker->numberBetween(1, 480),
            'summary' => $this->faker->paragraph(),
        ];
    }
}
