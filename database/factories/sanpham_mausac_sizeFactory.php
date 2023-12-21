<?php

namespace Database\Factories;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\sanpham;
use App\Models\mausac;
use App\Models\size;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\sanpham_mausac_size>
 */
class sanpham_mausac_sizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\sanpham_mausac_size::class;
    public function definition(): array
    {
        $sanpham = sanpham::inRandomOrder()->first();
        $mausac = mausac::inRandomOrder()->first();
        $size = DB::select("SELECT MASIZE FROM sizes ORDER BY RAND() DESC LIMIT 1");
 
            return [
                'MASP' => $sanpham->MASP,
                'MAMAU' => $mausac->MAMAU,
                'MASIZE' => $size[0]->MASIZE,
                'SOLUONG' => $this->faker->randomNumber(2, false),
            ]; 
        
    }
}
