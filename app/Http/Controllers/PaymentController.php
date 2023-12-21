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
class PaymentController extends Controller
{
    public function infoForPayment(Request $request){
        $matk = $request->query('matk');
        $clickPaymentFromCart = $request->query('clickPaymentFromCart');
        if($clickPaymentFromCart == 1){
            $data_sanpham = DB::select(
                "SELECT sanphams.MASP, TENSP, TONGGIA, SOLUONG, TENMAU, 
                chitiet_giohangs.MASIZE, GIABAN, SELECTED, mausacs.MAMAU, imgURL
                FROM chitiet_giohangs, sanphams, mausacs, hinhanhsanphams
                WHERE MATK = $matk AND chitiet_giohangs.MASP = sanphams.MASP 
                -- AND SELECTED = 1
                AND MAHINHANH LIKE '%thumnail%'
                AND mausacs.MAMAU = chitiet_giohangs.MAMAU
                AND hinhanhsanphams.MASP = sanphams.MASP"
            );
        }
        else{
            $data_sanpham = DB::select(
                "SELECT sanphams.MASP, TENSP, TONGGIA, SOLUONG, TENMAU, 
                chitiet_giohangs.MASIZE, GIABAN, SELECTED, mausacs.MAMAU, imgURL
                FROM chitiet_giohangs, sanphams, mausacs, hinhanhsanphams
                WHERE MATK = $matk AND chitiet_giohangs.MASP = sanphams.MASP 
                AND SELECTED = 1
                AND MAHINHANH LIKE '%thumnail%'
                AND mausacs.MAMAU = chitiet_giohangs.MAMAU
                AND hinhanhsanphams.MASP = sanphams.MASP"
            );
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $data_voucher = DB::select(
            "SELECT MAVOUCHER, GIATRIGIAM, GIATRI_GIAM_MAX, PHANLOAI_VOUCHER, GIATRI_DH_MIN, SOLUONG_CONLAI, SOLUONG
            FROM vouchers 
            WHERE THOIGIANBD <= '$currentDate'
            AND THOIGIANKT >= '$currentDate'"
        );

        $data_adress = DB::select("SELECT * FROM thongtingiaohangs where MATK = $matk");
         

        return response()->json([ 
            'data_sanpham' => $data_sanpham,
            'data_voucher' => $data_voucher,
            'data_adress' => $data_adress,  
        ]);
    }   
    public function saveInfoForPayment(Request $request){
        $matk = $request->input('matk');
        $mattgh = $request->input('mattgh');
        $name_ship = $request->input('name_ship');
        $numberPhone_ship = $request->input('numberPhone_ship');
        $address_ship = $request->input('address_ship');
        $option_thanhpho = $request->input('option_thanhpho');
        $option_quan = $request->input('option_quan');
        $option_phuong = $request->input('option_phuong');

        $ngayorder = $request->input('ngayorder');
        $tongtien_sp = $request->input('tongtien_SP');
        $mavoucher = $request->input('mavoucher');
        $vouchergiam = $request->input('vouchergiam'); 
        $tongtiendonhang = $request->input('tongtiendonhang');
        $phivanchuyen = $request->input('phivanchuyen');
        $hinhthuc_thanhtoan = $request->input('hinhthucthanhtoan');
        $trangthai_thanhtoan = $request->input('trangthaithanhtoan');
        $trangthai_donhang = $request->input('trangthaidonhang');
        $mattgh = $request->input('mattgh');
        $ghichu = $request->input('ghichu');

        $infoProductJSON = $request->input('infoProductJSON');
        $infoProduct = json_decode($infoProductJSON, true);
        $mattgh_conver = $mattgh;
        $error_soluong_SP = [];
        // $error_soluong_SP_i = 0;
        foreach($infoProduct as $item){
            $kiemtra_sl = DB::select(
                "SELECT TENSP, TENMAU, SOLUONG, MASIZE
                from sanpham_mausac_sizes, sanphams, mausacs 
                where sanphams.MASP = ? 
                AND MASIZE = ? 
                AND sanpham_mausac_sizes.MAMAU = ?
                AND sanpham_mausac_sizes.SOLUONG < ?
                AND sanpham_mausac_sizes.MASP = sanphams.MASP
                AND mausacs.MAMAU = sanpham_mausac_sizes.MAMAU", 
                [$item['MASP'], $item['MASIZE'], $item['MAMAU'], $item['SOLUONG']]
            );
            if($kiemtra_sl != null){
                $TENSPP = $kiemtra_sl[0]->TENSP;
                $TENMAUU = $kiemtra_sl[0]->TENMAU;
                $SOLUONGG = $kiemtra_sl[0]->SOLUONG;
                $MASIZEE = $kiemtra_sl[0]->MASIZE;
                // $error_soluong_SP_i++
                $error_soluong_SP[] = "Số lượng của sản phẩm $TENSPP, màu $TENMAUU, size $MASIZEE chỉ còn $SOLUONGG sản phẩm";  
            }
        }
        if(count($error_soluong_SP) == 0){
            if($mattgh == ''){
                DB::insert(
                    "INSERT INTO thongtingiaohangs(MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG)
                    VALUES( '$matk', '$name_ship', '$numberPhone_ship', '$address_ship', 
                    '$option_thanhpho', '$option_quan', '$option_phuong', 1)"
                );
                $mattgh = DB::select(
                    "SELECT MATTGH FROM thongtingiaohangs 
                    WHERE MATK = $matk AND TEN = '$name_ship' AND SDT = '$numberPhone_ship'
                    AND DIACHI = '$address_ship' AND TINH_TP = '$option_thanhpho' AND QUAN_HUYEN = '$option_quan'
                    AND PHUONG_XA = '$option_phuong'"
                );
                if($mattgh != null)
                    $mattgh_conver = $mattgh[0]->MATTGH;
            }
            else{
                $mattgh = DB::select(
                    "SELECT MATTGH FROM thongtingiaohangs 
                    WHERE MATK = $matk AND TEN = '$name_ship' AND SDT = '$numberPhone_ship'
                    AND DIACHI = '$address_ship' AND TINH_TP = '$option_thanhpho' AND QUAN_HUYEN = '$option_quan'
                    AND PHUONG_XA = '$option_phuong'"
                );
                if($mattgh == null){
                    DB::insert(
                        "INSERT INTO thongtingiaohangs(MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG)
                        VALUES( '$matk', '$name_ship', '$numberPhone_ship', '$address_ship', 
                        '$option_thanhpho', '$option_quan', '$option_phuong', 1)"
                    );
                    $mattgh = DB::select(
                        "SELECT MATTGH FROM thongtingiaohangs 
                        WHERE MATK = $matk AND TEN = '$name_ship' AND SDT = '$numberPhone_ship'
                        AND DIACHI = '$address_ship' AND TINH_TP = '$option_thanhpho' AND QUAN_HUYEN = '$option_quan'
                        AND PHUONG_XA = '$option_phuong'"
                    );
                    if($mattgh != null)
                        $mattgh_conver = $mattgh[0]->MATTGH;
                } 
            }
            DB::insert(
                "INSERT INTO donhangs(MATK, NGAYORDER, TONGTIEN_SP, VOUCHERGIAM, 
                TONGTIENDONHANG, PHIVANCHUYEN, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, TRANGTHAI_DONHANG, MATTGH, GHICHU) 
                VALUES('$matk', '$ngayorder', $tongtien_sp
                , $vouchergiam, $tongtiendonhang, '$phivanchuyen', '$hinhthuc_thanhtoan', '$trangthai_thanhtoan'
                , '$trangthai_donhang',  $mattgh_conver, '$ghichu')"
            );
    
            
            $madh = DB::select("SELECT MADH FROM donhangs ORDER BY MADH DESC LIMIT 1"); 
    
            if($mavoucher == ''){
                // DB::insert("INSERT INTO donhang_vouchers values( ? , ? )", ['Kurtis', $madh[0]->MADH]);
            }
            else{
                DB::insert("INSERT INTO donhang_vouchers values( ? , ? )", [$mavoucher, $madh[0]->MADH]);
            }
            $list_maxdsp = [];
            foreach($infoProduct as $item){
                $maxdsp = DB::select(
                    "SELECT * from sanpham_mausac_sizes 
                    where MASP = ? AND MASIZE = ? AND MAMAU = ?", 
                    [$item['MASP'], $item['MASIZE'], $item['MAMAU']]
                );
                $list_maxdsp[] = $maxdsp[0];
                DB::insert("INSERT INTO chitiet_donhangs(MADH, MAXDSP, TONGTIEN, SOLUONG, DADANHGIA)
                VALUES(?, ?, ?, ?, ?)", 
                [$madh[0]->MADH,  $maxdsp[0]->MAXDSP, $item['TONGGIA'], $item['SOLUONG'], 0]);
            }
    
            if($hinhthuc_thanhtoan == "Chuyển khoản"){
                $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_Returnurl = "http://localhost:3000/paymentResult";
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
                $returnData = [
                    'code' => '00'
                    , 'message' => 'success'
                    , 'data' => $vnp_Url
                ];
                if (isset($_POST['redirect'])) {
                    header('Location: ' . $vnp_Url);
                    die();
                } else {
                    return response()->json([
                        'data' => $returnData
                    ]);
                }
    
                
            }
            else{
                $ji = 0;
                foreach($infoProduct as $item){ 
                    DB::delete(
                        "DELETE FROM chitiet_giohangs 
                        where MATK = $matk AND  MASP = ? AND MASIZE = ? AND MAMAU = ? ", 
                        [$item['MASP'], $item['MASIZE'],$item['MAMAU']]
                    );
                    DB::update(
                        "UPDATE sanpham_mausac_sizes 
                        SET SOLUONG = ? - ?
                        where MAXDSP = ?", 
                        [$list_maxdsp[$ji]->SOLUONG, $item['SOLUONG'], $list_maxdsp[$ji]->MAXDSP]
                    );
    
                    $ji++;
                }
                if($mavoucher != ''){
                    $info_voucher = DB::select("SELECT SOLUONG_CONLAI FROM vouchers WHERE MAVOUCHER = '$mavoucher'");
                    DB::update(
                        "UPDATE vouchers 
                        SET SOLUONG_CONLAI = ? - 1
                        where MAVOUCHER = ?", 
                        [$info_voucher[0]->SOLUONG_CONLAI, $mavoucher]
                    );
                }
    
            } 
        }
        

        return response()->json([
            'message' => 200,
            'error_soluong_SP' => $error_soluong_SP
        ]);
    }
    // public function payOnline(Request $request){

