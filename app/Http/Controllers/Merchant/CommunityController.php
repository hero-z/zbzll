<?php
namespace App\Http\Controllers\Merchant;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Packages\alipay\request\AlipayEcoCplifeCommunityCreateRequest;
use Packages\alipay\request\AlipayEcoCplifeCommunityDetailsQueryRequest;
use Packages\alipay\request\AlipayEcoCplifeCommunityModifyRequest;

class CommunityController extends BaseController
{
    //小区首页
    public function index(){
        try{
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id) ;
           //获取小区信息
            $communityInfo=Community::whereIn('merchant_id',$merchant_id)->OrderBy("created_at","desc")->paginate(8);
            return view('merchant.community.communityInfo',compact('communityInfo'));
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            return view('error',compact('line','error'));
        }
    }
    //创建物业小区
    public  function createCommunity(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckPremission('createCommunity')){
                $data['community_name']=$request->get("community_name");
                //检验是否为空
                $check=[];
                if(!$data['community_name']){
                    $check['community_name']=1;
                }
                $data['community_address']=$request->get("community_address");
                if(!$data['community_address']){
                    $check['community_address']=1;
                }
                $data['district_code']=$request->get("district_code");
                $data['city_code']=$request->get("city_code");
                $data['province_code']=$request->get("province_code");
                $data['province']=$request->province;
                if(!$data['province']){
                    $check['province']=1;
                }
                $data['city']=$request->city;
                if(!$data['city']){
                    $check['city']=1;
                }
                $data['district']=$request->district;
                if(!$data['district']){
                    $check['district']=1;
                }
                $community_locations=$request->get("community_locations");
                if(!$community_locations){
                    $check['community_locations']=1;
                }
                $data['community_locations']=str_replace(",","|",$community_locations);//视情况处理|分割
                $data['hotline']=$request->get("hotline");
                if(!$data['hotline']){
                    $check['hotline']=1;
                }
                $data['account']=$request->get("account");
                if(!$data['account']){
                    $check['account']=1;
                }
                if($check){
                    return json_encode($check);
                }
                $data['out_community_id']=time().rand(100000,999999);
                $data['merchant_id']=Auth::guard('merchant')->user()->id;
                if(Community::create($data)){
                    return json_encode([
                        "success"=>1,
                        "msg"=>"创建小区成功,如需上线,请同步到支付宝!"
                    ]);
                }else{
                    return json_encode([
                        "succcess"=>0,
                        "msg"=>"创建失败,请联系开发人员!"
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
    //同步小区到支付宝
    public function uploadCommunity(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckPremission('uploadCommunity')) {
                 $id=$request->id;
                 //获取小区信息
                 $communityInfo=Community::where('id',$id)->first();
                 //获取物业公司主账号id
                 $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                 //获取物业公司授权token
                 $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                 $community_name=$communityInfo->community_name;
                 $community_address=$communityInfo->community_address;
                 $district_code=$communityInfo->district_code;
                 $city_code=$communityInfo->city_code;
                 $province_code=$communityInfo->province_code;
                 $community_locations=$communityInfo->community_locations;
                 $hotline=$communityInfo->hotline;
                 $out_community_id=$communityInfo->out_community_id;
                 $aop=$this->AopClient();
                 $aop->method="alipay.eco.cplife.community.create";
                 $requests = new AlipayEcoCplifeCommunityCreateRequest ();
                    $requests->setBizContent("{" .
                        "\"community_name\":\"".$community_name."\"," .
                        "\"community_address\":\"".$community_address."\"," .
                        "\"district_code\":\"".$district_code."\"," .
                        "\"city_code\":\"".$city_code."\"," .
                        "\"province_code\":\"".$province_code."\"," .
                        "      \"community_locations\":[" .
                        "        \"".$community_locations."\"" .
                        "      ]," .
                        "\"hotline\":\"".$hotline."\"," .
                        "\"out_community_id\":\"".$out_community_id."\"" .
                        "  }");
                    $result = $aop->execute ( $requests,"",$app_auth_token);
                    $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                    $resultCode = $result->$responseNode->code;
                    if(!empty($resultCode)&&$resultCode == 10000){
                        $data['alipay_status']=$result->$responseNode->status;
                        $data['community_id']=$result->$responseNode->community_id;
                        Community::where("id",$id)->update($data);
                        return json_encode([
                            "success"=>1,
                            "msg"=>'同步成功'
                        ]);
                    } else {
                        return json_encode([
                            "success"=>0,
                            "msg"=>$result->$responseNode->msg
                        ]);
                    }
            }else{
                $error='您还没有权限操作';
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
    //编辑时获取小区信息
    public function getCommunity(Request $request){
        $id=$request->id;
        try{
        //获取小区信息
        $communityInfo=Community::where('id',$id)->first();
        if($communityInfo){
            return json_encode([
                "success"=>1,
                "data"=>$communityInfo
            ]);
        }else{
            return json_encode([
                "success"=>0,
                "msg"=>"获取小区信息失败"
            ]);
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
    //更新小区信息
    public function editCommunity(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckPremission('editCommunity')) {
              $out_community_id=$request->out_community_id;
              $alipay_status=$request->alipay_status;
              $community=Community::where('out_community_id',$out_community_id)->first();
              $data['account']=$request->account;
              $data['community_address']=$request->community_address;
              $data['community_name']=$request->community_name;
              $data['community_locations']=$request->community_locations;
              $data['hotline']=$request->hotline;
              if($request->province_code!='-1'){
                  $data['province_code']=$request->province_code;
                  $data['province']=$request->province;
              }else{
                  $data['province_code']=$community->province_code;
              }
                if($request->city_code!='-1'){
                    $data['city_code']=$request->city_code;
                    $data['city']=$request->city;
                }else{
                    $data['city_code']=$community->city_code;
                }
                if($request->district_code!='-1'){
                    $data['district_code']=$request->district_code;
                    $data['district']=$request->district;
                }else{
                    $data['district_code']=$community->district_code;
                }
              //判断小区状态
              if($alipay_status=="NONE"){
                if(Community::where("out_community_id",$out_community_id)->update($data)){
                    return json_encode([
                        "success"=>1,
                        "msg"=>"修改小区信息成功!"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "msg"=>"修改小区信息失败"
                    ]);
                }
              }
              if($alipay_status=="ONLINE"||$alipay_status=="PENDING_ONLINE"||$alipay_status=="OFFLINE"){
                  $aop = $this->AopClient ();
                  $aop->method="alipay.eco.cplife.community.modify ";
                  //获取物业公司主账号id
                  $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                  //获取物业公司授权token
                  $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                  $requests = new AlipayEcoCplifeCommunityModifyRequest ();
                  $requests->setBizContent("{" .
                      "\"community_id\":\"".$community['community_id']."\"," .
                      "\"community_name\":\"".$data['community_name']."\"," .
                      "\"community_address\":\"".$data['community_address']."\"," .
                      "\"district_code\":\"".$data['district_code']."\"," .
                      "\"city_code\":\"".$data['city_code']."\"," .
                      "\"province_code\":\"".$data['province_code']."\"," .
                      "      \"community_locations\":[" .
                      "        \"".$data['community_locations']."\"" .
                      "      ]," .
                      "\"hotline\":\"".$data['hotline']."\"," .
                      "\"out_community_id\":\"".$out_community_id."\"" .
                      "  }");
                  $result = $aop->execute ( $requests,"",$app_auth_token);
                  $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                  $resultCode = $result->$responseNode->code;
                  if(!empty($resultCode)&&$resultCode == 10000){
                      $data['alipay_status']=$result->$responseNode->status;
                      Community::where("out_community_id",$out_community_id)->update($data);
                      return json_encode([
                          "success"=> 1,
                          "msg"=>"修改小区信息成功"
                      ]);
                  } else {
                      return json_encode([
                          "success"=> 0,
                          "msg"=>$result->$responseNode->sub_msg
                      ]);
                  }
              }else{
                  return json_encode([
                      "success"=>0,
                      "msg"=>"修改小区信息失败"
                  ]);
              }
            }else{
                $error='您还没有权限操作';
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
    //删除小区
    public function delCommunity(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckPremission('delCommunity')){
            $id=$request->id;
            $community=Community::where("id",$id)->first();
            if($community->alipay_status=="NONE"){
                if(Community::where('id',$id)->delete()){
                    return json_encode([
                        "success"=>1,
                        "msg"=>"小区已删除!"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "msg"=>"删除小区失败!"
                    ]);
                }
            }else{
                return json_encode([
                    "success"=>0,
                    "msg"=>"删除小区失败!"
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
    //查询单个物业小区信息
    public function getCode(Request $request){
            $aop = $this->AopClient ();
        //获取物业公司主账号id
        $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
        //获取物业公司授权token
        $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
            $aop->method="alipay.eco.cplife.community.details.query";
            $community_id=$request->community_id;
            $requests = new AlipayEcoCplifeCommunityDetailsQueryRequest ();
            $requests->setBizContent("{" .
                "\"community_id\":\"".$community_id."\"" .
                "  }");
            $result = $aop->execute ( $requests,"",$app_auth_token);
            $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            //更新小区状态
                return json_encode([
                   "success"=>1,
                   "msg"=>$result->$responseNode->community_services[0]->qr_code_image
                ]);
        }else{
            return json_encode([
                "success"=>0,
                "msg"=>"获取测试支付码失败"
            ]);
        }
    }
}