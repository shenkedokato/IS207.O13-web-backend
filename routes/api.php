<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ManageProductController; 
use App\Http\Controllers\PayOnlineController;
use App\Http\Controllers\AddProductController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminManageOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InfoAccountController;
 
use App\Http\Controllers\SearchProductController;
use App\Http\Controllers\UpdateProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManageAccountCustomerController;
use App\Http\Controllers\ManageAccountStaff;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\ReviewProduct;
use App\Http\Controllers\SetDataController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\VoucherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Home
Route::get('getInfoAtStartLoadingHome', [HomeController::class, 'getInfoAtStartLoadingHome']);

//Login
Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('sendMailRecoverPassword', [LoginController::class, 'sendMailRecoverPassword']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('logout', [LoginController::class, 'logout']); 
});
Route::get('/email/verify/{id}/{hash}', [LoginController::class, 'verify']) 
->middleware(['signed'])->name('verification.verify');

//AdminLogin
Route::post('adminRegister', [AdminLoginController::class, 'adminRegister']);
Route::post('Adminlogin', [AdminLoginController::class, 'Adminlogin']);



//Search Product
Route::get('search', [SearchProductController::class, 'search']);
Route::get('filterSearchProduct', [SearchProductController::class, 'filterSearchProduct']);


//InfoProduct
Route::get('infoProduct', [InfoProductController::class, 'infoProduct']);
Route::post('addToCart', [InfoProductController::class, 'addToCart']);
Route::get('getInfoReviewProduct', [InfoProductController::class, 'getInfoReviewProduct']);
Route::post('updateQuantityProductInCart', [InfoProductController::class, 'updateQuantityProductInCart']);
Route::get('getRelativeProduct', [InfoProductController::class, 'getRelativeProduct']);
//Cart
Route::get('infoCart', [CartController::class, 'infoCart']);
Route::post('updateSelectedProperty', [CartController::class, 'updateSelectedProperty']);
Route::post('deleteItemCart', [CartController::class, 'deleteItemCart']);
Route::post('updateQuantityProperty', [CartController::class, 'updateQuantityProperty']);

//Payment + Payment Online VNPAY
Route::get('infoForPayment', [PaymentController::class, 'infoForPayment']);
Route::post('saveInfoForPayment', [PaymentController::class, 'saveInfoForPayment']);
Route::post('payOnline', [PayOnlineController::class, 'payOnline']);

//Payment Result
Route::post('processPaymentResult', [PaymentController::class, 'processPaymentResult']);

//MyOrder 
Route::get('getInfoMyOrder', [MyOrderController::class, 'getInfoMyOrder']);
Route::get('infoOrderDetail_myOder', [MyOrderController::class, 'infoOrderDetail_myOder']);
Route::get('getQuantityOrderToDevidePage__myOder', [MyOrderController::class, 'getQuantityOrderToDevidePage__myOder']);

//Info Account
Route::get('getInfoAccount', [InfoAccountController::class, 'getInfoAccount']);
Route::post('saveInfoAccount', [InfoAccountController::class, 'saveInfoAccount']);
Route::post('changePassword', [InfoAccountController::class, 'changePassword']);

//ReviewProduct
Route::post('saveReviewProduct', [ReviewProduct::class, 'saveReviewProduct']);

//Collection
Route::get('getQuantityCollectionToDevidePage', [CollectionController::class, 'getQuantityCollectionToDevidePage']);
Route::get('getInfoCollection', [CollectionController::class, 'getInfoCollection']);


//Admin - AddProduct
Route::post('addProduct', [AddProductController::class, 'addProduct']);
Route::get('getInfoForAddProduct', [AddProductController::class, 'getInfoForAddProduct']);
Route::get('linkImageProduct', [AddProductController::class, 'linkImageProduct']);
Route::get('updateQuantity', [AddProductController::class, 'updateQuantity']);
Route::get('getDetailCategory2', [AddProductController::class, 'getDetailCategory2']);



