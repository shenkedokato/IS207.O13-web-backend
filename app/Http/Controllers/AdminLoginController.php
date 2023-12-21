<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\taikhoan; 
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Verified; 
use Illuminate\Support\Str;
use stdClass; 

class AdminLoginController extends Controller
{
    public function adminRegister(Request $request){
        $Validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email:191|unique:taikhoans',
            'password' => 'required|max:191|min:6',
            'confirmPassword' => 'required|same:password',
            'gender' => 'required',
        ]);

        if($Validator->fails()){
            return response()->json([
                'validation_errors' => $Validator->messages(), 422
            ]);
        }
        else{
            $taikhoan = taikhoan::create([
                'ten' => $request->name,
                'email' => $request->email,
                'GIOITINH' => $request->gender,
                'password' =>Hash::make($request->password),
                'ROLE' => $request->role,
                'AdminVerify' => $request->AdminVerify,
            ]);
  
            $taikhoan->sendEmailVerificationNotification();
            $token = $taikhoan->createToken($taikhoan->email.'_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'email' => $taikhoan->email,
                'token' => $token,
                'role' => $taikhoan->ROLE,
                'massage' => 'Registered Successfully',
            ]);
        }
    }

    public function Adminlogin(Request $request){ 
        $taikhoan = taikhoan::where('email', $request->email)->first();   

        $validator = Validator::make($request->all(), [
            'email' => 'required|email:191',
            'password' => 'required',
        ]);
        
        if($validator->fails())
        {
            return response()->json([
            'validation_errors' =>$validator->messages(),
            ]);
        }
        else
        {
            if(!$taikhoan) { 
                $data = new stdClass();
                $data->email = "Email không tồn tại"; 
                return response()->json([
                    'status'=>401,
                    'validation_errors' => $data,
                ]);  
            }
            else if(!Hash::check($request->password,$taikhoan->PASSWORD)){
                $data = new stdClass();
                $data->password = "Mật khẩu sai";
                return response()->json([
                    'status'=>401,
                    'validation_errors' =>$data,
                ]);
            }
            else { 
                if (is_null($taikhoan->email_verified_at)) { 
                    $data = new stdClass();
                    $data->password = "Tài khoản chưa được xác nhận";
                    return response()->json([
                        'status'=>401,
                        'validation_errors' =>$data,
                    ]);
                } 
                if ($taikhoan->AdminVerify == 0 && $taikhoan->ROLE == "Nhân viên") { 
                    $data = new stdClass();
                    $data->password = "Tài khoản chưa được Admin xác nhận";
                    return response()->json([
                        'status'=>401,
                        'validation_errors' =>$data,
                    ]);
                } 
                if ($taikhoan->AdminVerify == 0 && $taikhoan->ROLE == "Khách hàng") { 
                    $data = new stdClass();
                    $data->password = "Đây là tài khoản khách hàng";
                    return response()->json([
                        'status'=>401,
                        'validation_errors' =>$data,
                    ]);
                } 
                $token = $taikhoan->createToken($taikhoan->email.'_Token')->plainTextToken;
                    
                    return response()->json([
                        'status' =>200,
                        'email' =>$taikhoan->EMAIL,
                        'matk' => $taikhoan->MATK,
                        'role' => $taikhoan->ROLE,
                        'token' => $token,
                        'message' =>'Logged In Successfully',
                        // 'role'=>$role,
                    ]);
                // }
            } 
        }
    }
}
