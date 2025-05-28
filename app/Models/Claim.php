<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'delevery_claims'; // Ensure this matches your actual table name
    protected $primaryKey = 'claims_id';

    protected $fillable = [
        'claims_customer',
        'parcel_id',
        'claims_time',
        'claims_object',
        'claims_statut',
    ];

    /**
     * Create a new claim in the database.
     *
     * @param int $customerId ID of the customer making the claim
     * @param int $parcelId ID of the parcel related to the claim
     * @param string $object Description or object of the claim
     * @param int $statut Status of the claim
     * @return int|null The new claim ID or null on failure
     */
    public static function createClaim($customerId, $parcelId, $object, $statut)
    {
        try {
            $newClaimId = DB::table('delevery_claims')->insertGetId([
                'claims_customer' => $customerId,
                'parcel_id' => $parcelId,
                'claims_time' => time(), // Set current time as claim time
                'claims_object' => $object,
                'claims_statut' => $statut,
            ]);

            return $newClaimId;
        } catch (\Exception $e) {
            \Log::error('Failed to create a new claim: ' . $e->getMessage());
            return null;
        }
    }
    
        public static function createClaimMsg($new_claim_id,$claim_created_by,$customer_id,$claim_msg)
    {
        try {
            $newClaimMsgId = DB::table('delevery_claims_msg')->insertGetId([
                'claims_msg_claim'=>$new_claim_id,
                'claims_msg_from_id'=>$customer_id,
                'claims_msg_from' => $claim_created_by,
                'claims_msg_time'=>time(),
                'claims_msg_text'=>$claim_msg
    
             
            ]);

            return $newClaimMsgId;
        } catch (\Exception $e) {
            \Log::error('Failed to create a new Msg claim: ' . $e->getMessage());
            return null;
        }
    }
}
