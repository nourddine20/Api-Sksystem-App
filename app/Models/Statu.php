<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Language;



class Statu extends Model
{
    use HasFactory;
    protected $table = 'delevery_parcel_statut';
    protected $primaryKey = 'parcel_statut_id';
    protected $fillable = [
        'parcel_statut_id',
        'parcel_statut_type',
        'parcel_statut_code',
        'parcel_statut_name',
        'parcel_statut_color',
        'parcel_statut_sms_active',
        'parcel_statut_sms_text'
    ];


public function getstatu($statutCode, $statutWhat)
{
    // Assuming you have a model named ParcelStatut
    $parcelStatut = DB::table('delevery_parcel_statut')
        ->where('parcel_statut_code', $statutCode)
        ->first();

    if (!$parcelStatut) {
        // Handle the case when no record is found
        return null;
    }

      
    if ($statutWhat == "name") {
        $statutReturn = Language::langString($parcelStatut->parcel_statut_name);
    } elseif ($statutWhat == "name_var") {
        $statutReturn = $parcelStatut->parcel_statut_name;
    } elseif ($statutWhat == "color") {
        $statutReturn = $parcelStatut->parcel_statut_color;
    } else {
        // Handle the case when $statutWhat is not recognized
        return null;
    }

    return $statutReturn;
}


}

