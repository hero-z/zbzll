<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Merchant;
use App\Http\Controllers\Controller;
use App\Models\MerchantRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class OauthRegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/oauthhome';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:merchantoauth');
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
            'phone' => 'required|string|max:11|unique:merchants',
            'password' => 'required|string|min:6|confirmed',
        ],$message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
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
    protected function create(array $data)
    {
        return Merchant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone'=>$data['phone'],
            'password' => bcrypt($data['password']),
        ]);
    }
    //重写视图
    public function showRegistrationForm()
    {
        return view('merchant.register.oauthregister');
    }
    //自定义认证驱动
    protected function guard()
    {
        return auth()->guard('merchantoauth');
    }
    //重写注册后跳转页
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        config(['entrust.role' => '\App\Models\MerchantRole']);
        config(['entrust.roles_table' => 'merchant_roles']);
        config(['entrust.permission' => '\App\Models\MerchantPermission']);
        config(['entrust.permissions_table' => 'merchant_permissions']);
        config(['entrust.permission_role_table' => 'merchant_permission_role']);
        config(['entrust.role_user_table' => 'role_merchant']);
        config(['entrust.user_foreign_key' => 'merchant_id']);
        config(['entrust.role_foreign_key' => 'merchant_role_id']);
        event(new Registered($user = $this->create($request->all())));
        $mrole=MerchantRole::create([
            'merchant_id'=>$user->id,
            "name"=>'root'.$user->id,
            "display_name"=>'超级管理员',
            "description"=>'超级管理员拥有最高权限',
        ]);
        $user->attachRole($mrole->id);
        return redirect(url('admin/oauthlogin'));
    }
}
