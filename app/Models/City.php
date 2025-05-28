<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class City extends Model
{
    use HasFactory;
    protected $table = 'delevery_cities';
    protected $primaryKey = 'cities_id';
    protected $fillable = [
        'cities_id',
        'code',
        'cities_nom',
        'statut',
        'cities_zone_id',
    ];

    public function ramasageCities()
    {
        return $this->belongsToMany(RamasageCity::class, 'delevery_cities_relation_ramassage');
    }

  

    public function getcityCode($cityId)
    {
        $city = DB::table('delevery_cities')
        ->where('cities_id', $cityId)
        ->first();

    if ($city) {
        return $city->code;
    }

        return null; // Handle the case when city is not found
    }


    public function getCityStatut($cityId)
{
    $city = DB::table('delevery_cities')
        ->where('cities_id', $cityId)
        ->first();

    if ($city) {
        return $city->statut;
    }

    return null;
}


public function checkCity($cityId)
{
    $num_cities = DB::table('delevery_cities')
        ->where('cities_id', $cityId)
        ->count();

    return $num_cities;
}


}
