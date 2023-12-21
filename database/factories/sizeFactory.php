<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\size>
 */
class sizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\size::class;

    public function definition(): array
    {
        return [
            'MASIZE' => $this->faker->randomLetter(),
        ];
    }
}
