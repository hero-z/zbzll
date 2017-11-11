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
        $id=$request->get("id");
        $data['logo1']=$request->get("logo1");
        $data['logo2']=$request->get("logo2");
        if($data['logo1']==""){
            $data['logo1']=$request->get("oldpic1");
        }
        if($data['logo2']==""){
            $data['logo2']=$request->get("oldpic2");
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
    }
}