    //     $matk = $request->input('matk');
    //     $ngayorder = $request->input('ngayorder');
    //     $tongtien_sp = $request->input('tongtien_SP');
    //     $mavoucher = $request->input('mavoucher');
    //     $vouchergiam = $request->input('vouchergiam'); 
    //     $tongtiendonhang = $request->input('tongtiendonhang');
    //     $hinhthuc_thanhtoan = $request->input('hinhthucthanhtoan');
    //     $trangthai_thanhtoan = $request->input('trangthaithanhtoan');
    //     $trangthai_donhang = $request->input('trangthaidonhang');
    //     $mattgh = $request->input('mattgh');
    //     $ghichu = $request->input('ghichu');

    //     $mattgh = $request->input('mattgh');
    //     $name_ship = $request->input('name_ship');
    //     $numberPhone_ship = $request->input('numberPhone_ship');
    //     $address_ship = $request->input('address_ship');
    //     $option_thanhpho = $request->input('option_thanhpho');
    //     $option_quan = $request->input('option_quan');
    //     $option_phuong = $request->input('option_phuong');

    //     if($mattgh == ''){
    //         DB::insert(
    //             "INSERT INTO thongtingiaohangs(MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA)
    //             VALUES( '$matk', '$name_ship', '$numberPhone_ship', '$address_ship', 
    //             '$option_thanhpho', '$option_quan', '$option_phuong')"
    //         );
    //         $mattgh = DB::select(
    //             "SELECT MATTGH FROM thongtingiaohangs WHERE 
    //             MATK = '$matk' AND TEN = '$name_ship' AND SDT = '$numberPhone_ship'
    //             AND DIACHI = '$address_ship' AND TINH_TP = '$option_thanhpho' AND QUAN_HUYEN = '$option_quan'
    //             AND PHUONG_XA = 'option_phuong'"
    //         );
    //     }

