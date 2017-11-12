<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LogoController extends Controller{
    public function companyLogo()
    {
     $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
     $list=Logo::where('merchant_id',$merchant_id)->first();
     return view('merchant.logo.logo',compact('list'));
    }
    //设置logo
    public function setCompanyLogo(Request $request)
    {
        $line=0;
        try{
            if(CheckRolePermissionController::CheckRoleRoot()){
                $id=$request->get("id");
                $data['logo1']=$request->get("logo1");
                if($data['logo1']==""){
                    $data['logo1']=$request->get("oldpic1");
                }
                $data['merchant_id']=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
                $list=Logo::where('merchant_id',$data['merchant_id'])->first();
                if($list){
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
                    if(Logo::create($data)){
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