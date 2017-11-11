<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/18
 * Time: 23:45
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    //角色管理
    public function rolePermission(Request $request){
        $line=0;
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('GET')){
                    $roles=Role::orderby('created_at','desc')->paginate(9);
                    return view('admin.agent.rolepermission',compact('roles'));
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
    //获取权限
    public function getRoles(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('POST')){
                    $roles=Role::where('id','!=',1)->where('name','!=','root')->orderby('created_at','desc')->get();
                    $agentid=$request->agent_id;
                    $targetrole=0;
                    if($agentid){
                        $targetrole=DB::table('role_user')->where('user_id',$agentid)->select('role_id')->first();
                    }
                    return json_encode([
                        "success"=>1,
                        "data"=>$roles,
                        "target"=>$targetrole,
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
            "success"=>0,
            "msg"=>"获取角色失败!".$error.$line
        ]);
    }
    //删除角色
    public function delRole(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('POST')){
                    $roleid=$request->id;
                    if($roleid!=1){
                        $re = Role::where('id', $roleid)->delete();
                        DB::table('permission_role')->where('role_id',$roleid)->delete();
                        if($re){
                            return json_encode([
                                "success"=>1,
                                "msg"=>"删除成功"
                            ]);
                        }else{
                            $error='删除角色失败';
                        }
                    }else{
                        $error='该角色不允许删除';
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
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"删除失败!".$error.$line
                ]);
            }
        }
        return json_encode([
            "success"=>0,
            "msg"=>"删除失败!".$error.$line
        ]);
    }
    //添加角色
    public function addRole(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('POST')){
                    $data = [
                        'name' => trim($request->name),
                        'display_name' => trim($request->display_name),
                        'description' => trim($request->description),
                        'created_at' => date('Y-m-d H:i:s', time()),
                    ];
                    $namerole=Role::where('name',$data['name'])->first();
                    if(!$namerole){
                        if(Role::create($data)){
                            return json_encode([
                                "success"=>1,
                                "msg"=>"添加成功"
                            ]);
                        }else{
                            $error='添加角色失败';
                        }
                    }else{
                        $error='该名称已存在,请更换名称重新添加';
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
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"添加失败!".$error.$line
                ]);
            }
        }
        return json_encode([
            "success"=>0,
            "msg"=>"添加失败!".$error.$line
        ]);
    }
    //角色的权限
    public function getPermission(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('POST')){
                    $roleid=$request->id;
                    $rolepermission=PermissionRole::where('role_id',$roleid)->get();
                    $permissions=Permission::get();
                    return json_encode([
                        "success"=>1,
                        "permissions"=>$permissions,
                        "rolepermission"=>$rolepermission
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
            "success"=>0,
            "msg"=>"获取权限失败!".$error.$line
        ]);
    }
    //设置角色权限
    public function setRolePermission(Request $request){
        $line=0;
        $error='未知错误';
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                if($request->isMethod('POST')){
                    $roleid=$request->role_id;
                    $role = Role::where('id', $roleid)->first();
                    if($roleid==1){
                        $data = Permission::all()->toArray();
                        PermissionRole::where('role_id', 1)->delete();
                        foreach ($data as $v) {
                            // $permission = Permission::where('id', $v)->first();
                            $role->attachPermission($v['id']);//追加权限到这个角色里面
                        }
                    }else{
                        $data = $request->id;
                        PermissionRole::where('role_id', $roleid)->delete();
                        if(!empty($data)){
                            foreach ($data as $k=>$v) {
                                // $permission = Permission::where('id', $v)->first();
                                $role->attachPermission($v);//追加权限到这个角色里面
                            }
                        }
                    }
                    return json_encode([
                        "success"=>1,
                        "msg"=>'权限已分配'
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
            "success"=>0,
            "msg"=>"获取权限失败!".$error.$line
        ]);
    }
}