<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewUser extends Model
{
    use HasFactory;
    protected $table = 'delevery_new_customers';
    protected $primaryKey = 'new_customers_id';
    protected $fillable = [
        'new_customers_id',
        'new_customers_name',
        'new_customers_store',
        'new_customers_phone',
        'new_customers_email',
        'new_customers_password',
        'new_customers_address',
        'new_customers_city',
        'new_customers_rc',
        'new_customers_website',
        'new_customers_company_type',
        'new_customers_brand',
        'new_customers_logo',
        'new_customers_bank',
        'new_customers_rib',
        'new_customers_statut',
        'new_customers_active_code',
        'new_customers_active',
        'new_customers_time',
    ];
    
}
