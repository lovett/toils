<?php

// phpcs:disable Squiz.Commenting

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomTime = Carbon::createFromDate(
            null,
            $this->faker->numberBetween(1, 12),
            $this->faker->numberBetween(1, 31)
        );

        $mimeTypes = ['application/pdf', 'image/jpeg', null];

        return [
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'sent' => $randomTime->copy()->addDays(30),
            'due' => $randomTime->copy()->addDays(61),
            'paid' => $this->faker->optional()->dateTimeBetween('-1 year', '-1 day'),
            'name' => sprintf('%s Invoice', $this->faker->colorName()),
            'start' => $randomTime,
            'end' => $randomTime->copy()->addDays(30),
            'summary' => $this->faker->paragraph(),
        ];
    }
}
