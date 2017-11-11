<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;

class CheckMerchantController extends Controller
{
    //获取用户id及所属用户id
    public static function CheckMerchant($id){

            //获取下一级id
            $s=merchant::where('pid',$id)->select('id')->get();
            if($s){
                $b=[];
                foreach($s as $k=>$v){
                 $b[]=$v['id'];
                }
                //获取所有用户id
               $merchant_id=Merchant::whereIn('pid',$b)
                          ->orwhere("id",$id)
                          ->orwhere('pid',$id)
                          ->select('id')
                          ->get();
                $array=[];
                foreach($merchant_id as $k=>$v){
                    $array[$k]=$v['id'];
                }
                $merchant_id=$array;
                return $merchant_id;
            }else{
                $merchant_id=$id;
                return $merchant_id;
            }
    }
//通过pid获取物业公司主账号id
    public static function selectMerchant($pid){
        if($pid==0){
            return Auth::guard('merchant')->user()->id;
        }else{
            $merchant=Merchant::where('id',$pid)->select('pid')->first();
            if($merchant->pid==0){
                return $pid;
            }else{
                return Merchant::where('id',$pid)->select('pid')->first()->pid;
            }
        }

    }
}