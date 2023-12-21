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

class InfoAccountController extends Controller
{
    public function getInfoAccount(Request $request){
        $matk = $request->input('matk');
        $infoAccount = DB::select("SELECT TEN, gioitinh, EMAIL, SDT, DIACHI FROM taikhoans WHERE MATK = ?", [$matk]);

        return response()->json([
            'infoAccount' => $infoAccount[0],
        ]);
    }
    public function saveInfoAccount(Request $request){
        $name = $request->name;
        $gioitinh = $request->gender;
        $numberPhone = $request->numberPhone;
        $address = $request->address;
        $matk = $request->matk;
        DB::update(
            "UPDATE taikhoans 
            SET TEN = '$name', gioitinh = '$gioitinh', SDT = '$numberPhone', DIACHI = '$address'
            WHERE MATK  = $matk"
        );
        return response()->json([
            'statuscode' => 200,
        ]);
    }
    public function changePassword(Request $request){
        $taikhoan = taikhoan::where('MATK', $request->matk)->first();   

        $Validator = Validator::make($request->all(), [ 
            'newPassword' => 'required|max:191|min:6', 
        ]);

        if($Validator->fails()){
            return response()->json([
                'validation_errors' => $Validator->messages(), 422
            ]);
        }
        else if(!Hash::check($request->oldPassword,$taikhoan->PASSWORD)){
            $data = new stdClass();
            $data->newPassword = "Mật cũ khẩu sai";
            return response()->json([
                'status'=>401,
                'validation_errors' =>$data,
            ]);
        }
        $password = Hash::make($request->newPassword);
        DB::update("UPDATE taikhoans set PASSWORD = '$password' where MATK = $request->matk");
        return response()->json([
            'status' =>200,  
        ]);
    }
}
