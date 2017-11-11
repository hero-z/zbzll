<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Residentinfo extends Model
{
    //
    protected $fillable=[
        "phone","remark","name","out_room_id","type","created_at","updated_at"
    ];
}
