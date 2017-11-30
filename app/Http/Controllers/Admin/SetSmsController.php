<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/11/24
 * Time: 15:42
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\SmsConfig;
use Illuminate\Http\Request;

class SetSmsController extends Controller
{
    public function setSms(Request $request)
    {
        $line='';
        $error='';
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('GET')){
                if($root){
                    $config=SmsConfig::firstOrCreate([]);
                    return view('admin.sms.config',compact('config'));
                }else{
                    $error='你没有该操作权限';
                }
            }elseif($request->isMethod('POST')){
                if($root){
                    $cid=$request->id;
                    $cfg=SmsConfig::find($cid);
                    if($cfg){
                        $data['aliyun_app_key']=trim($request->aliyun_app_key);
                        $data['aliyun_app_secret']=trim($request->aliyun_app_secret);
                        $data['sign_name']=trim($request->sign_name);
                        $data['template_code']=trim($request->template_code);
                        $data['template_msg']=trim($request->template_msg);
                        if($cfg->update($data)){
                            return json_encode([
                                "success"=>1,
                                "msg"=>"操作成功"
                            ]);
                        }else{
                            $error='更新异常';
                        }
                    }else{
                        $error='查询异常';
                    }
                }else{
                    $error='你没有该操作权限';
                }
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"查询失败!".$error.$line
                ]);
            }
        }
        return view('error',compact('line','error'));
    }
}