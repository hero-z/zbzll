<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Merchant;
use App\Models\MerchantRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller{
    //物业公司员工信息
    public function merchantInfo(){
        try{
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id) ;
            //获取员工信息
            $merchantInfo=Merchant::whereIn('id',$merchant_id)->OrderBy("created_at","desc")->paginate(8);
            //如果pid不为0,获取父级pid
            if(Auth::guard('merchant')->user()->pid!=0){
                $pid=Merchant::where('id',Auth::guard('merchant')->user()->pid)->first()->pid;
            }else{
                $pid=Auth::guard('merchant')->user()->pid;
            }
            return view('merchant.user.merchantinfo',compact('merchantInfo','pid'));
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            return view('error',compact('line','error'));
        }
    }
    //添加员工
    public function addMerchant(Request $request)
    {
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||CheckRolePermissionController::CheckPremission('addMerchant')){
                $requestdata=$request->except('_token','token','role_id');
                $role_id=$request->role_id;
                $validate=$this->validator($requestdata)->errors()->toArray();
                if(!self::IsTel($request->phone)){
                    $validate['phone']=['手机格式不正确'];
                }
                if(!empty($validate)){
                    return json_encode([
                        'success'=>2,
                        'msg'=>$validate
                    ]);
                }
                $requestdata['pid']=Auth::guard('merchant')->user()->id;
                $res=$this->create(array_except($requestdata,'password_confirmation'));
                if(!empty($res)){
                    if($role_id){
                        $res->attachRole($role_id);
                    }
                }
                return json_encode([
                    'success'=>1,
                    'msg'=>'已成功添加员工'
                ]);
            }else{
                $error='亲,你还没有该操作权限!';
            }

        }catch (\Exception $e) {
            $error = $e->getMessage();
            $line = $e->getLine();
        }
        return json_encode([
           "success"=>0,
           'msg'=>$error.$line
        ]);
    }
    //获取角色
    public function getRole(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addMerchant')){
                $merchant_id=Auth::guard('merchant')->user()->id;
                if($request->isMethod('POST')){
                    $roles=MerchantRole::where('id','!=',1)->where('merchant_id',$merchant_id)->where('name','!=','root')->orderby('created_at','desc')->get();
                    foreach ($roles as $k=>$v){
                        $roles[$k]->name=rtrim( $v->name,$merchant_id);
                    }
                    //获取当前角色id
                    $id=DB::table('role_merchant')->where('merchant_id',$request->id)->select('merchant_role_id')->first();
                    if($id){
                        $id=$id->merchant_role_id;
                        return json_encode([
                            "success"=>1,
                            "data"=>$roles,
                            'id'=>$id
                        ]);
                    }
                    return json_encode([
                        "success"=>1,
                        "data"=>$roles
                    ]);
                }else{
                    $error='方法调用出错';
                }
            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>"获取角色失败!".$error.$line
        ]);
    }

    //获取小区信息
    public function getCommunity(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('assignCommunity')){
                $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id) ;
                if($request->isMethod('POST')){
                 $community=Community::whereIn('merchant_id',$merchant_id)->select('id','merchant_id','community_name')->orderby('created_at','desc')->get();
                    return json_encode([
                        "success"=>1,
                        "data"=>$community
                    ]);
                }else{
                    $error='方法调用出错';
                }
            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>"获取小区失败!".$error.$line
        ]);
    }
    //分配小区
    public function assignCommunity(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('assignCommunity')){
                $data['merchant_id']=$request->merchant_id;
                $community_id=$request->community_id;
                if(!$community_id){
                    return json_encode([
                        'success'=>0,
                        'msg'=>"分配失败,请勾选小区!"
                    ]);
                }
                if($request->isMethod('POST')){
                    if(Community::whereIn('id',$community_id)->update($data)){
                        return json_encode([
                           'success'=>1,
                           'msg'=>"分配小区成功!"
                        ]);
                    }else{
                        return json_encode([
                            'success'=>0,
                            'msg'=>"分配小区失败!"
                        ]);
                    }
                }else{
                    $error='方法调用出错';
                }
            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>"分配小区失败!".$error.$line
        ]);
    }
    //分配角色
    //分配角色
    public function setRole(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $agentid=$request->agent_id;
                $roleid=$request->role_id;
                $agent=Merchant::where('id',$agentid)->first();
                if($root&&$agent){
                    DB::table('role_merchant')->where('merchant_id',$agentid)->delete();
                    $re=$agent->attachRole($roleid);
                    return json_encode([
                        "success"=>1,
                        "msg"=>'分配角色成功'
                    ]);
                }else{
                    $error='没有权限!';
                }
            }else{
                $error='方法调用错误!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>"设置失败!".$error.$line
        ]);
    }
    //获取要修改的员工信息
    public function getMerchantInfo(Request $request)
    {
        $line=0;
        try{
            $id=$request->id;
            $merchant=Merchant::where('id',$id)->first();
            if($merchant){
                return json_encode([
                   "success"=>1,
                   'data'=>$merchant
                ]);
            }else{
                $error='获取员工信息失败!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
           'success'=>0,
           'msg'=>$error.$line
        ]);
    }
    //执行修改员工信息
    public function updateMerchantInfo(Request $request)
    {
        $line=0;
        try{
                if($request->password||$request->password_confirmation){
                    $requestdata=$request->except('_token','token');
                    $validate=$this->validator($requestdata)->errors()->toArray();
                    if(!self::IsTel($request->phone)){
                        $validate['phone']=['手机格式不正确'];
                    }
                    if(!empty($validate)){
                        return json_encode([
                            'success'=>2,
                            'msg'=>$validate
                        ]);
                    }
                    $updateMerchant= Merchant::where('id',$request->id)->update([
                        'name' => $requestdata['name'],
                        'email' => $requestdata['email'],
                        'phone'=>$requestdata['phone'],
                        'password' => bcrypt($requestdata['password']),
                    ]);
                    if($updateMerchant){
                        return json_encode([
                            'success'=>1,
                            'msg'=>'已成功修改员工'
                        ]);
                    }else{
                        return json_encode([
                            'success'=>0,
                            'msg'=>'修改员工信息失败'
                        ]);
                    }

                }else{
                    $requestdata=$request->except('_token','token','password','password_confirmation');
                    $validate=$this->validators($requestdata)->errors()->toArray();
                    if(!self::IsTel($request->phone)){
                        $validate['phone']=['手机格式不正确'];
                    }
                    if(!empty($validate)){
                        return json_encode([
                            'success'=>2,
                            'msg'=>$validate
                        ]);
                    }
                    $updateMerchant= Merchant::where('id',$request->id)->update([
                        'name' => $requestdata['name'],
                        'email' => $requestdata['email'],
                        'phone'=>$requestdata['phone'],
                    ]);
                    if($updateMerchant){
                        return json_encode([
                            'success'=>1,
                            'msg'=>'已成功修改员工'
                        ]);
                    }else{
                        return json_encode([
                            'success'=>0,
                            'msg'=>'修改员工信息失败'
                        ]);
                    }
                }
        }catch (\Exception $e) {
            $error = $e->getMessage();
            $line = $e->getLine();
        }
        return json_encode([
            "success"=>0,
            'msg'=>$error.$line
        ]);
    }
    //删除员工
    public function delMerchant(Request $request){
        $line=0;
        $error='未知错误';
        try{
            $id=$request->id;
            if(Merchant::where('id',$id)->delete()){
                //删除成功后,转移小区
                $data['merchant_id']=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                Community::where('merchant_id',$id)->update($data);
                return json_encode([
                    "success"=>1,
                    "msg"=>"删除成功"
                ]);

            }else{
                return json_encode([
                    "success"=>0,
                    "msg"=>"删除失败"
                ]);
            }

        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>"删除失败!".$error.$line
        ]);
    }
    protected function validator(array $data)
    {
        $message=$this->validatorMessage();
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'phone' => 'required|string|min:11|max:11|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ],$message);
    }
    protected function validators(array $data)
    {
        $message=$this->validatorMessages();
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'phone' => 'required|string|min:11|max:11|unique:admins',
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
    protected  function validatorMessages(){
        $messages = [
            'name.required' => '名称不能为空',
            'email.required' => '邮箱不能为空',
            'phone.required' => '手机号不能为空',
            'phone.numeric' => '手机号格式不正确',
            'name.max' => '名称必须在255个字符以内',
            "name.unique"=>"名称已存在!",
            "email.email"=>"邮箱格式有误!",
            "email.max"=>"邮箱长度必须在255个字符以内",
            "email.unique"=>"邮箱已被占用!",
            "phone.unique"=>"手机号已被占用!",
            "phone.min"=>"手机号必须为11位",
            "phone.max"=>"手机号必须为11位",
            'between' => '密码必须是6~20位之间',
        ];
        return $messages;
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return Merchant::create([
            'pid'=>$data['pid'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone'=>$data['phone'],
            'password' => bcrypt($data['password']),
        ]);
    }
    //校验手机号
    public static function IsTel($tel) {
        $a= preg_match("/^1(3^1(3[4-9]|4[7]|5[0-27-9]|7[08]|8[2-478])\\d{8}$[4-9]|4[7]|5[0-27-9]|7[08]|8[2-478])\\d{8}$/",$tel);
        $b= preg_match("/^1(3[0-2]|4[5]|5[56]|7[0156]|8[56])\\d{8}$/",$tel);
        $c= preg_match("/^1(3[3]|4[9]|53|7[037]|8[019])\\d{8}$/",$tel);
        return $a||$b||$c;
    }
}