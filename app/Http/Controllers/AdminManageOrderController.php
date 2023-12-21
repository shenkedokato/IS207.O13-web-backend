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
use GuzzleHttp\Psr7\Response;
use stdClass; 
class AdminManageOrderController extends Controller
{
    public function getQuantityOrderToDevidePage(Request $request){ 
         
        $quantity = DB::select(
            "SELECT COUNT(MADH)AS SL_MADH , TRANGTHAI_DONHANG 
            FROM donhangs GROUP BY TRANGTHAI_DONHANG "
        ); 

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoManageOrder(Request $request){
        $tenTrangThai = $request->input('tenTrangThai');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');
        // $orderList_DB = donhang::where('TRANGTHAI_DONHANG', 'LIKE', "%$tenTrangThai%")->orderBy('MADH', 'desc')
        // ->skip($start)->take($numberOrderEachPage)->get(); 

        $orderList_DB = DB::select(
            "SELECT donhangs.MADH, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG, NGAYORDER,
            TRANGTHAI_THANHTOAN, HINHTHUC_THANHTOAN, TONGTIENDONHANG, TONGTIEN_SP
            FROM donhangs, thongtingiaohangs 
            WHERE TRANGTHAI_DONHANG = '$tenTrangThai' 
            AND thongtingiaohangs.MATTGH = donhangs.MATTGH
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
    public function infoOrderDetail(Request $request){
        $madh = $request->query('madh');
        
        $data_relative_Donhang =  DB::select(
            "SELECT donhangs.MADH, thongtingiaohangs.TEN, SDT, DIACHI, 
            TINH_TP, QUAN_HUYEN, PHUONG_XA, TONGTIEN, TONGTIEN_SP,
            VOUCHERGIAM, TONGTIENDONHANG, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, 
            GHICHU, NGAYORDER, TRANGTHAI_DONHANG, PHIVANCHUYEN
            from donhangs, chitiet_donhangs, thongtingiaohangs 
            where donhangs.MADH = $madh  AND donhangs.MADH = chitiet_donhangs.MADH 
            AND thongtingiaohangs.MATTGH = donhangs.MATTGH"
        );
        $data_sanPham_relative_CTDH = DB::select(
            "SELECT sanphams.MASP, TENSP, GIABAN, TENMAU, HEX, MASIZE, TONGTIEN, chitiet_donhangs.SOLUONG, imgURL, sanpham_mausac_sizes.MAXDSP  
            from mausacs, chitiet_donhangs, sanphams, sanpham_mausac_sizes, hinhanhsanphams
            where chitiet_donhangs.MADH = $madh AND chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP 
            AND sanpham_mausac_sizes.MASP = sanphams.MASP AND sanpham_mausac_sizes.MAMAU = mausacs.MAMAU
            AND sanpham_mausac_sizes.MASP = hinhanhsanphams.MASP AND hinhanhsanphams.MAHINHANH LIKE '%thumnail%'"
        );

        

        foreach ($data_relative_Donhang as $order) {
            $order->NGAYORDER = Carbon::parse($order->NGAYORDER)->format('d/m/Y');
        }
        return response()->json([
            'data_relative_Donhang' => $data_relative_Donhang,
            'data_sanPham_relative_CTDH' => $data_sanPham_relative_CTDH,
            'ok'=> "ok"
        ]);
    }
    public function saveNote(Request $request){
        $data = $request->all();
        $madh = $data['madh'];
        $note = $data['note'];
        DB::update("UPDATE donhangs SET GHICHU = '$note' WHERE MADH = $madh");
        return response()->json([]);
    } 

    public function getQuantityOrderToDevidePage_Search(Request $request){ 
        
        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');
        $state = '';
        if(is_numeric($keySearch) && $typeSearch == 'MADH'){
            $keySearch = intval($keySearch);
            $quantity = DB::select(
                "SELECT COUNT(MADH)AS SL_MADH , TRANGTHAI_DONHANG 
                FROM donhangs
                -- , taikhoans
                WHERE $typeSearch = $keySearch
                -- AND taikhoans.MATK = donhangs.MATK
                GROUP BY TRANGTHAI_DONHANG "
            ); 
            $state = 'madh';
        }
        else{
            $quantity = DB::select(
                "SELECT COUNT(MADH)AS SL_MADH , TRANGTHAI_DONHANG 
                FROM donhangs
                , thongtingiaohangs
                WHERE $typeSearch LIKE '%$keySearch%' 
                AND thongtingiaohangs.MATK = donhangs.MATK
                GROUP BY TRANGTHAI_DONHANG "
            ); 
            $state = 'non-madh';
        }

        return response()->json([
            'quantity'=> $quantity, 
            'state' => $state,
        ]); 
    }
    public function getInfoSearchOrder(Request $request){
        $tenTrangThai = $request->query('tenTrangThai');
        $start = $request->query('start');
        $numberOrderEachPage = $request->query('numberOrderEachPage');
        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');

        
        $state = '';
        // $orderList_DB = donhang::where('TRANGTHAI_DONHANG', 'LIKE', "%$tenTrangThai%")->orderBy('MADH', 'desc')
        // ->skip($start)->take($numberOrderEachPage)->get(); 
        if(is_numeric($keySearch) && $typeSearch == 'MADH'){
            intval($keySearch);
            $orderList_DB = DB::select(
                "SELECT donhangs.MADH, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG, NGAYORDER,
                TRANGTHAI_THANHTOAN, HINHTHUC_THANHTOAN, TONGTIENDONHANG, TONGTIEN_SP
                FROM donhangs, thongtingiaohangs 
                WHERE $typeSearch = $keySearch 
                AND thongtingiaohangs.MATTGH = donhangs.MATTGH
                AND TRANGTHAI_DONHANG = '$tenTrangThai' 
                ORDER BY donhangs.MADH DESC
                LIMIT $start, $numberOrderEachPage"
            );
            $state = 'madh';

        }
        else{
            $orderList_DB = DB::select(
                "SELECT donhangs.MADH, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG, NGAYORDER,
                TRANGTHAI_THANHTOAN, HINHTHUC_THANHTOAN, TONGTIENDONHANG, TONGTIEN_SP
                FROM donhangs, thongtingiaohangs 
                WHERE $typeSearch like '%$keySearch%' 
                AND thongtingiaohangs.MATTGH = donhangs.MATTGH
                AND TRANGTHAI_DONHANG = '$tenTrangThai' 
                ORDER BY donhangs.MADH DESC
                LIMIT $start, $numberOrderEachPage"
            );
            $state = 'non-madh';

        }
        foreach ($orderList_DB as $order) {
            $order->NGAYORDER = Carbon::parse($order->NGAYORDER)->format('d/m/Y');
        }
        return response()->json([
            'orderList_DB' => $orderList_DB, 
            'state' => $state,

        ]);
    }

    public function updateOrderStatus(Request $request){
        $nameStatusWillUpdate = $request->input('nameStatusWillUpdate');
        $listMASPTranferState = $request->input('listMASPTranferState');

        // $listMASPTranferState_Array = json_decode($listMASPTranferState);
        // $listMASPTranferStateArray = explode(",", $listMASPTranferState);

        foreach($listMASPTranferState as $item){
            DB::update("UPDATE donhangs set TRANGTHAI_DONHANG = '$nameStatusWillUpdate' where MADH = $item");
            if($nameStatusWillUpdate === 'Đã huỷ'){
                $data_CTDH = DB::select(
                    "SELECT sanpham_mausac_sizes.MAXDSP, 
                    sanpham_mausac_sizes.SOLUONG as SLSP, chitiet_donhangs.SOLUONG as SLSP_Huy, 
                    MATK, MASP, MASIZE, MAMAU
                    FROM chitiet_donhangs, donhangs, sanpham_mausac_sizes
                    WHERE donhangs.MADH = $item
                    AND chitiet_donhangs.MADH = donhangs.MADH
                    AND sanpham_mausac_sizes.MAXDSP = chitiet_donhangs.MAXDSP"
                ); 
                foreach($data_CTDH as $item2){  
                    DB::update(
                        "UPDATE sanpham_mausac_sizes 
                        SET SOLUONG = ?
                        where MAXDSP = ?", 
                        [$item2->SLSP + $item2->SLSP_Huy, $item2->MAXDSP]
                    ); 
                } 
                $obj_mavoucher = DB::select("SELECT MAVOUCHER, MADH FROM donhang_vouchers WHERE MADH = ?", [$item]);
                if($obj_mavoucher != null){
                    $mavoucher = $obj_mavoucher[0]->MAVOUCHER;
                    $info_voucher = DB::select("SELECT SOLUONG_CONLAI FROM vouchers WHERE MAVOUCHER = '$mavoucher'");
                    DB::update(
                        "UPDATE vouchers 
                        SET SOLUONG_CONLAI = ? + 1
                        where MAVOUCHER = ?", 
                        [$info_voucher[0]->SOLUONG_CONLAI, $mavoucher]
                    );
                }
            }
        }
        // DB::update("UPDATE donhangs set TRANGTHAI_DONHANG = '$nameStatusWillUpdate' where MADH = 55");

    }
}
