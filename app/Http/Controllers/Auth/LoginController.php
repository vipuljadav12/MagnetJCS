<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\District\Models\District;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $request->email, 'password' => $request->password, 'status' => 'Y'];
        if (Auth::attempt($credentials)) {

            if (Session::get('district_id') == "0") {
                $districts = District::where("status", "Y")->where('district_slug', 'magnet-hcs')->first();
                if (empty($districts)) {
                    $districts = District::where("status", "Y")->first();
                }
                //dd($districts);
                if (!empty($districts)) {
                    Session::put("district_id", $districts->id);
                    Session::put("theme_color", $districts->theme_color);
                    date_default_timezone_set($districts->district_timezone);

                    Session::save();
                } else {
                    echo "not";
                    exit;
                }
            }

            $enrollments = Enrollment::where('district_id', Session::get('district_id'))
                ->where('status', 'Y')
                ->orderBy('created_at', 'desc')
                ->first();

            if (isset($enrollments)) {
                Session::put('enrollment_id', $enrollments->id);
            }

            changeTimezone();
            return redirect('admin/dashboard');
        } else {
            Session::flash('mess', "Invalid Credentials, Please try again.");
            return redirect('/login');
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect('/login');
        } else {
            return redirect('/login');
        }
    }
}
