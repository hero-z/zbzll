<?php
namespace App\Http\Controllers\Merchant;
use App\Models\Community;
use App\Models\Company_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Packages\alipay\request\AlipayEcoCplifeBasicserviceInitializeRequest;
use Packages\alipay\request\AlipayEcoCplifeBasicserviceModifyRequest;

class BasicServiceController extends BaseController{
    //初始化小区服务
    public function initializeBasicService(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckPremission('initializeBasicService')){
                $id=$request->id;
                $basicservice_status=$request->basicservice_status;
                $aop=$this->AopClient();
                $aop->method="alipay.eco.cplife.basicservice.initialize";
                $community=Community::where('id',$id)->first();
                $xternal_invoke_address=url('/merchant/notify');
                //获取物业公司主账号id
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                //获取物业公司授权token
                $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
               if($community->basicservice_status=="NONE"){
                   $requests = new AlipayEcoCplifeBasicserviceInitializeRequest ();
                   $requests->setBizContent("{" .
                       "\"community_id\":\"".$community->community_id."\"," .
                       "\"service_type\":\"PROPERTY_PAY_BILL_MODE\"," .
                       "\"external_invoke_address\":\"".$xternal_invoke_address."\"," .
                       "\"account_type\":\"ALIPAY_PARTNER_ID\"," .
                       "\"account\":\"".$community->account."\"," .
                       "\"service_expires\":\"2030-12-31 23:59:59\"" .
                       "  }");
               }else{
                   $aop->method="alipay.eco.cplife.basicservice.modify";
                   $requests = new AlipayEcoCplifeBasicserviceModifyRequest ();
                   $requests->setBizContent("{" .
                       "\"community_id\":\"".$community->community_id."\"," .
                       "\"service_type\":\"PROPERTY_PAY_BILL_MODE\"," .
                       "\"status\":\"".$basicservice_status."\"," .
                       "\"external_invoke_address\":\"".$xternal_invoke_address."\"," .
                       "\"account_type\":\"ALIPAY_PARTNER_ID\"," .
                       "\"account\":\"".$community->account."\"," .
                       "\"service_expires\":\"2030-12-31 23:59:59\"" .
                       "  }");
               }
                $result = $aop->execute ( $requests,"",$app_auth_token);
                $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    $data['basicservice_status']=$result->$responseNode->status;
                    Community::where("community_id",$community->community_id)->update($data);
                    return json_encode([
                        "success"=> 1,
                        "msg"=>"初始化小区信息成功"
                    ]);
                } else {
                    return json_encode([
                        "success"=> $result->$responseNode->code,
                        "msg"=>$result->$responseNode->msg
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
            "msg"=>$error.$line
        ]);
    }

}