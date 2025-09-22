<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;
use App\AuditTrail;
use Illuminate\Support\Facades\Auth;

class FilterAll
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd(AuditTrail::all());
        $response = $next($request);
        $data["user_id"] = isset(Auth::user()->id) ? Auth::user()->id : null;
        $data["previous_url"] = URL::previous();
        $data["path"] = $request->path();
        $data["method"] = $request->method();
        $data["attributes"] = json_encode($request->all());
        // $data["response"] = json_encode($response->content());
        $data["status_code"] = $response->status();

        AuditTrail::create($data);
        // print_r($data);
        // exit;
        // dd($response);
        // print_r(json_encode($request));  
        // dd(($request));  
        /*$data  = array();
        $data["user_id"] = Auth::user()->id;
        $data["method"] = $request->method();
        $data["path"] = $request->path();
        $data["previous"] = URL::previous();
        $data["data"] = $request->all();
        dd($data);*/
        // dd(Auth::user());
        // exit;  
        // throw new   \Exception("Error Processing Request", 1);

        return $next($request);
    }
}
