<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/26
 * Time: 16:19
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Company_info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyManageController extends Controller
{
    /**
     * @param Request $request
     * 物业公司信息
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function companysInfo(Request $request){
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($root||CheckRolePermissionController::CheckPremission('CompanysInfo')){
                $agentinfo=Auth::guard('admin')->user();
                $companystatus=$request->companystatus;
                $searchname=$request->searchname;
                $where=[];
                if($companystatus){
                    $where[]=['company_infos.status',$companystatus==3?0:$companystatus];
                }
                if($searchname){
                    $where[]=['company_name','like','%'.$searchname.'%'];
                }
                if($root){
                    $company=DB::table('company_infos')
                        ->join('admins','admins.id','company_infos.admin_id')
                        ->where($where)
                        ->select('company_infos.*','admins.name as admin_name')
                        ->orderby('company_infos.created_at','desc')
                        ->paginate(9);

                }else{
                    $where[]=['admin_id',$agentinfo->id];
                    $company=Company_info::where($where)
                        ->orderby('created_at','desc')
                        ->paginate(9);
                }
                return view('admin.company.companyinfo',compact('company','companystatus','searchname'));
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
     * 修改物业公司状态
     * @return string
     */
    public function changeStatus(Request $request)
    {
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $companyid=$request->company_id;
                $status=$request->status;
                //2额外权限 CompanyCheck CompanyClose
                $company=Company_info::find($companyid);
                if($company){
                    if($root||$company->admin_id==Auth::guard('admin')->user()->id){
                        $currentstatus=$company->status;
                        if($status-$currentstatus==1){
                            //升权
                            if($status==1){
                                //审核
                                if($root||CheckRolePermissionController::CheckPremission('CompanyCheck')){
                                    $company->status=$status;
                                    $re=$company->save();
                                    if($re){
                                        return json_encode([
                                            "success"=>1,
                                            "msg"=>'审核成功'
                                        ]);
                                    }else{
                                        $error='审核失败';
                                    }
                                }else{
                                    $error='亲,你目前还没有该操作权限';
                                }
                            }elseif($status==2){
                                //关闭服务
                                if($root||CheckRolePermissionController::CheckPremission('CompanyClose')){
                                    $company->status=$status;
                                    $re=$company->save();
                                    if($re){
                                        return json_encode([
                                            "success"=>1,
                                            "msg"=>'关闭服务成功'
                                        ]);
                                    }else{
                                        $error='关闭服务失败';
                                    }
                                }else{
                                    $error='亲,你目前还没有该操作权限';
                                }
                            }else{
                                $error='参数有误';
                            }
                        }elseif($currentstatus-$status==1){
                            //降权
                            if($currentstatus==2){
                                //重新开启服务
                                if($root){
                                    $company->status=$status;
                                    $re=$company->save();
                                    if($re){
                                        return json_encode([
                                            "success"=>1,
                                            "msg"=>'重新开启服务成功'
                                        ]);
                                    }else{
                                        $error='重新开启服务失败';
                                    }
                                }else{
                                    $error='亲,你目前还没有该操作权限';
                                }
                            }else{
                                $error='参数有误';
                            }
                        }else{
                            $error='状态操作异常';
                        }
                    }else{
                        $error='非法操作,没有权限';
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
     * 删除物业公司
     * @return string
     */
    public function delCompany(Request $request)
    {
        $line=0;
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                $companyid=$request->company_id;
                $status=$request->status;
                //2额外权限 CompanyCheck CompanyClose
                $company=Company_info::find($companyid);
                if($company){
                    if($root||$company->admin_id==Auth::guard('admin')->user()->id){
                        $currentstatus=$company->status;
                        if($currentstatus==0){
                            $re=$company->delete();
                            if($re){
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>'删除成功'
                                ]);
                            }else{
                                $error='删除失败';
                            }
                        }else{
                            $error='物业状态不允许删除!';
                        }
                    }else{
                        $error='非法操作,没有权限';
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

    /**root获取所有代理商
     * @param Request $request
     */
    public function getAllAgents(Request $request)
    {
        $line='';
        $error='';
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                if($root){
                    $agents=Admin::where('status',1)->pluck('name','id');
                    return json_encode([
                        "success"=>1,
                        "data"=>$agents
                    ]);
                }else{
                    $error='没有权限调用!';
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
            "msg"=>"获取信息失败!".$error.$line
        ]);
    }

    /**修改归属
     * @param Request $request
     * @return string
     */
    public function changeOwner(Request $request)
    {
        $line='';
        $error='';
        try{
            $root=CheckRolePermissionController::CheckRoleRoot();
            if($request->isMethod('POST')){
                if($root){
                    $password=trim($request->password);
                    $company_id=$request->company_id;
                    $targetagent=$request->targetagent;
                    if(Hash::check($password,Auth::guard('admin')->user()->password)){
                        $company=Company_info::find($company_id);
                        if($company){
                            $re=$company->update(['admin_id'=>$targetagent]);
                            if($re){
                                return json_encode([
                                    "success"=>1,
                                    "msg"=>"操作成功!"
                                ]);
                            }else{
                                $error='修改失败!';
                            }
                        }else{
                            $error='查询异常!';
                        }
                    }else{
                        $error='密码错误!';
                    }
                }else{
                    $error='没有权限调用!';
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
            "msg"=>"操作失败!".$error.$line
        ]);
    }
}