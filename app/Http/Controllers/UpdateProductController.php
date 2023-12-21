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
class UpdateProductController extends Controller
{
    public function updateProduct(Request $request){
        $objectInfoUpdateProduct = json_decode($request->input('infoUpdateProduct')); 
        $listQuantity = json_decode($request->input('listQuantity')); 

        $TENSP = $objectInfoUpdateProduct->nameProduct;
        $GIAGOC = $objectInfoUpdateProduct->originPrice;
        $GIABAN = $objectInfoUpdateProduct->sellPrice;
        $MAPL_SP = $objectInfoUpdateProduct->typeProduct;
        $MAPL_SP2 = $objectInfoUpdateProduct->typeProduct2;
        $MOTA = $objectInfoUpdateProduct->desctiption;
        $checkboxColor = $objectInfoUpdateProduct->checkboxColor;
        $checkboxSize = $objectInfoUpdateProduct->checkboxSize;
        $indexThumnail = $objectInfoUpdateProduct->indexThumnail;
        $imgurl = $objectInfoUpdateProduct->imgurl;
        $maHinhAnhDeleted = $objectInfoUpdateProduct->maHinhAnhDeleted;
        $masp_query = $objectInfoUpdateProduct->masp;
        $quantityImgurl = $objectInfoUpdateProduct->quantityImgurl;
        $mahinhanh = $objectInfoUpdateProduct->mahinhanh;
        $indexThumnailCong1 = $indexThumnail + 1;


        DB::update(
            "UPDATE sanphams 
            SET TENSP = '$TENSP', GIAGOC = $GIAGOC, GIABAN = $GIABAN, 
            MAPL_SP = $MAPL_SP, MAPL_SP2 = $MAPL_SP2, MOTA = '$MOTA', updated_at = NOW()
            WHERE MASP = $masp_query"
        ); 
 
        $soluong = 0;
        foreach ($checkboxSize as $itemSize){
            foreach($checkboxColor as $itemColor){
                foreach($listQuantity as $itemQuantity){
                    if($itemQuantity->maSize == $itemSize && $itemQuantity->mamau == $itemColor){
                        $soluong = $itemQuantity->soluong;
                        break; 
                    }
                }
                
                $test_daco = DB::select(
                    "SELECT MAXDSP FROM sanpham_mausac_sizes 
                    WHERE MASP = $masp_query AND MAMAU = $itemColor AND MASIZE = '$itemSize'"
                );
                if( count($test_daco) > 0){
                    $maxdsp = $test_daco[0]->MAXDSP;
                    DB::update("UPDATE sanpham_mausac_sizes SET SOLUONG = $soluong WHERE MAXDSP = $maxdsp");
                }
                else if(count($test_daco) == 0){
                    DB::insert("INSERT into sanpham_mausac_sizes(MASP, MAMAU, MASIZE, SOLUONG) values($masp_query, $itemColor, '$itemSize', $soluong)");
                } 
            }
        }

        $KiemTraDeLuocBo = DB::select(
            "SELECT MAMAU, MASIZE, MAXDSP FROM sanpham_mausac_sizes 
            WHERE MASP = $masp_query"
        );

        foreach($KiemTraDeLuocBo as $item){
            $i = 0; 
            foreach ($checkboxSize as $itemSize){
                foreach($checkboxColor as $itemColor){
                    if($item->MASIZE == $itemSize && $item->MAMAU == $itemColor){
                        $i++;
                        break;
                    }
                }
                if($i != 0){
                    break;
                }
            }
            if($i == 0){
                $KiemTraCoTrongCTDHkhong = DB::select(
                    "SELECT COUNT(MAXDSP) FROM chitiet_donhangs 
                    WHERE MAXDSP = $item->MAXDSP"
                );
                if(count($KiemTraCoTrongCTDHkhong) == 0){
                    DB::delete("DELETE FROM sanpham_mausac_sizes Where MAXDSP = $item->MAXDSP");
                }
                else{
                    DB::update("UPDATE sanpham_mausac_sizes SET SOLUONG = 0 WHERE MAXDSP = $item->MAXDSP ");
                }
            }
        }

        // $hex = DB::select("SELECT distinct(HEX) from mausacs, sanpham_mausac_sizes where mausacs.MAMAU = sanpham_mausac_sizes.MAMAU AND MASP = $masp_query");

        $images = $request->file('images'); // 'image' là tên của trường chứa tệp tin trong FormData
        
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        
        foreach ($maHinhAnhDeleted as $item){ 
            DB::delete("DELETE FROM hinhanhsanphams WHERE MAHINHANH = '$item'");
        }
    
        $List_MAHINHANH_compare = DB::select("SELECT MAHINHANH from hinhanhsanphams WHERE MASP = $masp_query");

        $compare_thumnail_old_and_new = true;
        $path_new_thumnail = $indexThumnailCong1 . '_thumnail'; 
        foreach($List_MAHINHANH_compare as $item){ 
            $haveOrNo = DB::select("SELECT MAHINHANH from hinhanhsanphams WHERE MAHINHANH LIKE '%$path_new_thumnail%' AND MASP = $masp_query");
            
            if(count($haveOrNo) > 0) $compare_thumnail_old_and_new = true;
            else $compare_thumnail_old_and_new = false; 
        }

        $path = public_path('images/products/' . $masp_query . '/'); 
        // Tạo thư mục mới nếu không tồn tại
        !is_dir($path) && mkdir($path, 0777, true);
        $index_image = $quantityImgurl;

        if($compare_thumnail_old_and_new){ // trường hợp ko thay đổi indexThumnail
            if($images != null){

                foreach ($images as $image) {  
                    if($index_image != $indexThumnail){
                        $imageName = $masp_query . '_' . (++$index_image) . '_' . time() .  '.' . $image->extension();
                    }
                    else{
                        $imageName = $masp_query . '_' . (++$index_image) . '_' . time() . '_thumnail' .  '.' . $image->extension();
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
            } 
        }
        else{//có thay đổi
            
            $thumnail_old = DB::select("SELECT MAHINHANH, imgURL, MASP  from hinhanhsanphams where MAHINHANH LIKE '%thumnail%' AND MASP = $masp_query");
            if($thumnail_old != null){

                $MAHINHANH_update =  str_replace("_thumnail", "_", $thumnail_old[0]->MAHINHANH); 
                $imgURL_update = $thumnail_old[0]->imgURL;
                
                //gỡ bõ thumnail của imgURL được chọn lúc trước
                $MHA_update_hinhanhs = $thumnail_old[0]->MAHINHANH;
                // DB::update("UPDATE hinhanhs SET MAHINHANH = '$imgURLName_update'  WHERE MAHINHANH LIKE '%$MHA_update_hinhanhs%'");
                // DB::update("UPDATE hinhanhsanphams SET MAHINHANH = '$imgURLName_update'  WHERE MAHINHANH LIKE '%thumnail%' AND MASP = $masp_query");
    
                DB::delete("DELETE FROM hinhanhsanphams WHERE MAHINHANH LIKE '%thumnail%' AND MASP = $masp_query ");
                DB::delete("DELETE FROM hinhanhs WHERE MAHINHANH LIKE '%$MHA_update_hinhanhs%'");
    
                DB::insert("INSERT INTO hinhanhs VALUES('$MAHINHANH_update')");
                DB::insert("INSERT INTO hinhanhsanphams VALUES('$MAHINHANH_update', $masp_query, '$imgURL_update')");
            }

            if($indexThumnail < $quantityImgurl){// index thay đổi đối với những img url có sẵn
                $imgURLBeforeUpdate_textQuery = $masp_query . '_' . $indexThumnailCong1;
                $imgURLBeforeUpdate_resultQuery = DB::select(
                    "SELECT MAHINHANH, imgURL from hinhanhsanphams 
                    where MAHINHANH LIKE '%$imgURLBeforeUpdate_textQuery%' 
                    AND MASP = $masp_query"
                );

                // for($i = $indexThumnailCong1; count($imgURLBeforeUpdate_resultQuery) > 0; $i++){ 
                //     $imgURLBeforeUpdate_textQuery = $masp_query . '_' . $i;
                //     $imgURLBeforeUpdate_resultQuery = DB::select(
                //         "SELECT MAHINHANH, imgURL from hinhanhsanphams 
                //         where MAHINHANH LIKE '%$imgURLBeforeUpdate_textQuery%' 
                //         AND MASP = $masp_query"
                //     );
                //     break; 
                // }
                $i = $indexThumnailCong1;
                while($imgURLBeforeUpdate_resultQuery == null){
                    $imgURLBeforeUpdate_textQuery = $masp_query . '_' . $i;
                    $imgURLBeforeUpdate_resultQuery = DB::select(
                        "SELECT MAHINHANH, imgURL from hinhanhsanphams 
                        where MAHINHANH LIKE '%$imgURLBeforeUpdate_textQuery%' 
                        AND MASP = $masp_query"
                    );
                    $i++;
                }

                $imgURL_newThumnail = $imgURLBeforeUpdate_resultQuery[0]->imgURL;

                $dotPosition = strrpos($imgURLBeforeUpdate_resultQuery[0]->MAHINHANH, '.');
                $imgURLPrepareUpdate = substr_replace($imgURLBeforeUpdate_resultQuery[0]->MAHINHANH, '_thumnail', $dotPosition, 0);

                DB::delete(
                    "DELETE FROM hinhanhsanphams 
                    WHERE MAHINHANH LIKE '%$imgURLBeforeUpdate_textQuery%' 
                    AND MASP = $masp_query"
                );
                DB::delete(
                    "DELETE FROM hinhanhs 
                    WHERE MAHINHANH LIKE '%$imgURLBeforeUpdate_textQuery%'"
                );

                DB::insert("INSERT INTO hinhanhs VALUES('$imgURLPrepareUpdate')");
                DB::insert("INSERT INTO hinhanhsanphams VALUES('$imgURLPrepareUpdate', $masp_query, '$imgURL_newThumnail')");

                 

                if($images != null){
                    foreach ($images as $image) {   
                        $imageName = $masp_query . '_' . (++$index_image) . '_' . time() .  '.' . $image->extension();
                        
                        $image->move($path, $imageName);
             
                        $pathImageURL = 'images/products/' . $masp_query . '/' . $imageName;
                        $imgURL = asset($pathImageURL);
            
                        DB::insert(
                            "INSERT into hinhanhs value('$imageName')"
                        );
                        DB::insert(
                            "INSERT into hinhanhsanphams value('$imageName', $masp_query, '$imgURL')"
                        );
                    }

                }
            }
            else if($indexThumnail >= $quantityImgurl){
                if($images != null){
                    foreach ($images as $image) {  
                        if($index_image != $indexThumnail){
                            $imageName = $masp_query . '_' . (++$index_image) . '_' . time() .  '.' . $image->extension();
                        }
                        else{
                            $imageName = $masp_query . '_' . (++$index_image) . '_' . time() . '_thumnail' .  '.' . $image->extension();
                        }
                        $image->move($path, $imageName);
             
                        $pathImageURL = 'images/products/' . $masp_query . '/' . $imageName;
                        $imgURL = asset($pathImageURL);
            
                        DB::insert(
                            "INSERT into hinhanhs value('$imageName')"
                        );
                        DB::insert(
                            "INSERT into hinhanhsanphams value('$imageName', $masp_query, '$imgURL')"
                        );
                    }

                }
            }
        }
 

        $infoBeforeUpdate = DB::select(
            "SELECT * FROM hinhanhsanphams 
            where MASP = $masp_query"
        );
        DB::delete("DELETE FROM hinhanhsanphams WHERE MASP = '$masp_query'");
        for ($i = 0; $i < count($infoBeforeUpdate); $i++) {
            $MHA_delete = $infoBeforeUpdate[$i]->MAHINHANH;
            DB::delete("DELETE FROM hinhanhs WHERE MAHINHANH LIKE '%$MHA_delete%'");
        }
        for ($i = 0; $i < count($infoBeforeUpdate); $i++) {
            $infoBeforeUpdate[$i]->MAHINHANH = preg_replace('/(?<=_)\d+(?=_)/', $i + 1, $infoBeforeUpdate[$i]->MAHINHANH, 1);
        }

        for ($i = 0; $i < count($infoBeforeUpdate); $i++) {
            $imageNameReloadNumber = $infoBeforeUpdate[$i]->MAHINHANH;
            $imgURLReloadNumber = $infoBeforeUpdate[$i]->imgURL;
            DB::insert(
                "INSERT into hinhanhs value('$imageNameReloadNumber')"
            );
            DB::insert(
                "INSERT into hinhanhsanphams value('$imageNameReloadNumber', $masp_query, '$imgURLReloadNumber')"
            );
        }

        return response()->json([ 
        ]); 
    }
}
