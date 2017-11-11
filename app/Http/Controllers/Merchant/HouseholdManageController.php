<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/29
 * Time: 20:58
 */

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Residentinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HouseholdManageController extends Controller
{
    public function houseHoldInfo(Request $request)
    {
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                $where=[];
                $namewhere=[];
                $out_community_id=$request->get("out_community_id");
                if($out_community_id){
                    $where[]=["communities.out_community_id",$out_community_id];
                }
                $name=$request->name;
                if($name){
                    $where[]=['room_infos.room','like','%'.$name."%"];
                    $namewhere[]=['residentinfos.name','like','%'.$name."%"];
                }
                $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
                $household=DB::table('residentinfos')
                    ->join('room_infos','residentinfos.out_room_id','room_infos.out_room_id')
                    ->join('units','room_infos.unit_id','units.id')
                    ->join('buildings','units.building_id','buildings.id')
                    ->join('communities','communities.out_community_id','buildings.out_community_id')
                    ->whereIn("communities.merchant_id",$merchant_id)
                    ->where("residentinfos.type",1)
                    ->where($where)
                    ->orwhere($namewhere)
                    ->select("communities.community_name",'residentinfos.id','residentinfos.name','residentinfos.out_room_id','residentinfos.phone',"buildings.building_name","units.unit_name","room_infos.room","residentinfos.remark","residentinfos.created_at")
                    ->orderBy("residentinfos.created_at","DESC")
                    ->paginate(8);
                //小区信息
                $communityInfo=Community::whereIn('merchant_id',$merchant_id)->select('community_name','out_community_id')->get();
                return view ('merchant.household.info',compact('household','communityInfo','out_community_id','name'));
            }else{
                $error='你还没有权限!';
            }
        }catch(\Exception $e){
            Log::info($e);
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }

    public function getHouseHold(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                if($request->isMethod('POST')){
                    $hid=$request->hid;
                    $household=Residentinfo::find($hid);
                    if($household){
                        return json_encode([
                            "success"=>1,
                            "data"=>$household
                        ]);
                    }else{
                        $error='查询异常';
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
            'success'=>0,
            'msg'=>'操作失败'.$error.$line,
        ]);
    }

    public function editHouseHold(Request $request)
    {
        $line='';
        $error='';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                if($request->isMethod('POST')){
                    $hid=$request->hid;
                    $data['name']=trim($request->name);
                    $data['phone']=trim($request->phone);
                    $data['remark']=trim($request->remark);
                    $household=Residentinfo::find($hid);
                    if($household){
                        $re=$household->update($data);
                        if($re){
                            return json_encode([
                                "success"=>1,
                                "msg"=>"操作成功"
                            ]);
                        }else{
                            $error='操作失败';
                        }
                    }else{
                        $error='查询异常';
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
            'success'=>0,
            'msg'=>$error.$line,
        ]);
    }

    public function getHouse(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                if($request->isMethod('POST')){
                    $out_room_id=$request->out_room_id;
                    $household=Residentinfo::where('out_room_id',$out_room_id)->get();
                    return json_encode([
                        "success"=>1,
                        "data"=>$household
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
            'success'=>0,
            'msg'=>'操作失败'.$error.$line,
        ]);
    }

    public function deleteHouse(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                if($request->isMethod('POST')){
                    $houseid=$request->houseid;
                    $house=Residentinfo::find($houseid);
                    if($house){
                        $re=$house->delete();
                        if($re){
                            return json_encode([
                                "success"=>1,
                                "msg"=>"删除成功"
                            ]);
                        }else{
                            $error='删除失败';
                        }
                    }else{
                        $error='查询异常';
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
            'success'=>0,
            'msg'=>'操作失败'.$error.$line,
        ]);
    }

    public function editHouses(Request $request)
    {
        $line='';
        $error='';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('householdManage')){
                if($request->isMethod('POST')){
                    $holds=$request->holds;
                    $holds_n=$request->holds_n;
                    $count=0;
                    if($holds){
                        foreach ($holds as $v){
                            $hold=Residentinfo::find($v[0]);
                            if($hold){
                                $hold->update([
                                    'name'=>$v[1],
                                    'phone'=>$v[2],
                                    'remark'=>$v[3],
                                ]);
                            }else{
                                $count++;
                            }
                        }
                    }
                    if($holds_n){
                        foreach ($holds_n as $v) {
                            $re=Residentinfo::create([
                                'name'=>$v[0],
                                'phone'=>$v[1],
                                'remark'=>$v[2],
                                'type'=>$v[3],
                                'out_room_id'=>$v[4],
                            ]);
                            if(!$re){
                                $count++;
                            }
                        }
                    }
                    return json_encode([
                                "success"=>1,
                                "msg"=>$count==0?'操作成功':$count.'条操作异常'
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
            'success'=>0,
            'msg'=>$error.$line,
        ]);
    }
}