<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\taikhoan;
use App\Models\sanpham;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\chitiet_giohang>
 */
class chitiet_giohangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\chitiet_giohang::class;
    public function definition(): array
    {
        $taikhoan = taikhoan::inRandomOrder()->first();
        $sanpham = sanpham::inRandomOrder()->first();
        return [
            'MATK' => $taikhoan->MATK,
            'MASP' => $sanpham->MASP,
            'SOLUONG' => $this->faker->randomNumber(2, false),
            'TONGGIA' => function (array $attributes) use ($sanpham) {
                // Lấy giá sản phẩm từ bảng 'sanphams'
                $giaSanPham = $sanpham->GIABAN;
        
                // Tính toán giá trị cho trường 'TONGGIA' bằng cách nhân 'SOLUONG' với 'GIA'
                return $attributes['SOLUONG'] * $giaSanPham;
            }
        ];
    }
}
