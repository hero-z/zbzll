<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    //
    protected $fillable=[
        "phone", "code","type", "expire_time","created_at","updated_at"
    ];
}
