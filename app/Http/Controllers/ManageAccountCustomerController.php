<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ManageAccountCustomerController extends Controller
{
    public function getQuantityAccountCustomerToDevidePage(Request $request){
        $quantity = DB::select(
            "SELECT COUNT(MATK) AS SL_MATK, ROLE
            FROM taikhoans 
            WHERE taikhoans.ROLE = 'Khách hàng' 
            GROUP BY ROLE"
        ); 

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoManageAccountCustomer(Request $request){
        // $AdminVerify = $request->input('AdminVerify');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');

        $data_thongtin_sanpham = DB::select(
            "SELECT EMAIL, TEN, GIOITINH, SDT, taikhoans.MATK, SUM(TONGTIENDONHANG) AS DOANHTHU, COUNT(MADH) AS SOLUONGDONHANG
            FROM taikhoans LEFT JOIN donhangs ON taikhoans.MATK = donhangs.MATK
            -- chitiet_donhangs, 
            WHERE ROLE = 'Khách hàng'
            -- and trangthai_donhang = 'Đã nhận'
            GROUP BY taikhoans.MATK, EMAIL, TEN, GIOITINH, SDT
            ORDER BY taikhoans.EMAIL DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
    public function searchAccountCustomer(Request $request){
        // $tenTrangThai = $request->query('AdminVerify');
        $start = $request->query('start');
        $numberOrderEachPage = $request->query('numberOrderEachPage');
        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');

         

        $data_thongtin_sanpham = DB::select(
            "SELECT EMAIL, TEN, GIOITINH, SDT, taikhoans.MATK, SUM(TONGTIENDONHANG) AS DOANHTHU, COUNT(MADH) AS SOLUONGDONHANG
            FROM taikhoans LEFT JOIN donhangs ON taikhoans.MATK = donhangs.MATK
            -- chitiet_donhangs, 
            WHERE ROLE = 'Khách hàng'
            -- and trangthai_donhang = 'Đã nhận'
            AND $typeSearch LIKE '%$keySearch%'
            GROUP BY taikhoans.MATK, EMAIL, TEN, GIOITINH, SDT
            ORDER BY taikhoans.EMAIL DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
}
