<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company_info extends Model
{
    //
    protected $fillable = [
        'user_id', 'company_name','phone','province_code','city_code','district_code',"province",'city','district','address','app_auth_token','created_at',
        'app_refresh_token',"expires_in","re_expires_in","auth_app_id","admin_id","merchant_id","status"
    ];
}
