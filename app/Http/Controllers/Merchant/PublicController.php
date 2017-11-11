<?php
namespace App\Http\Controllers\merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PublicController extends Controller{
    public function upload(Request $request){
        $file = Input::file('file');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            if($entension=="xls"||$entension=="xlsx") {
                $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
                $path = $file->move(public_path() . '/app/', $newName);
                $data = [
                    'path' => '/app/' . $newName,
                    'status' => 1,
                ];
            }else{
                $data = [
                    'status' => 0,
                    "message"=>"上传格式必须为xls"
                ];
            }
        }
        return json_encode($data);
    }
}
?>