//Admin - ManageOrder
Route::get('getQuantityOrderToDevidePage', [AdminManageOrderController::class, 'getQuantityOrderToDevidePage']);
Route::get('getInfoManageOrder', [AdminManageOrderController::class, 'getInfoManageOrder']);
Route::get('infoOrderDetail', [AdminManageOrderController::class, 'infoOrderDetail']);
Route::post('saveNote', [AdminManageOrderController::class, 'saveNote']);
Route::post('updateOrderStatus', [AdminManageOrderController::class, 'updateOrderStatus']);

//Admin - SearchOrder
Route::get('getInfoSearchOrder', [AdminManageOrderController::class, 'getInfoSearchOrder']);
Route::get('getQuantityOrderToDevidePage_Search', [AdminManageOrderController::class, 'getQuantityOrderToDevidePage_Search']);


Route::get('testtest', [TestController::class, 'testtest']);
Route::get('testlaythongtin', [TestController::class, 'testlaythongtin']);


//Admin - ManageProduct
Route::get('getQuantityProductToDevidePage', [ManageProductController::class, 'getQuantityProductToDevidePage']);
Route::get('getInfoManageProduct', [ManageProductController::class, 'getInfoManageProduct']);
Route::get('infoProductDetail', [ManageProductController::class, 'infoProductDetail']);
Route::post('deleteProduct', [ManageProductController::class, 'deleteProduct']);

//Admin - SearchProduct
Route::get('getInfoSearchProductAdmin', [ManageProductController::class, 'getInfoSearchProductAdmin']);
Route::get('getQuantityProductToDevidePage_SearchProductAdmin', [ManageProductController::class, 'getQuantityProductToDevidePage_SearchProductAdmin']);
 
//Admin - UpdateProduct
Route::post('updateProduct', [UpdateProductController::class, 'updateProduct']);
Route::post('updateProduct2', [UpdateProductController::class, 'updateProduct2']);


//Admin - ManageAccountStaff 
Route::get('getQuantityAccountStaffToDevidePage', [ManageAccountStaff::class, 'getQuantityAccountStaffToDevidePage']);
Route::get('getInfoManageAccountStaff', [ManageAccountStaff::class, 'getInfoManageAccountStaff']);
Route::post('deleteAccountStaff', [ManageAccountStaff::class, 'deleteAccountStaff']);
Route::post('updateStatusOfAccountStaff', [ManageAccountStaff::class, 'updateStatusOfAccountStaff']);
Route::get('searchAccountStaff', [ManageAccountStaff::class, 'searchAccountStaff']);

//Admin - ManageAccountCustomer 
Route::get('getQuantityAccountCustomerToDevidePage', [ManageAccountCustomerController::class, 'getQuantityAccountCustomerToDevidePage']);
Route::get('getInfoManageAccountCustomer', [ManageAccountCustomerController::class, 'getInfoManageAccountCustomer']);
Route::get('searchAccountCustomer', [ManageAccountCustomerController::class, 'searchAccountCustomer']);

//Admin - Voucher
Route::post('addVoucher', [VoucherController::class, 'addVoucher']);
Route::get('getQuantityVoucherToDevidePage', [VoucherController::class, 'getQuantityVoucherToDevidePage']);
Route::get('getInfoManageVoucher', [VoucherController::class, 'getInfoManageVoucher']);
Route::get('infoVoucherDetail', [VoucherController::class, 'infoVoucherDetail']);
Route::post('updateVoucher', [VoucherController::class, 'updateVoucher']);
Route::get('getInfoSearchVoucher', [VoucherController::class, 'getInfoSearchVoucher']);
Route::post('deleteVoucher', [VoucherController::class, 'deleteVoucher']);

//Admin - Statistic
Route::get('statisticRevenue', [StatisticController::class, 'statisticRevenue']);
Route::get('performModifyDataset', [StatisticController::class, 'performModifyDataset']);
Route::get('statisticOrderAndPayMethod', [StatisticController::class, 'statisticOrderAndPayMethod']);
Route::get('getTopProducts', [StatisticController::class, 'getTopProducts']);
Route::get('getTopKHACHHANG', [StatisticController::class, 'getTopKHACHHANG']);

//SETDATA
Route::get('setdata', [SetDataController::class, 'setdata']);
