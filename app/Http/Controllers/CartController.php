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

class CartController extends Controller
{
    public function infoCart(Request $request){
        $matk = $request->input('matk');
        $data_sanpham = Sanpham::select(
            'sanphams.MATK', 'sanphams.MASP', 'TENSP', 'TONGGIA', 'SOLUONG', 'TENMAU', 
            'chitiet_giohangs.MASIZE', 'GIABAN', 'SELECTED', 'mausacs.MAMAU', 'imgURL'
        )
        ->join('chitiet_giohangs', 'chitiet_giohangs.MASP', '=', 'sanphams.MASP')
        ->join('mausacs', 'mausacs.MAMAU', '=', 'chitiet_giohangs.MAMAU')
        ->join('hinhanhsanphams', 'hinhanhsanphams.MASP', '=', 'sanphams.MASP')
        ->where('sanphams.MATK', $matk)
        ->where('MAHINHANH', 'LIKE', '%thumnail%')
        ->get();
        return response()->json([ 
            'data' => $data_sanpham, 
        ]);
    } 

    public function updateSelectedProperty(Request $request){
        $matk = $request->input('matk');
        $mamau = $request->input('mamau');
        $masize = $request->input('masize');
        $masp = $request->input('masp');
        $selected = $request->input('selected');
        DB::update("UPDATE chitiet_giohangs SET SELECTED = '$selected' WHERE MASP = '$masp' AND MATK = '$matk' AND MAMAU = '$mamau' AND MASIZE = '$masize' ");
        return response()->json([
            'message' => 200,
        ]);
    }

    public function deleteItemCart(Request $request){
        $matk = $request->input('matk');
        $mamau = $request->input('mamau');
        $masize = $request->input('masize');
        $masp = $request->input('masp');

        DB::delete("DELETE FROM chitiet_giohangs WHERE MATK = '$matk' AND MASP = '$masp' AND MAMAU = '$mamau' AND MASIZE = '$masize' ");
        return response()->json([
            'message' => 200,
        ]);
    }

    public function updateQuantityProperty(Request $request){
        $matk = $request->input('matk');
        $mamau = $request->input('mamau');
        $masize = $request->input('masize');
        $masp = $request->input('masp');
        $soluong = $request->input('soluong');
        $tonggia = $request->input('tonggia');

        DB::update(
            "UPDATE chitiet_giohangs 
            SET SOLUONG = '$soluong', TONGGIA = '$tonggia' 
            WHERE MASP = '$masp' 
            AND MATK = '$matk' 
            AND MAMAU = '$mamau' 
            AND MASIZE = '$masize' "
        );
        return response()->json([
            'message' => 200,
            'matk' => $matk,
        ]);
    }
}
