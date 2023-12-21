<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayOnlineController extends Controller
{
    public function payOnline(Request $request){

        $matk = $request->input('matk');
        $ngayorder = $request->input('ngayorder');
        $tongtien_sp = $request->input('tongtien_SP');
        $mavoucher = $request->input('mavoucher');
        $vouchergiam = $request->input('vouchergiam'); 
        $tongtiendonhang = $request->input('tongtiendonhang');
        $hinhthuc_thanhtoan = $request->input('hinhthucthanhtoan');
        $trangthai_thanhtoan = $request->input('trangthaithanhtoan');
        $trangthai_donhang = $request->input('trangthaidonhang');
        $mattgh = $request->input('mattgh');
        $ghichu = $request->input('ghichu');

        $mattgh = $request->input('mattgh');
        $name_ship = $request->input('name_ship');
        $numberPhone_ship = $request->input('numberPhone_ship');
        $address_ship = $request->input('address_ship');
        $option_thanhpho = $request->input('option_thanhpho');
        $option_quan = $request->input('option_quan');
        $option_phuong = $request->input('option_phuong');

        if($mattgh == ''){
            DB::insert(
                "INSERT INTO thongtingiaohangs 
                VALUES( '$matk', '$name_ship', '$numberPhone_ship', '$address_ship', 
                '$option_thanhpho', '$option_quan', '$option_phuong',)"
            );
            $mattgh = DB::select(
                "SELECT MATTGH FROM thongtingiaohangs WHERE 
                MATK = '$matk' AND TEN = '$name_ship' AND SDT = '$numberPhone_ship'
                AND DIACHI = '$address_ship' AND TINH_TP = '$option_thanhpho' AND QUAN_HUYEN = '$option_quan'
                AND PHUONG_XA = 'option_phuong'"
            );
        }

        DB::insert(
            "INSERT INTO donhangs(MATK, NGAYORDER, NGAYGIAOHANG, TONGTIEN_SP, VOUCHERGIAM, 
            TONGTIENDONHANG, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, TRANGTHAI_DONHANG, MATTGH, GHICHU) 
            VALUES('$matk', '$ngayorder', '$ngayorder', $tongtien_sp
            , $vouchergiam, $tongtiendonhang, '$hinhthuc_thanhtoan', '$trangthai_thanhtoan'
            , '$trangthai_donhang', '$mattgh', '$ghichu')"
        );

        $madh = DB::select("SELECT MADH FROM donhangs ORDER BY NGAYORDER DESC LIMIT 1");

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:3000/payment";
        $vnp_TmnCode = "NCH1W7SL";//Mã website tại VNPAY 
        $vnp_HashSecret = "TMINKHSYJSMHVKASBDQYUDZXMHHAEOGL"; //Chuỗi bí mật

        $vnp_TxnRef = $madh[0]->MADH; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'Thanh toán hoá đơn';
        $vnp_OrderType = 'Hoá đơn thời trang';
        $vnp_Amount = $tongtiendonhang * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = '' ;
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        //Billing
        $vnp_Bill_Mobile = $numberPhone_ship;
        // $vnp_Bill_Email = $_POST['txt_billing_email'];
        $fullName = trim($name_ship);
        if (isset($fullName) && trim($fullName) != '') {
            $name = explode(' ', $fullName); 
        } 
        // $vnp_Bill_Country=$_POST['txt_bill_country'];
        $vnp_Bill_State=$trangthai_donhang; 
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",  
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef, 
            // "vnp_Bill_Mobile"=>$vnp_Bill_Mobile, 
            // "vnp_Bill_Address"=>$vnp_Bill_Address,
            // "vnp_Bill_City"=>$vnp_Bill_City, 
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = ($vnp_BankCode);
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = ($vnp_Bill_State);
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode(($value));
            } else {
                $hashdata .= urlencode(($key)) . "=" . urlencode(($value));
                $i = 1;
            }
            $query .= urlencode(($key)) . "=" . urlencode(($value)) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                echo json_encode($returnData);
            }
    }
}
