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

class AddProductController extends Controller
{
    public function addProduct(Request $request){
        $objectInfoAddNewProduct = json_decode($request->input('infoAddNewProduct')); 
        $listQuantity = json_decode($request->input('listQuantity')); 

        $TENSP = $objectInfoAddNewProduct->nameProduct;
        $GIAGOC = $objectInfoAddNewProduct->originPrice;
        $GIABAN = $objectInfoAddNewProduct->sellPrice;
        $MAPL_SP = $objectInfoAddNewProduct->typeProduct;
        $MAPL_SP2 = $objectInfoAddNewProduct->typeProduct2;
        $MOTA = $objectInfoAddNewProduct->desctiption;
        $checkboxColor = $objectInfoAddNewProduct->checkboxColor;
        $checkboxSize = $objectInfoAddNewProduct->checkboxSize;
        $indexThumnail = $objectInfoAddNewProduct->indexThumnail;

        DB::insert(
            "INSERT into sanphams(TENSP, GIAGOC, GIABAN, MAPL_SP, MAPL_SP2, MOTA, created_at, updated_at) 
            values('$TENSP', $GIAGOC, $GIABAN, $MAPL_SP, $MAPL_SP2, '$MOTA', NOW(), NOW())"
        );
        $masp = DB::select("SELECT MASP FROM sanphams ORDER BY created_at DESC LIMIT 1");
        $masp_query = $masp[0]->MASP;

        $i = 0;
        foreach ($checkboxSize as $itemSize){
            foreach($checkboxColor as $itemColor){
                $soluong = $listQuantity[$i];
                DB::insert("insert into sanpham_mausac_sizes(MASP, MAMAU, MASIZE, SOLUONG) values($masp_query, $itemColor, '$itemSize', $soluong)");
                $i++;
            }
        }

        $hex = DB::select("SELECT distinct(HEX) from mausacs, sanpham_mausac_sizes where mausacs.MAMAU = sanpham_mausac_sizes.MAMAU AND MASP = $masp_query");

        $images = $request->file('images'); // 'image' là tên của trường chứa tệp tin trong FormData
        
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        
        $path = public_path('images/products/' . $masp_query . '/'); 
        // Tạo thư mục mới nếu không tồn tại
        !is_dir($path) && mkdir($path, 0777, true);

        $index_image = 0;

        foreach ($images as $image) {  
            if($index_image != $indexThumnail){
                $imageName = $masp_query . '_' . (++$index_image) . '_' . time() .  '.' . $image->extension();
            }
            else{
                $imageName = $masp_query . '_' . (++$index_image) . '_thumnail' .  '.' . $image->extension();
            }
            $image->move($path, $imageName);
 
            $pathImageURL = 'images/products/' . $masp_query . '/' . $imageName;
            $imgURL = asset($pathImageURL);

            DB::insert(
                "INSERT into hinhanhs value('$imageName')"
            );
            DB::insert(
                "INSERT into hinhanhsanphams value('$imageName', '$masp_query', '$imgURL')"
            );
        }
        return response()->json([
            'listHEX' =>  $hex,
        ]); 
    }

    public function getInfoForAddProduct(){
        $listColor = DB::select("SELECT * FROM mausacs ");
        return response()->json([
            'listColor' =>  $listColor,
        ]);
    }
    public function linkImageProduct(Request $request){
        // $imgpaths = DB::select("SELECT MAHINHANH from hinhanhsanphams where MASP = 20");
        $imgpaths = hinhanhsanpham::all();

        $imgURLs = $imgpaths->map(function($item){
            $masp = $item->MASP;
            $maha =  $item->MAHINHANH;
            $path = 'images/products/' . $masp . '/' . $maha;
            return asset($path);
        });
        return response()->json([
            'data_imgURL' => $imgURLs,
        ]);
    }

    public function updateQuantity(Request $request){
        $listHEXQuantity = $request->query('listHEXQuantity');

        foreach($listHEXQuantity as $item){
            $soluong = $item['Quantity'];
            DB::insert("UPDATE hinhanhsanphams");
        }
    }
    public function getDetailCategory2(Request $request){
        $typeProduct_mapl = $request->typeProduct_mapl;
        $listDetailCategory2 = DB::select(
            "SELECT * from phanloai_sanpham2s 
            WHERE MAPL1 = $typeProduct_mapl"
        );
        return response()->json([
            'listDetailCategory2' => $listDetailCategory2,
        ]);
    }
}
