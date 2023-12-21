<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class VoucherController extends Controller
{
    public function addVoucher(Request $request){
        $objectInfoAddNewVoucher = json_decode($request->input('infoAddNewVoucher'));  

        $MAVOUCHER = $objectInfoAddNewVoucher->showNameVoucher;
        $PHANLOAI_VOUCHER = $objectInfoAddNewVoucher->typeVoucher;
        $GIATRIGIAM = (float)$objectInfoAddNewVoucher->decreasePersent;
        $THOIGIANBD = $objectInfoAddNewVoucher->startDate;
        $THOIGIANKT = $objectInfoAddNewVoucher->endDate;
        $GIATRI_DH_MIN = $objectInfoAddNewVoucher->minOrderValue;
        $GIATRI_GIAM_MAX = $objectInfoAddNewVoucher->maxDecreaseMoney;
        $SOLUONG = $objectInfoAddNewVoucher->quantityUse;
        $MOTA = $objectInfoAddNewVoucher->desctiption; 

        DB::insert(
            "INSERT into vouchers(MAVOUCHER, PHANLOAI_VOUCHER, GIATRIGIAM, THOIGIANBD, THOIGIANKT, 
            GIATRI_DH_MIN, GIATRI_GIAM_MAX, SOLUONG, SOLUONG_CONLAI, MOTA) 
            values('$MAVOUCHER', '$PHANLOAI_VOUCHER', $GIATRIGIAM, '$THOIGIANBD', '$THOIGIANKT', 
            $GIATRI_DH_MIN, $GIATRI_GIAM_MAX, $SOLUONG, $SOLUONG, '$MOTA')"
        ); 
        return response()->json([]); 
    }
    public function getQuantityVoucherToDevidePage(Request $request){
        $currentDate = now()->format('Y-m-d');

        $quantity_chuaApDung = DB::select(
            "SELECT COUNT(MAVOUCHER) AS SL_MAVOUCHER,
            'Chưa áp dụng' AS TEN_TRANGTHAI
            FROM vouchers 
            WHERE vouchers.THOIGIANBD > $currentDate "
        ); 
        $quantity_DangSuDung = DB::select(
            "SELECT COUNT(MAVOUCHER) AS SL_MAVOUCHER,
            'Đang áp dụng' AS TEN_TRANGTHAI 
            FROM vouchers 
            WHERE '$currentDate' 
            BETWEEN vouchers.THOIGIANBD 
            AND vouchers.THOIGIANKT 
            AND SOLUONG_CONLAI > 0"
        );
        $quantity_daSuDung = DB::select(
            "SELECT COUNT(MAVOUCHER) AS SL_MAVOUCHER,
            'Đã qua sử dụng' AS TEN_TRANGTHAI 
            FROM vouchers 
            WHERE vouchers.THOIGIANKT < '$currentDate' OR SOLUONG_CONLAI = 0"
        );

        $quantity = array_merge($quantity_chuaApDung, $quantity_DangSuDung, $quantity_daSuDung);

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoManageVoucher(Request $request){
        $tenDanhMuc = $request->input('tenDanhMuc');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');
        $where = '';
        $currentDate = now()->format('Y-m-d');

        if($tenDanhMuc === 'Chưa áp dụng'){
            $where = "WHERE vouchers.THOIGIANBD > '$currentDate'";
        }
        else if($tenDanhMuc === 'Đang áp dụng'){
            $where = "WHERE '$currentDate' 
            BETWEEN vouchers.THOIGIANBD 
            AND vouchers.THOIGIANKT 
            AND SOLUONG_CONLAI > 0";
        }
        else if($tenDanhMuc === 'Đã qua sử dụng'){
            $where = "WHERE vouchers.THOIGIANKT < '$currentDate' OR SOLUONG_CONLAI = 0";
        }

        $data_thongtin_sanpham = DB::select(
            "SELECT MAVOUCHER, SOLUONG, THOIGIANBD, THOIGIANKT, GIATRIGIAM, GIATRI_DH_MIN, GIATRI_GIAM_MAX, PHANLOAI_VOUCHER, MOTA
            FROM vouchers
            -- chitiet_donhangs, 
            $where 
            ORDER BY THOIGIANBD DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
    public function infoVoucherDetail(Request $request){
        $mavoucher = $request->input('mavoucher');
        $dataVoucherDetail_sanphams = DB::select(
            "SELECT MAVOUCHER, SOLUONG, THOIGIANBD, THOIGIANKT, GIATRIGIAM, GIATRI_DH_MIN, GIATRI_GIAM_MAX, PHANLOAI_VOUCHER, MOTA
            FROM vouchers
            -- chitiet_donhangs, 
            WHERE MAVOUCHER = '$mavoucher'" 
        );  
        return response()->json([
            'dataVoucherDetail_sanphams' => $dataVoucherDetail_sanphams, 
        ]);
    }
    public function updateVoucher(Request $request){
        $objectInfoUpdateVoucher = json_decode($request->input('infoUpdateVoucher'));  

        $MAVOUCHER = $objectInfoUpdateVoucher->showNameVoucher;
        $PHANLOAI_VOUCHER = $objectInfoUpdateVoucher->typeVoucher;
        $GIATRIGIAM = (float)$objectInfoUpdateVoucher->decreasePersent;
        $THOIGIANBD = $objectInfoUpdateVoucher->startDate;
        $THOIGIANKT = $objectInfoUpdateVoucher->endDate;
        $GIATRI_DH_MIN = $objectInfoUpdateVoucher->minOrderValue;
        $GIATRI_GIAM_MAX = $objectInfoUpdateVoucher->maxDecreaseMoney;
        $SOLUONG = $objectInfoUpdateVoucher->quantityUse;
        $MOTA = $objectInfoUpdateVoucher->desctiption; 

        DB::update(
            "UPDATE vouchers 
            SET PHANLOAI_VOUCHER = '$PHANLOAI_VOUCHER', 
            GIATRIGIAM = '$GIATRIGIAM', THOIGIANBD = '$THOIGIANBD', 
            THOIGIANKT = '$THOIGIANKT', GIATRI_DH_MIN = '$GIATRI_DH_MIN',
            GIATRI_GIAM_MAX = '$GIATRI_GIAM_MAX', SOLUONG = '$SOLUONG', 
            MOTA = '$MOTA' WHERE  MAVOUCHER = '$MAVOUCHER'"
        );
        return response()->json([]); 

    }
    public function getInfoSearchVoucher(Request $request){
        $tenDanhMuc = $request->input('tenDanhMuc');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');
        $keySearch = $request->input('keySearch');
        $typeSearch = $request->input('typeSearch');
        $where = '';
        $search = "AND $typeSearch LIKE '%$keySearch%'";
        $currentDate = now()->format('Y-m-d');

        if($tenDanhMuc === 'Chưa áp dụng'){
            $where = "WHERE vouchers.THOIGIANBD > '$currentDate'";
        }
        else if($tenDanhMuc === 'Đang áp dụng'){
            $where = "WHERE '$currentDate' 
            BETWEEN vouchers.THOIGIANBD 
            AND vouchers.THOIGIANKT 
            AND SOLUONG_CONLAI > 0
            ";
        }
        else if($tenDanhMuc === 'Đã qua sử dụng'){
            $where = "WHERE (vouchers.THOIGIANKT < '$currentDate' OR SOLUONG_CONLAI = 0)";
        }

        $data_thongtin_sanpham = DB::select(
            "SELECT MAVOUCHER, SOLUONG, THOIGIANBD, THOIGIANKT, GIATRIGIAM, GIATRI_DH_MIN, GIATRI_GIAM_MAX, PHANLOAI_VOUCHER, MOTA
            FROM vouchers
            -- chitiet_donhangs, 
            $where $search
            ORDER BY THOIGIANBD DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }
    public function deleteVoucher(Request $request){
        $mavoucher = $request->mavoucher;
        DB::delete("DELETE FROM vouchers WHERE MAVOUCHER = '$mavoucher'");
    }
}


