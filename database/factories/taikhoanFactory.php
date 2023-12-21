<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\taikhoan>
 */
class taikhoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'TEN' => $this->faker->name(),
            'EMAIL' => $this->faker->email(),
            'PASSWORD' => $this->faker->password(),
            
        ];
    }
}
