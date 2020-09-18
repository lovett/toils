<?php

// phpcs:disable Squiz.Commenting

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => sprintf(
                '%s %s',
                $this->faker->colorName(),
                $this->faker->randomDigit()
            ),
            'active' => $this->faker->boolean(75),
            'billable' => $this->faker->boolean(90),
            'taxDeducted' => $this->faker->boolean(20),
            'allottedTotalMinutes' => $this->faker->numberBetween(60, 60000),
            'allottedWeeklyMinutes' => $this->faker->numberBetween(30, 2400),
        ];
    }
}
