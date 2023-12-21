<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\mausac>
 */
class mausacFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\mausac::class;

    public function definition(): array
    {
        return [
            'TENMAU' => $this->faker->colorName(),
            'HEX' => $this->faker->hexColor(),
        ];
    }
}
