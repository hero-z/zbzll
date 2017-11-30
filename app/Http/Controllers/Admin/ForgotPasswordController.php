<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/11/28
 * Time: 22:51
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Models\Admin;
use App\Models\Merchant;
use App\Models\SmsCode;
use App\Models\SmsConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends  Controller
{
    public function forget()
    {
        return view('admin.login.forgetpassword');
    }

    public function selfResetPsw(Request $request)
    {
        $line='';
        $error='';
        try{
            $phone=trim($request->phone);
            $code=trim($request->code);
            $type=$request->type;
            $password=trim($request->password);
            $password_confirmation=trim($request->password_confirmation);
            //是否配置短信功能
            $smscode=SmsCode::where('phone',$phone)->where('type',$type)->orderBy('updated_at','desc')->first();
            Log::info((array)$smscode);
            if($smscode){
                if($smscode->code==$code){
                    if(floor(time()-strtotime($smscode->updated_at))<=$smscode->expire_time){
                        $data['password']=$password;
                        $data['password_confirmation']=$password_confirmation;
                        $updata['password']=bcrypt($password);
                        $validate=$this->validator3($data)->errors()->toArray();
                        if(!empty($validate)){
                            return json_encode([
                                'success'=>2,
                                'msg'=>$validate
                            ]);
                        }
                        if($type==1){
                            $user=Admin::where('phone',$phone)->first();
                        }else{
                            $user=Merchant::where('phone',$phone)->first();
                        }
                        if($user){
                            $upre=$user->update($updata);
                            if($upre){
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>'密码重置成功'
                                ]);
                            }else{
                                $error='密码重置失败';
                            }
                        }else{
                            $error='查询异常2';
                        }
                    }else{
                        $error='验证码已过期!';
                    }
                }else{
                    $error='验证码不正确!';
                }
            }else{
                $error='查询异常!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>$error.$line
        ]);
    }
    public function getCode(Request $request)
    {
        $line='';
        $error='';
        try{
            $phone=trim($request->phone);
            $type=$request->type;
            //是否配置短信功能
            $cfg=$this->checkSmsConfig();
            if($cfg){
                $ck=$this->checkPhone($phone,$type);
                if($ck['status']){
                    $code = rand(100000, 999999);
                    $smscode=SmsCode::create([
                        'phone'=>$phone,
                        'code'=>$code,
                        'type'=>$type,
                    ]);
                    $response = SmsController::sendSms(
                        $cfg['sign_name'], // 短信签名
                        $cfg['template_code'], // 短信模板编号
                        $phone, // 短信接收者
                        $cfg['aliyun_app_key'], // 阿里云key
                        $cfg['aliyun_app_secret'], // 阿里云secret
                        Array(  // 短信模板中字段的值
                            "code"=>$code
                        )
                        /*,
                        "123"   // 流水号,选填*/
                    );
                    $responseArr=(array)$response;
                    if($responseArr['Code']=='OK'){
                        return json_encode([
                            "success"=>1,
                            "msg"=>'发送成功.有效期15分钟!'
                        ]);
                    }else{
                        $error=$responseArr['Message'];
                        $smscode->delete();
                    }
                }else{
                    $error=$ck['msg'];
                }
            }else{
                $error='服务商短信功能配置不正确!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            if(stripos('####'.$error,'InvalidAccessKeyId')){
                $error='短信配置中不合法的阿里云Key';
                $line='';
            }
            if(stripos('####'.$error,'SignatureDoesNotMatch')){
                $error='短信配置中:阿里云Key和Secret不匹配!';
                $line='';
            }

        }
        return json_encode([
            "success"=>0,
            "msg"=>$error.$line
        ]);
    }
    /**修改密码验证
     * @param array $data
     * @return mixed
     */
    protected function validator3(array $data)
    {
        $message=$this->validatorMessage();
        return Validator::make($data, [
            'password' => 'required|string|min:6|confirmed',
        ],$message);
    }
    //重写验证信息
    protected  function validatorMessage(){
        $messages = [
            'name.required' => '名称不能为空',
            'email.required' => '邮箱不能为空',
            'password.required' => '密码不能为空',
            'password.confirmed' => '密码两次输入不一致',
            'phone.required' => '手机号不能为空',
            'phone.numeric' => '手机号格式不正确',
            'name.max' => '名称必须在255个字符以内',
            "name.unique"=>"名称已存在!",
            "email.email"=>"邮箱格式有误!",
            "email.max"=>"邮箱长度必须在255个字符以内",
            "email.unique"=>"邮箱已被占用!",
            "password.min"=>"密码必须在6位以上",
            "phone.unique"=>"手机号已被占用!",
            "phone.min"=>"手机号必须为11位",
            "phone.max"=>"手机号必须为11位",
            'between' => '密码必须是6~20位之间',
        ];
        return $messages;
    }
    /**检验手机号
     * @param $phone
     * @param int $type
     * @return array
     */
    protected function checkPhone($phone,$type=1)
    {
        $msg='';
        if($type==1){
            $istel=AgentMangeController::IsTel($phone);
            if($istel){
                $admin=Admin::where('phone',$phone)->first();
                if($admin){
                    if($admin->status==1){
                        //验证码库
                        $count=SmsCode::where('phone',$phone)->where('type',$type)->where('updated_at','>',date('Ymd'))->count('id');
                        if($count<5){
                            return ['status'=>1];
                        }else{
                            $msg='该账号今天发送短信已超过5次,请明天重试!';
                        }
                    }else{
                        $msg='用户状态异常!';
                    }
                }else{
                    $msg='服务商端不存在此用户!';
                }
            }else{
                $msg='手机号格式不正确';
            }
        }
        return ['status'=>0,'msg'=>$msg];
    }

    /**检查配置
     * @return bool
     */
    protected function checkSmsConfig()
    {
        $re=SmsConfig::first();
        if($re){
            $re=$re->toArray();
            foreach ($re as $v){
                if($v==''||is_null($v)){
                    return false;
                }
            }
        }else{
            return false;
        }
        return $re;
    }
}