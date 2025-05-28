<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Http\Controllers\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\CityRamasageCity;
use App\Models\RamasageCity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    //
 

public function get_generalStatistics()
{
    try {
        $allTotalPriceParcels = DB::table('delevery_parcel')
            ->where('parcel_customer', Auth::user()['customers_id'])
            ->sum('parcel_price');

        $allTotalPriceLivre = DB::table('delevery_parcel')
            ->where([
                'parcel_customer' => Auth::user()['customers_id'],
                'parcel_status' => 'DELIVERED'
            ])
            ->sum('parcel_price');
$allTotalOurCommission  =  DB::table('delevery_parcel')
            ->where([
                'parcel_customer' => Auth::user()['customers_id'],
                'parcel_status' => 'DELIVERED'
            ])
            ->sum('parcel_delivered_fees');
            
          
            
            
        $allTotalRevenueNet =$allTotalPriceLivre - $allTotalOurCommission;


        $allParcelsCount = DB::table('delevery_parcel')
            ->where('parcel_customer', Auth::user()['customers_id'])
            ->count();
            
             $TotalNbrParcelsLivre  = DB::table('delevery_parcel')
            ->where([
                'parcel_customer' => Auth::user()['customers_id'],
                'parcel_status' => 'DELIVERED'
            ])->count();
            
             $TotalNbrParcelsEnProcess  =  DB::table('delevery_parcel')
             ->where('parcel_customer' , Auth::user()['customers_id'])
             ->where('parcel_status',"IN_PROGRESS")->count();
             
                $TotalNbrParcelsNouveau  = DB::table('delevery_parcel')
             ->where('parcel_customer' , Auth::user()['customers_id'])
             ->whereIn('parcel_status',['WAITING_PICKUP', 'NEW_PARCEL'])->count();
            
            $TotalNbrParcelsPreparation =  DB::table('delevery_parcel')
             ->where('parcel_customer' , Auth::user()['customers_id'])
             ->whereIn('parcel_status',['RECEIVED', 'SENT','PICKED_UP'])->count();
            
             $TotalNbrParcelsReturn  = DB::table('delevery_parcel')
            ->where([
                'parcel_customer' => Auth::user()['customers_id'],
                'parcel_status' => ['RETURNED','REFUSE','CANCELED']
            ])->count();
   
  
    
        $array_data = [
            'allTotalPriceParcels' => (int)$allTotalPriceParcels,
            'allTotalPriceLivre' => (int)$allTotalPriceLivre,
            'allTotalRevenueNet' => $allTotalRevenueNet,
            'allParcelsCount' => (int)$allParcelsCount,
            'TotalNbrParcelsLivre'=>(int)$TotalNbrParcelsLivre,
            'TotalNbrParcelsEnProcess'=>(int)$TotalNbrParcelsEnProcess,
            'TotalNbrParcelsNouveau'=>(int)$TotalNbrParcelsNouveau,
            'TotalNbrParcelsPreparation'=>(int)$TotalNbrParcelsPreparation,
            'TotalNbrParcelsReturn'=>(int)$TotalNbrParcelsReturn
        ];

           
        return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $array_data
        ], 200);
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error($e);

        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}
  
}
