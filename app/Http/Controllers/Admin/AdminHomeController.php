<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\App;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin=Auth::guard('admin')->user();
        if($admin->status!=1){
            return view('admin.auth_force',compact('admin'));
        }
        return view('admin.adminhome');
    }
    public function adminIndex(){
        $data = App::where('id', 1)->first();
        return view("admin.index",compact('data'));
    }
    public function forceOauth(Request $request){
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        return redirect()->guest('checkoauth');
    }
}