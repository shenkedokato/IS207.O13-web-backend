<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;


class SetDataController extends Controller
{
    private $productNames = [
        'Áo Polo Cổ Bẻ (Mã: PB210)',
        'Áo Polo Dệt Kim (Mã: DK315)',
        'Áo Polo Có Logo (Mã: PLG007)',
        'Áo Polo Đơn Sắc (Mã: DS499)',
        'Áo Polo Nút Cài (Mã: NC601)',
        'Áo Polo Nữ Form Rộng (Mã: FR701)',
        'Áo Polo Nữ Dệt Kim (Mã: DKN803)',
        'Áo Polo Nữ Đa Sắc (Mã: DSN909)',
        'Áo Polo Nữ Slim-fit (Mã: SLF999)',
        'Áo Polo Nữ In Họa Tiết (Mã: IHN111)',
        'Quần Short Nam Trẻ Em (Mã: QSN205)',
        'Quần Short Nữ Bé Gái (Mã: QSG309)',
        'Quần Short Jean Trẻ Em (Mã: QSJ415)',
        'Quần Short Thể Thao Trẻ Em (Mã: QST520)',
        'Quần Short Bé Trai Dài (Mã: QBD625)',
        'Đầm Bé Gái Xòe (Mã: DGX730)',
        'Đầm Bé Trai Thể Thao (Mã: DTT840)',
        'Đầm Bé Gái Hoa Văn (Mã: DGH945)',
        'Áo Thun Graphic Print (Mã: GP202)',
        'Áo Thun Polo Nam (Mã: PL155)',
        'Áo Thun Sọc Ngang (Mã: SN309)',
        'Áo Thun Henley (Mã: HN008)',
        'Áo Thun Crop Top (Mã: CT102)',
        'Áo Thun Nữ Dài Tay (Mã: DT305)',
        'Áo Thun Nữ In Họa Tiết (Mã: IH401)',
        'Áo Thun Oversize (Mã: OV507)',
        'Áo Thun Phong Cách Boyfriend (Mã: BF610)',
    ];
    private function getRandomProductName()
    {
        // Chọn ngẫu nhiên một tên sản phẩm từ danh sách
        $randomIndex = array_rand($this->productNames);
        return $this->productNames[$randomIndex];
    }
    private function findMatchingMAPL2()
    { 
        return rand(1, 3); // Adjust this based on your logic
    }

