<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\taikhoan;
use App\Models\User;
use App\Models\sanpham;
use App\Models\chitiet_giohang;
use App\Models\hinhanhsanpham;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use stdClass; 

class TestController extends Controller
{
    public function getInfoAtStartLoadingHome(){
        $dataNewProduct = DB::select(
            "SELECT * FROM sanphams, hinhanhsanphams 
            WHERE hinhanhsanphams.masp = sanphams.masp 
            AND imgURL LIKE '%thumnail%'  
            ORDER BY created_at ASC LIMIT 20"
        );
        $dataHotProduct = DB::select(
            "SELECT sanphams.MASP, TENSP, GIAGOC, GIABAN, MAPL_SP 
            FROM sanphams, chitiet_donhangs,  sanpham_mausac_sizes
            WHERE sanpham_mausac_sizes.MAXDSP = chitiet_donhangs.MAXDSP 
            AND sanphams.MASP = sanpham_mausac_sizes.MASP
            GROUP BY sanphams.MASP, TENSP, GIAGOC, GIABAN, MAPL_SP
            ORDER BY SUM(chitiet_donhangs.SOLUONG)"
        ); 
        return response()->json([
            'dataNewProduct' => $dataNewProduct,
            'dataHotProduct' => $dataHotProduct, 
        ]);
    }

