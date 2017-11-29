<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConfig extends Model
{
    //
    protected $fillable=[
        "aliyun_app_key", "aliyun_app_secret","sign_name", "template_code","template_msg","created_at","updated_at"
    ];
}
