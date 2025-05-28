<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RamasageCity extends Model
{
    use HasFactory;

    protected $table = 'delevery_ramassage_cities';
    protected $primaryKey = 'cities_id';
    protected $fillable = [
        'ramassage_cities_id',      // Corresponds to a primary key column of type int(11), non-nullable, auto-increment
        'ramassage_cities_code',    // Corresponds to a column of type varchar(50), non-nullable
        'ramassage_cities_nom',     // Corresponds to a column of type varchar(25), non-nullable
        'ramassage_cities_statut',  // Corresponds to a column of type tinyint(1)
    ];



    public function cities()
    {
        return $this->belongsToMany(City::class, 'delevery_cities_relation_ramassage');
    }
}
