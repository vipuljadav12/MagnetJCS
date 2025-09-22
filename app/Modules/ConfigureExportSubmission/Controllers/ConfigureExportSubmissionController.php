<?php

namespace App\Modules\ConfigureExportSubmission\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use Illuminate\Support\Facades\Session;

class ConfigureExportSubmissionController extends Controller
{
    public $module_base = 'admin/ConfigureExportSubmission';

    public function index()
    {
        $module_base = $this->module_base;
        $match_ary = [
            'district_id' => Session('district_id'),
            'name' => 'submission_export_fields'
        ];
        $data = DistrictConfiguration::where($match_ary)->first();
        if (isset($data)) {
            $export_fields = json_decode($data->value);
        } else {
            $export_fields = [];
        }
        return view('ConfigureExportSubmission::index', compact('module_base', 'export_fields'));
    }

    public function update(Request $request)
    {
        $module_base = $this->module_base;
        $match_ary = [
            'district_id' => Session('district_id'),
            'name' => 'submission_export_fields'
        ];

        if (isset($request->fields) && !empty($request->fields)) {
            $value = json_encode($request->fields);
            $result = DistrictConfiguration::updateOrCreate(
                $match_ary,
                ['value' => $value]
            );
        } else {
            $result = DistrictConfiguration::where($match_ary)->delete();
        }

        if (isset($result)) {
            Session::flash('success', 'Data Updated successfully.');
        } else {
            Session::flash('warning', 'Something went wrong, please try again.');
        }
        return redirect($module_base);
    }
}
