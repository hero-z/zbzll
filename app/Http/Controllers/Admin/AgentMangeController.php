<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/20
 * Time: 20:47
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminInfo;
use App\Models\Bill;
use App\Models\Company_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AgentMangeController extends  Controller
{
    /**
     * @param Request $request
     * 代理商列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function agentsInfo(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||CheckRolePermissionController::CheckPremission('agentManage')){
                $agentstatus=$request->agentstatus;
                $searchname=$request->searchname;
                $where=[];
                $agentsarr=[];
                if($agentstatus){
                    $where[]=['status',$agentstatus==3?0:$agentstatus];
                }
                if($searchname){
                    $where[]=['name','like','%'.$searchname.'%'];
                }
                $agentinfo=Auth::guard('admin')->user();
                if(!$root){
                    $where[]=['pid',$agentinfo->id];
                }
                if($root){
                    $agents=Admin::where('id','!=',1)->get();
                    foreach ($agents as $v){
                        $agentsarr[$v->id]=$v->name;
                    }
                }
                $agents=Admin::where('id','!=',1)
                    ->where($where)
                    ->when(!$root,function ($query )use ($agentinfo){
                        return $query->where('pid',$agentinfo->id);})
                    /*->when($agentinfo->pid,function ($query )use ($agentinfo){
                        return $query->orwhere('id',$agentinfo->pid);})*/
                    ->orderby('pid')
                    ->orderby('created_at','desc')
                    ->paginate(8);
                return view('admin.agent.agent',compact('agents','searchname','agentstatus','agentsarr'));
            }else{
                $error='亲,你还没有该操作权限!';
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

    /**
     * @param Request $request
     * 获取代理商资料
     * @return string
     */
    public function getAgentInfo(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            $agentid=$request->id;
            $auth=Auth::guard('admin')->user();
            if($root||CheckRolePermissionController::CheckPremission('agentManage')||$auth->id==$agentid){
                if($request->isMethod('POST')){
                    $agentinfo=Admin::where('id',$agentid)->first();
                    if($root||$agentinfo&&($agentinfo->pid==$auth->id||$agentinfo->id==$auth->id)){
                        $agent=AdminInfo::where('admin_id',$agentid)->first();
                        return json_encode([
                            "success"=>1,
                            "data"=>$agent
                        ]);
                    }else{
                        $error='查询异常!';
                    }
                }else{
                    $error='方法调用错误!';
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
            "msg"=>"查询失败!".$error.$line
        ]);
    }
    protected $AdminImgs=['id_card_front','id_card_hold','bank_card_front','bank_card_hold'];
    /**
     * @param Request $request
     * 修改资料信息
     * @return string
     */
    public function editAgentInfo(Request $request){
        $line='';
        $error='';
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $agentid=$request->admin_id;
                $agent=Admin::where('id',$agentid)->first();
                $admin=Auth::guard('admin')->user();
                $updatedata=$request->except('_token','images','admin_id');
                $images=$request->images;
                if($images){
                    foreach($images as $k=>$v){
                        $updatedata[$this->AdminImgs[$k]]=$v;
                    }
                }
                if($root||$agent&&($agent->pid==$admin->id||$agentid==$admin->id)&&$agent->status!=1){
                    $agentinfo=AdminInfo::where('admin_id',$agentid)->first();
                    if($agentinfo){
                        $re=$this->updateAgentFile($agentid,$updatedata);
                    }else{
                        $updatedata['admin_id']=$agentid;
                        $re=AdminInfo::create($updatedata);
                    }
                    if($re){
                        //更新成功 修改状态
                        if(!$root){
                            Admin::where('id',$agentid)->update([
                                'status'=>0//到后台审核
                            ]);
                        }
                        return json_encode([
                            "success"=>1,
                            "msg"=>'更新成功'
                        ]);
                    }else{
                        $error='更新失败';
                    }
                }else{
                    $error='查询异常!';
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
            "msg"=>"更新失败!".$error.$line
        ]);
    }
    /**
     * @param Request $request
     * 审核
     * @return string
     */
    public function checkAgent(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $status=$request->status;
                $agentid=$request->agent_id;
                $agent=Admin::where('id',$agentid)->first();
                if($agentid!=1&&$agent&&$agent->status!=1){
                    $re=Admin::where('id',$agentid)->update([
                        'status'=>$status//到后台审核
                    ]);
                    if($re){
                        return json_encode([
                            "success"=>1,
                            "msg"=>'操作成功'
                        ]);
                    }else{
                        $error='操作失败';
                    }
                }else{
                    $error='查询异常!';
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
            "msg"=>"更新失败!".$error.$line
        ]);
    }

    /**
     * @param Request $request
     * 分配角色
     * @return string
     */
    public function setAgentRole(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $agentid=$request->agent_id;
                $roleid=$request->role_id;
                $agent=Admin::where('id',$agentid)->first();
                if($root&&$agent){
                    DB::table('role_user')->where('user_id',$agentid)->delete();
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
    //修改账号
    public function setAgentFile(Request $request)
    {
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $data=$validate=[];
                $agentid=$request->agent_id;
                $data['name']=trim($request->name);
                $data['email']=trim($request->email);
                $password=trim($request->password);
                $password_confirmation=trim($request->password_confirmation);
                $agent=Admin::find($agentid);
                if($root&&$agent){
                    if($agent->email==$data['email']){
                        array_forget($data,'email');
                    }
                    $updata=$data;
                    if($password){
                        $data['password']=$password;
                        $data['password_confirmation']=$password_confirmation;
                        $updata['password']=bcrypt($password);
                    }
                    $validate=$this->validator2($data)->errors()->toArray();
                    if(!empty($validate)){
                        return json_encode([
                            'success'=>2,
                            'msg'=>$validate
                        ]);
                    }
                    $re=$agent->update($updata);
                    if($re){
                        return json_encode([
                            "success"=>1,
                            "msg"=>'修改成功'
                        ]);
                    }else{
                        $error='修改失败';
                    }

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
    //重置密码
    public function resSetPsw(Request $request)
    {
        $line='';
        $error='';
        try{
            if($request->isMethod('GET')){
                return view('admin.agent.resetpsw');
            }elseif($request->isMethod('POST')){
                $rpassword=trim($request->rpassword);
                $password=trim($request->password);
                $password_confirmation=trim($request->password_confirmation);
                $user=Auth::guard('admin')->user();
                if(Hash::check($rpassword,Auth::guard('admin')->user()->password)){
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
                    $re=$user->update($updata);
                    if($re){
                        return json_encode([
                            "success"=>1,
                            "msg"=>'修改成功'
                        ]);
                    }else{
                        $error='修改失败';
                    }
                }else{
                    $error='原密码错误!';
                }
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        if($request->isMethod('POST')){
            return json_encode([
                "success"=>0,
                "msg"=>$error.$line
            ]);
        }
        return view('error',compact('line','error'));
    }
    //个人资料
    public function getMe(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('GET')){
                if($root){
                    return redirect('admin/adminindex');
                }
                return view('admin.agent.me',compact('agent'));
            }elseif($request->isMethod('POST')){
                $agentid=$request->id;
                $agentinfo=Admin::where('id',$agentid)->first();
                if($root||$agentinfo&&$agentinfo->pid==Auth::guard('admin')->user()->id){
                    $agent=AdminInfo::where('admin_id',$agentid)->first();
                    return json_encode([
                        "success"=>1,
                        "data"=>$agent
                    ]);
                }else{
                    $error='查询异常!';
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

    /**
     * @param $AgentId
     * @param array $parmas
     * 更新代理商的个人资料
     * @return mixed
     */
    protected function updateAgentFile($AgentId,Array $parmas=[]){
        //修改代理商个人资料
        $updateData['updated_at']=date("YmdHis");
        if(array_key_exists('name',$parmas)){
            $updateData['name']=$parmas['name'];
        }
        if(array_key_exists('id_card_no',$parmas)){
            $updateData['id_card_no']=$parmas['id_card_no'];
        }
        if(array_key_exists('bank_card_no',$parmas)){
            $updateData['bank_card_no']=$parmas['bank_card_no'];
        }
        if(array_key_exists('id_card_front',$parmas)){
            $updateData['id_card_front']=$parmas['id_card_front'];
        }
        if(array_key_exists('id_card_back',$parmas)){
            $updateData['id_card_back']=$parmas['id_card_back'];
        }
        if(array_key_exists('id_card_hold',$parmas)){
            $updateData['id_card_hold']=$parmas['id_card_hold'];
        }
        if(array_key_exists('bank_card_front',$parmas)){
            $updateData['bank_card_front']=$parmas['bank_card_front'];
        }
        if(array_key_exists('bank_card_hold',$parmas)){
            $updateData['bank_card_hold']=$parmas['bank_card_hold'];
        }
        return AdminInfo::where('admin_id',$AgentId)->update($updateData);
    }
    public function test(Request $request){
        if($request->isMethod("GET")){

            return view('admin.upload.test');
        }elseif($request->isMethod('POST')){
//            dd($request->except('_token'));
            $datastr=$request->data;
            $imgdata = substr($datastr,strpos($datastr,",") + 1);
            $decodedData = base64_decode($imgdata);
            file_put_contents('upload/tmp/test.png',$decodedData );
            dd($imgdata);
        }
    }

    /**
     * @param Request $request
     * 添加代理商
     * @return string
     */
    public function addAgent(Request $request){
        $line='';
        $error='';
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||CheckRolePermissionController::CheckPremission('agentManage')){
                if($request->isMethod('POST')){
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
                    $requestdata['pid']=$root?0:Auth::guard('admin')->user()->id;
                    $requestdata['status']=$root?1:0;
                    $res=$this->create(array_except($requestdata,'password_confirmation'));
                    if(!empty($res)){
                        if($role_id){
                            $res->attachRole($role_id);
                        }
                    }
                    return json_encode([
                        'success'=>1,
                        'msg'=>'已成功创建用户'
                    ]);
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
            "msg"=>"设置失败!".$error.$line
        ]);
    }

    /**
     * @param Request $request
     * 删除
     * @return string
     */
    public function delAgent(Request $request){
        $line='';
        $error='';
        try{
            $agentid=$request->id;
            $agent=Admin::where('id',$agentid)->where('id','!=',1)->first();
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||$agent&&Auth::guard('admin')->user()->id==$agent->pid){
                if($request->isMethod('POST')){
                    if($agentid!=1){
                        if($agent->status!=1){
                            $re=Admin::where('id',$agentid)->delete();
                            //删除对应资料
                            AdminInfo::where('admin_id',$agentid)->delete();
                            if($re){
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>"删除成功"
                                ]);
                            }else{
                                $error='删除代理失败';
                            }
                        }else{
                            if($root){
                                $bills=Bill::where('admin_id',$agentid)->get();
                                $company=Company_info::where('admin_id',$agentid)->get();
                                if($bills->isEmpty()){
                                    if($company->isEmpty()){
                                        $agent->delete();
                                        return json_encode([
                                            "success"=>1,
                                            "msg"=>"删除成功"
                                        ]);
                                    }else{
                                        $error='该代理商下有物业公司,请先进行物业公司转移';
                                    }
                                }else{
                                    $error='该代理商下小区有账单,不建议删除,建议给空角色.';
                                }
                            }else{
                                $error='该代理商不存在或者状态不允许删除';
                            }
                        }
                    }else{
                        $error='该代理不允许删除';
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
            "msg"=>"删除失败!".$error.$line
        ]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
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

    /**修改账号信息验证
     * @param array $data
     * @return mixed
     */
    protected function validator2(array $data)
    {
        $message=$this->validatorMessage();
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:admins',
            'password' => 'string|min:6|confirmed',
        ],$message);
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
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return Admin::create([
            'pid'=>$data['pid'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone'=>$data['phone'],
            'status'=>$data['status'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * @param $tel
     * 校验手机号
     * @return bool
     */
    public static function IsTel($tel) {
        $a= preg_match("/^1[34578]{1}\d{9}$/",$tel);
//        $a= preg_match("/^1(3^1(3[4-9]|4[7]|5[0-27-9]|7[08]|8[2-478])\\d{8}$[4-9]|4[7]|5[0-27-9]|7[08]|8[2-478])\\d{8}$/",$tel);
        $b= preg_match("/^1(3[0-2]|4[5]|5[56]|7[0156]|8[56])\\d{8}$/",$tel);
        $c= preg_match("/^1(3[3]|4[9]|53|7[037]|8[019])\\d{8}$/",$tel);
        return $a||$b||$c;
    }


}