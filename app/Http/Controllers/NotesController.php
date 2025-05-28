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

class NotesController extends Controller
{
    
    public function getInvoicenotes(){
         try{
              $GetInvoiceNotesCustomer = DB::table('delevery_invoice')
                ->where('invoice_customer', Auth::user()['customers_id'])
                ->get();
                
                
           
            
              return response([
                'message' => 'The Statistics data was retrieved successfully',
                'data' => $GetInvoiceNotesCustomer
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
    
    
    public function CreateDeliverynote(){
        
        
        $dnId = DB::table('delevery_delivery_note')->insertGetId([
            'delivery_note_customer' => Auth::user()->customers_id,
            'delivery_note_time'=>time(),
            'delivery_note_delivered'=>0
        ]);

        
        
        $cstmrId = Auth::user()->customers_id; // assuming this is your $cstmrId

        $newDnRef = "BL-" . date("dmy") . '-0' . $dnId . '0-' . rand(10, 99) . '-' . $cstmrId;
        
     
          // Update the record with the generated reference
      $is_updated =   DB::table('delevery_delivery_note')
            ->where('delivery_note_id', $dnId)
            ->update(['delivery_note_ref' => $newDnRef]);
            
           
           
        if($is_updated > 0){
             
             return response([
                'message' => 'The delivery note was created with Success',
                'new_delivery_note_ref'=>$newDnRef
            ], 201);
        } else {
            return response([
                'message' => 'Error: Cannot create  deliverynote'
            ], 401);
        }
        
         

    }
    
    
    public function DeleteParcelFromDeliverynote(Request $request){
        
          $parcel_id = $request->parcel_id;
        $deliverynote_ref = $request->deliverynote_ref;
        
         Parcel::find($parcel_id)->update([
                'parcel_status' => "NEW_PARCEL",
            ]);
            
    
    //   Parcel::addParcelHistory($parcel_id,Parcel::getParceInfo("city",$parcel_id),"NOT_PAID","WAITING_PICKUP",NULL,0);

            
            $getdelivertnote_id = DB::table('delevery_delivery_note')
            ->where('delivery_note_ref', $deliverynote_ref)
            ->value('delivery_note_id');
        
         $now = time();
    
      $getparceltodelivertnote_id =  DB::table('delevery_dn_parcels')
    ->where('dn_parcels_dn', $getdelivertnote_id)
    ->where('dn_parcels_parcel', $parcel_id)
    ->delete();
        
        
         
         if($getparceltodelivertnote_id){
             
             return response([
                'message' => 'The parcel was Deleted from Delivery note with Success'
            ], 201);
        } else {
            return response([
                'message' => 'Error: Cannot create your parcel'
            ], 401);
        }
        
        
        
    }
    
    
    public function DeleteDeliverynote(Request $request){
       
              
        $deliverynote_ref = $request->deliverynote_ref;
         
            $delivery_notes = DB::table('delevery_delivery_note')
    ->where('delivery_note_ref', $deliverynote_ref)
    ->get();
  
    foreach($delivery_notes as $dn){
      
        $delevery_note_id = $dn->delivery_note_id;
        $delivery_note_delivered = $dn->delivery_note_delivered;
        
    }
          
             $getparcelsIds = DB::table('delevery_dn_parcels')
    ->where('dn_parcels_dn', $delevery_note_id)
    ->value('dn_parcels_parcel');  
    
   $getparcelsCount = DB::table('delevery_dn_parcels')
    ->where('dn_parcels_dn', $delevery_note_id)
    ->count();
    
    //   Parcel::addParcelHistory($parcel_id,Parcel::getParceInfo("city",$parcel_id),"NOT_PAID","WAITING_PICKUP",NULL,0);



if($delivery_note_delivered == 1 || $delivery_note_delivered == '1'){
       return response([
                'message' => 'Erreur : Impossible de supprimer votre bon de livraison avec le statut de recu '
            ], 500);
}elseif($getparcelsCount != 0){
        return response([
                'message' => 'Erreur : Impossible de supprimer votre bon de livraison contien des colis '
            ], 500);
}
    
  
            
            $getdeleteddelivertnote = DB::table('delevery_delivery_note')
            ->where('delivery_note_ref', $deliverynote_ref)
            ->delete();
        
         $now = time();
    
   
        
         
         if($getdeleteddelivertnote){
             
             return response([
                'message' => 'Le bon de livraison a été supprimé  avec succès'
            ], 201);
        } else {
            return response([
                'message' => 'Erreur : Impossible de supprimer Le bon de livraison'
            ], 500);
        }
        
        
        

        

        
    }
    
    
    public function AddParcelToDeliverynote(Request $request){
    
        $parcel_id = $request->parcel_id;
        $deliverynote_ref = $request->deliverynote_ref;
        
         Parcel::find($parcel_id)->update([
                'parcel_status' => "WAITING_PICKUP",
            ]);
            
    
       Parcel::addParcelHistory($parcel_id,Parcel::getParceInfo("city",$parcel_id),"NOT_PAID","WAITING_PICKUP",NULL,0);

            
            $getdelivertnote_id = DB::table('delevery_delivery_note')
            ->where('delivery_note_ref', $deliverynote_ref)
            ->value('delivery_note_id');
        
         $now = time();
    
      $getparceltodelivertnote_id =   DB::table('delevery_dn_parcels')->insert([
            
            'dn_parcels_dn' => $getdelivertnote_id,
            'dn_parcels_parcel' => $parcel_id,
            'dn_parcels_time' => $now,
        
        ]);
        
        
         
         if($getparceltodelivertnote_id){
             
             return response([
                'message' => 'The parcel was added to Delivery note with Success'
            ], 201);
        } else {
            return response([
                'message' => 'Error: Cannot create your parcel'
            ], 401);
        }
        
        
        
    }
    
    public function getParcelsByTypeEdit(Request $request){
        
        $delivery_note_ref = $request->deliverynote_ref;
              try{
            
   
$getParcelsByTypeEdit = [];
    
   
            
            if($request->type_action == "add"){
                
                
        $customerId = Auth::user()->customers_id;

        $getParcelsByTypeEdit = DB::table('delevery_parcel')
            ->join('delevery_cities', 'delevery_parcel.parcel_city', '=', 'delevery_cities.cities_id')
            ->join('delevery_parcel_history', function ($join) {
                $join->on('delevery_parcel.parcel_id', '=', 'delevery_parcel_history.parcel_history_parcel')
                     ->whereColumn('delevery_parcel_history.parcel_history_id', '=', DB::raw("(SELECT MAX(parcel_history_id) FROM delevery_parcel_history WHERE parcel_history_parcel = delevery_parcel.parcel_id)"));
            })
            ->where('delevery_parcel.parcel_customer', $customerId)
            ->whereIn('delevery_parcel.parcel_status', ['NEW_PARCEL'])
            ->select('delevery_parcel.*', 'delevery_cities.cities_nom', 'delevery_parcel_history.parcel_history_status', 'delevery_parcel_history.parcel_history_data AS reported_date')
            ->orderByDesc('delevery_parcel_history.parcel_history_id')
            ->get();

    
                
            }else if($request->type_action == "delete"){
                
                
                   $getParcelsByTypeEdit = DB::table('delevery_delivery_note')
    ->select(
        'delevery_parcel.parcel_id',
        'delevery_parcel.parcel_code',
        'delevery_parcel.parcel_receiver',
        'delevery_parcel.parcel_phone',
        'delevery_parcel.parcel_city',
        'delevery_parcel.parcel_price',
        'delevery_parcel.parcel_time'
    )
    ->join('delevery_dn_parcels', 'delevery_dn_parcels.dn_parcels_dn', '=', 'delevery_delivery_note.delivery_note_id')
    ->join('delevery_parcel', 'delevery_parcel.parcel_id', '=', 'delevery_dn_parcels.dn_parcels_parcel')
    ->where('delevery_delivery_note.delivery_note_customer', Auth::user()['customers_id'])
    ->where('delevery_delivery_note.delivery_note_ref', '=', $delivery_note_ref)
    ->groupBy(
        'delevery_parcel.parcel_id',
        'delevery_parcel.parcel_code',
        'delevery_parcel.parcel_receiver',
        'delevery_parcel.parcel_phone',
        'delevery_parcel.parcel_city',
        'delevery_parcel.parcel_price',
        'delevery_parcel.parcel_time'
    )
    ->get();
    
    
            }

            
              return response([
                'message' => 'The Statistics data was retrieved successfully',
                'data' => $getParcelsByTypeEdit
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
    
    
    public function getDeliverynotes(){
        
        try{
            
  $GetDeliveryNotesCustomer = DB::table('delevery_delivery_note')
    ->select('delevery_delivery_note.*', DB::raw('COUNT(delevery_parcel.parcel_id) as num_parcels'), DB::raw('CASE WHEN delevery_delivery_note.delivery_note_delivered = 0 THEN "nouveau" ELSE "recu" END AS delivery_note_delivered_name'))
    ->leftJoin('delevery_dn_parcels', 'delevery_dn_parcels.dn_parcels_dn', '=', 'delevery_delivery_note.delivery_note_id')
    ->leftJoin('delevery_parcel', 'delevery_parcel.parcel_id', '=', 'delevery_dn_parcels.dn_parcels_parcel')
    ->where('delevery_delivery_note.delivery_note_customer', Auth::user()['customers_id'])
    ->groupBy('delevery_delivery_note.delivery_note_id', 'delevery_delivery_note.delivery_note_customer', 'delevery_delivery_note.delivery_note_ref', 'delevery_delivery_note.delivery_note_time', 'delevery_delivery_note.delivery_note_delivered')
    ->orderByDesc('delevery_delivery_note.delivery_note_time') // Ordering in descending order
    ->get();


            
              return response([
                'message' => 'The Statistics data was retrieved successfully',
                'data' => $GetDeliveryNotesCustomer
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
    
    
      public function getReturnednotes(){
        
        try{
              $GetReturnedNotesCustomer = DB::table('delevery_retrun_note')
                ->where('retrun_note_for', Auth::user()['customers_id'])
                ->where('retrun_note_type',3)
                ->get();
                
                
           
            
              return response([
                'message' => 'The Statistics data was retrieved successfully',
                'data' => $GetReturnedNotesCustomer
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