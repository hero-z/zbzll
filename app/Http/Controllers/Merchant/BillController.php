<?php
namespace App\Http\Controllers\Merchant;
use App\Models\Bill;
use App\Models\BillQuestion;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\Residentinfo;
use App\Models\RoomInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Packages\alipay\request\AlipayEcoCplifeBillBatchUploadRequest;
use Packages\alipay\request\AlipayEcoCplifeBillDeleteRequest;
use Packages\alipay\request\AlipayEcoCplifeBillModifyRequest;

class BillController extends BaseController{
    //账单信息差查询
    public function billInfo(Request $request)
    {
        $where=[];
        $out_community_id=$request->get("out_community_id");
        if($out_community_id){
            $where[]=["communities.out_community_id",$out_community_id];
        }
        $roomwhere=[];
        $room=$request->room;
        $unitwhere=[];
        $residentwhere=[];
        if($room){
            $roomwhere[]=['room_infos.room','like','%'.$room."%"];
            $unitwhere[]=['units.unit_name','like','%'.$room."%"];
            $residentwhere[]=['bills.remark_str','like','%'.$room."%"];
        }
        try{
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
            $billInfo=DB::table("bills")
                ->join('room_infos',"bills.out_room_id","=","room_infos.out_room_id")
                ->join("units","room_infos.unit_id","=","units.id")
                ->join("buildings","units.building_id","=","buildings.id")
                ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                ->whereIn("communities.merchant_id",$merchant_id)
                ->where($where)
                ->where($roomwhere)
                ->orwhere($unitwhere)
                ->orwhere($residentwhere)
                ->select("communities.community_name","communities.alipay_status","communities.basicservice_status","buildings.building_name","units.unit_name","room_infos.room","bills.*")
                ->orderBy("room_infos.room")
                ->paginate(8);
            //小区信息
            $communityInfo=Community::whereIn('merchant_id',$merchant_id)->select('community_name','out_community_id')->get();
            return view ('merchant.bill.billinfo',compact('billInfo','communityInfo','out_community_id','room'));
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }
    //添加账单
    public function addBill(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addBill')){
                $data=$request->except('_token');
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                $data['company_id']=Company_info::where('merchant_id',$merchant_id)->first()->id;
                $data['admin_id']=Company_info::where('merchant_id',$merchant_id)->first()->admin_id;
                $resident=Residentinfo::where('out_room_id',$request->out_room_id)->first();
                $data['remark_str']=$resident->name;
                $data['bill_entry_id']=date("YmdHis").rand(10000,99999).time();
                if(Bill::create($data)){
                    return json_encode([
                        "success"=>1,
                        "msg"=>'添加账单成功!'
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "msg"=>'添加账单失败!'
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
    //批量导入账单
    public function addBills(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addBill')){
                $file = public_path($request->get('file'));
                $data['cost_type']=$request->cost_type;
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                $data['company_id']=Company_info::where('merchant_id',$merchant_id)->first()->id;
                $data['admin_id']=Company_info::where('merchant_id',$merchant_id)->first()->admin_id;
                $excel= Excel::load($file, function($reader) {
                    $reader->noHeading();
                    $excel = $reader->all();
                })->toArray();
                foreach ($excel as $k=>$v){
                    if($k==0||$v==""){
                        continue;
                    }
                    $data['out_room_id']=$v[1];
                    $data['out_community_id']=RoomInfo::where('out_room_id',$v[1])->first()->out_community_id;
                    $community=Community::where("out_community_id", $data['out_community_id'])->first();
                    $data['community_id']=$community->community_id;
                    $resident=Residentinfo::where('out_room_id',$v[1])->first();
                    $data['remark_str']=$resident->name;
                    $data['acct_period']=$v[2];
                    $data['release_day']=$v[3];
                    $data['bill_entry_id']=date("YmdHis").rand(10000000,99999999).time();
                    $data['bill_entry_amount']=$v[5];
                    $data['deadline']=$v[6];
                    if( Bill::create($data)){

                    }else{
                        return json_encode([
                            'success'=>0,
                            'msg'=>"第".($k+1)."条信息出现异常,请检查是否输入有误!"
                        ]);
                    }
                }
                return json_encode([
                    "success"=>1,
                    "msg"=>"批量导入成功"
                ]);

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
    //单个同步到支付宝
    public function uploadBill(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('uploadBill')){
                $id=$request->id;
                $out_community_id=$request->out_community_id;
                //获取物业公司主账号id
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                //获取物业公司授权token
                $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                //获取要同步的账单
                $bill_set=Bill::where("id",$id)
                    ->where("bill_status","NONE")
                    ->select("out_room_id","bill_entry_id","cost_type","bill_entry_amount","acct_period","release_day","deadline","remark_str")
                    ->get();
                //遍历时间格式转为字符串格式
                foreach($bill_set as $k=>$v){
                    $bill_set[$k]->acct_period=str_replace('-','',$bill_set[$k]->acct_period);
                    $bill_set[$k]->release_day=str_replace('-','',$bill_set[$k]->release_day);
                    $bill_set[$k]->deadline=str_replace('-','',$bill_set[$k]->deadline);
                    if($bill_set[$k]->cost_type=="property_fee"){
                        $bill_set[$k]->cost_type="物业管理费";
                    }
                    if($bill_set[$k]->cost_type=="public_property_fee"){
                        $bill_set[$k]->cost_type="物业管理费公摊";
                    }
                    if($bill_set[$k]->cost_type=="rubbish_fee"){
                        $bill_set[$k]->cost_type="垃圾费";
                    }
                    if($bill_set[$k]->cost_type=="elevator_fee"){
                        $bill_set[$k]->cost_type="电梯费";
                    }
                }
                $community=Community::where('out_community_id',$out_community_id)->first();
                $data['batch_id'] = time() . date("YmdHis") . rand(1000000, 9999999);
                $data['community_id'] = $community->community_id;
                $bill_sets=json_encode($bill_set);
                $aop = $this->AopClient ();
                $aop->method = "alipay.eco.cplife.bill.batch.upload";
                $requests = new AlipayEcoCplifeBillBatchUploadRequest();
                $requests->setBizContent("{" .
                    "\"batch_id\":\"" . $data['batch_id'] . "\"," .
                    "\"community_id\":\"" . $data['community_id'] . "\"," .
                    "      \"bill_set\":" .$bill_sets.
                    "  }");
                $result = $aop->execute ( $requests,"",$app_auth_token);
                $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    $data['bill_status']="ONLINE";
                    foreach($bill_set as $k=>$v){
                        Bill::where("bill_entry_id",$bill_set[$k]->bill_entry_id)->update($data);
                    }
                    return json_encode([
                        "success"=> 1,
                        "msg"=>'同步账单信息成功!'
                    ]);
                } else {
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"同步账单信息失败".$result->$responseNode->sub_msg
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
            "msg"=>"同步失败".$error.$line
        ]);
    }
    //批量同步
    public function uploadBills(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('uploadBill')){
                $unit_id=$request->unit_id;
                $out_community_id=$request->out_community_id;
                //获取物业公司主账号id
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                //获取物业公司授权token
                $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                //获取要同步的账单
                $bill_set=DB::table('bills')
                    ->join('room_infos','bills.out_room_id',"=","room_infos.out_room_id")
                    ->join('units',"room_infos.unit_id","=",'units.id')
                    ->whereIn('bills.cost_type',["property_fee","public_property_fee","rubbish_fee","elevator_fee"])
                    ->where("units.id",$unit_id)
                    ->where("bill_status","NONE")
                    ->select("bills.out_room_id","bills.bill_entry_id","bills.cost_type","bills.bill_entry_amount","bills.acct_period","bills.release_day","bills.deadline","bills.remark_str")
                    ->get();
                if($bill_set->isEmpty()){
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"该单元下无可同步的账单!"
                    ]);
                }
                //遍历时间格式转为字符串格式
                foreach($bill_set as $k=>$v){
                    $bill_set[$k]->acct_period=str_replace('-','',$bill_set[$k]->acct_period);
                    $bill_set[$k]->release_day=str_replace('-','',$bill_set[$k]->release_day);
                    $bill_set[$k]->deadline=str_replace('-','',$bill_set[$k]->deadline);
                    if($bill_set[$k]->cost_type=="property_fee"){
                        $bill_set[$k]->cost_type="物业管理费";
                    }
                    if($bill_set[$k]->cost_type=="public_property_fee"){
                        $bill_set[$k]->cost_type="物业管理费公摊";
                    }
                    if($bill_set[$k]->cost_type=="rubbish_fee"){
                        $bill_set[$k]->cost_type="垃圾费";
                    }
                    if($bill_set[$k]->cost_type=="elevator_fee"){
                        $bill_set[$k]->cost_type="电梯费";
                    }
                }
                $community=Community::where('out_community_id',$out_community_id)->first();
                $data['batch_id'] = time() . rand(10000, 99999) . date("YmdHis");
                $data['community_id'] = $community->community_id;
                $bill_sets=json_encode($bill_set);
                $aop = $this->AopClient ();
                $aop->method = "alipay.eco.cplife.bill.batch.upload";
                $requests = new AlipayEcoCplifeBillBatchUploadRequest();
                $requests->setBizContent("{" .
                    "\"batch_id\":\"" . $data['batch_id'] . "\"," .
                    "\"community_id\":\"" . $data['community_id'] . "\"," .
                    "      \"bill_set\":" .$bill_sets.
                    "  }");
                $result = $aop->execute ( $requests,"",$app_auth_token);
                $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    $data['bill_status']="ONLINE";
                    foreach($bill_set as $k=>$v){
                        Bill::where("bill_entry_id",$bill_set[$k]->bill_entry_id)->update($data);
                    }
                    return json_encode([
                        "success"=> 1,
                        "msg"=>'同步账单信息成功!'
                    ]);
                } else {
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"同步账单信息失败".$result->$responseNode->sub_msg
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
            "msg"=>"同步失败".$error.$line
        ]);
    }
    //线下结算申请
    public function editLineBill(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('editLineBill')){
                $id=$request->id;
                if($request->bill_status=='ONLINE'){
                    $data['bill_status']="ONLINE_UNDERREVIEW";
                }else{
                    $data['bill_status']="UNDERREVIEW";
                }
                if($request->bill_status=="ONLINE_UNDERREVIEW"){
                    $billInfo=Bill::where('id',$id)->first();
                    //获取物业公司主账号id
                    $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                    //获取物业公司授权token
                    $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                    $aop = $this->AopClient ();
                    $aop->method="alipay.eco.cplife.bill.delete";
                    $community_id=$billInfo->community_id;
                    $bill_entry_id=$billInfo->bill_entry_id;
                    $requests = new AlipayEcoCplifeBillDeleteRequest ();
                    $requests->setBizContent("{" .
                        "\"community_id\":\"".$community_id."\"," .
                        "      \"bill_entry_id_list\":[" .
                        "        \"".$bill_entry_id."\"," .
                        "      ]" .
                        "  }");
                    $result = $aop->execute ( $requests,"",$app_auth_token);
                    $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                    $resultCode = $result->$responseNode->code;
                    if(!empty($resultCode)&&$resultCode == 10000){
                        $data['bill_status']="TRADE_SUCCESS";
                    } else {
                        return json_encode([
                            "success"=>0,
                            "msg"=>"提交审核失败!".$result->$responseNode->sub_msg
                        ]);
                    }
                }
                if($request->bill_status=='UNDERREVIEW'){
                    $data['bill_status']="TRADE_SUCCESS";
                }
                $data['type']="money";
                if(Bill::where('id',$id)->update($data)){
                    return json_encode([
                        "success"=>1,
                        "msg"=>"线下结算已提交审核!"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "msg"=>"提交审核失败!"
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
    //线下结算账单管理
    public function lineBillInfo()
    {

        try{
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
            $billInfo=DB::table("bills")
                ->join('room_infos',"bills.out_room_id","=","room_infos.out_room_id")
                ->join("units","room_infos.unit_id","=","units.id")
                ->join("buildings","units.building_id","=","buildings.id")
                ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                ->whereIn("communities.merchant_id",$merchant_id)
                ->whereIn('bills.bill_status',["UNDERREVIEW","ONLINE_UNDERREVIEW"])
                ->select("communities.community_name","communities.alipay_status","communities.basicservice_status","buildings.building_name","units.unit_name","room_infos.room","bills.*")
                ->orderBy("bills.created_at","DESC")
                ->paginate(8);
            return view ('merchant.bill.linebillinfo',compact('billInfo'));
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }
    //存疑账单提交
    public function questionbillsubmit(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('questionBill')){
                $data=$request->except('_token');
                $data['merchant_id']=Auth::guard('merchant')->user()->id;
                if(BillQuestion::where('bill_id',$request->bill_id)->first()){
                    $data['status']='NONE';
                    if(BillQuestion::where('bill_id',$request->bill_id)->update($data)){
                        return json_encode([
                            "success"=>1,
                            "msg"=>"申请提交成功!"
                        ]);
                    }else{
                        return json_encode([
                            "success"=>0,
                            "msg"=>"申请提交失败!"
                        ]);
                    }
                }else{
                    if(BillQuestion::create($data)){
                        return json_encode([
                            "success"=>1,
                            "msg"=>"申请提交成功!"
                        ]);
                    }else{
                        return json_encode([
                            "success"=>0,
                            "msg"=>"申请提交失败!"
                        ]);
                    }
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
    //存疑账单查询
    public function questionBillInfo(Request $request)
    {
        try{
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
            $billInfo=DB::table("bill_questions")
                ->join('bills','bill_questions.bill_id',"bills.id")
                ->join('room_infos',"bills.out_room_id","=","room_infos.out_room_id")
                ->join("units","room_infos.unit_id","=","units.id")
                ->join("buildings","units.building_id","=","buildings.id")
                ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                ->whereIn("communities.merchant_id",$merchant_id)
                ->select("communities.community_name","communities.alipay_status","communities.basicservice_status","buildings.building_name","units.unit_name","room_infos.room",'bills.bill_entry_amount',"bills.cost_type","bills.bill_status",'bills.acct_period',"bill_questions.*")
                ->orderBy("bills.created_at","DESC")
                ->paginate(8);
            return view ('merchant.bill.questionbillinfo',compact('billInfo'));
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }
    //矫正账单
    public function CheckBill(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('questionBill')){
                $bill_id=$request->bill_id;
                $data['bill_entry_amount']=$request->correct_bill_amount;
                $billInfo=Bill::where('id',$bill_id)->first();
                if($billInfo->bill_status=='ONLINE'){
                    //获取物业公司主账号id
                    $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                    //获取物业公司授权token
                    $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                    $aop = $this->AopClient ();
                    $aop->method="alipay.eco.cplife.bill.modify";
                    $community_id=$billInfo->community_id;
                    $bill_entry_amount=$request->correct_bill_amount;
                    $bill_entry_id=$billInfo->bill_entry_id;
                    $requests = new AlipayEcoCplifeBillModifyRequest ();
                    $requests->setBizContent("{" .
                        "\"community_id\":\"".$community_id."\"," .
                        "      \"bill_entry_list\":[{" .
                        "        \"bill_entry_id\":\"".$bill_entry_id."\"," .
                        "\"bill_entry_amount\":\"".$bill_entry_amount."\"," .
                        "        }]" .
                        "  }");
                    $result = $aop->execute ( $requests,"",$app_auth_token);
                    $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                    $resultCode = $result->$responseNode->code;
                    if(!empty($resultCode)&&$resultCode == 10000){
                        $billInfo->update($data);
                        $datas['status']="OK";
                        BillQuestion::where('bill_id',$bill_id)->update($datas);
                    } else {
                        return json_encode([
                            "success"=>0,
                            "msg"=>"矫正账单失败!".$result->$responseNode->sub_msg
                        ]);
                    }
                }else{
                    $billInfo->update($data);
                    $datas['status']="OK";
                    BillQuestion::where('bill_id',$bill_id)->update($datas);
                }
                return json_encode([
                    "success"=>1,
                    "msg"=>"矫正账单成功!"
                ]);
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
    //忽略账单
    public function delCheckBill(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('uploadBill')){
                $id=$request->id;
                if(BillQuestion::where('id',$id)->delete()){
                    return json_encode([
                        "success"=>1,
                        "msg"=>"请求已忽略!"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "msg"=>"请求忽略失败!"
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
            "msg"=>"同步失败".$error.$line
        ]);
    }
}