<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    //
    protected $fillable=[
        "account", "merchant_id",'community_name','community_address',"community_locations","district_code","city_code","province_code","province","city","district","hotline","out_community_id","community_id","alipay_status","basicservice_status",'created_at',"updated_at"
    ];
}
