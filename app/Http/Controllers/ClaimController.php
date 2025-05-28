<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Http\Controllers\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Claim;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    
  public function createclaim(Request $request){
           try {
        $validator = Validator::make($request->all(), [
       
            'object' => 'required|string',
            'message' => 'required|string',
        ]);
    
   
   if(Parcel::getParcelId($request->type) != null && Parcel::getParcelId($request->type) != 0){
       $parcelId = Parcel::getParcelId($request->type);
   }else{
       $parcelId = NULL;
   }
   
        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
            
        }
        
        $cstmrId = Auth::user()['customers_id'];
        $type = $request->type;
        $object = $request->object;
      
        $message  = $request->message;
        
       
           $claim_parcel_count = DB::table('delevery_claims')
                    ->where('claims_customer',$cstmrId)
                    ->whereNotNull('parcel_id')
                    ->where('parcel_id', $parcelId)
                    ->count();
                    
        
                if($claim_parcel_count > 0){
                       return response([
                            'message' => 'Sorry ! Parcel has already Claim created ',
                           
                        ], 500);
                        
                }else{
                    
               
                      $IdClaimInserted =  Claim::createClaim($cstmrId, $parcelId, $object,1);
                   
                 $IdClaimMsgInserted  =  Claim::createClaimMsg($IdClaimInserted,'1',$cstmrId,$message);
                
                 
                    
                        return response([
                            'message' => 'Claim and ClaimMsg  created successfully',
                            'data' => $IdClaimMsgInserted,
                        ], 200);
                        
                }  
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
  }
    
 public function getclaims(Request $request)
{
    try {
        $GetClaimsCustomer = [];
        
      
        if ($request->claims_type == 'general') {
            
            $GetClaimsCustomer = DB::table('delevery_claims')
                ->where('parcel_id', null)
                ->where('claims_customer', Auth::user()['customers_id'])
                ->orderByDesc('delevery_claims.claims_time')
                ->get();
        } else if ($request->claims_type == 'parcels') {
            $GetClaimsCustomer = DB::table('delevery_claims')
                ->join('delevery_parcel', 'delevery_claims.parcel_id', '=', 'delevery_parcel.parcel_id')
                ->whereNotNull('delevery_claims.parcel_id')
                ->where('delevery_claims.parcel_id', '>', 0)
                ->where('delevery_claims.claims_customer', Auth::user()['customers_id'])
                ->select('delevery_claims.*', 'delevery_parcel.*')
                ->orderByDesc('delevery_claims.claims_time')
                ->get();
        }
        
        
        $claims_statut_color = '';
        // Iterate through the result set and add claims_statut_color attribute
        foreach ($GetClaimsCustomer as $claim) {
            switch ($claim->claims_statut) {
                case 1:
                 $claims_statut_color= '0xFF0000FF';
                    break;
                case 2:
                  $claims_statut_color= '0xFFFFFF00';
                    break;
                case 3:
                    $claims_statut_color = '0xFF808080';
                    break;
                default:
                    // Handle other cases if needed
                    $claims_statut_color = null;
            }
            
            
               // Create a new array to include claims_statut_color and other columns
     
            $responseData[] = [
                'claims_id' => $claim->claims_id,
                'claims_customer' => $claim->claims_customer,
                'parcel_id' => $claim->parcel_id,
                   'parcel_code' => $claim->parcel_code ?? null,
                'claims_time' => $claim->claims_time,
                'claims_object' => $claim->claims_object,
                'claims_statut' => $claim->claims_statut,
                // Add other attributes as needed
                'claims_statut_color' => $claims_statut_color // Add claims_statut_color
            ];
        
        
        }

     

        return response([
            'message' => 'The Statistics data was retrieved successfully',
            'data' => $responseData
        ], 200);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function save_messages_claim(Request $request)
{
      try {
        $claim_id = $request->claims_id;
        $message =  $request->message_text;
 $cstmrId = Auth::user()['customers_id'];


 $IdClaimMsgInserted  =  Claim::createClaimMsg($claim_id,'1',$cstmrId,$message);



        if ($IdClaimMsgInserted <= 0) {
            return response()->json([
                'message' => 'No chat message saved for the given claim ID',
               
            ], 404);
        }

        return response()->json([
            'message' => 'Chat messages saved successfully',
           
        ], 200);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getchatclaim(Request $request)
{
   
    try {
        $claim_id = $request->claims_id;

$chatMessages = DB::table('delevery_claims_msg')
    ->select('claims_msg_id', 'claims_msg_claim', 'claims_msg_from','claims_msg_text', 'claims_msg_from_id', 'claims_msg_time', 
            
              DB::raw('DATE(FROM_UNIXTIME(claims_msg_time)) as date'),
              DB::raw('YEAR(FROM_UNIXTIME(claims_msg_time)) as year'),
              DB::raw('MONTH(FROM_UNIXTIME(claims_msg_time)) as month'),
              DB::raw('DAY(FROM_UNIXTIME(claims_msg_time)) as day'),
              DB::raw('HOUR(FROM_UNIXTIME(claims_msg_time)) as hour'),
              DB::raw('MINUTE(FROM_UNIXTIME(claims_msg_time)) as minute'),
              DB::raw('SECOND(FROM_UNIXTIME(claims_msg_time)) as second'),
              DB::raw('COUNT(*) as total_messages'))
    ->where('claims_msg_claim', $claim_id)
    ->groupBy('claims_msg_id', 'claims_msg_claim', 'claims_msg_from','claims_msg_text', 'claims_msg_from_id', 'claims_msg_time', 
            
               DB::raw('DATE(FROM_UNIXTIME(claims_msg_time))')
            
               )
    ->get();



        if ($chatMessages->isEmpty()) {
            return response()->json([
                'message' => 'No chat messages found for the given claim ID',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Chat messages retrieved successfully',
            'data' => $chatMessages,
        ], 200);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return response()->json([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
  
    
}