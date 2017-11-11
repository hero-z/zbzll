<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillQuestion extends Model
{
    protected $fillable = [
       "bill_id","description",'correct_bill_amount',"status","merchant_id"
    ];
}
