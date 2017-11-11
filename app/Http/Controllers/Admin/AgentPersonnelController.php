<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/18
 * Time: 16:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class AgentPersonnelController extends Controller
{
    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
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
            'email' => 'required|string|email|max:255|unique:merchants',
            'phone' => 'required|string|phone|max:11|unique:merchants',
            'password' => 'required|string|min:6|confirmed',
        ],$message);
    }
    //重写验证信息
    protected  function validatorMessage(){
         $messages = [
            'name.required' => '名称不能为空',
            'email.required' => '邮箱不能为空',
            'password.required' => '密码不能为空',
            'phone.required' => '手机号不能为空',
            'name.max' => '名称必须在255个字符以内',
            "name.unique"=>"名称已存在!",
            "email.email"=>"邮箱格式有误!",
            "email.max"=>"邮箱长度必须在255个字符以内",
            "email.unique"=>"邮箱已被占用!",
            "password.min"=>"密码必须在6位以上",
            "phone.unique"=>"手机号已被占用!",
            "phone.min"=>"手机号必须为11位",
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
            'password' => bcrypt($data['password']),
        ]);
    }
    //重写视图
    public function showRegistrationForm()
    {
        return view('auth.register.index');
    }
    //自定义认证驱动
    protected function guard()
    {
        return auth()->guard('admin');
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

//        $this->guard()->login($user);  //手动添加去除自动登录状态

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selfregister(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
    private function adminadd(Request $request){

    }
    public function agentsInfo(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||CheckRolePermissionController::CheckPremission('agentManage')){
                if($request->isMethod('GET')){
                    $where=[];
                    $agentinfo=Auth::guard('admin')->user();
                    if(!$root){
                        $where[]=['pid',$agentinfo->id];
                    }
                    $agents=Admin::where('id','!=',1)
                        ->where($where)
                        ->when(!$root,function ($query )use ($agentinfo){
                            return $query->where('pid',$agentinfo->id);})
                        ->when($agentinfo->pid,function ($query )use ($agentinfo){
                            return $query->orwhere('id',$agentinfo->pid);})
                        ->paginate(9);
                    dd($agents);
                    return view('admin.agent.agent',compact('agents'));
                }elseif($request->isMethod('POST')){
//                    $args=$request->except('_token');
//                    if(AlipayConfig::where("id",1)->update($args)){
//                        return json_encode([
//                            "success"=>1,
//                            "msg"=>"设置成功"
//                        ]);
//                    }else{
//                        return json_encode([
//                            "success"=>0,
//                            "msg"=>"设置失败!"
//                        ]);
//                    }
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
}