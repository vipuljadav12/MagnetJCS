<?php

namespace App\Modules\HeaderFooter\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HeaderFooter\Models\HeaderFooter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class HeaderFooterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $districtid = Session::get('district_id');
        $data = HeaderFooter::where('district_id', $districtid)->get();
        (Session::get('district_id') != '') ? Session::get('district_id') : Session::put('district_id', 1);
        $config_data = array();
        foreach ($data as $row) {
            $config_data[$row->config_name] = $row->config_value;
        }
        return view("HeaderFooter::index", compact('config_data'));
    }

    public function save(Request $request)
    {

        $districtid = Session::get('district_id');
        $input = $request::all();
        foreach ($input['config'] as $key => $value) {
            if ($value != '') {
                $existance = HeaderFooter::where('district_id', $districtid)->where('config_name', $key)->first();
                if (!empty($existance)) {
                    HeaderFooter::where('district_id', $districtid)->where('config_name', $key)->update(array('status' => 'Y', 'config_value' => $value));
                } else {
                    HeaderFooter::insert(array('district_id' => $districtid, 'status' => 'Y', 'config_value' => $value, 'config_name' => $key));
                }
            } else {
                HeaderFooter::where('district_id', $districtid)->where('config_name', $key)->delete();
            }
        }
        Session::flash('success', 'Header & Footer Information Updated Successfully.');
        return  redirect('admin/HeaderFooterConfig');
    }
}
