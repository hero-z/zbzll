<?php

namespace App\Http\Middleware;
use App\Http\Controllers\Merchant\CheckMerchantController;
use App\Models\Company_info;
use Closure;
use Illuminate\Support\Facades\Auth;
class MerchantAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$guard = null)
    {
        config(['entrust.role' => '\App\Models\MerchantRole']);
        config(['entrust.roles_table' => 'merchant_roles']);
        config(['entrust.permission' => '\App\Models\MerchantPermission']);
        config(['entrust.permissions_table' => 'merchant_permissions']);
        config(['entrust.permission_role_table' => 'merchant_permission_role']);
        config(['entrust.role_user_table' => 'role_merchant']);
        config(['entrust.user_foreign_key' => 'merchant_id']);
        config(['entrust.role_foreign_key' => 'merchant_role_id']);
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('merchant/login');
            }
        }
        try{
            $line=0;
            $company=Company_info::where('merchant_id',CheckMerchantController::selectMerchant(Auth::guard('merchant')->user()->pid))->first();
            if($company){
                if($company->status==1){
                    return $next($request);
                } else{
                    Auth::guard('merchant')->logout();
                    $request->session()->invalidate();
                    return redirect()->guest('info');
                }
            }else{
                Auth::guard('merchant')->logout();
                $request->session()->invalidate();
                return redirect()->guest('check');
            }
        }catch(\Exception $e){
            Auth::guard('merchant')->logout();
            $request->session()->invalidate();
            return redirect()->guest('error');
        }
    }
}