    //     DB::insert(
    //         "INSERT INTO donhangs(MATK, NGAYORDER, NGAYGIAOHANG, TONGTIEN_SP, VOUCHERGIAM, 
    //         TONGTIENDONHANG, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, TRANGTHAI_DONHANG, MATTGH, GHICHU) 
    //         VALUES('$matk', '$ngayorder', '$ngayorder', $tongtien_sp
    //         , $vouchergiam, $tongtiendonhang, '$hinhthuc_thanhtoan', '$trangthai_thanhtoan'
    //         , '$trangthai_donhang', '$mattgh', '$ghichu')"
    //     );

    //     $madh = DB::select("SELECT MADH FROM donhangs ORDER BY NGAYORDER DESC LIMIT 1");

    //     $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    //     $vnp_Returnurl = "http://localhost:3000/payment";
    //     $vnp_TmnCode = "NCH1W7SL";//Mã website tại VNPAY 
    //     $vnp_HashSecret = "TMINKHSYJSMHVKASBDQYUDZXMHHAEOGL"; //Chuỗi bí mật

    //     $vnp_TxnRef = $madh[0]->MADH; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
    //     $vnp_OrderInfo = 'Thanh toán hoá đơn';
    //     $vnp_OrderType = 'Hoá đơn thời trang';
    //     $vnp_Amount = $tongtiendonhang * 100;
    //     $vnp_Locale = 'vn';
    //     $vnp_BankCode = '' ;
    //     $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    //     //Add Params of 2.0.1 Version
    //     // $vnp_ExpireDate = $_POST['txtexpire'];
    //     //Billing
    //     $vnp_Bill_Mobile = $numberPhone_ship;
    //     // $vnp_Bill_Email = $_POST['txt_billing_email'];
    //     $fullName = trim($name_ship);
    //     if (isset($fullName) && trim($fullName) != '') {
    //         $name = explode(' ', $fullName); 
    //     } 
    //     // $vnp_Bill_Country=$_POST['txt_bill_country'];
    //     $vnp_Bill_State=$trangthai_donhang; 
    //     $inputData = array(
    //         "vnp_Version" => "2.1.0",
    //         "vnp_TmnCode" => $vnp_TmnCode,
    //         "vnp_Amount" => $vnp_Amount,
    //         "vnp_Command" => "pay",  
    //         "vnp_CreateDate" => date('YmdHis'),
    //         "vnp_CurrCode" => "VND",
    //         "vnp_IpAddr" => $vnp_IpAddr,
    //         "vnp_Locale" => $vnp_Locale,
    //         "vnp_OrderInfo" => $vnp_OrderInfo,
    //         "vnp_OrderType" => $vnp_OrderType,
    //         "vnp_ReturnUrl" => $vnp_Returnurl,
    //         "vnp_TxnRef" => $vnp_TxnRef, 
    //         // "vnp_Bill_Mobile"=>$vnp_Bill_Mobile, 
    //         // "vnp_Bill_Address"=>$vnp_Bill_Address,
    //         // "vnp_Bill_City"=>$vnp_Bill_City, 
    //     );

