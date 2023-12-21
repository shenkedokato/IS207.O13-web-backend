<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getInfoAtStartLoadingHome(){
        $dataNewProduct = DB::select(
            "SELECT * FROM sanphams, hinhanhsanphams 
            WHERE hinhanhsanphams.masp = sanphams.masp 
            AND MAHINHANH LIKE '%thumnail%'  
            ORDER BY created_at ASC 
            LIMIT 15"
        );
        $dataHotProduct = DB::select(
            "SELECT sanphams.MASP, TENSP, GIAGOC, GIABAN, MAPL_SP, imgURL
            FROM sanphams, chitiet_donhangs,  sanpham_mausac_sizes, hinhanhsanphams
            WHERE sanpham_mausac_sizes.MAXDSP = chitiet_donhangs.MAXDSP 
            AND sanphams.MASP = sanpham_mausac_sizes.MASP
            AND MAHINHANH LIKE '%thumnail%'  
            AND hinhanhsanphams.masp = sanphams.masp 
            GROUP BY sanphams.MASP, TENSP, GIAGOC, GIABAN, MAPL_SP, imgURL
            ORDER BY SUM(chitiet_donhangs.SOLUONG)
            LIMIT 15"
        ); 
        return response()->json([
            'dataNewProduct' => $dataNewProduct,
            'dataHotProduct' => $dataHotProduct, 
        ]);
    }
}
