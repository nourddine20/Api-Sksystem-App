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
use Illuminate\Support\Facades\Validator;
use App\Models\Pickuprequest;

class PickuprequestController extends Controller
{
    
  public function createpickuprequest(Request $request){
      
    
         try {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:SIMPLE_PARCEL,STOCK', // Add other validation rules as needed
            'remark' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);
    
    var_dump(Pickuprequest::totalCstmrNewPickUpRequests(Auth::user()['customers_id']));
        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }else if(Pickuprequest::totalCstmrNewPickUpRequests(Auth::user()['customers_id']) >= 3){
            
              var_dump('hello is create controller');
      
              return response([
                'message' => 'Vous avez déja 3 demandes de ramassage',
                'errors' => 'error',
            ], 422);
            
        }
        
        $cstmrId = Auth::user()['customers_id'];
        $type = $request->type;
        $phone = $request->phone;
        $request_ramassage_city =Auth::user()['customers_ramassage_cities_id'];
        $address  = $request->address;
        $note = $request->remark;
        
       $IdPickupRequestInserted =  Pickuprequest::newPickUpRequest($cstmrId, $type, 0, $phone, $request_ramassage_city, $address, $note, 'NEW');
    
            //  // If validation passes, create a new pickup request
            // $pickupRequest = new Pickuprequest();  // Assuming your model is named Pickuprequest
            // $pickupRequest->pickup_request_type = $request->input('type');
            // $pickupRequest->pickup_request_note = $request->input('remark');
            // $pickupRequest->pickup_request_customer = Auth::user()['customers_id'];
           
            // $pickupRequest->pickup_request_phone = $request->input('phone');
            // $pickupRequest->pickup_request_address = $request->input('address');
            // // Add other fields as needed
        
            // // Save the pickup request
            // $pickupRequest->save();
        
            return response([
                'message' => 'Pickup request created successfully',
                'data' => $IdPickupRequestInserted,
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
          
  }
    
  public function getpickuprequests(Request $request)
{
    
    try {
        
       
         $GetPickupRequestsCustomer = DB::table('delevery_pickup_request')
    ->join('delevery_ramassage_cities', 'delevery_pickup_request.pickup_request_ramassage_city', '=', 'delevery_ramassage_cities.ramassage_cities_id')
    ->whereNotNull('delevery_pickup_request.pickup_request_ramassage_city')
    ->where('delevery_pickup_request.pickup_request_ramassage_city', '>', 0)
    ->where('delevery_pickup_request.pickup_request_customer', Auth::user()['customers_id'])
    ->select('delevery_pickup_request.*', 'delevery_ramassage_cities.*')
    ->orderByDesc('delevery_pickup_request.pickup_request_time') // Add this line for ordering
    ->get();
    
        
      return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $GetPickupRequestsCustomer
        ], 200);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    
}

    
  
    
}