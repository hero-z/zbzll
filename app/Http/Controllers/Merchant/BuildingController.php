<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Community;
use App\Models\Company_info;
use App\Models\RoomInfo;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller{
    //楼宇信息
    public function BuildingInfo(Request $request)
    {
        $line='';
        try{
            $out_community_id=$request->get("out_community_id");
            $where=[];
            if($out_community_id){
                $where[]=["buildings.out_community_id",$out_community_id];
            }
            $building_name=$request->get("building_name");
            $array=[];
            if($building_name){
                $array[]=["buildings.building_name","like","%".$building_name."%"];
            }
            $merchant_id=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
            //楼宇信息
            $buildingInfo=DB::table("buildings")
                ->join("communities","buildings.out_community_id","=","communities.out_community_id")
                ->whereIn("communities.merchant_id",$merchant_id)
                ->where($where)
                ->where($array)
                ->select("communities.community_name","communities.alipay_status","communities.basicservice_status","buildings.*")
                ->orderBy("buildings.created_at","DESC")
                ->paginate(8);
            //小区信息
            $communityInfo=Community::whereIn('merchant_id',$merchant_id)->select('community_name','id','out_community_id')->get();
            return view('merchant.building.buildinginfo',compact('buildingInfo',"communityInfo",'building_name','out_community_id'));
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));
    }

    public function createBuilding(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('addBuilding')){
                if($request->isMethod('POST')){
                    $data['out_community_id']=trim($request->get("out_community_id"));
                    $data['building_name']=trim($request->get('building_name'));
                    $data['level']=trim($request->get('level'));
                    $data['type']=trim($request->get('type'));
                    $data['description']=trim($request->get('description'));
                    $unit_name=$request->get('unit_name');
                    $community=Community::where('out_community_id',$data['out_community_id'])->first();
                    if($community){
                        $mids=CheckMerchantController::CheckMerchant(Auth::guard('merchant')->user()->id);
                        if(in_array($community->merchant_id,$mids)){
                            $currentb=Building::where("building_name",$data['building_name'])
                                ->where("out_community_id",$data['out_community_id'])
                                ->first();
//                            dd($currentb);
                            if(!$currentb){
                                //获取插入的楼宇ID
                                $bobj=Building::create($data);
                                if($bobj){
                                    if($unit_name){
                                        foreach($unit_name as $v){
                                            //插入并关联单元
                                            Unit::create([
                                                'building_id'=>$bobj->id,
                                                'unit_name'=>$v,
                                            ]);
                                        }
                                    }
                                    return json_encode([
                                        "success"=>1,
                                        "msg"=>"添加楼宇成功"
                                    ]);
                                }else{
                                    $error='添加楼宇失败';
                                }
                            }else{
                                $error='该楼栋名已存在';
                            }
                        }else{
                            $error='无权操作';
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
    //获取单个楼宇
    public function getBuilding(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('buildingManage')){
                if($request->isMethod('POST')){
                    $bid=$request->bid;
                    $building=Building::find($bid);
                    if($building){
                        $units=Unit::where("building_id",$bid)->get();
                        return json_encode([
                            "success"=>1,
                            "building"=>$building,
                            "units"=>$units,
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
    //删除单元
    public function deleteUnit(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('buildingManage')){
                if($request->isMethod('POST')){
                    $unitid=$request->unitid;
                    $unit=Unit::find($unitid);
                    if($unit){
                        if(!RoomInfo::where("unit_id",$unitid)->first()){
                            $re=$unit->delete();
                            if($re){
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>"删除成功"
                                ]);
                            }else{
                                $error='删除失败';
                            }
                        }else{
                            $error='该单元下有房屋,无法删除';
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
    //编辑楼宇
    public function editBuilding(Request $request)
    {
        $line='';
        $error='';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('buildingManage')){
                if($request->isMethod('POST')){
                    $bid=$request->building_id;
                    $out_community_id=$request->out_community_id;
                    $data['building_name']=trim($request->building_name);
                    $data['level']=trim($request->level);
                    $data['type']=trim($request->type);
                    $data['description']=trim($request->description);
                    $units=$request->units;
                    $units_n=$request->units_n;
                    $room_info_set=DB::table("room_infos")
                        ->join("units","room_infos.unit_id","=","units.id")
                        ->join("buildings","units.building_id","=","buildings.id")
                        ->where('buildings.id',$bid)
                        ->where("room_infos.status","!=","NONE")
                        ->get()
                        ->toArray();
                    if(!empty($room_info_set)){
                        if($units_n){
                            foreach ($units_n as $v){
                                Unit::create([
                                    'building_id'=>$bid,
                                    'unit_name'=>$v,
                                ]);
                            }
                            $error='新单元已添加,';
                        }
                        $error.='如需修改楼宇信息,请先删除其下房屋!';
                    }else{
                        $building=Building::where('building_name',$data['building_name'])
                            ->where('out_community_id',$out_community_id)
                            ->where('id','!=',$bid)
                            ->first();
                        if(!$building){
                            if(Building::find($bid)->update($data)){
                                if($units){
                                    foreach ($units as $v){
                                        $unit=explode('***',$v);
                                        Unit::find($unit[1])->update([
                                            'unit_name'=>$unit[0]
                                        ]);
                                    }
                                }
                                if($units_n){
                                    foreach ($units_n as $v){
                                        Unit::create([
                                            'building_id'=>$bid,
                                            'unit_name'=>$v,
                                        ]);
                                    }
                                }
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>"更新成功"
                                ]);
                            }else{
                                $error='更新信息失败';
                            }
                        }else{
                            $error='小区该楼栋名称已存在';
                        }
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
    //删除楼宇
    public function deleteBuilding(Request $request)
    {
        $line='';
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()||CheckRolePermissionController::CheckPremission('deleteBuilding')){
                if($request->isMethod('POST')){
                    $bid=$request->bid;
                    $building=Building::find($bid);
                    if($building){
                        $community=Community::where('out_community_id',$building->out_community_id)->first();
                        if($community&&$community->alipay_status=='NONE'&&$community->basicservice_status=="NONE"){
                            if(!Unit::where('building_id',$bid)->first()){
                                $re=$building->delete();
                                if($re){
                                    return json_encode([
                                        "success"=>1,
                                        "msg"=>"删除成功"
                                    ]);
                                }else{
                                    $error='删除失败';
                                }
                            }else{
                                $error='该楼栋有单元,请先删除单元';
                            }
                        }else{
                            $error='楼宇所属小区状态不允许删除！';
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
}