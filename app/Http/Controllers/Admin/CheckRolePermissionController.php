<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/18
 * Time: 13:18
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckRolePermissionController extends Controller
{
    //检查是否是Root角色
    public static function CheckRoleRoot(){
        return Auth::guard('admin')->user()->hasRole('root');
    }
    //是否有权限
    public static function CheckPremission($permissionName){
        return Auth::guard('admin')->user()->can($permissionName);
    }
}