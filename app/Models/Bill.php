<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    //
    protected $fillable = [
      'admin_id','company_id',"buyer_user_id",'type',"bill_status",'trade_no', "community_id","out_community_id",  'batch_id', 'community_id', 'bill_entry_id','out_room_id','room_address','cost_type','bill_entry_amount','acct_period','release_day',"deadline","relate_id","remark_str",'created_at'
    ];
}
