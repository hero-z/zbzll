<?php
namespace App\Http\Controllers\Merchant;
use App\Models\Bill;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\Residentinfo;
use App\Models\RoomInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Packages\alipay\request\AlipayEcoCplifeRoominfoDeleteRequest;
use Packages\alipay\request\AlipayEcoCplifeRoominfoUploadRequest;

class RoomInfoController extends BaseController{
    public function roomInfo(Request $request)
    {
        $where=[];
        $out_community_id=$request->get("out_community_id");
        if($out_community_id){
            $where[]=["communities.out_community_id",$out_community_id];
        }
        $roomwhere=[];
        $room=$request->room;
        if($room){
            $roomwhere[]=['room_infos.room','like','%'.$room."%"];
        }
       try{
           $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
           $roomInfo=DB::table("room_infos")
               ->join("units","room_infos.unit_id","=","units.id")
               ->join("buildings","units.building_id","=","buildings.id")
               ->join('residentinfos','room_infos.out_room_id','=','residentinfos.out_room_id')
               ->join("communities","buildings.out_community_id","=","communities.out_community_id")
               ->whereIn("communities.merchant_id",$merchant_id)
               ->where($where)
               ->where($roomwhere)
               ->select("communities.community_name","communities.alipay_status","communities.basicservice_status",'residentinfos.name','residentinfos.phone',"buildings.building_name","units.unit_name","room_infos.*")
               ->orderBy("room_infos.created_at","DESC")
               ->paginate(8);
            //小区信息
           $communityInfo=Community::whereIn('merchant_id',$merchant_id)->select('community_name','out_community_id')->get();
           return view ('merchant.room.roominfo',compact('roomInfo','communityInfo','out_community_id','room'));
       }catch(\Exception $e){
           $error=$e->getMessage();
           $line=$e->getLine();
       }
        return view('error',compact('line','error'));
    }
    //添加房屋联动
    public function getRoomInfo(Request $request)
    {
        try{
            $out_community_id=$request->get('out_community_id');
            $where=[];
            if($out_community_id){
                $table="buildings";
                $where[]=['out_community_id',$out_community_id];
            }
            $building_id=$request->get('building_id');

            if($building_id){
                $table="units";
                $where[]=['building_id',$building_id];
            }
            $unit_id=$request->get('unit_id');
            if($unit_id){
                $table="room_infos";
                $where[]=['unit_id',$unit_id];
            }
            $info=DB::table($table)
                ->where($where)
                ->orderBy("created_at","DESC")
                ->get()
                ->toArray();
            return json_encode([
                'data'=>$info,
                "success"=>1
            ]);
        }catch(\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            "success"=>0,
            "msg"=>$error.$line
        ]);
    }
    //添加房屋
    public function createRoom(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addRoom')){
            $room['out_room_id']=date('YmdHis').time().rand(100000,999999);
            $room['out_community_id']=$request->out_community_id;
            $room['room']=$request->room;
            $room['building_id']=$request->building_id;
            $room['unit_id']=$request->unit_id;
            $room['area']=$request->area;
            $address=DB::table('units')
                ->join('buildings',"units.building_id",'=','buildings.id')
                ->join('communities','buildings.out_community_id','=','communities.out_community_id')
                ->where('buildings.out_community_id',$request->out_community_id)
                ->where('units.id',$room['unit_id'])
                ->where('buildings.id',$room['building_id'])
                ->first();
            $room['community_id']=$address->community_id;
            $room['address']=$address->building_name.$address->unit_name.$request->room;
            $resident['out_room_id']=$room['out_room_id'];
            $resident['phone']=$request->phone;
            $resident['name']=$request->name;
            if( RoomInfo::create($room)&&Residentinfo::create($resident)){
                return json_encode([
                   'success'=>1,
                   'msg'=>'添加成功'
                ]);
            }else{
                DB::rollback();
                return json_encode([
                    'success'=>0,
                    'msg'=>'失败'
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
    //批量添加房屋
    public function createRooms(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addRoom')){
                $file = public_path($request->get('file'));
                $out_community_id=$request->out_community_id;
                $building_id=$request->building_id;
                $unit_id=$request->unit_id;

                $address=DB::table('units')
                    ->join('buildings',"units.building_id",'=','buildings.id')
                    ->join('communities','buildings.out_community_id','=','communities.out_community_id')
                    ->where('buildings.out_community_id',$request->out_community_id)
                    ->where('units.id',$unit_id)
                    ->where('buildings.id',$building_id)
                    ->first();
                $community_id=$address->community_id;
                $address=$address->building_name.$address->unit_name;
               $excel= Excel::load($file, function($reader) {
                   $reader->noHeading();
                    $excel = $reader->all();
                })->toArray();
                foreach($excel as $k=>$v){
                    if($k==0){
                        continue;
                    }
                        $room['out_community_id']=$out_community_id;
                        $room['building_id']=$building_id;
                        $room['unit_id']=$unit_id;
                        $room['community_id']=$community_id;
                        $room['room']=$excel[$k][0];
                        $room['area']=$excel[$k][1];
                        $room['out_room_id']=date('YmdHis').time().rand(100000,999999);;
                        $room['address']=$address.$excel[$k][0];
                        $resident['name']=$excel[$k][2];
                        $resident['phone']=$excel[$k][3];
                        $resident['out_room_id']=$room['out_room_id'];
                        $check=RoomInfo::where('out_community_id',$out_community_id)
                            ->where('building_id',$building_id)
                            ->where('unit_id',$unit_id)
                            ->where('room',$room['room'])
                            ->first();
                        if($check){
                            return json_encode([
                                'success'=>0,
                                'msg'=> "第".($k+1)."条信息已存在,请检查是否输入有误!"
                            ]);
                        }
                        if( RoomInfo::create($room)&&Residentinfo::create($resident)){

                        }else{
                            DB::rollback();
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
    public function uploadRoom(Request $request){
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('uploadRoom')){
                $id=$request->id;
                //获取物业公司主账号id
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                //获取物业公司授权token
                $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                $room_info_set=DB::table("room_infos")
                    ->join("units","room_infos.unit_id","=","units.id")
                    ->join("buildings","units.building_id","=","buildings.id")
                    ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                    ->where('room_infos.id',$id)
                    ->where("room_infos.status","NONE")
                    ->select("room_infos.out_room_id","communities.community_id","buildings.building_name as building","units.unit_name as unit","room_infos.room")
                    ->get()
                    ->toArray();
                foreach( $room_info_set as $k =>$v){
                    $room_info_set[$k]->address= $room_info_set[$k]->building. $room_info_set[$k]->unit. $room_info_set[$k]->room;
                    $data['community_id'] = $room_info_set[$k]->community_id;
                }
                $data['batch_id'] = time() . rand(10000, 99999) . date("YmdHis");
                $room_info_set=json_encode($room_info_set);
                $aop = $this->AopClient ();
                $aop->method="alipay.eco.cplife.roominfo.upload";
                $requests = new AlipayEcoCplifeRoominfoUploadRequest ();
                $requests->setBizContent("{" .
                    "\"batch_id\":\"".$data['batch_id']."\"," .
                    "\"community_id\":\"".$data['community_id']."\"," .
                    "      \"room_info_set\":".$room_info_set .
                    "  }");
                $result = $aop->execute ( $requests,"",$app_auth_token);
                $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    $data['status']="ONLINE";
                    foreach($result->$responseNode->room_info_set as $k=>$v){
                        $data['room_id']=$result->$responseNode->room_info_set[$k]->room_id;
                        RoomInfo::where("out_room_id",$result->$responseNode->room_info_set[$k]->out_room_id)->update($data);
                    }
                    return json_encode([
                        "success"=> 1,
                        "msg"=>'同步房屋信息成功!'
                    ]);
                } else {
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"同步房屋信息失败".$result->$responseNode->sub_msg
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
    //批量同步到支付宝
    public function uploadRooms(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('uploadRoom')){
                $unit_id=$request->unit_id;
                //获取物业公司主账号id
                $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                //获取物业公司授权token
                $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                $room_info_set=DB::table("room_infos")
                    ->join("units","room_infos.unit_id","=","units.id")
                    ->join("buildings","units.building_id","=","buildings.id")
                    ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                    ->where('units.id',$unit_id)
                    ->where("room_infos.status","NONE")
                    ->select("room_infos.out_room_id","communities.community_id","buildings.building_name as building","units.unit_name as unit","room_infos.room")
                    ->get()
                    ->toArray();
                if(empty($room_info_set)){
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"该单元没有可更新的房屋!"
                    ]);
                }
                foreach( $room_info_set as $k =>$v){
                    $room_info_set[$k]->address= $room_info_set[$k]->building. $room_info_set[$k]->unit. $room_info_set[$k]->room;
                    $data['community_id'] = $room_info_set[$k]->community_id;
                }
                $data['batch_id'] = time() . rand(10000, 99999) . date("YmdHis");
                $room_info_set=json_encode($room_info_set);
                $aop = $this->AopClient ();
                $aop->method="alipay.eco.cplife.roominfo.upload";
                $requests = new AlipayEcoCplifeRoominfoUploadRequest ();
                $requests->setBizContent("{" .
                    "\"batch_id\":\"".$data['batch_id']."\"," .
                    "\"community_id\":\"".$data['community_id']."\"," .
                    "      \"room_info_set\":".$room_info_set .
                    "  }");
                $result = $aop->execute ( $requests,"",$app_auth_token);
                $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                $resultCode = $result->$responseNode->code;
                if(!empty($resultCode)&&$resultCode == 10000){
                    $data['status']="ONLINE";
                    foreach($result->$responseNode->room_info_set as $k=>$v){
                        $data['room_id']=$result->$responseNode->room_info_set[$k]->room_id;
                        RoomInfo::where("out_room_id",$result->$responseNode->room_info_set[$k]->out_room_id)->update($data);
                    }
                    return json_encode([
                        "success"=> 1,
                        "msg"=>'同步房屋信息成功!'
                    ]);
                } else {
                    return json_encode([
                        "success"=> 0,
                        "msg"=>"同步房屋信息失败".$result->$responseNode->sub_msg
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
    //删除房屋
    public function delRoom(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('delRoom')){
                $out_room_id=$request->out_room_id;
                //判断房屋下是否有账单
                if(Bill::where('out_room_id',$out_room_id)->first()){
                    return json_encode([
                       'success'=>0,
                       'msg'=> "该房屋下已有账单,无法删除,你可以选择更新账单信息!"
                    ]);
                }
                $roomInfo=RoomInfo::where('out_room_id',$out_room_id)->first();
                $residentInfo=Residentinfo::where('out_room_id',$out_room_id)->first();
                if($roomInfo&&$roomInfo->status=="NONE"){
                    $roomInfo->delete();
                    if($residentInfo){
                        $residentInfo->delete();
                    }
                    return json_encode([
                       'success'=>1,
                       "msg"=>'删除成功'
                    ]);
                }else{
                    $aop = $this->AopClient ();
                    $aop->method="alipay.eco.cplife.roominfo.delete";
                    //获取物业公司主账号id
                    $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                    //获取物业公司授权token
                    $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                    $room_info_set=DB::table("room_infos")
                        ->join("units","room_infos.unit_id","=","units.id")
                        ->join("buildings","units.building_id","=","buildings.id")
                        ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                        ->where("room_infos.out_room_id",$out_room_id)
                        ->where("room_infos.status","!=","NONE")
                        ->select("room_infos.out_room_id","communities.community_id","room_infos.batch_id")
                        ->get();
                    foreach( $room_info_set as $k =>$v){
                        $room_info_set[$k]=$v->out_room_id;
                        $community_id = $v->community_id;
                        $batch_id=$v->batch_id;
                    }
                    $out_room_id=json_encode($room_info_set);//小区房屋信息out_room_id数组json集合
                    $requests = new AlipayEcoCplifeRoominfoDeleteRequest ();
                    $requests->setBizContent("{" .
                        "\"batch_id\":\"".$batch_id."\"," .
                        "\"community_id\":\"".$community_id."\"," .
                        "      \"out_room_id_set\":".$out_room_id .
                        "  }");

                    $result = $aop->execute ( $requests,"",$app_auth_token);
                    $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                    $resultCode = $result->$responseNode->code;
                    if(!empty($resultCode)&&$resultCode == 10000){
                        $roomInfo->delete();
                        if($residentInfo){
                            $residentInfo->delete();
                        }
                        return json_encode([
                            "success"=> 1,
                            "msg"=>"删除成功"
                        ]);
                    } else {
                        return json_encode([
                            "success"=>0,
                            "msg"=>$result->$responseNode->sub_msg
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
            "msg"=>"删除失败".$error.$line
        ]);
    }
    //批量删除房屋
    public function delRooms(Request $request)
    {
        $line=0;
        $error="未知错误";
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('delRoom')){

                $unit_id=$request->unit_id;
                $room_info_set=DB::table("room_infos")
                    ->join("units","room_infos.unit_id","=","units.id")
                    ->join("buildings","units.building_id","=","buildings.id")
                    ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                    ->join('bills','room_infos.out_room_id',"!=","bills.out_room_id")
                    ->where('units.id',$unit_id)
                    ->where("room_infos.status","NONE")
                    ->select("room_infos.out_room_id")
                    ->get();

                $room_info_sets=DB::table("room_infos")
                    ->join("units","room_infos.unit_id","=","units.id")
                    ->join("buildings","units.building_id","=","buildings.id")
                    ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                    ->join('bills','room_infos.out_room_id',"!=","bills.out_room_id")
                    ->where('units.id',$unit_id)
                    ->where("room_infos.status","!=","NONE")
                    ->select("room_infos.out_room_id","communities.community_id","room_infos.batch_id")
                    ->get();
                if(!$room_info_set&&!$room_info_sets){
                    return json_encode([
                        "success"=>0,
                        "msg"=>"该单元下没有可供删除的房屋信息"
                    ]);
                }
                if($room_info_set){
                    foreach($room_info_set as $k=>$v){
                        RoomInfo::where('out_room_id',$v->out_room_id)->delete();
                        Residentinfo::where('out_room_id',$v->out_room_id)->delete();
                    }
                }
               if($room_info_sets){
                   //获取物业公司主账号id
                   $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                   //获取物业公司授权token
                   $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
                       $aop = $this->AopClient ();
                       $aop->method="alipay.eco.cplife.roominfo.delete";
                       foreach($room_info_sets as $k=>$v){
                           $community_id = $v->community_id;
                           $room_info_set[$k]=$v->out_room_id;
                       }
                       $batch_id=date("YmdHis").time().rand(10000,99999);
                       $out_room_id=json_encode($room_info_set);//小区房屋信息out_room_id数组json集合
                       $requests = new AlipayEcoCplifeRoominfoDeleteRequest ();
                       $requests->setBizContent("{" .
                           "\"batch_id\":\"".$batch_id."\"," .
                           "\"community_id\":\"".$community_id."\"," .
                           "      \"out_room_id_set\":".$out_room_id .
                           "  }");
                       $result = $aop->execute ( $requests,"",$app_auth_token);
                       $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                       $resultCode = $result->$responseNode->code;
                       if(!empty($resultCode)&&$resultCode == 10000){
                            RoomInfo::whereIn('out_room_id',$room_info_set)->delete();
                            Residentinfo::whereIn('out_room_id',$room_info_set)->delete();
                       } else {
                           return json_encode([
                               "success"=>0,
                               "msg"=>$result->$responseNode->sub_msg
                           ]);
                       }
               }
                return json_encode([
                    "success"=> 1,
                    "msg"=>"删除成功"
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
            "msg"=>"删除失败".$error.$line
        ]);
    }

    public function ceshi(Request $request){
        //获取物业公司主账号id
        $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
        //获取物业公司授权token
        $app_auth_token=Company_info::where('merchant_id',$merchant_id)->where('status',1)->select('app_auth_token')->first()->app_auth_token;
        $aop = $this->AopClient ();$aop = $this->AopClient ();
        $aop->method="alipay.eco.cplife.roominfo.delete";
        $requests = new AlipayEcoCplifeRoominfoDeleteRequest ();
        $out_room_id=RoomInfo::where('community_id','A4K36WR905001')->get();
        foreach($out_room_id as $k=>$v){
            $room_info_set[$k]=$v->out_room_id;
        }
        $out_room_id=json_encode($room_info_set);
        $batch_id=date("YmdHis").time().rand(10000,99999);
        $requests->setBizContent("{" .
            "\"batch_id\":\"".$batch_id."\"," .
            "\"community_id\":\"A4K36WR905001\"," .
            "      \"out_room_id_set\":".$out_room_id .
            "  }");
        $result = $aop->execute ( $requests,"",$app_auth_token);
        dd($result);
    }
}