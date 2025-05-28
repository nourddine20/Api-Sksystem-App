<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Pickuprequest extends Model
{
    use HasFactory;

    protected $table = 'delevery_pickup_request';
    protected $primaryKey = 'pickup_request_id';

    protected $fillable = [
        'pickup_request_id',
        'pickup_request_customer',
        'pickup_request_type',
        'pickup_request_data_id',
        'pickup_request_phone',
        'pickup_request_ramassage_city',
        'pickup_request_address',
        'pickup_request_note',
        'pickup_request_time',
        'pickup_request_statut',
        'pickup_request_affected_to',
    ];
    
    
    function newPickUpRequest($cstmrId, $type, $dataId, $phone, $request_ramassage_city, $address, $note, $statut)
    {
        try {
            $newRequestId = DB::table('delevery_pickup_request')->insertGetId([
                'pickup_request_customer' => $cstmrId,
                'pickup_request_type' => $type,
                'pickup_request_data_id' => $dataId,
                'pickup_request_phone' => $phone,
                'pickup_request_ramassage_city' => $request_ramassage_city,
                'pickup_request_address' => $address,
                'pickup_request_note' => $note,
                'pickup_request_time' => time(),
                'pickup_request_statut' => $statut,
                'pickup_request_affected_to' => 0,
            ]);
    
            return $newRequestId;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            // Handle the exception as needed
            return null;
        }
    }

    
   public function totalCstmrNewPickUpRequests($cstmrId)
    {
        try {
            
            $numRequests = DB::table('delevery_pickup_request')
                ->where('pickup_request_customer', $cstmrId)
                ->where('pickup_request_statut', 'NEW')
                ->count();
      
            return $numRequests;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            // Handle the exception as needed
            return 0;
        }
    }
    
    

}
