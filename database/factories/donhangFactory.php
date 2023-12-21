<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\taikhoan;
use App\Models\voucher;
use App\Models\thongtingiaohang;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\donhang>
 */
class donhangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected $model = \App\Models\donhang::class; 

    public function definition(): array
    {
        $taikhoan = taikhoan::inRandomOrder()->first();
        $voucher = voucher::inRandomOrder()->first();
        $thongtingiaohang = thongtingiaohang::inRandomOrder()->first();

        return [
            'MATK' => $taikhoan->MATK,            
            'GIA' => $this->faker->randomNumber(5, false),
            'NGAYGIAO' => $this->faker->dateTime($max = 'now', $timezone = null),

            // 'NGAYMUA' => $this->faker->dateTime($max = 'now', $timezone = null), // DateTime('2008-04-25 08:37:17', 'UTC')
            'HINHTHUC_THANHTOAN' => $this->faker->name(),
            'TRANGTHAI_THANHTOAN' => $this->faker->name(),
            'TRANGTHAI_DONHANG' => $this->faker->name(),
            'MAVOUCHER' => $voucher->MAVOUCHER,
            'MATTGH' => $thongtingiaohang->MATTGH,
            'GHICHU' => $this->faker->paragraph(),
        ];
    }
}
