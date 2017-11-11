<?php
namespace App\Http\Controllers\Admin;
use App\Models\AlipayConfig;
use App\Models\Company_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Packages\alipay\AlipayOpenAuthTokenAppRequest;

class OauthController extends BaseController{
    //授权码
    public function code(){
        //拼装二维码
        $code_url=url("/admin/oauth?admin_id=".Auth::guard('admin')->user()->id);
        return view('admin.company.app_oauth', compact('code_url'));
    }
    //第三方应用授权拼装
    public function oauth(Request $request){
        try{
            $admin_id=$request->get("admin_id");
            $config =AlipayConfig::where('id', 1)->first();
            $url = urlencode($config['callback']);
            $appid = $config['app_id'];
            $app_oauth_url =$config['app_oauth_url'];
            $code_url = $app_oauth_url . '?app_id=' . $appid . '&redirect_uri=' . $url . "&state=App_" . $admin_id;
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            return view('error',compact('line','error'));
        }
        return redirect($code_url);
    }
    //授权回调获取物业公司的token
    public function callback(Request $request){
        try{
            $state = $request->get('state', 'App_1');//个人授权有这个参数商户授权没有这个参数
            $arr = explode('_', $state);
            $merchant_id=Auth::guard("merchant")->user()->id;
            //第三方应用授权
            if ($arr[0] == "App") {
                //1.初始化参数配置
                $c = $this->AopClient();
                //2.执行相应的接口获得相应的业务
                //获取app_auth_code
                $app_auth_code = $request->get('app_auth_code');
                $admin_id = $arr[1];
                //使用app_auth_code换取app_auth_token
                $obj = new AlipayOpenAuthTokenAppRequest();
                $obj->setApiVersion('2.0');
                $obj->setBizContent("{" .
                    "    \"grant_type\":\"authorization_code\"," .
                    "    \"code\":\"$app_auth_code\"," .
                    "  }");
                $data = $c->execute($obj);
                $app_response = $data->alipay_open_auth_token_app_response;

                $model = [
                    "user_id" => $app_response->user_id,
                    "app_auth_token" => $app_response->app_auth_token,
                    "app_refresh_token" => $app_response->app_refresh_token,
                    "expires_in" => $app_response->expires_in,
                    "re_expires_in" => $app_response->re_expires_in,
                    "auth_app_id" => $app_response->auth_app_id,
                    "admin_id" => $admin_id,
                    "merchant_id"=>$merchant_id,
                    "company_name" => "",
                    "phone" => "",
                ];
                $company_info =Company_info::where('user_id', $app_response->user_id)->first();//如果存在修改信息
                if ($company_info) {
                    //更新
                    Company_info::where('user_id', $app_response->user_id)->update($model);

                } else {
                    $company_info =Company_info::where('merchant_id', $merchant_id)->first();
                    if($company_info){
                        Company_info::where('merchant_id', $merchant_id)->update($model);
                    }else{
                        Company_info::create($model);//新增信息
                    }

                }
                //Cache::put('key', 'value', '527040');//一年
                //这里拿到商户信息如下 auth_token 有效期1年
                //  +"code": "10000"
                // +"msg": "Success"
                // +"app_auth_token": "201610BB7bae5f482d3042b58926dcb331b80X20"
                // +"app_refresh_token": "201610BB206dad017d0049218f89418fb048eX20"
                //  +"auth_app_id": "2016072800112318"
                //  +"expires_in": 31536000
                // +"re_expires_in": 32140800
                //  +"user_id": "2088102168897200"
                return redirect("/admin/addcompany?user_id=" . $app_response->user_id);
            }
        }catch (\Exception $e){
            Log::info($e);
            $error=$e->getMessage();
            $line=$e->getLine();
            return view('error',compact('line','error'));
        }
    }
    //公司信息添加页
    public function addCompany(Request $request){
        $user_id=$request->get("user_id");
        return view("admin.company.addcompany",compact('user_id'));
    }
    //完善物业公司注册信息
    public function createCompany(Request $request){
        try{
            $user_id = $request->get('user_id', 1);
            $name = $request->get('name');
            $phone = $request->get('phone');
            $company_name=$request->get("company_name");
            $province_code=$request->get("province_code");
            $city_code=$request->get("city_code");
            $district_code=$request->get("district_code");
            $province=$request->get('province');
            $city=$request->city;
            $district=$request->district;
            $address=$request->get("address");
            if ($user_id) {
                if ($name && $phone) {
                    $update = [
                        'name' => $name,
                        'phone' => $phone,
                        "company_name"=>$company_name,
                        "province_code"=>$province_code,
                        "city_code"=>$city_code,
                        "district_code"=>$district_code,
                        "province"=>$province,
                        'city'=>$city,
                        'district'=>$district,
                        "address"=>$address
                    ];
                    Company_info::where('user_id', $user_id)->update($update);
                    return json_encode(['status_code' => 1, 'message' => "添加成功"]);
                } else {
                    return json_encode(['status_code' => 0, 'message' => "请填写完整"]);
                }
            } else {
                return redirect('/admin/Estate/code');//重新跳转授权
            }
        }catch(\Exception $e){
            return json_encode([
                "status_code"=>$e->getCode(),
                "message"=>$e->getMessage()
            ]);
        }
    }
}