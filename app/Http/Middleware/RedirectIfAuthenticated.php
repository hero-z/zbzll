<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // 根据不同 guard 跳转到不同的页面
            if($guard=="admin"){
                $url='/';
            }elseif($guard=="merchant"){
                $url='merchant/home';
            }elseif($guard=="merchantoauth"){
                $url='admin/oauthhome';
            }else{
                $url="/home";
            }
            return redirect($url);
        }

        return $next($request);
    }
}