<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\sanpham;
use App\Models\donhang;
use App\Models\size;
use App\Models\mausac;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\chitiet_donhang>
 */
class chitiet_donhangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\chitiet_donhang::class;

    public function definition(): array
    {
        $sanpham = sanpham::inRandomOrder()->first();
        $donhang = donhang::inRandomOrder()->first();
        $size = size::inRandomOrder()->first();
        $mausac = mausac::inRandomOrder()->first();

        return [
            'MADH' => $donhang->MADH,
            'MASP' => $sanpham->MASP,
            'SOLUONG' => $this->faker->randomNumber(2, false),
            'TONGTIEN' => function (array $attributes) use ($sanpham) {
                // Lấy giá sản phẩm từ bảng 'sanphams'
                $giaSanPham = $sanpham->GIABAN;
        
                // Tính toán giá trị cho trường 'TONGGIA' bằng cách nhân 'SOLUONG' với 'GIA'
                return $attributes['SOLUONG'] * $giaSanPham;
            },
            'MASIZE' => $size->MASIZE,
            'MAUSAC' => $mausac->TENMAU,
        ];
    }
}
