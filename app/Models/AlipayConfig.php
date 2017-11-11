<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayConfig extends Model
{
    protected $fillable = [
        'app_id', 'pid', 'rsaPrivateKey','rsaPrivateKeyFilePath','alipayrsaPublicKey','rsaPublicKeyFilePath','callback','operate_notify_url','notify','created_at'
    ];
}
