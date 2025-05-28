<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Parcel extends Model
{
    use HasFactory;
    protected $table = 'delevery_parcel';
    protected $primaryKey = 'parcel_id';
    protected $fillable = [
        'parcel_id',
        'parcel_code',
        'parcel_amana_code',
        'parcel_receiver',
        'parcel_phone',
        'parcel_city',
        'parcel_agence',
        'parcel_ramassage_city',
        'parcel_peux_ovrire',
        'parcel_fragile',
        'parcel_district',
        'parcel_price',
        'parcel_address',
        'parcel_note',
        'parcel_situation',
        'parcel_status',
        'parcel_amana_last_statut',
        'parcel_status_second',
        'parcel_livreur',
        'parcel_delivered_by',
        'parcel_ramassage_by',
        'parcel_refused_by',
        'parcel_paid_livreur',
        'parcel_refuse_livreur',
        'parcel_paid_city',
        'parcel_refuse_city',
        'parcel_returned_city',
        'parcel_returned_livreur',
        'parcel_returned_customer',
        'parcel_invoiced',
        'parcel_edit_request',
        'parcel_edited',
        'parcel_delivered_fees',
        'parcel_returned_fees',
        'parcel_refused_fees',
        'parcel_scanner_pickup',
        'parcel_scanner_sent',
        'parcel_scanner_distrubition',
        'parcel_scanner_paid',
        'parcel_scanner_returned',
        'parcel_customer',
        'parcel_replace',
        'parcel_time',
        'parcel_pickup_time',
        'parcel_sent_time',
        'parcel_delivery_time',
        'parcel_last_update',
        'parcel_from_stock',
        'parcel_stock_ready',
        'parcel_product_name',
        'parcel_product_qty'
    ];


    public function newParcelCode($cityCode, $cstmrId, $parcelId)
    {
        $rdmLettres = strtoupper(chr(rand(65, 90)) . chr(rand(65, 90)));

        $newParcelCode = $cityCode . date("m") . date("y") . $parcelId . $rdmLettres;
        $num_code = self::where('parcel_code', $newParcelCode)->count();

        if ($num_code != 0) {
            return self::newParcelCode($cityCode, $cstmrId, $parcelId);
        } else {
            return $newParcelCode;
        }
    }
    
    
    public function removeParcelFromDn($choose_parcel_id){
        
          $deleted_parcel = DB::table('delevery_dn_parcels')
            ->where([
                'dn_parcels_parcel' => $choose_parcel_id
            ])->delete();
            
        return $deleted_parcel;
    }
    
    
    public function getParcelId($parcelcode){
         $getparcel_data = DB::table('delevery_parcel')
            ->where('parcel_code', $parcelcode)
            ->value('parcel_id');
    
          return $getparcel_data;
    }
    
    
    public function getParceInfo($parcel_what,$ParcelId){
       
       
       if($parcel_what == "code"){
              $getparcel_data = DB::table('delevery_parcel')
    ->where('parcel_id', $ParcelId)
    ->value('parcel_code');
            
       }
       
      if($parcel_what == "city"){
           
              $getparcel_data = DB::table('delevery_parcel')
    ->where('parcel_id', $ParcelId)
    ->value('parcel_city');
            
       }
       
        if($parcel_what == "livreur"){
           
              $getparcel_data = DB::table('delevery_parcel')
    ->where('parcel_id', $ParcelId)
    ->value('parcel_livreur');
    
  
            
       }
    
    
      
        if($parcel_what == "comment"){
           
              $getparcel_data = DB::table('delevery_parcel')
    ->where('parcel_id', $ParcelId)
    ->value('parcel_history_comment');
    
   
            
       }
    
         
          
       return $getparcel_data;
    }


   
    public function getParceHistoryInfo($historyparcel_what,$HistoryParcelId){
       
       
       $gethistoryparcel_livreur = DB::table('delevery_parcel_history')
    ->where('parcel_history_id', $HistoryParcelId)
    ->value('parcel_history_livreur');
    
       
        if($historyparcel_what == "livreur"){
           
              $gethistoryparcel_data = DB::table('delevery_users')
    ->where('users_id', $gethistoryparcel_livreur)
    ->value('users_name').' '.DB::table('delevery_users')
    ->where('users_id', $gethistoryparcel_livreur)
    ->value('users_phone');
    
  
            
       }
    
    
      
        if($historyparcel_what == "comment"){
           
              $gethistoryparcel_data = DB::table('delevery_parcel_history')
    ->where('parcel_history_id', $HistoryParcelId)
    ->value('parcel_history_comment');
    
   
            
       }
    
         
          
       return $gethistoryparcel_data;
    }




    public function addParcelHistory($parcelId, $parcelCity, $parcelSituation, $parcelStatus,$parcel_livreur, $parcelBy)
    {
        $now = time();
    
        DB::table('delevery_parcel_history')->insert([
            'parcel_history_parcel' => $parcelId,
            'parcel_history_city' => $parcelCity,
            'parcel_history_situation' => $parcelSituation,
            'parcel_history_status' => $parcelStatus,
            'parcel_history_time' => $now,
            'parcel_history_livreur' => $parcel_livreur ?? null,
            'parcel_history_by' => $parcelBy,
            'changed_by' => 0,
        ]);
    
        $new_history_id = DB::getPdo()->lastInsertId();
    
        DB::table('delevery_parcel')
            ->where('parcel_id', $parcelId)
            ->update(['parcel_last_update' => $now]);
    
        // sendParcelSMS($parcelId, $parcelStatus);
    
        return $new_history_id;
    }
    
}
