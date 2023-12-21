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
class ManageProductController extends Controller
{
    public function getQuantityProductToDevidePage(Request $request){ 
        $quantity = DB::select(
            "SELECT COUNT(MASP)AS SL_MASP , TENPL 
            FROM sanphams, phanloai_sanphams
            WHERE sanphams.MAPL_SP = phanloai_sanphams.MAPL
            GROUP BY MAPL, TENPL "
        ); 

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoManageProduct(Request $request){
        $tenDanhMuc = $request->input('tenDanhMuc');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');

        $data_thongtin_sanpham = DB::select(
            "SELECT sanphams.MASP, TENSP, GIABAN, GIAGOC, TENPL2,
            -- SUM(chitiet_donhangs.SOLUONG) AS SOLUONGDABAN
            SUM(sanpham_mausac_sizes.SOLUONG) AS SOLUONGCONLAI
            FROM sanphams, sanpham_mausac_sizes, phanloai_sanphams, phanloai_sanpham2s
            -- chitiet_donhangs, 
            WHERE TENPL = '$tenDanhMuc'
            AND sanphams.MASP = sanpham_mausac_sizes.MASP 
            -- AND chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP
            AND phanloai_sanphams.MAPL = sanphams.MAPL_SP
            AND sanphams.MAPL_SP2 = phanloai_sanpham2s.MAPL2 
            AND phanloai_sanphams.MAPL = phanloai_sanpham2s.MAPL1 
            GROUP BY sanphams.MASP, TENSP, GIABAN, GIAGOC, TENPL2
            ORDER BY sanphams.MASP DESC
            LIMIT $start, $numberOrderEachPage" 
        ); 
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }

    public function infoProductDetail(Request $request){
        $masp = $request->input('masp');
        $dataProductDetail_sanphams = DB::select(
            "SELECT TENSP, GIAGOC, GIABAN, MAPL_SP, MAPL_SP2, MOTA from sanphams where MASP = $masp"
        );
        $dataProductDetail_sanpham_mausac_sizes__sizes = DB::select("SELECT DISTINCT(MASIZE) FROM sanpham_mausac_sizes where MASP = $masp");
        $dataProductDetail_sanpham_mausac_sizes__colors = DB::select(
            "SELECT mausacs.MAMAU, HEX, TENMAU
            FROM mausacs, sanpham_mausac_sizes 
            WHERE mausacs.MAMAU = sanpham_mausac_sizes.MAMAU  AND MASP = $masp
            GROUP BY mausacs.MAMAU, HEX, TENMAU"
        );
        $dataProductDetail_sanpham_mausac_sizes__hinhanhs = DB::select(
            "SELECT imgURL, MAHINHANH from hinhanhsanphams WHERE MASP = $masp"
        );
        $dataProductDetail_sanpham_mausac_sizes__soluongs = DB::select(
            "SELECT MAXDSP, MASIZE, mausacs.MAMAU, SOLUONG, HEX
            FROM mausacs, sanpham_mausac_sizes 
            WHERE mausacs.MAMAU = sanpham_mausac_sizes.MAMAU AND MASP = $masp
            GROUP BY MAXDSP, MASIZE, mausacs.MAMAU, SOLUONG, HEX"
        );
        $string_get_indexthumnail = DB::select(
            "SELECT MAHINHANH from hinhanhsanphams 
            where MASP = '$masp' AND MAHINHANH LIKE '%thumnail%'"
        );

        if($string_get_indexthumnail != null){
            $parts = explode('_', $string_get_indexthumnail[0]->MAHINHANH);
            if (count($parts) >= 3) {
                $indexthumnail = $parts[1]; 
            }
        }
        else{
            $indexthumnail = 0;  
        }

        return response()->json([
            'dataProductDetail_sanphams' => $dataProductDetail_sanphams,
            'dataProductDetail_sanpham_mausac_sizes__sizes' => $dataProductDetail_sanpham_mausac_sizes__sizes,
            'dataProductDetail_sanpham_mausac_sizes__colors' => $dataProductDetail_sanpham_mausac_sizes__colors,
            'dataProductDetail_sanpham_mausac_sizes__hinhanhs' => $dataProductDetail_sanpham_mausac_sizes__hinhanhs,
            'dataProductDetail_sanpham_mausac_sizes__soluongs' => $dataProductDetail_sanpham_mausac_sizes__soluongs,
            'indexthumnail' => $indexthumnail,
        ]);
    }   

    public function getQuantityProductToDevidePage_SearchProductAdmin(Request $request){ 

        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');

        if(is_numeric($keySearch) && $typeSearch == 'MASP'){
            $quantity = DB::select(
                "SELECT COUNT(MASP)AS SL_MASP , TENPL 
                FROM sanphams, phanloai_sanphams 
                WHERE sanphams.MAPL_SP = phanloai_sanphams.MAPL
                AND sanphams.$typeSearch = $keySearch
                GROUP BY MAPL, TENPL "
            );  
        }
        else{
            $quantity = DB::select(
                "SELECT COUNT(MASP)AS SL_MASP , TENPL 
                FROM sanphams, phanloai_sanphams 
                WHERE sanphams.MAPL_SP = phanloai_sanphams.MAPL
                AND sanphams.$typeSearch LIKE '%$keySearch%'
                GROUP BY MAPL, TENPL "
            ); 
        }


        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }

    public function getInfoSearchProductAdmin(Request $request){
        $tenDanhMuc = $request->query('tenDanhMuc');
        $start = $request->query('start');
        $numberOrderEachPage = $request->query('numberOrderEachPage'); 
        
        $keySearch = $request->query('keySearch');
        $typeSearch = $request->query('typeSearch');
        $data_thongtin_sanpham = [];
        if(is_numeric($keySearch) && $typeSearch == 'MASP'){
            $data_thongtin_sanpham = DB::select(
                "SELECT sanphams.MASP, TENSP, GIABAN, GIAGOC, 
                -- SUM(chitiet_donhangs.SOLUONG) AS SOLUONGDABAN
                SUM(sanpham_mausac_sizes.SOLUONG) AS SOLUONGCONLAI
                FROM sanphams, sanpham_mausac_sizes, phanloai_sanphams
                -- chitiet_donhangs, 
                WHERE TENPL = '$tenDanhMuc'
                AND sanphams.$typeSearch = $keySearch
                AND sanphams.MASP = sanpham_mausac_sizes.MASP 
                -- AND chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP
                AND phanloai_sanphams.MAPL = sanphams.MAPL_SP
                GROUP BY sanphams.MASP, TENSP, GIABAN, GIAGOC
                ORDER BY sanphams.MASP DESC
                LIMIT $start, $numberOrderEachPage" 
            );

        }
        else{
            $data_thongtin_sanpham = DB::select(
                "SELECT sanphams.MASP, TENSP, GIABAN, GIAGOC, 
                -- SUM(chitiet_donhangs.SOLUONG) AS SOLUONGDABAN
                SUM(sanpham_mausac_sizes.SOLUONG) AS SOLUONGCONLAI
                FROM sanphams, sanpham_mausac_sizes, phanloai_sanphams
                -- chitiet_donhangs, 
                WHERE TENPL = '$tenDanhMuc'
                AND $typeSearch LIKE '%$keySearch%'
                AND sanphams.MASP = sanpham_mausac_sizes.MASP 
                -- AND chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP
                AND phanloai_sanphams.MAPL = sanphams.MAPL_SP
                GROUP BY sanphams.MASP, TENSP, GIABAN, GIAGOC
                ORDER BY sanphams.MASP DESC
                LIMIT $start, $numberOrderEachPage" 
            );
        }
        // $data_soluong_daban = DB::select(
        //     "SELECT chitiet_donhangs.maxdsp, sum(chitiet_donhangs.soluong), sanphams.masp, count(madh) 
        //     from chitiet_donhangs, sanpham_mausac_sizes, phanloai_sanphams, sanphams 
        //     where chitiet_donhangs.maxdsp = sanpham_mausac_sizes.maxdsp 
        //     and phanloai_sanphams.mapl = sanphams.mapl_sp 
        //     and sanpham_mausac_sizes.masp = sanphams.masp and TENPL = '$tenDanhMuc'
        //     GROUP BY SANPHAMS.MASP"
        // );

        // $data_soluong_daban = SanPham::join('phanloai_sanphams', 'phanloai_sanphams.mapl', '=', 'sanphams.mapl_sp')
        // ->join('sanpham_mausac_sizes', 'sanpham_mausac_sizes.masp', '=', 'sanphams.masp')
        // ->join('chitiet_donhangs', 'chitiet_donhangs.maxdsp', '=', 'sanpham_mausac_sizes.maxdsp')
        // ->where('tenpl', 'Nam')
        // ->select('sanphams.masp', 'sanphams.tensp', 'sanphams.giasp', \DB::raw('MAX(chitiet_donhangs.maxdsp) as maxdsp'), \DB::raw('SUM(chitiet_donhangs.soluong) as total_soluong'), \DB::raw('COUNT(madh) as madh_count'))
        // ->groupBy('sanphams.masp')
        // ->get();
        return response()->json([
            'data_thongtin_sanpham' => $data_thongtin_sanpham,
            // 'data_soluong_daban' => $data_soluong_daban,
        ]);
    }

    public function deleteProduct(Request $request){
        $masp = $request->input('masp');  
        $haveInCTDH = DB::select(
            "SELECT sanpham_mausac_sizes.MAXDSP 
            FROM sanpham_mausac_sizes, chitiet_donhangs
            WHERE MASP = $masp
            AND sanpham_mausac_sizes.MAXDSP = chitiet_donhangs.MAXDSP"
        );
        
        if(count($haveInCTDH) == 0){
            DB::delete("DELETE FROM sanpham_mausac_sizes WHERE MASP = $masp");
            DB::delete("DELETE FROM hinhanhsanphams WHERE MASP = $masp");
            DB::delete("DELETE FROM sanphams WHERE MASP = $masp");
            return response()->json([
                'massage' => 'xoa thanh cong',
            ]);
        }
        return response()->json([
            'massage' => 'xoa khong thanh cong',
        ]);
    }
}
