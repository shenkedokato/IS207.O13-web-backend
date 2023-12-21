<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Models\taikhoan;
use App\Models\User;
use App\Models\sanpham;
use App\Models\chitiet_giohang;
use App\Models\donhang;
use App\Models\hinhanhsanpham;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use stdClass; 
class ManageAccountStaff extends Controller
{
    public function getQuantityAccountStaffToDevidePage(){
        $quantity = DB::select(
            "SELECT COUNT(MATK) AS SL_MATK, AdminVerify
            FROM taikhoans 
            WHERE taikhoans.ROLE = 'Nhân viên'
            Group by taikhoans.AdminVerify"
        ); 

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoManageAccountStaff(Request $request){
        $AdminVerify = $request->input('AdminVerify');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');

        $data_thongtin_sanpham = DB::select(
            "SELECT EMAIL, TEN, GIOITINH, SDT, MATK
            FROM taikhoans
            -- chitiet_donhangs, 
            WHERE AdminVerify = '$AdminVerify' AND ROLE = 'Nhân viên' 
            ORDER BY taikhoans.EMAIL DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
    public function deleteAccountStaff(Request $request){ 
        $matk = $request->input('matk');  
        $haveIntaikhoans = DB::select(
            "SELECT MATK
            FROM taikhoans
            WHERE MATK = $matk"
        );
        
        if(count($haveIntaikhoans) == 1){
            // DB::delete("DELETE FROM sanpham_mausac_sizes WHERE MASP = $masp");
            // DB::delete("DELETE FROM hinhanhsanphams WHERE MASP = $masp");
            DB::delete("UPDATE taikhoans SET ROLE = 'Khách hàng' WHERE MATK = $matk");
            return response()->json([
                'massage' => 'xoa thanh cong',
            ]);
        }
        return response()->json([
            'massage' => 'xoa khong thanh cong',
        ]);
    }
    public function updateStatusOfAccountStaff(Request $request){
        $nameStatusWillUpdate = $request->input('nameStatusWillUpdate');
        $matk = $request->input('matk');

        // $listMASPTranferState_Array = json_decode($listMASPTranferState);
        // $listMASPTranferStateArray = explode(",", $listMASPTranferState);
  
        DB::update("UPDATE taikhoans set AdminVerify = '$nameStatusWillUpdate' where matk = $matk"); 
    }

    public function searchAccountStaff(Request $request){
        $tenTrangThai = $request->query('AdminVerify');
        $start = $request->query('start');
        $numberOrderEachPage = $request->query('numberOrderEachPage');
        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');

        
        $data_thongtin_sanpham = DB::select(
            "SELECT EMAIL, TEN, GIOITINH, SDT, MATK
            FROM taikhoans
            -- chitiet_donhangs, 
            WHERE AdminVerify = '$tenTrangThai' AND ROLE = 'Nhân viên' 
            AND $typeSearch LIKE '%$keySearch%'
            ORDER BY taikhoans.EMAIL DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
}
