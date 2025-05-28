<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CityRamasageCity extends Model
{
    use HasFactory;
    protected $table = 'delevery_cities_relation_ramassage';
    protected $primaryKey = ['cities_id','ramassage_city_id'];
    protected $fillable = [
        'city_id',
        'ramassage_city_id',
        'customer_id',
        'is_pers',
        'prix_livraison',
        'prix_retour',
        'prix_refuse',
        'delai_livraison',
    ];

    public function getCityFeesByName($cityId,$ramassage_city_id,$customerId,$what_getfees)
    {
    
       
        if ($customerId != null) {
         
            $ramasageCityId = DB::table('delevery_cities_relation_ramassage')->where('city_id', '=', $cityId)
            ->where('ramasage_city_id', '=', $ramassage_city_id)
            ->where('customer_id', '=', $customerId)
            ->get();
           
            foreach ($ramasageCityId as $item) {
              if($what_getfees == 'prix_livraison'){
                return $item->prix_livraison;
              }else if($what_getfees == 'prix_retour'){
                return $item->prix_retour;
              }else if($what_getfees == 'prix_refuse'){
                  return $item->prix_refuse;
              }else{
                return  null;
              }
              
            }
        
          
        }else{
            $ramasageCityId = DB::table('delevery_cities_relation_ramassage')->where('city_id', '=', $cityId)
            ->where('ramasage_city_id', '=', $ramassage_city_id)
            ->where('customer_id','is', NULL)
            ->get();	;
           
            foreach ($ramasageCityId as $item) {
           
              if($what_getfees == 'prix_livraison'){
                return $item->prix_livraison;
              }else if($what_getfees == 'prix_retour'){
                return $item->prix_retour;
              }else if($what_getfees == 'prix_refuse'){
                  return $item->prix_refuse;
              }else{
                return  null;
              }
            }
        
        }
        
        return null;
    }
}
