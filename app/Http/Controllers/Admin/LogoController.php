<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;

class LogoController extends Controller{
    public function logoIndex()
    {
        $list=Logo::find(1);
        return view("admin.logo.logo",compact("list"));
    }
    public function setLogo(Request $request)
    {
        $line=0;
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                $id=$request->get("id");
                $data['logo1']=$request->get("logo1");
                $data['logo2']=$request->get("logo2");
                $data['logo3']=$request->get('logo3');
                $data['logo4']=$request->get('logo4');
                if($data['logo1']==""){
                    $data['logo1']=$request->get("oldpic1");
                }
                if($data['logo2']==""){
                    $data['logo2']=$request->get("oldpic2");
                }
                if($data['logo3']==""){
                    $data['logo3']=$request->get("oldpic3");
                }
                if($data['logo4']==""){
                    $data['logo4']=$request->get("oldpic4");
                }
                if(Logo::where("id",$id)->update($data)){
                    return json_encode([
                        "success"=>1
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "sub_msg"=>"设置失败"
                    ]);
                }
            }else{
                $error='亲,你还没有该操作权限!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return view('error',compact('line','error'));

    }
}