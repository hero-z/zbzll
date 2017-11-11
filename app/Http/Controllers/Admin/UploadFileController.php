<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/10/24
 * Time: 21:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

const ROOTPATH='/upload/';
class UploadFileController extends Controller
{
    //上传图片
    public function uploadImg(Request $request,$OriginalName=false){
        //获取到img
        $file = Input::file('img');
        $params=$request->except('_token','img');
        $path=ROOTPATH;
        $name=date('YmdHis').rand(1000,9999);
        if(array_key_exists('path',$params)){
            //指定路径(子文件夹)
            $path.=$params['path'];
        }
        if(array_key_exists('name',$params)){
            //指定文件名
            $name=$params['name'];
        }
        if($OriginalName){
            $name=$file->getClientOriginalName();
        }
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $re = $file->move(public_path() . $path, $name.".".$entension);
            return json_encode([
                'success'=>1,
                'res'=>$re,
                'path'=>$path,
                'name'=>$name,
                'entension'=>$entension,
            ]);
        }else{
            return json_encode([
                'success'=>0,
                'msg'=>'文件不可用'
            ]);
        }
    }
}