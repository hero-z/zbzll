<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Company_info;
use App\Models\Logo;
use App\Models\MerchantRole;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MerchantHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.merchant:merchant');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchant_id=CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid);
        $merchantLogo=Logo::where("merchant_id",$merchant_id)->first();
        return view("merchant.merchanthome",compact('merchantLogo'));
    }
    public function merchantIndex(){
        return view("merchant.index");
    }
}