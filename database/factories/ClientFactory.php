<?php

// phpcs:disable Squiz.Commenting

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'active' => $this->faker->boolean(50),
            'name' => $this->faker->company(),
            'contactName' => $this->faker->name(),
            'contactEmail' => $this->faker->safeEmail(),
            'address1' => $this->faker->streetAddress(),
            'address2' => $this->faker->secondaryAddress(),
            'city' => $this->faker->city(),
            'locality' => $this->faker->stateAbbr(),
            'postalCode' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
