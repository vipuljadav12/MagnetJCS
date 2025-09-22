<?php

namespace App\Modules\District\Controllers;

use App\Modules\District\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DistrictController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $districts = District::where('status', '!=', 't')->get();
        return view("District::index", compact('districts'));
    }
    public function create()
    {
        //
        return view('District::create');
    }

    public function store(Request $request)
    {
        // return $request;

        $msg = [/*'mobile_number.regex'=>'The Mobile number is not valid',*/'zipcode.regex' => 'The zip code must be an integer. '];
        $validateData = $request->validate([
            'name' => 'required|max:255',
            // 'district_slug' =>'required|max:255|unique:District',
            'short_name' => 'max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zipcode' => ['required', 'regex:/^\d+$/', 'max:6', 'min:5'],
            'district_logo' => 'required|mimes:jpg,png,jpeg,gif',
            'magnet_program_logo' => 'mimes:jpg,png,jpeg,gif',
            'district_slug' => 'required|max:255|unique:district',
            'theme_color' => 'required|max:7',
            'magnet_point_contact' => 'max:255',
            'magnet_point_contact_title' => 'max:255',
            'magnet_point_contact_email' => 'max:255',
            //            'magnet_point_contact_phone'=>['regex:/^(\+\d{1,3}[- ]?)?\d{10}$/'],
            'sis_connection' => 'max:255',
            'sis_username' => 'max:255',
            'sis_password' => 'max:255',
            'sis_applcation_key' => 'max:255',
            'school_sis_contact' => 'max:255',
            'school_sis_contact_title' => 'max:255',
            'school_sis_contact_email' => 'max:255',
            //            'school_sis_contact_phone'=>['regex:/^(\+\d{1,3}[- ]?)?\d{10}$/'],
            /* 'billing_start_date'=> 'required',
            'billing_end_date'=> 'required',
            'notify_renewal_date'=> 'required',*/
        ], $msg);

        $districtlogo = $request->file('district_logo');
        $magnet_program_logo = $request->file('magnet_program_logo');
        $districtlogo->move('resources/filebrowser/' . $request->district_slug . '/logo/', $request->district_slug . '_logo.png');
        if (isset($magnet_program_logo)) {
            $magnet_program_logo->move('resources/filebrowser/' . $request->district_slug . '/logo/', $request->district_slug . '_magnet_program_logo.png');
        }
        $data = [
            'district_slug' => $request->district_slug,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'district_logo' => $request->district_slug . '_logo.png',
            'magnet_program_logo' => $request->district_slug . '_magnet_program_logo.png',
            'district_slug' => $request->district_slug,
            'theme_color' => $request->theme_color,
            'magnet_point_contact' => $request->magnet_point_contact,
            'magnet_point_contact_title' => $request->magnet_point_contact_title,
            'magnet_point_contact_email' => $request->magnet_point_contact_email,
            'magnet_point_contact_phone' => $request->magnet_point_contact_phone,
            'desegregation_compliance' => $request->desegregation_compliance == 'on' ? 'Yes' : 'No',
            'lottery_number_display' => $request->lottery_number_display == 'on' ? 'Yes' : 'No',
            'zone_requirements' => $request->zone_requirements == 'on' ? 'Yes' : 'No',
            'birthday_cutoff_requirement' => $request->birthday_cutoff_requirement == 'on' ? 'Yes' : 'No',
            'sis_connection' => $request->sis_connection,
            'sis_api_url' => $request->sis_api_url,
            'sis_username' => $request->sis_username,
            'sis_password' => $request->sis_password,
            'sis_application_key' => $request->sis_application_key,
            'school_sis_contact' => $request->school_sis_contact,
            'school_sis_contact_title' => $request->school_sis_contact_title,
            'school_sis_contact_email' => $request->school_sis_contact_email,
            'school_sis_contact_phone' => $request->school_sis_contact_phone,
            'zone_api' => $request->zone_api == 'on' ? 'Yes' : 'No',
            'zone_api_existing' => $request->zone_api_existing == 'on' ? 'Yes' : 'No',
            'mcpss_zone_api' => $request->mcpss_zone_api == 'on' ? 'Yes' : 'No',
            'admin_mcpss_zone_api' => $request->admin_mcpss_zone_api == 'on' ? 'Y' : 'N',
            'internal_zone_api_url' => $request->internal_zone_api_url,
            'internal_zone_point_contact' => $request->internal_zone_point_contact,
            'internal_zone_point_title' => $request->internal_zone_point_title,
            'internal_zone_point_email' => $request->internal_zone_point_email,
            'internal_zone_point_phone' => $request->internal_zone_point_phone,
            'external_organization_name' => $request->external_organization_name,
            'district_timezone' => $request->district_timezone,
            'external_organization_url' => $request->external_organization_url,
            'external_organization_point_contact' => $request->external_organization_point_contact,
            'external_organization_point_title' => $request->external_organization_point_title,
            'external_organization_point_email' => $request->external_organization_point_email,
            'external_organization_point_phone' => $request->external_organization_point_phone,
            'billing_start_date' => isset($request->billing_start_date) ? date('Y-m-d', strtotime($request->billing_start_date)) : null,
            'billing_end_date' => isset($request->billing_end_date) ? date('Y-m-d', strtotime($request->billing_end_date)) : null,
            'notify_renewal_date' => $request->notify_renewal_date,
            'feeder' => $request->feeder == 'on' ? 'Yes' : 'No',
            'sibling' => $request->sibling == 'on' ? 'Yes' : 'No',
            'magnet_employee' => $request->magnet_employee == 'on' ? 'Yes' : 'No',
            'current_over_new' => $request->current_over_new == 'on' ? 'Yes' : 'No',
            'magnet_student' => $request->magnet_student == 'on' ? 'Yes' : 'No',
        ];
        $result = District::create($data);

        if (isset($result)) {
            Session::flash("success", "District added successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/District');
        }
        return redirect('admin/District/edit/' . $result->id);
    }

    public function edit($id)
    {
        $district = District::where('id', $id)->first();
        if (isset($district)) {
            return view('District::edit', compact('district'));
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        //        return $request;
        //
        $msg = [/*'mobile_number.regex'=>'The Mobile number is not valid',*/'zipcode.regex' => 'The zip code must be an integer. '];
        $validateData = $request->validate([
            'name' => 'required|max:255',
            'short_name' => 'max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:255',
            'zipcode' => ['required', 'regex:/^\d+$/', 'max:6', 'min:5'],
            'district_logo' => 'mimes:jpg,png,jpeg,gif',
            'magnet_program_logo' => 'mimes:jpg,png,jpeg,gif',
            'district_slug' => 'required|max:255',
            'theme_color' => 'required|max:7',
            'magnet_point_contact' => 'max:255',
            'magnet_point_contact_title' => 'max:255',
            'magnet_point_contact_email' => 'max:255',
            //            'magnet_point_contact_phone'=>['regex:/^(\+\d{1,3}[- ]?)?\d{10}$/'],
            'sis_connection' => 'max:255',
            'sis_username' => 'max:255',
            'sis_password' => 'max:255',
            'sis_applcation_key' => 'max:255',
            'school_sis_contact' => 'max:255',
            'school_sis_contact_title' => 'max:255',
            'school_sis_contact_email' => 'max:255',
            //            'school_sis_contact_phone'=>['regex:/^(\+\d{1,3}[- ]?)?\d{10}$/'],
            /*'billing_start_date'=> 'required',
            'billing_end_date'=> 'required',
            'notify_renewal_date'=> 'required',*/
        ], $msg);
        //         return $request;
        $data = [
            'name' => $request->name,
            'short_name' => $request->short_name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'district_logo' => $request->district_slug . '_logo.png',
            'magnet_program_logo' => $request->district_slug . '_magnet_program_logo.png',
            'district_slug' => $request->district_slug,
            'theme_color' => $request->theme_color,
            'magnet_point_contact' => $request->magnet_point_contact,
            'magnet_point_contact_title' => $request->magnet_point_contact_title,
            'magnet_point_contact_email' => $request->magnet_point_contact_email,
            'magnet_point_contact_phone' => $request->magnet_point_contact_phone,
            'desegregation_compliance' => $request->desegregation_compliance == 'on' ? 'Yes' : 'No',
            'zone_requirements' => $request->zone_requirements == 'on' ? 'Yes' : 'No',
            'birthday_cutoff_requirement' => $request->birthday_cutoff_requirement == 'on' ? 'Yes' : 'No',
            'lottery_number_display' => $request->lottery_number_display == 'on' ? 'Yes' : 'No',
            'sis_connection' => $request->sis_connection,
            'sis_api_url' => $request->sis_api_url,
            'sis_username' => $request->sis_username,
            'sis_password' => $request->sis_password,
            'sis_application_key' => $request->sis_application_key,
            'school_sis_contact' => $request->school_sis_contact,
            'school_sis_contact_title' => $request->school_sis_contact_title,
            'school_sis_contact_email' => $request->school_sis_contact_email,
            'school_sis_contact_phone' => $request->school_sis_contact_phone,
            'zone_api' => $request->zone_api == 'on' ? 'Yes' : 'No',
            'zone_api_existing' => $request->zone_api_existing == 'on' ? 'Yes' : 'No',
            'mcpss_zone_api' => $request->mcpss_zone_api == 'on' ? 'Yes' : 'No',
            'admin_mcpss_zone_api' => $request->admin_mcpss_zone_api == 'on' ? 'Y' : 'N',
            'internal_zone_api_url' => $request->internal_zone_api_url,
            'internal_zone_point_contact' => $request->internal_zone_point_contact,
            'internal_zone_point_title' => $request->internal_zone_point_title,
            'internal_zone_point_email' => $request->internal_zone_point_email,
            'internal_zone_point_phone' => $request->internal_zone_point_phone,
            'external_organization_name' => $request->external_organization_name,
            'external_organization_url' => $request->external_organization_url,
            'district_timezone' => $request->district_timezone,
            'external_organization_point_contact' => $request->external_organization_point_contact,
            'external_organization_point_title' => $request->external_organization_point_title,
            'external_organization_point_email' => $request->external_organization_point_email,
            'external_organization_point_phone' => $request->external_organization_point_phone,
            'billing_start_date' => isset($request->billing_start_date) ? date('Y-m-d', strtotime($request->billing_start_date)) : null,
            'billing_end_date' => isset($request->billing_end_date) ? date('Y-m-d', strtotime($request->billing_end_date)) : null,
            'notify_renewal_date' => $request->notify_renewal_date,
            'feeder' => $request->feeder == 'on' ? 'Yes' : 'No',
            'sibling' => $request->sibling == 'on' ? 'Yes' : 'No',
            'magnet_employee' => $request->magnet_employee == 'on' ? 'Yes' : 'No',
            'current_over_new' => $request->current_over_new == 'on' ? 'Yes' : 'No',
            'magnet_student' => $request->magnet_student == 'on' ? 'Yes' : 'No',
            //        ];
        ];
        //            return $data;
        //if district slug change than image folder change
        $old_district_slug = District::where('id', $id)->first();
        if ($old_district_slug->district_slug != $request->district_slug) {
            File::makeDirectory('resources/filebrowser/' . $request->district_slug . '/logo/', 0777, true, true);
            File::copy('resources/filebrowser/' . $old_district_slug->district_slug . '/logo/' . $old_district_slug->district_slug . '_logo.png', 'resources/filebrowser/' . $request->district_slug . '/logo/' . $request->district_slug . '_logo.png');
            if (file_exists('resources/filebrowser/' . $old_district_slug->district_slug . '/logo/' . $old_district_slug->district_slug . '_magnet_program_logo.png')) {
                File::copy('resources/filebrowser/' . $old_district_slug->district_slug . '/logo/' . $old_district_slug->district_slug . '_magnet_program_logo.png', 'resources/filebrowser/' . $request->district_slug . '/logo/' . $request->district_slug . '_magnet_program_logo.png');
            }
            File::deleteDirectory('resources/filebrowser/' . $old_district_slug->district_slug);
        }

        //store image
        $districtlogo = $request->file('district_logo');
        $magnet_program_logo = $request->file('magnet_program_logo');
        if (isset($districtlogo)) {
            $districtlogo->move('resources/filebrowser/' . $request->district_slug . '/logo/', $request->district_slug . '_logo.png');
        }
        if (isset($magnet_program_logo)) {
            $magnet_program_logo->move('resources/filebrowser/' . $request->district_slug . '/logo/', $request->district_slug . '_magnet_program_logo.png');
        }

        $result = District::where('id', $id)->update($data);

        changeTimezone();

        if (isset($result)) {
            Session::flash("success", "District Updated successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            return redirect('admin/District');
        }
        return redirect('admin/District/edit/' . $id);
    }
    public function delete($id)
    {
        //
        $result = District::where('id', $id)->update(['status' => 'T']);
        if (isset($result)) {
            Session::flash("success", "District Data Move into Trash successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        return redirect('admin/District');
    }
    public function trash()
    {
        $districts = District::where('status', 'T')->get();
        return view("District::trash", compact('districts'));
    }
    public function restore($id)
    {
        $result = District::where('id', $id)->update(['status' => 'Y']);
        if (isset($result)) {
            Session::flash("success", "District Data restore successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        return redirect('admin/District');
    }
    public function uniqueUrl(Request $request)
    {
        $result = District::where('id', '!=', $request->id)->where('district_slug', $request->district_slug)->first();
        if (isset($result)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }
    public function uniqueSlug(Request $request)
    {
        $result = District::where('district_slug', $request->district_slug)->first();
        if (isset($result)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }
    public function status(Request $request)
    {
        //        return $request;
        $result = District::where('id', $request->id)->update(['status' => $request->status]);
        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }
}
