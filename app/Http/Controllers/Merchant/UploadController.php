<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UploadController extends Controller{
    public function uploadLogo(Request $request)
    {
        $file = Input::file('image');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/logo' . '/', $newName);

        }

        $data = [
            'image_url' => '/uploads/logo/' . $newName,
            'status' => 1,
        ];
        return json_encode($data);
    }
}