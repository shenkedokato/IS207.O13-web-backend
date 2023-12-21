<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\voucher>
 */
class voucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\voucher::class;

    public function definition(): array
    {
        $THOIGIANBD = $this->faker->date($format = 'Y-m-d', $max = 'now');
        $THOIGIANKT = $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d');
        
        return [
            'MAVOUCHER' => $this->faker->firstNameMale(), 
            'SOLUONG' => $this->faker->randomNumber(2, false),
            
            'THOIGIANBD' => $THOIGIANBD,
            'THOIGIANKT' => $THOIGIANKT,
            'MOTA' => $this->faker->paragraph(),
            'GIATRIGIAM' => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 1) //0.1
        ];
    }
}
