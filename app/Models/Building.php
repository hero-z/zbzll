<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    //
    protected $fillable = [
        "out_community_id","building_name","unit_number","level","type","description","created_at","updated_at"
    ];
}
