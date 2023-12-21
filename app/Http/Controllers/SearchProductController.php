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


class SearchProductController extends Controller
{
    public function filterSearchProduct(Request $request){
        $filter = $request->input('filter'); 
        $tensp = $request->input('textQuery');
        $data_product = [];    
        $data_query =  
            "SELECT * FROM sanphams, hinhanhsanphams 
            WHERE hinhanhsanphams.masp = sanphams.masp 
            AND MAHINHANH LIKE '%thumnail%'  
            AND TENSP LIKE '%$tensp%'";

        if($filter == 'moinhat'){
            // $data_product = DB::select("SELECT * from sanphams where TENSP LIKE '%$tensp%' ORDER BY created_at DESC");
            $data_product = DB::select("$data_query ORDER BY created_at DESC");
            return response()->json([
                'data_product' => $data_product, 
                'filter'=> $filter,
            ]);
        }
        else if($filter == 'banchay'){
            $data_product = DB::select(
                "SELECT sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                from chitiet_donhangs, sanphams, hinhanhsanphams, sanpham_mausac_sizes
                where chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP 
                AND hinhanhsanphams.masp = sanphams.masp
                AND sanpham_mausac_sizes.masp = sanphams.masp
                AND MAHINHANH LIKE '%thumnail%'  
                AND TENSP LIKE '%$tensp%' 
                group by sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                order by SUM(chitiet_donhangs.SOLUONG) DESC"
            );
        }
        else if($filter == 'thapDenCao'){
            $data_product = DB::select("$data_query ORDER BY GIABAN ASC");
        }
        else if($filter == 'caoDenThap'){
            $data_product = DB::select("$data_query ORDER BY GIABAN DESC");
        }

        return response()->json([
            'data_product' => $data_product, 
            'filter'=> $filter, 
        ]);
    }
    public function search(Request $request){
        $searchQuery = $request->query('query');
        // $data = sanpham::where('TENSP', 'LIKE', "%$searchQuery%")->get();
        $data = DB::select(
            "SELECT * FROM sanphams, hinhanhsanphams 
            WHERE hinhanhsanphams.masp = sanphams.masp 
            AND MAHINHANH LIKE '%thumnail%'
            AND TENSP LIKE '%$searchQuery%' 
            ORDER BY created_at ASC"
        );
        return response()->json([
            'data' => $data,
        ]);
    }
}
