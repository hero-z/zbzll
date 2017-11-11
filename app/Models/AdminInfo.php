<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInfo extends Model
{
    //
    protected $fillable = [
        'admin_id', 'name', 'id_card_no','bank_card_no','id_card_front','id_card_back','id_card_hold','bank_card_hold'
    ];
}
