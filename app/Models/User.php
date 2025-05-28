<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'delevery_customers';
    protected $primaryKey = 'customers_id';
    protected $fillable = [
        'customers_id',
        'customers_name',
        'customers_cin',
        'customers_store',
        'customers_phone',
        'customers_phone_personal',
        'customers_email',
        'customers_password',
        'customers_address',
        'customers_city',
        'customers_rc',
        'customers_website',
        'customers_company_type',
        'customers_brand',
        'customers_logo',
        'customers_cin_recto_image',
        'customers_rib_image',
        'customers_cin_verso_image',
        'customers_ramassage_cities_id',
        'customers_bank',
        'customers_rib',
        'customers_isactive_pers',
        'customers_delivered_price',
        'customers_returned_price',
        'customers_refused_price',
        'customers_time',
        'customers_by',
        'customers_active',
        'customers_api',
        'customers_api_key',
        'remember_token',
        'email_verified_at',
        'customers_stock',
        'customers_ticket_msg',
        'created_at',
        'updated_at',
    ];
    

    public function customer_info($what_col,$webapp_customer_id){
       
        if($webapp_customer_id != 0 && !empty(self::find($webapp_customer_id)->$what_col)){
           
            return self::find($webapp_customer_id)->$what_col;
        }else{
            return 0;
        }
       
    }
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'customers_password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
