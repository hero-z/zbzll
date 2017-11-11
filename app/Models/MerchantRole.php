<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class MerchantRole extends EntrustRole
{
    //
    protected $fillable = [
        'name', 'display_name', 'description',"merchant_id"
    ];
}
