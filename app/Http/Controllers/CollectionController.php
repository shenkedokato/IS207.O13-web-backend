<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function getQuantityCollectionToDevidePage(Request $request){ 
        $mapl_sp = $request->mapl_sp; 
        $mapl_sp2 = $request->mapl_sp2;
        $query_data = $request->query_data;
        $quantity = 0;
        if($query_data == null){
            $quantity = DB::select(
                "SELECT COUNT(MASP) AS SL_MASP, MAPL_SP
                FROM sanphams
                WHERE MAPL_SP = $mapl_sp
                AND MAPL_SP2 = $mapl_sp2
                GROUP BY MAPL_SP"
            ); 
        }
        else{
            $quantity = DB::select(
                "SELECT COUNT(MASP) AS SL_MASP
                FROM sanphams
                WHERE TENSP LIKE '%$query_data%'"
            );
        }

        return response()->json([
            'quantity'=> $quantity, 
        ]); 
    }
    public function getInfoCollection(Request $request){
        $collection = $request->input('collection');
        $start = $request->input('start');
        $numberOrderEachPage = $request->input('numberOrderEachPage');
        $mapl_sp = $request->input('mapl_sp');
        $mapl_sp2 = $request->input('mapl_sp2');
        $filter = $request->input('filter');
        $query_data = $request->query_data;
        $quantity = 0;
  
        $orderList_DB = [];    
        $data_queryOrMAPL = "";
        if($query_data == null){
            $data_queryOrMAPL =  "AND MAPL_SP = $mapl_sp
                                AND MAPL_SP2 = $mapl_sp2"; 
        }
        else{
            $data_queryOrMAPL =  "AND TENSP LIKE '%$query_data%'"; 
        }
        $data_query =  
            "SELECT * FROM sanphams, hinhanhsanphams 
            WHERE hinhanhsanphams.masp = sanphams.masp 
            AND MAHINHANH LIKE '%thumnail%'  
            $data_queryOrMAPL";
        
        $data_query2 = "LIMIT $start, $numberOrderEachPage";

        if($filter == 'moinhat'){
            // $orderList_DB = DB::select("SELECT * from sanphams where TENSP LIKE '%$tensp%' ORDER BY created_at DESC");
            $orderList_DB = DB::select("$data_query ORDER BY created_at DESC $data_query2"); 
        }
        else if($filter == 'banchay'){ 
            $orderList_DB_GetQuantity = DB::select( 
                "SELECT sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                from chitiet_donhangs, sanphams, hinhanhsanphams, sanpham_mausac_sizes
                where chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP 
                AND hinhanhsanphams.masp = sanphams.masp
                AND sanpham_mausac_sizes.masp = sanphams.masp
                AND MAHINHANH LIKE '%thumnail%'  
                $data_queryOrMAPL
                group by sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                order by SUM(chitiet_donhangs.SOLUONG) DESC"
            ); 
            $orderList_DB = DB::select( 
                "SELECT sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                from chitiet_donhangs, sanphams, hinhanhsanphams, sanpham_mausac_sizes
                where chitiet_donhangs.MAXDSP = sanpham_mausac_sizes.MAXDSP 
                AND hinhanhsanphams.masp = sanphams.masp
                AND sanpham_mausac_sizes.masp = sanphams.masp
                AND MAHINHANH LIKE '%thumnail%'  
                $data_queryOrMAPL
                group by sanphams.MASP, TENSP, GIAGOC, GIABAN, imgURL
                order by SUM(chitiet_donhangs.SOLUONG) DESC
                $data_query2"
            ); 
            $quantity = [
                [
                    'SL_MASP' => count($orderList_DB_GetQuantity),
                    'MAPL_SP' => $mapl_sp
                ]
            ];
        }
        else if($filter == 'thapDenCao'){
            $orderList_DB = DB::select("$data_query ORDER BY GIABAN ASC $data_query2");
        }
        else if($filter == 'caoDenThap'){
            $orderList_DB = DB::select("$data_query ORDER BY GIABAN DESC $data_query2");
        } 

        
        if($query_data == null && $quantity == 0){
            $quantity = DB::select(
                "SELECT COUNT(MASP) AS SL_MASP, MAPL_SP
                FROM sanphams
                WHERE MAPL_SP = $mapl_sp
                AND MAPL_SP2 = $mapl_sp2
                GROUP BY MAPL_SP"
            ); 
        }
        else if($quantity == 0){
            $quantity = DB::select(
                "SELECT COUNT(MASP) AS SL_MASP
                FROM sanphams
                WHERE TENSP LIKE '%$query_data%'"
            );
        }

        return response()->json([
            'orderList_DB' => $orderList_DB, 
            'quantity' => $quantity,
        ]);
    }
}
