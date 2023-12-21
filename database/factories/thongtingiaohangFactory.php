<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\taikhoan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\thongtingiaohang>
 */
class thongtingiaohangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\thongtingiaohang::class;

    public function definition(): array
    {
        $taikhoan = taikhoan::inRandomOrder()->first();
        $taikhoandefault = 1;
        $DANGSUDUNGdefault = 1;
        return [
            'MATK' => $taikhoandefault,
            'DIACHI' => $this->faker->address(),
            'TEN' =>$this->faker->name(), 
            'SDT' => $this->faker->e164PhoneNumber(),
            'TINH_TP' =>  $this->faker->firstNameMale(),
            'QUAN_HUYEN' => $this->faker->firstNameMale(),
            'PHUONG_XA' =>  $this->faker->firstNameMale(),
            'DANGSUDUNG' => $DANGSUDUNGdefault
        ];
    }
}