    private function mota()
    { 
        return "🔹 Bảng size Outerity
        S: Dài 69 Rộng 52.5 | 1m50 - 1m65, 45 - 55Kg
        M: Dài 73 Rộng 55 | 1m60 - 1m75, 50 - 65Kg
        L: Dài 76.5 Rộng 57.5 | 1m7 - 1m8, 65Kg - 80Kg
        👉 Nếu chưa biết lựa size bạn có thể inbox để được chúng mình tư vấn.
        
        ‼️LƯU Ý ▪️Khi giặt sản phẩm bằng tay: Vui lòng hoà tan kĩ nước giặt, bột giặt với nước sau đó mới cho sản phẩm vào. ▪️Khi giặt sản phẩm bằng máy giặt: Vui lòng đổ nước giặt, bột giặt vào khay của máy.
        
         🚫TUYỆT ĐỐI KHÔNG đổ nước giặt, bột giặt trực tiếp vào sản phẩm. Như vậy sẽ ảnh hưởng đến màu sắc của sản phẩm và làm cho áo có tình trạng loang màu. Outerity xin cảm ơn ạ🖤
        
        🔹 Chính sách đổi trả Outerity.
        - Miễn phí đổi hàng cho khách mua ở Outerity trong trường hợp bị lỗi từ nhà sản xuất, giao nhầm hàng, nhầm size.
        - Quay video mở sản phẩm khi nhận hàng, nếu không có video unbox, khi phát hiện lỗi phải báo ngay cho Outerity trong 1 ngày tính từ ngày giao hàng thành công. Qua 1 ngày chúng mình không giải quyết khi không có video unbox.
        - Sản phẩm đổi trong thời gian 3 ngày kể từ ngày nhận hàng
        - Sản phẩm còn mới nguyên tem, tags, sản phẩm chưa giặt và không dơ bẩn, hư hỏng bởi những tác nhân bên ngoài cửa hàng sau khi mua hàng."; // Adjust this based on your logic
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private function getRandomNames($count) {
        $names = ['An', 'Bảo', 'Cường', 'Đạt', 'Duy', 'Hải', 'Hùng', 'Khánh', 'Linh', 'Minh', 'Nam', 'Nghĩa', 'Phúc', 'Quân', 'Quang', 'Sơn', 'Tùng', 'Vân', 'Việt', 'Vũ'];
        $randomNames = [];
    
        for ($i = 0; $i < $count; $i++) {
            $randomNames[] = $names[array_rand($names)];
        }
    
        return $randomNames;
    }
    
    // Hàm để tạo địa chỉ ngẫu nhiên ở Việt Nam
    private function getRandomAddress() {
        $provinces = ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'An Giang', 'Bạc Liêu', 'Bắc Kạn', 'Bắc Giang', 'Bắc Ninh'];
        $districts = ['Quận 1', 'Quận 2', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 6', 'Quận 7', 'Quận 8', 'Quận 9', 'Quận 10'];
        $wards = ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7', 'Phường 8', 'Phường 9', 'Phường 10'];
    
        $randomProvince = $provinces[array_rand($provinces)];
        $randomDistrict = $districts[array_rand($districts)];
        $randomWard = $wards[array_rand($wards)];
    
        return "$randomProvince, $randomDistrict, $randomWard";
    }
    
    public function setdata(){

        // $baseDirectory = public_path('images/products/');

        // // Duyệt qua từng thư mục sản phẩm
        //     for ($i = 1; $i <= 364; $i++) {
        //         $directory = $baseDirectory . $i . '/';

        //         // Lấy danh sách các tệp trong thư mục
        //         $files = File::glob($directory . '*');

        //         $thumbnailCount = 0;
        //         $thumbnailFile = null;

        //         // Kiểm tra và đổi tên các tệp trong thư mục
        //         foreach ($files as $file) {
        //             $fileName = pathinfo($file, PATHINFO_FILENAME);

        //             if (strpos($fileName, 'thumnail') !== false) {
        //                 $thumbnailCount++;
        //                 $thumbnailFile = $file;

        //                 // Nếu có nhiều hơn một tệp có chứa "thumnail", đổi tên các tệp còn lại
        //                 if ($thumbnailCount > 1) {
        //                     $newFileName = '1702717402'; // Tên mới của file

        //                     // Đường dẫn mới của tệp tin
        //                     $newFilePath = $directory . $newFileName . '.' . pathinfo($file, PATHINFO_EXTENSION);

        //                     // Đổi tên tệp tin
        //                     File::move($file, $newFilePath);

        //                     // Update thông tin trong cơ sở dữ liệu nếu cần
        //                     // ...
        //                 }
        //             }
        //         }

        //         // Đổi tên tệp còn lại thành '1702717402'
        //         if ($thumbnailFile && $thumbnailCount === 1) {
        //             $newFilePath = $directory . '1702717402.' . pathinfo($thumbnailFile, PATHINFO_EXTENSION);

        //             File::move($thumbnailFile, $newFilePath);

        //             // Update thông tin trong cơ sở dữ liệu nếu cần
        //             // ...
        //         }
        //     }

        // for ($i = 1; $i <= 364; $i++) {
        //     $filePath = public_path("images/products/{$i}/1702717402.png");
        
        //     // Kiểm tra nếu tệp tin tồn tại thì xoá
        //     if (File::exists($filePath)) {
        //         File::delete($filePath);
        //     }
        // }

        
        // $sourceFolder = public_path('images/products/1'); // Thư mục nguồn trong Laravel
        // $destinationFolder = public_path('images/products'); // Thư mục đích trong Laravel

        // for ($i = 2; $i <= 365; $i++) {
        //     $newFolder = $destinationFolder . '/' . $i;
        //     File::makeDirectory($newFolder);

        //     $files = File::files($sourceFolder);

        //     foreach ($files as $key => $file) {
        //         $fileName = pathinfo($file, PATHINFO_FILENAME);
        //         $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        //         // Tạo tên file mới theo mẫu
        //         if ($key === 0) {
        //             $newFileName = "{$i}_1_" . str_replace('1_', '', $fileName) . ".{$fileExtension}";
        //         } else {
        //             $newFileName = "{$i}_" . str_replace('1_', '', $fileName) . ".{$fileExtension}";
        //         }

        //         // Đường dẫn mới cho file trong thư mục mới
        //         $newFilePath = $newFolder . '/' . $newFileName;

        //         // Sao chép và đổi tên file
        //         File::copy($file, $newFilePath);
        //     }
        // }

        //1. phanloai_sanphams
        {
            $categories = [
                ['MAPL' => 1, 'TENPL' => 'Nam'],
                ['MAPL' => 2, 'TENPL' => 'Nữ'],
                ['MAPL' => 3, 'TENPL' => 'Trẻ em'],
            ];
    
            foreach ($categories as $category) {
                DB::table('phanloai_sanphams')->insert($category);
            }
        }
        
        //11. mausacs
        {
            $colors = [
                ['TENMAU' => 'Đen', 'HEX' => '#000000'],
                ['TENMAU' => 'Trắng', 'HEX' => '#FFFFFF'],
                ['TENMAU' => 'Đỏ', 'HEX' => '#FF0000'],
                ['TENMAU' => 'Xanh dương', 'HEX' => '#0000FF'],
                ['TENMAU' => 'Xanh lá', 'HEX' => '#00FF00'],
                ['TENMAU' => 'Vàng', 'HEX' => '#FFFF00'],
                ['TENMAU' => 'Cam', 'HEX' => '#FFA500'],
                ['TENMAU' => 'Vàng', 'HEX' => '#FFFF00'],
                ['TENMAU' => 'Tím', 'HEX' => '#800080'],
                ['TENMAU' => 'Xanh đậm', 'HEX' => '#000080'],
                ['TENMAU' => 'Vàng cam', 'HEX' => '#FFD700'],
                ['TENMAU' => 'Vàng xanh', 'HEX' => '#00FF00'],
                ['TENMAU' => 'Đỏ tím', 'HEX' => '#8B008B'],
            ];
            
            foreach ($colors as $color) {
                DB::table('mausacs')->insert($color);
            }
        }

        //2. phanloai_sanpham2s 
        {
            $categories = [
                ['MAPL2' => 1, 'MAPL1' => 1, 'TENPL2' => 'Áo thun'],
                ['MAPL2' => 2, 'MAPL1' => 1, 'TENPL2' => 'Áo POLO'],
                ['MAPL2' => 3, 'MAPL1' => 1, 'TENPL2' => 'Quần short'],
                ['MAPL2' => 1, 'MAPL1' => 2, 'TENPL2' => 'Áo thun'],
                ['MAPL2' => 2, 'MAPL1' => 2, 'TENPL2' => 'Váy'],
                ['MAPL2' => 3, 'MAPL1' => 2, 'TENPL2' => 'Đầm'],
                ['MAPL2' => 1, 'MAPL1' => 3, 'TENPL2' => 'Áo'],
                ['MAPL2' => 2, 'MAPL1' => 3, 'TENPL2' => 'Quần'],
                ['MAPL2' => 3, 'MAPL1' => 3, 'TENPL2' => 'Set đồ'],
                // ... any other entries you want to include
            ];
            foreach ($categories as $category) {
                DB::insert('INSERT INTO phanloai_sanpham2s (MAPL2, MAPL1, TENPL2) VALUES (?, ?, ?)', [
                    $category['MAPL2'],
                    $category['MAPL1'],
                    $category['TENPL2'],
                ]);
            }

        }

        // 10. size
        {
            $sizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
            foreach ($sizes as $size) {
                DB::insert('INSERT INTO sizes (MASIZE) VALUES (?)', [$size]);
            }
        }

        // //5. taikhoans
        {

            DB::insert('INSERT INTO taikhoans (TEN, EMAIL, PASSWORD, GIOITINH, ROLE, AdminVerify, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                'Đạt',
                'khachhang@gmail.com',
                '$2y$10$ybrP/W6BpE4I.N50D7Usa.ETNx60CSFsk1IJ9O6GvySLkx4GeYWn2',
                'Nam',
                'Khách hàng',
                'Đã xác nhận',
                '2023-12-14 10:16:07',
                '2023-12-14 10:15:48',
                '2023-12-14 10:16:07',
            ]);

            DB::insert('INSERT INTO taikhoans (TEN, EMAIL, PASSWORD, GIOITINH, ROLE, AdminVerify, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                'Đạt',
                'nhanvien@gmail.com',
                '$2y$10$ybrP/W6BpE4I.N50D7Usa.ETNx60CSFsk1IJ9O6GvySLkx4GeYWn2',
                'Nam',
                'Nhân viên',
                'Đã xác nhận',
                '2023-12-14 10:16:07',
                '2023-12-14 10:15:48',
                '2023-12-14 10:16:07',
            ]);
            
            // Câu truy vấn insert cho dòng dữ liệu thứ hai
            DB::insert('INSERT INTO taikhoans (TEN, EMAIL, PASSWORD, GIOITINH, ROLE, AdminVerify, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                'Đạtt',
                '21521932@gm.uit.edu.vn',
                '$2y$10$nJgPXErZJOzbHhFf/oL88O79JF.KQFByZcM9yVUz3O8x.hN.02Ida',
                'Nam',
                'Admin',
                'Đã xác nhận',
                '2023-12-14 10:17:51',
                '2023-12-14 10:17:42',
                '2023-12-14 10:17:51',
            ]);

            
            $randomNames = $this->getRandomNames(30);

