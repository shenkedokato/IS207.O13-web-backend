<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class MyOrderController extends Controller
{
    public function getInfoMyOrder(Request $request){
        $tenTrangThai = $request->input('tenTrangThai');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');
        $matk = $request->input('matk');
        // $orderList_DB = donhang::where('TRANGTHAI_DONHANG', 'LIKE', "%$tenTrangThai%")->orderBy('MADH', 'desc')
        // ->skip($start)->take($numberOrderEachPage)->get(); 

        $orderList_DB = DB::select(
            "SELECT donhangs.MADH, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG, NGAYORDER,
            TRANGTHAI_THANHTOAN, HINHTHUC_THANHTOAN, TONGTIENDONHANG, TONGTIEN_SP
            FROM donhangs, thongtingiaohangs 
            WHERE TRANGTHAI_DONHANG = '$tenTrangThai' 
            AND thongtingiaohangs.MATTGH = donhangs.MATTGH
            AND donhangs.MATK = $matk
            ORDER BY donhangs.MADH DESC
            LIMIT $start, $numberOrderEachPage"
        );

        foreach ($orderList_DB as $order) {
            $order->NGAYORDER = Carbon::parse($order->NGAYORDER)->format('d/m/Y');
        }

        return response()->json([
            'orderList_DB' => $orderList_DB, 
        ]);
    }
    public function infoOrderDetail_myOder(Request $request){
        $madh = $request->query('madh');
        $OK = '1'; 
        $data_relative_Donhang =  DB::select(
            "SELECT donhangs.MADH, thongtingiaohangs.TEN, SDT, DIACHI, 
            TINH_TP, QUAN_HUYEN, PHUONG_XA, TONGTIEN, TONGTIEN_SP,
            VOUCHERGIAM, TONGTIENDONHANG, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, GHICHU
            from donhangs, chitiet_donhangs, thongtingiaohangs 
            where donhangs.MADH = $madh  AND donhangs.MADH = chitiet_donhangs.MADH 
            AND thongtingiaohangs.MATTGH = donhangs.MATTGH"
        );
        $data_sanPham_relative_CTDH = DB::select(
            "SELECT TENSP, GIABAN, TENMAU, HEX, MASIZE, 
            TONGTIEN, chitiet_donhangs.SOLUONG, imgURL, sanpham_mausac_sizes.MASP, sanpham_mausac_sizes.MAXDSP
            from mausacs, chitiet_donhangs, sanphams, sanpham_mausac_sizes, hinhanhsanphams
            where chitiet_donhangs.MADH = $madh
            AND DADANHGIA = 0
            AND hinhanhsanphams.MAHINHANH LIKE '%thumnail%'
            AND chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP 
            AND sanpham_mausac_sizes.MASP = sanphams.MASP 
            AND sanpham_mausac_sizes.MAMAU = mausacs.MAMAU
            AND sanpham_mausac_sizes.MASP = hinhanhsanphams.MASP"
        );
        return response()->json([
            'data_relative_Donhang' => $data_relative_Donhang,
            'data_sanPham_relative_CTDH' => $data_sanPham_relative_CTDH,
            'ok'=> "ok"
        ]);
    }
    public function getQuantityOrderToDevidePage__myOder(Request $request){
        $matk = $request->input('matk');

        $quantity = DB::select(
            "SELECT COUNT(MADH)AS SL_MADH , TRANGTHAI_DONHANG 
            FROM donhangs 
            WHERE  donhangs.MATK = $matk
            GROUP BY TRANGTHAI_DONHANG "
        ); 

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
}
