<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Permissions
{
	protected $auth;


	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}


	public function handle($request, Closure $next)
	{
		$myrole = Auth::user()->role_id;

		$mainurl =  url('/') . '/admin/';
		$url = URL::current();
		$mainslug = $slug = str_replace($mainurl, '', $url);

		$hasPermissionAdded = DB::table('permissions')->where('slug', $slug)->first();
		if (!empty($hasPermissionAdded) && !in_array($myrole, [1])) {
			$data = DB::table('roles_permissions')
				->where('permission_id', $hasPermissionAdded->id)
				->where('role_id', $myrole)
				->first();

			if (!empty($data)) {
				return $next($request);
			} else {
				$previous = url()->previous();
				$previous = str_replace($mainurl, '', $previous);
				if ($previous == $mainslug) {
					Session::flash('error', 'Sorry, you are not allowed to access this page.');
					return redirect('admin/dashboard');
				} else {
					Session::flash('error', 'Sorry, you are not allowed to access this page.');
					return redirect()->back();
				}
			}
		} else {
			return $next($request);
		}
	}
}
