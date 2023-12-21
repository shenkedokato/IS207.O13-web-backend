<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\hinhanhsanpham;
use App\Models\hinhanh;
use App\Models\sanpham;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\hinhanhsanpham>
 */
class hinhanhsanphamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
     protected $model = \App\Models\hinhanhsanpham::class;
    public function definition(): array
    { 
        $sanpham = sanpham::inRandomOrder()->first();
        $hinhanh = hinhanh::inRandomOrder()->first();

        return [ 
            'MAHINHANH' => $hinhanh->MAHINHANH,
            'MASP' => $sanpham->MASP,
        ];
    }
}
