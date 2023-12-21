<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\size;
use App\Models\sanpham;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\phanloai_size>
 */
class phanloai_sizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\phanloai_size::class;
    public function definition(): array
    {
        $size = size::inRandomOrder()->first(); 
        $sanpham = sanpham::inRandomOrder()->first();

        return [
            'MASIZE' => $size->MASIZE,
            'MASP' => $sanpham->MASP,
            'SOLUONG' => $this->faker->randomNumber(2, false),
        ];
    }
}