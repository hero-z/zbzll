<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Company_info;
use App\Models\MerchantRole;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OauthHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('merchant.oauth:merchantoauth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $code_url=url('/admin/oauth?admin_id=1');
        return redirect($code_url);
//        return view('merchant.check',compact('code_url'));
    }
}