    public function register(Request $request){
        $Validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email:191|unique:taikhoans',
            'password' => 'required|max:191|min:6',
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
                'password' =>Hash::make($request->password),
            ]);
            $mail = $request->mail;
            Mail::send([], [], function($email){
                $email->to('dosidat15031712@gmail.com')
                    ->subject('Subject of the email')
                    ->setBody('Email content');
            });

            $token = $taikhoan->createToken($taikhoan->email.'_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'email' => $taikhoan->email,
                'token' => $token,
                'massage' => 'Registered Successfully',
            ]);
        }
    }

    public function login(Request $request){
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
            $taikhoan = taikhoan::where('email', $request->email)->first();   
            if(!$taikhoan) { 
                $data = new stdClass();
                $data->email = "email sai"; 
                return response()->json([
                    'status'=>401,
                    'validation_errors' => $data,
                ]);  
            }
            else if(!Hash::check($request->password,$taikhoan->PASSWORD)){
                $data = new stdClass();
                $data->password = "mật khẩu sai";
                return response()->json([
                    'status'=>401,
                    'validation_errors' =>$data,
                ]);
            }
            else {
                // if($taikhoan->role_as == 1) //1 = admin
                // {
                //     $role = 'admin';
                //     $token = $taikhoan->createToken($taikhoan->email.'_AdminToken',['server:admin'])->plainTextToken;
                // }
                // else
                // {
                //     $role = '';

                // đoạn này có hay không cũng được, đều đúng
                // $credentials = $request->only('email', 'password');
                // if (Auth::attempt($credentials)) {
                //     $user = Auth::user();
                //     $token_test = $user->createToken($user->email.'_Token')->plainTextToken;
                //     return response()->json([
                //         'status' =>200,
                //         'email' =>$taikhoan->EMAIL,
                //         'token' => $token_test,
                //         'message' =>'Logged In Successfully',
                //         // 'role'=>$role,
                //     ]);
                // }
                 
                $token = $taikhoan->createToken($taikhoan->email.'_Token')->plainTextToken;
                    
                    return response()->json([
                        'status' =>200,
                        'email' =>$taikhoan->EMAIL,
                        'matk' => $taikhoan->MATK,
                        'token' => $token,
                        'message' =>'Logged In Successfully',
                        // 'role'=>$role,
                    ]);
                // }
            } 
        } 
    }
    public function logout() { 
        Auth::user()->tokens()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Logged out Successfully',
        ]);
    }

    public function search(Request $request){
        $searchQuery = $request->query('query');
        $data = sanpham::where('TENSP', 'LIKE', "%$searchQuery%")->get();

        return response()->json([
            'data' => $data,
        ]);
    }
    public function infoProduct(Request $request){
        $id = $request->query('id');
        $data_sanpham = sanpham::where('MASP', 'LIKE', "%$id%")->get();
        $data_mausac = DB::select("SELECT * FROM sanpham_mausac_sizes WHERE MASP = '$id' ");
        $data_MAMAU = DB::select("SELECT DISTINCT(mausacs.MAMAU), HEX, TENMAU FROM sanpham_mausac_sizes, mausacs WHERE sanpham_mausac_sizes.MAMAU = mausacs.MAMAU AND MASP = '$id' ");
        $data_SIZE = DB::select("SELECT DISTINCT(MASIZE) FROM sanpham_mausac_sizes WHERE MASP = '$id'");

        return response()->json([
            'data_sanpham' => $data_sanpham,
            'data_mausac' => $data_mausac,
            'data_mamau' => $data_MAMAU,
            'data_size' => $data_SIZE,
        ]);
    }
    public function addToCart(Request $request){ 
        chitiet_giohang::create([
            'MATK' => $request->matk,
            'MASP' => $request->masp,
            'MASIZE' => $request->masize,
            'MAMAU' => $request->mamau,
            'SOLUONG' => $request->soluongsp,
            'TONGGIA' => $request->tonggia,

        ]);
        return response()->json([
            'status_code' => 200,
        ]);
    } 
    public function infoCart(Request $request){
        $matk = $request->input('matk');
        $data_sanpham = DB::select("SELECT MATK, sanphams.MASP, TENSP, TONGGIA, SOLUONG, TENMAU, chitiet_giohangs.MASIZE, GIABAN, SELECTED, mausacs.MAMAU  FROM chitiet_giohangs, sanphams, mausacs WHERE MATK = '$matk' AND chitiet_giohangs.MASP = sanphams.MASP AND mausacs.MAMAU = chitiet_giohangs.MAMAU");
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

        DB::update("UPDATE chitiet_giohangs SET SOLUONG = '$soluong', TONGGIA = '$tonggia' WHERE MASP = '$masp' AND MATK = '$matk' AND MAMAU = '$mamau' AND MASIZE = '$masize' ");
        return response()->json([
            'message' => 200,
            'matk' => $matk,
        ]);
    }

    // public function infoForPayment(Request $request){
    //     $matk = $request->query('matk');
    //     $data_sanpham = DB::select("SELECT sanphams.MASP, TENSP, TONGGIA, SOLUONG, TENMAU, chitiet_giohangs.MASIZE, GIABAN, SELECTED, mausacs.MAMAU  FROM chitiet_giohangs, sanphams, mausacs WHERE MATK = 1 AND chitiet_giohangs.MASP = sanphams.MASP AND mausacs.MAMAU = chitiet_giohangs.MAMAU");

    //     $currentDate = Carbon::now()->format('Y-m-d');
    //     $data_voucher = DB::select("SELECT MAVOUCHER, GIATRIGIAM FROM vouchers WHERE SOLUONG > 0 AND THOIGIANKT > '$currentDate'");

    //     $data_adress = DB::select("SELECT * FROM thongtingiaohangs ");
         

    //     return response()->json([ 
    //         'data_sanpham' => $data_sanpham,
    //         'data_voucher' => $data_voucher,
    //         'data_adress' => $data_adress,  
    //     ]);
    // }   
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
            TONGTIENDONHANG, PHIVANCHUYEN, HINHTHUC_THANHTOAN, TRANGTHAI_THANHTOAN, TRANGTHAI_DONHANG, MATTGH, GHICHU) 
            VALUES('$matk', '$ngayorder', '$ngayorder', $tongtien_sp
            , $vouchergiam, $tongtiendonhang, '$phivanchuyen', '$hinhthuc_thanhtoan', '$trangthai_thanhtoan'
            , '$trangthai_donhang', '$mattgh', '$ghichu')"
        );

        
        $madh = DB::select("SELECT MADH FROM donhangs ORDER BY NGAYORDER DESC LIMIT 1"); 

        if($mavoucher == ''){
            DB::insert("INSERT INTO donhang_vouchers values( ? , ? )", ['Kurtis', $madh[0]->MADH]);
        }
        else{
            DB::insert("INSERT INTO donhang_vouchers values( ? , ? )", [$mavoucher, $madh[0]->MADH]);
        }

        foreach($infoProduct as $item){
            $maxdsp = DB::select(
                "SELECT MAXDSP from sanpham_mausac_sizes 
                where MASP = ? AND MASIZE = ? AND MAMAU = ?", 
                [$item['MASP'], $item['MASIZE'], 
                $item['MAMAU']]
            );
            DB::insert("INSERT INTO chitiet_donhangs(MADH, MAXDSP, TONGTIEN, SOLUONG)
             VALUES(?, ?, ?, ?)", 
             [$madh[0]->MADH,  $maxdsp[0]->MAXDSP, $item['TONGGIA'], $item['SOLUONG']]);
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

        return response()->json([
            'message' => 200,
        ]);
    }

    public function updateQuantityProductInCart(Request $request){
        $soluong = $request->input('soluongsp');
        $matk = $request->input('matk');
        $masp = $request->input('masp');
        $masize = $request->input('masize');
        $mamau = $request->input('mamau');

        DB::update("UPDATE chitiet_giohangs SET SOLUONG = SOLUONG + $soluong WHERE MATK = '$matk' 
        AND MASP = '$masp' AND MASIZE = '$masize' AND MAMAU = '$mamau'");
    }

    public function addProduct(Request $request){
        $objectInfoAddNewProduct = json_decode($request->input('infoAddNewProduct'));
        // $TENSP = $request->input('nameProduct');
        // $GIAGOC = $request->input('originPrice');
        // $GIABAN = $request->input('sellPrice');
        // $MAPL_SP = $request->input('typeProduct');
        // $MOTA = $request->input('desctiption');
        // $checkboxColor = $request->input('checkboxColor');
        // $checkboxSize = $request->input('checkboxSize'); 

        $TENSP = $objectInfoAddNewProduct->nameProduct;
        $GIAGOC = $objectInfoAddNewProduct->originPrice;
        $GIABAN = $objectInfoAddNewProduct->sellPrice;
        $MAPL_SP = $objectInfoAddNewProduct->typeProduct;
        $MOTA = $objectInfoAddNewProduct->desctiption;
        $checkboxColor = $objectInfoAddNewProduct->checkboxColor;
        $checkboxSize = $objectInfoAddNewProduct->checkboxSize;
        $indexThumnail = $objectInfoAddNewProduct->indexThumnail;

        DB::insert(
            "INSERT into sanphams(TENSP, GIAGOC, GIABAN, MAPL_SP, MOTA, created_at, updated_at) 
            values('$TENSP', '$GIAGOC', '$GIABAN', '$MAPL_SP', '$MOTA', NOW(), NOW())"
        );
        $masp = DB::select("SELECT MASP FROM sanphams ORDER BY created_at DESC LIMIT 1");
        $masp_query = $masp[0]->MASP;
        foreach ($checkboxSize as $itemSize){
            foreach($checkboxColor as $itemColor){
                DB::insert("insert into sanpham_mausac_sizes(MASP, MAMAU, MASIZE, SOLUONG) values($masp_query, $itemColor, '$itemSize', 0)");
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
                $imageName = 't' . '_' . $masp_query . '_' . (++$index_image) . '_' . time() . '_thumnail' .  '.' . $image->extension();
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

    public function filterSearchProduct(Request $request){
        $filter = $request->input('filter'); 
        $tensp = $request->input('textQuery');
        $data_product = [];    
 
        if($filter == 'moinhat'){
            $data_product = DB::select("SELECT * from sanphams where TENSP LIKE '%$tensp%' ORDER BY created_at DESC");
            return response()->json([
                'data_product' => $data_product, 
                'filter'=> $filter,
            ]);
        }
        else if($filter == 'banchay'){
            $data_product = DB::select("SELECT chitiet_donhangs.MASP, TENSP, GIAGOC, GIABAN from chitiet_donhangs, sanphams
                                        where chitiet_donhangs.MASP = sanphams.MASP AND TENSP = '$tensp' 
                                        group by chitiet_donhangs.MASP
                                        order by SUM(SOLUONG) DESC
                                        ");
        }
        else if($filter == 'thapDenCao'){
            $data_product = DB::select("SELECT * from sanphams where TENSP = '$tensp' ORDER BY GIABAN ASC");
        }
        else if($filter == 'caoDenThap'){
            $data_product = DB::select("SELECT * from sanphams where TENSP = '$tensp' ORDER BY GIABAN DESC");
        }

        return response()->json([
            'data_product' => $data_product, 
            'filter'=> $filter,
        ]);
    }


    public function testtest(Request $request){
        $var_1 = $request->input('vnp_Amount');
        $var_2 = $request->input('vnp_BankCode');
        $data = DB::select("SELECT MADH from donhangs where MADH = '$var_1' OR MADH = '$var_2'");
        return response()->json([
            'data' => $data,
        ]);
    }

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
        if($vnp_ResponseCode == '00')
            DB::update("UPDATE donhangs set TRANGTHAI_THANHTOAN = 'da thanh toan' Where MADH = ?", [$vnp_TxnRef]);
            DB::delete("DELETE from chitiet_giohangs where selected = 1");
        return response()->json([
            'data' => $returnData,
            // 'secureHash' => $secureHash,
            // 'vnp_SecureHash' => $vnp_SecureHash,
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
}

