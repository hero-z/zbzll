<?php

namespace App\Models;
use Zizaco\Entrust\EntrustPermission;
class MerchantPermission extends EntrustPermission
{
    //
    protected $fillable = [
        'id','pid','name', 'display_name', 'description'
    ];
}
