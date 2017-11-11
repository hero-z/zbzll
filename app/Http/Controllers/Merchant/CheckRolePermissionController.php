<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/18
 * Time: 13:18
 */

namespace App\Http\Controllers\merchant;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckRolePermissionController extends Controller
{
    //检查是否是Root角色
    public static function CheckRoleRoot(){
        $merchant=Auth::guard('merchant')->user();
        return $merchant->hasRole('root'.$merchant->id);
    }
    //是否有权限
    public static function CheckPremission($permissionName){
        return Auth::guard('merchant')->user()->can($permissionName);
    }
    /*
  * 逻辑模板
  * */
    public function example(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('Config')){

            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }
}