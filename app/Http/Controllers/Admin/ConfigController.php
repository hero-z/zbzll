<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/18
 * Time: 12:40
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\AlipayConfig;
use Illuminate\Http\Request;

class ConfigController extends  Controller
{
    public function setIsvConfig(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('Config')){
                if($request->isMethod('GET')){
                    $cfg=AlipayConfig::where('id',1)->first();
                    return view('admin.config.isv',compact('cfg'));
                }elseif($request->isMethod('POST')){
                    $args=$request->except('_token');
                    if(AlipayConfig::where("id",1)->update($args)){
                        return json_encode([
                            "success"=>1,
                            "msg"=>"设置成功"
                        ]);
                    }else{
                        return json_encode([
                            "success"=>0,
                            "msg"=>"设置失败!"
                        ]);
                    }
                }

            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"设置失败!".$error.$line
                ]);
            }
        }
        return view('error',compact('line','error'));
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