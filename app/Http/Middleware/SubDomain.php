<?php

namespace App\Http\Middleware;

use Closure;

class SubDomain
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
        /*if($request->getHost()=="redefineapp.io")
        {
            Session::put('DistrictID', '0');
            Session::put('SubDomain', '');
        }
        else
        {
            $tmp = explode(".",$request->getHost());
            $slug = $tmp[0];
            $organisation = District::where('district_url', '=', $slug)->first();
            if($organisation)
            {
                Session::put('DistrictID', $organisation->id);
                Session::put('SubDomain', $slug);
            }
            else
            {
                Session::put('DistrictID', '0'); 
                Session::put('SubDomain', '');  
            }
        }*/
        return $next($request);
    }
}
