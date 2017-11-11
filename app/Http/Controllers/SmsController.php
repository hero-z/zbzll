<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/19
 * Time: 14:32
 */

namespace App\Http\Controllers;


use Flc\Alidayu\App;
use Flc\Alidayu\Client;

class SmsController extends Controller
{
    protected $appkey='24659814';
    protected $appsecret='67e8487fccd1970ea026fbbaaf14047e';
    protected $auth;
    public function getClient(){
        $config = [
            'app_key' =>$this->appkey,
            'app_secret' =>  $this->appsecret,
        ];
        return $client = new Client(new App($config));
    }
}