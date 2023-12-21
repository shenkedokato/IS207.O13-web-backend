<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\phanloai_sanpham;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\sanpham>
 */
class sanphamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\sanpham::class;
    public function definition(): array
    {

        $phanloai_sanpham = phanloai_sanpham::inRandomOrder()->first();
        return [
            'TENSP' => $this->faker->name(),
            'GIAGOC' => $this->faker->randomNumber(5, false),
            'GIABAN' => $this->faker->randomNumber(5, false),
            'MOTA' => $this->faker->paragraph(),
            'MAPL_SP' => $phanloai_sanpham->MAPL,
        ];
    }
}