    //     if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    //         $inputData['vnp_BankCode'] = ($vnp_BankCode);
    //     }
    //     if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
    //         $inputData['vnp_Bill_State'] = ($vnp_Bill_State);
    //     }

    //     //var_dump($inputData);
    //     ksort($inputData);
    //     $query = "";
    //     $i = 0;
    //     $hashdata = "";
    //     foreach ($inputData as $key => $value) {
    //         if ($i == 1) {
    //             $hashdata .= '&' . urlencode($key) . "=" . urlencode(($value));
    //         } else {
    //             $hashdata .= urlencode(($key)) . "=" . urlencode(($value));
    //             $i = 1;
    //         }
    //         $query .= urlencode(($key)) . "=" . urlencode(($value)) . '&';
    //     }

    //     $vnp_Url = $vnp_Url . "?" . $query;
    //     if (isset($vnp_HashSecret)) {
    //         $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
    //         $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    //     }
    //     $returnData = array('code' => '00'
    //         , 'message' => 'success'
    //         , 'data' => $vnp_Url);
    //         if (isset($_POST['redirect'])) {
    //             header('Location: ' . $vnp_Url);
    //             die();
    //         } else {
    //             echo json_encode($returnData);
    //         }
    // }
    public function processPaymentResult(Request $request){
        

        /* Payment Notify
        * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
        * Các bước thực hiện:
        * Kiểm tra checksum 
        * Tìm giao dịch trong database
        * Kiểm tra số tiền giữa hai hệ thống
        * Kiểm tra tình trạng của giao dịch trước khi cập nhật
        * Cập nhật kết quả vào Database
        * Trả kết quả ghi nhận lại cho VNPAY
        */
    
        $inputData = array();
        $returnData = array();
        // $infoPaymentResult = $request->all();
        // foreach ($infoPaymentResult as $key => $value) {
        //     if (substr($key, 0, 4) == "vnp_") {
        //         $inputData[$key] = $value;
        //     }       
        // }

        // $vnp_SecureHash = $inputData['vnp_SecureHash'];
        // unset($inputData['vnp_SecureHash']);
        // ksort($inputData);
        // $i = 0;
        // $hashData = "";
        // foreach ($inputData as $key => $value) {
        //     if ($i == 1) {
        //         $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
        //     } else {
        //         $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        //         $i = 1;
        //     }
        // }

        // $secureHash = hash_hmac('sha512', $hashData, $vnp_SecureHash);
        // $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        // $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        // $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi

        // $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        // $orderId = $inputData['vnp_TxnRef']; 
        //     //Check Orderid    
        //     //Kiểm tra checksum của dữ liệu
        //     if ($secureHash == $vnp_SecureHash) {
        //         //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
        //         //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
        //         //Giả sử: $order = mysqli_fetch_assoc($result);   

        //         $order = NULL;
        //         if ($order != NULL) {
        //             if($order["Amount"] == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
        //             {
        //                 if ($order["Status"] != NULL && $order["Status"] == 0) {
        //                     if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
        //                         $Status = 1; // Trạng thái thanh toán thành công
        //                     } else {
        //                         $Status = 2; // Trạng thái thanh toán thất bại / lỗi
        //                     }
        //                     //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
        //                     //
        //                     //
        //                     //
        //                     //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công                
        //                     $returnData['RspCode'] = '00';
        //                     $returnData['Message'] = 'Confirm Success';
        //                 } else {
        //                     $returnData['RspCode'] = '02';
        //                     $returnData['Message'] = 'Order already confirmed';
        //                 }
        //             }
        //             else {
        //                 $returnData['RspCode'] = '04';
        //                 $returnData['Message'] = 'invalid amount';
        //             }
        //         } else {
        //             $returnData['RspCode'] = '01';
        //             $returnData['Message'] = 'Order not found';
        //         }
        //     } else {
        //         $returnData['RspCode'] = '97';
        //         $returnData['Message'] = 'Invalid signature';
        //     }
        
        //Trả lại VNPAY theo định dạng JSON
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef');
            if($vnp_ResponseCode == '00'){
                $data_CTDH = DB::select(
                    "SELECT sanpham_mausac_sizes.MAXDSP, 
                    sanpham_mausac_sizes.SOLUONG as SLSP, chitiet_donhangs.SOLUONG as SLSP_Cart, 
                    MATK, MASP, MASIZE, MAMAU
                    FROM chitiet_donhangs, donhangs, sanpham_mausac_sizes
                    WHERE donhangs.MADH = $vnp_TxnRef
                    AND chitiet_donhangs.MADH = donhangs.MADH
                    AND sanpham_mausac_sizes.MAXDSP = chitiet_donhangs.MAXDSP"
                ); 
                foreach($data_CTDH as $item){ 
                    DB::delete(
                        "DELETE FROM chitiet_giohangs 
                        where MATK = ? AND  MASP = ? AND MASIZE = ? AND MAMAU = ? ", 
                        // [$item['MATK'], $item['MASP'], $item['MASIZE'],$item['MAMAU']]
                        [$item->MATK, $item->MASP, $item->MASIZE, $item->MAMAU]
                    );
                    DB::update(
                        "UPDATE sanpham_mausac_sizes 
                        SET SOLUONG = ?
                        where MAXDSP = ?", 
                        [$item->SLSP - $item->SLSP_Cart, $item->MAXDSP]
                    ); 
                }
                DB::update("UPDATE donhangs set TRANGTHAI_THANHTOAN = 'Đã thanh toán', TRANGTHAI_DONHANG = 'Chuẩn bị hàng' Where MADH = ?", [$vnp_TxnRef]);
                // DB::delete("DELETE from chitiet_giohangs where selected = 1");
                $obj_mavoucher = DB::select("SELECT MAVOUCHER, MADH FROM donhang_vouchers WHERE MADH = ?", [$vnp_TxnRef]);
                if($obj_mavoucher != null){
                    $mavoucher = $obj_mavoucher[0]->MAVOUCHER;
                    $info_voucher = DB::select("SELECT SOLUONG_CONLAI FROM vouchers WHERE MAVOUCHER = '$mavoucher'");
                    DB::update(
                        "UPDATE vouchers 
                        SET SOLUONG_CONLAI = ? - 1
                        where MAVOUCHER = ?", 
                        [$info_voucher[0]->SOLUONG_CONLAI, $mavoucher]
                    );
                }
                
            }
            return response()->json([
                'data' => $returnData,
                // 'secureHash' => $secureHash,
                // 'vnp_SecureHash' => $vnp_SecureHash,
            ]);
    }
}
