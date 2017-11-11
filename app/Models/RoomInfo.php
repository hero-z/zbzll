<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomInfo extends Model
{
    //
    protected $fillable=[
        "unit_id", "area","building_id", "out_community_id","community_id","batch_id","room_id","out_room_id","room","address","status","created_at","updated_at"
    ];
}