            foreach ($randomNames as $name) {
                $gender = rand(0, 1) ? 'Nam' : 'Nữ';
                $address = $this->getRandomAddress();
                $phoneNumber = '0' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT); // Số điện thoại bắt đầu bằng 0
                $role = rand(0, 1) ? 'Nhân viên' : 'Khách hàng';
                $AdminVerify = rand(0, 1) ? 'Chờ xác nhận' : 'Đã xác nhận';

                DB::insert(
                    "INSERT INTO taikhoans 
                    (TEN, EMAIL, PASSWORD, GIOITINH, SDT, DIACHI, ROLE, AdminVerify, 
                    email_verified_at, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                    [
                        $name,
                        strtolower(str_replace(' ', '', $name)) . '@example.com',
                        '$2y$10$ybrP/W6BpE4I.N50D7Usa.ETNx60CSFsk1IJ9O6GvySLkx4GeYWn2',
                        $gender,
                        $phoneNumber,
                        $address,
                        $role,
                        $AdminVerify,
                        '2023-12-14',
                        '2023-12-14',
                        '2023-12-14',
                    ]
                );
            }
        }

        //3. sanphams
        {
            $startDate = Carbon::create(2023, 1, 1);
            $endDate = Carbon::create(2023, 12, 31);

            $currentDate = $startDate;

            $categories = [
                ['MAPL' => 1, 'TENPL' => 'Nam'],
                ['MAPL' => 2, 'TENPL' => 'Nữ'],
                ['MAPL' => 3, 'TENPL' => 'Trẻ em'],
            ];
            
            while ($currentDate <= $endDate) { 
                    
                    $randomIndex = rand(0, count($categories) - 1);
                    $category = $categories[$randomIndex];
                    $TENSP = $this->getRandomProductName(); // Generate product name

                    $GIAGOC = rand(230000, 500000);
                    $minDiscount = 5; // Phần trăm giảm giá tối thiểu
                    $maxDiscount = 40; // Phần trăm giảm giá tối đa

                    // Tính toán GIABAN từ GIAGOC
                    $discountPercentage = rand($minDiscount, $maxDiscount);
                    $GIABAN = $GIAGOC * (100 - $discountPercentage) / 100;
                    $GIABAN = round($GIABAN / 1000) * 1000; // Làm cho GIABAN có 3 số cuối là số 0

                    // Đảm bảo GIAGOC có 3 số cuối là số 0
                    $GIAGOC = round($GIAGOC / 1000) * 1000;

                    // Kiểm tra nếu GIABAN lớn hơn hoặc bằng GIAGOC, hoán đổi giá trị để đảm bảo GIABAN luôn nhỏ hơn GIAGOC
                    if ($GIABAN >= $GIAGOC) {
                        $temp = $GIAGOC;
                        $GIAGOC = $GIABAN;
                        $GIABAN = $temp;
                    }

                    $MAPL_SP = $category['MAPL'];
                    $MAPL_SP2 = $this->findMatchingMAPL2();
                    $MOTA = $this->mota();
    
                    // Thêm sản phẩm vào bảng sanphams 
                    DB::table('sanphams')->insert([
                        'TENSP' => $TENSP,
                        'GIAGOC' => $GIAGOC,
                        'GIABAN' => $GIABAN,
                        'MAPL_SP' => $MAPL_SP,
                        'MAPL_SP2' => $MAPL_SP2,
                        'MOTA' => $MOTA,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ]); 
    
                $currentDate->addDay();
            }
        }

        // //4. hinhanhs và hinhanhsanphams
        {
            $imageDirectories = glob(public_path('images/products/*'), GLOB_ONLYDIR);

            foreach ($imageDirectories as $imageDir) {
                $masp = basename($imageDir);

                // Lấy danh sách các tập tin trong thư mục sản phẩm
                $imageFiles = File::files($imageDir);

                foreach ($imageFiles as $imageFile) {
                    $fileName = pathinfo($imageFile, PATHINFO_FILENAME);
                    $fileExtension = pathinfo($imageFile, PATHINFO_EXTENSION);

                    // Xây dựng URL hình ảnh
                    $imgUrl = url("/images/products/{$masp}/{$fileName}.{$fileExtension}");

                    // Thêm vào bảng hinhanhsanphams
                    DB::table('hinhanhs')->insert([
                        'MAHINHANH' => $fileName
                    ]);
                    DB::table('hinhanhsanphams')->insert([
                        'MAHINHANH' => $fileName,
                        'MASP' => $masp,
                        'imgURL' => $imgUrl,
                    ]);
                }
            }
        }
        
        // //6. thongtingiaohangs
        {
            // Câu truy vấn insert cho dòng dữ liệu thứ nhất
            DB::insert('INSERT INTO thongtingiaohangs (MATTGH, MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                1,
                2,
                'đỗ sĩ đạt',
                '0968795749',
                'triệu phong',
                'Tỉnh Nam Định',
                'Huyện Trực Ninh',
                'Xã Trực Thanh',
                1,
            ]);

            // Câu truy vấn insert cho dòng dữ liệu thứ hai
            DB::insert('INSERT INTO thongtingiaohangs (MATTGH, MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                2,
                2,
                'nguyễn văn a',
                '0123456789',
                'triệu phong',
                'Thành phố Hải Phòng',
                'Quận Hải An',
                'Phường Đằng Lâm',
                1,
            ]);

            // Câu truy vấn insert cho dòng dữ liệu thứ ba
            DB::insert('INSERT INTO thongtingiaohangs (MATTGH, MATK, TEN, SDT, DIACHI, TINH_TP, QUAN_HUYEN, PHUONG_XA, DANGSUDUNG) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                3,
                2,
                'đỗ sĩ đạt',
                '0968795749',
                'triệu phong',
                'Tỉnh Bắc Kạn',
                'Huyện Ba Bể',
                'Xã Khang Ninh',
                1,
            ]);

        }

        // //7.voucher 
        {
            for ($i = 0; $i < 5; $i++) {
                $randomDateStart = Carbon::now()->addDays(5)->toDateString(); // THOIGIANBD > thời gian hiện tại và cách thời gian hiện tại 5 ngày
                $randomDateEnd = Carbon::now()->addDays(10)->toDateString(); // THOIGIANKT > thời gian hiện tại và cách thời gian hiện tại 10 ngày
            
                $MAVOUCHER = $this->generateRandomString(10);
                $SOLUONG = rand(1, 999);
                $SOLUONG_CONLAI = $SOLUONG;
                $GIATRI_DH_MIN = rand(100000, 500000); // 700000 < GIATRI_DH_MIN <= 1000000
                $GIATRI_GIAM_MAX = rand(1, 20) * 10000; // GIATRI_GIAM_MAX là bội của số 10000 và < 200000
                $THOIGIANBD = $randomDateStart;
                $THOIGIANKT = $randomDateEnd;
                $PHANLOAI_VOUCHER = rand(0, 1) ? 'Vận chuyển' : 'Đơn hàng'; // Chọn ngẫu nhiên 'Vận chuyển' hoặc 'Đơn hàng'
                $GIATRIGIAM = rand(1, 20) / 100; // GIATRIGIAM là bội của 0.05 và <= 1
                $MOTA = "Số lượng voucher: $SOLUONG, 
                    Giá trị đơn hàng tối thiểu: $GIATRI_DH_MIN,
                    Giá trị giảm tối đa: $GIATRI_GIAM_MAX, 
                    Thời gian bắt đầu: $THOIGIANBD, 
                    Thời gian kết thúc: $THOIGIANKT,
                    Loại voucher: $PHANLOAI_VOUCHER, 
                    Giá trị giảm: $GIATRIGIAM.";
            
                DB::insert('INSERT INTO vouchers (MAVOUCHER, SOLUONG, SOLUONG_CONLAI, GIATRI_DH_MIN, GIATRI_GIAM_MAX, THOIGIANBD, THOIGIANKT, MOTA, PHANLOAI_VOUCHER, GIATRIGIAM) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $MAVOUCHER,
                    $SOLUONG,
                    $SOLUONG_CONLAI,
                    $GIATRI_DH_MIN,
                    $GIATRI_GIAM_MAX,
                    $THOIGIANBD,
                    $THOIGIANKT,
                    $MOTA,
                    $PHANLOAI_VOUCHER,
                    $GIATRIGIAM,
                ]);
            }
            for ($i = 0; $i < 5; $i++) {
                $randomDateStart = Carbon::now()->subDays(5)->addDays($i)->toDateString(); // THOIGIANBD từ 5 ngày trước đến ngày hiện tại
                $randomDateEnd = Carbon::now()->addDays(10)->toDateString(); // THOIGIANKT từ ngày hiện tại đến 10 ngày sau
                
                $MAVOUCHER = $this->generateRandomString(30);
                $SOLUONG = rand(1, 999);
                $SOLUONG_CONLAI = $SOLUONG;
                $GIATRI_DH_MIN = rand(100000, 500000); // 700000 < GIATRI_DH_MIN <= 1000000
                $GIATRI_GIAM_MAX = rand(1, 20) * 10000; // GIATRI_GIAM_MAX là bội của số 10000 và < 200000
                $THOIGIANBD = $randomDateStart;
                $THOIGIANKT = $randomDateEnd;
                $PHANLOAI_VOUCHER = rand(0, 1) ? 'Vận chuyển' : 'Đơn hàng'; // Chọn ngẫu nhiên 'Vận chuyển' hoặc 'Đơn hàng'
                $GIATRIGIAM = rand(1, 20) / 100; // GIATRIGIAM là bội của 0.05 và <= 1
                $MOTA = "Số lượng voucher: $SOLUONG, 
                    Giá trị đơn hàng tối thiểu: $GIATRI_DH_MIN,
                    Giá trị giảm tối đa: $GIATRI_GIAM_MAX, 
                    Thời gian bắt đầu: $THOIGIANBD, 
                    Thời gian kết thúc: $THOIGIANKT,
                    Loại voucher: $PHANLOAI_VOUCHER, 
                    Giá trị giảm: $GIATRIGIAM.";
            
                DB::insert('INSERT INTO vouchers (MAVOUCHER, SOLUONG, SOLUONG_CONLAI, GIATRI_DH_MIN, GIATRI_GIAM_MAX, THOIGIANBD, THOIGIANKT, MOTA, PHANLOAI_VOUCHER, GIATRIGIAM) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $MAVOUCHER,
                    $SOLUONG,
                    $SOLUONG_CONLAI,
                    $GIATRI_DH_MIN,
                    $GIATRI_GIAM_MAX,
                    $THOIGIANBD,
                    $THOIGIANKT,
                    $MOTA,
                    $PHANLOAI_VOUCHER,
                    $GIATRIGIAM,
                ]);
            }
            for ($i = 0; $i < 5; $i++) {
                $randomDateStart = Carbon::now()->subDays(5 + $i)->toDateString(); // THOIGIANBD từ 5 ngày trước đến ngày hiện tại
                $randomDateEnd = Carbon::now()->addDays(10)->toDateString(); // THOIGIANKT từ ngày hiện tại đến 10 ngày sau
               
                $MAVOUCHER = $this->generateRandomString(30);
                $SOLUONG = rand(1, 999);
                $SOLUONG_CONLAI = $SOLUONG;
                $GIATRI_DH_MIN = rand(100000, 500000); // 700000 < GIATRI_DH_MIN <= 1000000
                $GIATRI_GIAM_MAX = rand(1, 20) * 10000; // GIATRI_GIAM_MAX là bội của số 10000 và < 200000
                $THOIGIANBD = $randomDateStart;
                $THOIGIANKT = $randomDateEnd;
                $PHANLOAI_VOUCHER = rand(0, 1) ? 'Vận chuyển' : 'Đơn hàng'; // Chọn ngẫu nhiên 'Vận chuyển' hoặc 'Đơn hàng'
                $GIATRIGIAM = rand(1, 20) / 100; // GIATRIGIAM là bội của 0.05 và <= 1
                $MOTA = "Số lượng voucher: $SOLUONG, 
                    Giá trị đơn hàng tối thiểu: $GIATRI_DH_MIN,
                    Giá trị giảm tối đa: $GIATRI_GIAM_MAX, 
                    Thời gian bắt đầu: $THOIGIANBD, 
                    Thời gian kết thúc: $THOIGIANKT,
                    Loại voucher: $PHANLOAI_VOUCHER, 
                    Giá trị giảm: $GIATRIGIAM.";
            
                DB::insert('INSERT INTO vouchers (MAVOUCHER, SOLUONG, SOLUONG_CONLAI, GIATRI_DH_MIN, GIATRI_GIAM_MAX, THOIGIANBD, THOIGIANKT, MOTA, PHANLOAI_VOUCHER, GIATRIGIAM) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $MAVOUCHER,
                    $SOLUONG,
                    $SOLUONG_CONLAI,
                    $GIATRI_DH_MIN,
                    $GIATRI_GIAM_MAX,
                    $THOIGIANBD,
                    $THOIGIANKT,
                    $MOTA,
                    $PHANLOAI_VOUCHER,
                    $GIATRIGIAM,
                ]);
            }
        }
        // //8. THỰC HIỆN INSERT DỮ LIỆU VÀO BẢNG HOÁ ĐƠN ĐỂ THỐNG KÊ
        {
            $startDate = '2023-01-01';
            $endDate = '2023-12-31';
    
            $currentDate = $startDate;
            $statuses = ['Đã giao', 'Chuẩn bị hàng', 'Đang giao', 'Đã huỷ'];
            $shippingFee = rand(0, 1) > 0.5 ? rand(25000, 30000) : rand(25000, 30000);
            $paymentStatus = rand(0, 1) ? 'Đã thanh toán' : 'Chưa thanh toán';
    
            while ($currentDate <= $endDate) {
                $totalProductPrice = rand(250, 1000) * 1000;
                $voucherDiscount = rand(10, 200) * 1000;
                $totalOrderPrice = $totalProductPrice + $shippingFee - $voucherDiscount;
            
                DB::table('donhangs')->insert([
                    'MATK' => rand(1, 30),
                    'NGAYORDER' => $currentDate,
                    'NGAYGIAOHANG' => $currentDate, // Có thể là null hoặc giống ngày đặt hàng
                    'TONGTIEN_SP' => $totalProductPrice,
                    'VOUCHERGIAM' => $voucherDiscount,
                    'TONGTIENDONHANG' => $totalOrderPrice,
                    'PHIVANCHUYEN' => $shippingFee,
                    'HINHTHUC_THANHTOAN' => rand(0, 1) > 0.5 ? 'Chuyển khoản' : 'Thanh toán khi nhận hàng',
                    'TRANGTHAI_THANHTOAN' => $paymentStatus,
                    'TRANGTHAI_DONHANG' => $statuses[array_rand($statuses)],
                    'MATTGH' => rand(1, 3),
                    'GHICHU' => 'Ghi chú'
                ]);
    
                // Tăng ngày thêm 1
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        // // //9. donhang_voucher
        // // {
        // //     // for ($i = 1; $i <= 50; $i++) {
        // //     //     $randomVoucher = DB::table('vouchers')->inRandomOrder()->first();
        // //     //     $randomOrder = DB::table('donhangs')->inRandomOrder()->first();
            
        // //     //     DB::table('donhang_vouchers')->insert([
        // //     //         'MAVOUCHER' => $randomVoucher->MAVOUCHER,
        // //     //         'MADH' => $randomOrder->MADH,
        // //     //     ]);
        // //     // }
        // // }
        
        
        
        // //12. sanpham_mausac_sizes
        {
            $sanPhams = DB::table('sanphams')->get();
            $mauSacs = DB::table('mausacs')->get();
            $sizes = DB::table('sizes')->get();

            foreach ($sanPhams as $sanPham) {
                $listSize = $sizes->random(rand(3, 5)); // Lấy ngẫu nhiên 1-3 kích thước
                $listColor = $mauSacs->random(rand(1, 4)); // Lấy ngẫu nhiên 1-3 màu sắc

                foreach ($listSize as $size) {
                    foreach ($listColor as $color) {
                        $maxdsp = DB::table('sanpham_mausac_sizes')->insertGetId([
                            'MASP' => $sanPham->MASP,
                            'MAMAU' => $color->MAMAU,
                            'MASIZE' => $size->MASIZE,
                            'SOLUONG' => rand(0, 1000),
                        ]);
                        // Xử lý sau khi thêm dữ liệu, nếu cần
                    }
                }
            }
        }
        // // 13. chitiet_donhangs
        {
            
            // Lấy danh sách MADH
            $donHangs = DB::table('donhangs')->select('MADH')->get()->pluck('MADH')->toArray();

            foreach ($donHangs as $madh) {
                // Lấy ngẫu nhiên từ 1 đến 5 MAXDSP cho mỗi MADH
                $maxdspCount = rand(1, 5);
                $maxdspList = DB::table('sanpham_mausac_sizes')->inRandomOrder()->limit($maxdspCount)->pluck('MAXDSP')->toArray();

                foreach ($maxdspList as $maxdsp) {
                    // Lấy thông tin sản phẩm từ MAXDSP
                    $sanpham = DB::table('sanpham_mausac_sizes')
                        ->select('MASP', 'SOLUONG')
                        ->where('MAXDSP', $maxdsp)
                        ->first();

                    // Tính TONGTIEN
                    $soluong = rand(1, 5);
                    $tongtien = $soluong * DB::table('sanphams')->where('MASP', $sanpham->MASP)->value('GIABAN');

                    // Insert vào bảng chitiet_donhangs
                    DB::table('chitiet_donhangs')->insert([
                        'MADH' => $madh,
                        'MAXDSP' => $maxdsp,
                        'TONGTIEN' => $tongtien,
                        'SOLUONG' => $soluong,
                        'DADANHGIA' => 0,
                    ]);
                }
            }
        }
        // // 14. danhgia_sanphams
        {
            // Lấy danh sách đơn hàng và MASP tương ứng của tất cả các đơn hàng
            $donhangs = DB::table('donhangs')->select('MADH', 'MATK')->get();

            foreach ($donhangs as $donhang) {
                // Lấy danh sách MAXDSP của đơn hàng này
                $maxdsps = DB::table('chitiet_donhangs')
                    ->where('MADH', $donhang->MADH)
                    ->pluck('MAXDSP')
                    ->toArray();

                foreach ($maxdsps as $maxdsp) {
                    // Lấy thông tin sản phẩm từ MAXDSP
                    $sanpham = DB::table('sanpham_mausac_sizes')
                        ->select('MASP')
                        ->where('MAXDSP', $maxdsp)
                        ->first();

                    // Tạo đánh giá ngẫu nhiên
                    $sosao = rand(1, 5);
                    $noidung = 'Đánh giá cho sản phẩm này là ' . $sosao . ' sao.';

                    // Insert đánh giá vào danhgia_sanphams
                    DB::table('danhgia_sanphams')->insert([
                        'MADH' => $donhang->MADH,
                        'MASP' => $sanpham->MASP,
                        'MATK' => $donhang->MATK,
                        'MAXDSP' => $maxdsp,
                        'SOLUONG_SAO' => $sosao,
                        'NOIDUNG_DANHGIA' => $noidung,
                    ]);
                }
            }
        }
        
        // DB::update("update donhangs set TRANGTHAI_DONHANG = 'Đã giao'");
    }
}
