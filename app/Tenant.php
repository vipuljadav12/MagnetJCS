<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Enrollment\Models\Enrollment;
use Session;

class Tenant extends Model
{
    //
    protected $table = "district";

    public static function getTenants()
    {
        $url = request()->getHttpHost();
        $url_array = explode(".", $url);
        $subdomain = $url_array[0];

        if($subdomain == "www")
            $subdomain = $url_array[1];
        $tenant = Tenant::where("district_slug", $subdomain)->where("status", "Y")->first();

        if(!$tenant)
        {
            Session::put("district_id", "0");
            Session::put("super_admin", "Y");
            Session::put("theme_color", "#00346b");
        }
        else
        {
            Session::put("district_id", $tenant->id);
            Session::put("super_admin", "N");
            Session::put("theme_color", $tenant->theme_color);
            //date_default_timezone_set($tenant->district_timezone);

            $enrollments = Enrollment::where('district_id', Session::get('district_id'))
                        ->where('status', 'Y')
                        ->where('begning_date','<',date('Y-m-d'))
                        ->where('ending_date','>',date('Y-m-d'))
                        ->orderBy('created_at','desc')
                        ->first();

            if(!isset($enrollments) && empty($enrollments)){
                $enrollments = Enrollment::where('district_id', Session::get('district_id'))
                            ->where('status', 'Y')
                            ->orderBy('created_at','desc')
                            ->first();
            }
            
            if(isset($enrollments)){
                Session::put('enrollment_id', $enrollments->id);
            }
        }

        Session::save();
    }
}