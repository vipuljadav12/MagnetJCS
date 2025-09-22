<?php

namespace App\Modules\ZonedSchool\Controllers;

use Illuminate\Http\Request;
use App\Modules\ZonedSchool\Models\ZonedSchool;
use App\Modules\ZonedSchool\Export\ZoneAddressExport;
use App\Modules\ZonedSchool\Export\ZonedSchoolImport;
use App\Modules\ZonedSchool\Models\NoZonedSchool;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ZonedSchoolController extends Controller
{

    public function __construct()
    {
        // session()->put('district_id', 1);  //26-6-20
    }

    public function index()
    {
        return view('ZonedSchool::index');
    }

    public function getZonedSchool(Request $request)
    {
        $send_arr = $data_arr = array();
        // $zoneData = ZonedSchool::take(1000)->get();
        $zoneData = ZonedSchool::getZonedSchoolList($request->all(), 1);
        $total = ZonedSchool::getZonedSchoolList($request->all(), 0);
        foreach ($zoneData as $value) {
            $send_arr[] = [
                $value->bldg_num,
                $value->street_name,
                $value->street_type,
                $value->city,
                $value->zip,
                "<a href=" . url('admin/ZonedSchool/edit', $value->id) . " title='edit'><i class='far fa-edit'></i></a>"
            ];
        }

        $data_arr['recordsTotal'] = $total;
        $data_arr['recordsFiltered'] = $total;
        $data_arr['data'] = $send_arr;
        return json_encode($data_arr);
    }

    public function edit($id)
    {
        $zonedschool = ZonedSchool::where('id', $id)->first();
        return view('ZonedSchool::edit', compact('zonedschool'));
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        $msg = [
            'street_name.required' => 'Street Name is required.',
            'zip.required' => 'Zip code is required.',
            'zip.regex' => 'The Zip code must be an integer.',
            'zip.max' => 'The Zip code may not be grater than 6 characters.',
            'zip.min' => 'The Zip code must be at least 5 characters.'
        ];

        $validateData = $request->validate([
            'street_name' => 'required|max:255',
            'zip' => 'required|regex:/^\d+$/|max:6|min:5',
        ], $msg);

        $data = [
            'bldg_num' => $request->bldg_num,
            'street_name' => $request->street_name,
            'street_type' => $request->street_type,
            'city' => $request->city,
            'zip' => $request->zip,
            'elementary_school' => $request->elementary_school,
            'intermediate_school' => $request->intermediate_school,
            'middle_school' => $request->middle_school,
            'high_school' => $request->high_school,
        ];

        $result = ZonedSchool::where('id', $id)->update($data);

        if (isset($result)) {
            Session::flash("success", "Zoned Address Updated successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            return redirect('admin/ZonedSchool');
        }
        return redirect('admin/ZonedSchool/edit/' . $id);
    }

    public function search(Request $request)
    {
        // return request()->url();
        $tmpaddress = $address = strtolower($request->address);
        $tmpaddress = str_replace(" ", "", $tmpaddress);
        $zip = $request->zip;
        $city = $request->city;
        $grade = $request->grade;
        $withoutspace = explode(" ", $address);

        $arr = array();
        $tmpstr = "";
        for ($i = 0; $i < count($withoutspace); $i++) {
            $tmpstr .= $withoutspace[$i];
            if ($i + 1 < count($withoutspace)) {
                $str = $withoutspace[$i] . "" . $withoutspace[$i + 1];
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $tmpstr));
            } else {
                $str = $withoutspace[$i];
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $tmpstr));
            }
            if ($i > 0) {
                $str = $withoutspace[$i - 1] . "" . $withoutspace[$i] . (isset($withoutspace[$i + 1]) ? $withoutspace[$i + 1] : "");
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
            }
        }
        $zoneData = ZonedSchool::where("zip", $zip)->where(DB::raw("LOWER(replace(city, ' ',''))"), strtolower(str_replace(" ", "", $city)));

        $zoneData->where(function ($q) use ($arr, $withoutspace) {
            $q->whereIn(DB::raw("LOWER(replace(street_name,' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(street_name,' ', ''))"), $arr);
        });
        $streetMatch = $zoneData->get(); //clone($zoneData);//->get();
        // echo $tmpaddress;exit;
        $streetPlusBldg = $zoneData->where(function ($q) use ($arr, $withoutspace, $tmpaddress) {
            $q->whereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name),' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name),' ', ''))"), $arr)
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->orWhereIn(DB::raw("LOWER(replace(concat(street_name, bldg_num),' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(concat(street_name, bldg_num),' ', ''))"), $arr)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, prefix_dir, street_name),' ', ''))"), $arr)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, prefix_type, street_name),' ', ''))"), $arr);
            /*->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,unit_info),' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,unit_info),' ', ''))"), $arr)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,street_type),' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,street_type),' ', ''))"), $arr)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,unit_info),' ', ''))"), $withoutspace)
                ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name,unit_info),' ', ''))"), $arr);*/
        })->get();
        // print_r($streetPlusBldg);exit;

        $addressDiv = $zoned_school = $exportData = "";
        $addressDiv .= "<table class='table' id='zoneList'>";
        // $addressDiv .= '<thead><tr><th>Bldg No</th><th>Street Address</th><th>City</th><th>Zip</th><th>Elementary School</th><th>Intermediate School</th><th>Middle School</th><th>High School</th></tr></thead><tbody>';
        $addressDiv .= '<thead><tr><th>Bldg No</th><th>Street Address</th><th>Street Type</th><th>City</th><th>Zip</th></tr></thead><tbody>';

        if (count($streetPlusBldg) > 0) {
            $count = 0;
            $exportData = $streetPlusBldg;
            foreach ($streetPlusBldg as $key => $value) {
                $addressDiv .= '<tr><td>' . $value->bldg_num . '</td>';
                $addressDiv .= '<td>' . $value->street_name . '</td>';
                $addressDiv .= '<td>' . $value->street_type . '</td>';
                $addressDiv .= '<td>' . $value->city . '</td>';
                $addressDiv .= '<td>' . $value->zip . '</td></tr>';
                // $addressDiv .= '<td>'.$value->elementary_school.'</td>';
                // $addressDiv .= '<td>'.$value->intermediate_school.'</td>';
                // $addressDiv .= '<td>'.$value->middle_school.'</td>';
                // $addressDiv .= '<td>'.$value->high_school.'</td></tr>';

                if ($count == 0) {
                    $elementary_school = $value->elementary_school;
                    preg_match('#\((.*?)\)#', $elementary_school, $pmatch);
                    if (isset($pmatch[1])) {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $pmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1] || $grade == "PreK" || $grade == "K") {
                            $zoned_school = $elementary_school;
                        }
                    }

                    $intermediate_school = $value->intermediate_school;
                    preg_match('#\((.*?)\)#', $intermediate_school, $imatch);
                    if (isset($imatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $imatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $intermediate_school;
                        }
                    }

                    $middle_school = $value->middle_school;
                    preg_match('#\((.*?)\)#', $middle_school, $mmatch);
                    if (isset($mmatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $mmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $middle_school;
                        }
                    }

                    $high_school = $value->high_school;
                    preg_match('#\((.*?)\)#', $high_school, $hmatch);
                    if (isset($hmatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $hmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $high_school;
                        }
                    }
                }
                $count++;
            }
        } else {
            $exportData = $streetMatch;
            $count = 0;
            foreach ($streetMatch as $key => $value) {
                $addressDiv .= '<tr><td>' . $value->bldg_num . '</td>';
                $addressDiv .= '<td>' . $value->street_name . '</td>';
                $addressDiv .= '<td>' . $value->street_type . '</td>';
                $addressDiv .= '<td>' . $value->city . '</td>';
                $addressDiv .= '<td>' . $value->zip . '</td></tr>';
                // $addressDiv .= '<td>'.$value->elementary_school.'</td>';
                // $addressDiv .= '<td>'.$value->intermediate_school.'</td>';
                // $addressDiv .= '<td>'.$value->middle_school.'</td>';
                // $addressDiv .= '<td>'.$value->high_school.'</td></tr>';

                if ($count == 0) {
                    $elementary_school = $value->elementary_school;
                    preg_match('#\((.*?)\)#', $elementary_school, $pmatch);
                    if (isset($pmatch[1])) {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $pmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1] || $grade == "PreK" || $grade == "K") {
                            $zoned_school = $elementary_school;
                        }
                    }

                    $intermediate_school = $value->intermediate_school;
                    preg_match('#\((.*?)\)#', $intermediate_school, $imatch);
                    if (isset($imatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $imatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $intermediate_school;
                        }
                    }

                    $middle_school = $value->middle_school;
                    preg_match('#\((.*?)\)#', $middle_school, $mmatch);
                    if (isset($mmatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $mmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $middle_school;
                        }
                    }

                    $high_school = $value->high_school;
                    preg_match('#\((.*?)\)#', $high_school, $hmatch);
                    if (isset($hmatch[1]) && $zoned_school == '') {
                        $sgrade = str_replace("grades", "", str_replace(" ", "", $hmatch[1]));
                        $tmp = explode("-", $sgrade);
                        if ($grade <= $tmp[1]) {
                            $zoned_school = $high_school;
                        }
                    }
                }
                $count++;
            }
        }
        $addressDiv .= "</tbody></table>";

        // $html = "<div class='col-12 row'><div class='col-6'>".$addressDiv."</div>";
        // $html .= "<div class='col-6'>Zoned School:<bR><strong>".$zoned_school."</strong></div></div>";
        $html = "<div class='card shadow'><div class='card-body'><div class='row'><div class='col-lg-12 mt-5'>Zoned School:<bR><strong>" . $zoned_school . "</strong></div></div></div></div>";
        $html .= "<div class='card shadow'><div class='card-body'><div class='row'><div class='col-lg-12'>" . $addressDiv . "</div></div></div></div>";

        if (str_contains(request()->url(), '/export')) {
            return $this->exportZonedSchool($exportData, $zoned_school);
        } else {
            echo $html;
        }
    }


    public function search1(Request $request)
    {
        $tmpaddress = $address = strtolower($request->address);
        $tmpaddress = str_replace(" ", "", $tmpaddress);
        $tmpaddress = str_replace(",", "", $tmpaddress);
        $zip = $request->zip;
        $city = $request->city;
        $grade = $request->grade;
        $withoutspace = explode(" ", $address);

        $arr = array();
        $tmpstr = "";
        for ($i = 0; $i < count($withoutspace); $i++) {
            $tmpstr .= $withoutspace[$i];
            if ($i + 1 < count($withoutspace)) {
                $str = $withoutspace[$i] . "" . $withoutspace[$i + 1];
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $tmpstr));
            } else {
                $str = $withoutspace[$i];
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $tmpstr));
            }
            if ($i > 0) {
                $str = $withoutspace[$i - 1] . "" . $withoutspace[$i] . (isset($withoutspace[$i + 1]) ? $withoutspace[$i + 1] : "");
                $arr[] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $str));
            }
        }
        $zoneData = ZonedSchool::where("zip", $zip)->where(DB::raw("LOWER(replace(city, ' ',''))"), strtolower(str_replace(" ", "", $city)));

        $cityMatchData = clone ($zoneData); //->get();


        $zoneData->where(function ($q) use ($arr, $withoutspace) {
            $count = 0;
            foreach ($withoutspace as $word) {

                if ($count == 0)
                    $q->where(DB::raw("LOWER(replace(street_name,' ', ''))"), 'LIKE', '%' . $word . '%');
                else
                    $q->orWhere(DB::raw("LOWER(replace(street_name,' ', ''))"), 'LIKE', '%' . $word . '%');
                $count++;
            }

            foreach ($arr as $word) {
                $q->orWhere(DB::raw("LOWER(replace(street_name,' ', ''))"), 'LIKE', '%' . $word . '%');
            }
        });
        $streetMatch = clone ($zoneData);

        $zoneData->where(function ($q) use ($arr, $withoutspace) {
            $count = 0;
            foreach ($withoutspace as $word) {
                if ($count == 0)
                    $q->where(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), 'LIKE', '%' . $word . '%');
                else
                    $q->orWhere(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), 'LIKE', '%' . $word . '%');
                $count++;
            }

            foreach ($arr as $word) {
                $q->orWhere(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), 'LIKE', '%' . $word . '%');
            }
        });
        $streetBldgMatch = clone ($zoneData);

        // echo $tmpaddress;exit;
        $streetPlusBldg = $zoneData->where(function ($q) use ($arr, $withoutspace, $tmpaddress) {
            $q->where(DB::raw("LOWER(replace(concat(bldg_num, street_name, prefix_dir),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->where(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, prefix_dir, unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, prefix_dir,unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, unit_info,prefix_dir),' ', ''))"), "LIKE", "%" . $tmpaddress . "%");
        })->get();
        //print_r($streetPlusBldg);exit;
        $addressDiv = $zoned_school = $exportData = "";

        if (count($streetPlusBldg) > 0) {
            $count = 0;
            $exportData = $streetPlusBldg;
            $str = "<select onchange='selectAddress(this.value)' class='form-control' id='addoptions'>";
            $str .= "<option value=''>Select any address</option>";
            $add = "";
            $exatmatch = $matched = "";
            foreach ($streetPlusBldg as $key => $value) {

                $add = $value->bldg_num . " " . $value->street_name . " " . $value->street_type . " " . $value->unit_info;
                $exatmatch = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $add));
                $exatmatch = str_replace(" ", "", $exatmatch);
                $exatmatch = str_replace(",", "", $exatmatch);
                if ($exatmatch == $tmpaddress)
                    $matched = $add;
                $str .= "<option value='" . trim($add) . "'>" . trim($add) . "</option>";
            }
            $str .= "</select>";
            if ($matched != "")
                return $request->address;
            if (count($streetPlusBldg) == 1) {
                echo $add;
            } else
                echo $str;
        } else {
            $exportData = $streetBldgMatch->get();
            if (count($exportData) <= 0) {
                $exportData = $streetMatch->get();
            }

            $count = 0;


            if (count($exportData) == 0) {
                $insert = array();
                $insert['street_address'] = $request->address;
                $insert['city'] = $request->city;
                $insert['zip'] = $request->zip;

                $nz = NoZonedSchool::create($insert);

                Session::forget("step_session");
                echo "NoMatch";
                exit;
            }
            $str = "<select onchange='selectAddress(this.value)' class='form-control' id='addoptions'>";
            $str .= "<option value=''>Select any address</option>";
            $add = "";
            $exatmatch = $matched = "";
            foreach ($exportData as $key => $value) {
                $add = $value->bldg_num . " " . $value->street_name . " " . $value->street_type . " " . $value->unit_info;
                $exatmatch = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $add));
                $exatmatch = str_replace(" ", "", $exatmatch);
                $exatmatch = str_replace(",", "", $exatmatch);
                if ($exatmatch == $tmpaddress)
                    $matched = $add;
                $str .= "<option value='" . trim($add) . "'>" . trim($add) . "</option>";
            }
            $str .= "</select>";

            if ($matched != "")
                return $request->address;


            if (count($exportData) == 1) {
                echo $add;
            } else
                echo $str;
        }
        exit;
    }

    public function exportZonedSchool($exportData, $zoned_school)
    {
        $data_ary = [];
        $heading = array(
            "Bldg No",
            "Street Address",
            "City",
            "Zip",
            "Zoned School"
        );
        $data_ary[] = $heading;


        foreach ($exportData as $key => $value) {
            $tmp = [];
            $tmp[] = $value->bldg_num;
            $tmp[] = $value->street_name;
            $tmp[] = $value->city;
            $tmp[] = $value->zip;
            $tmp[] = $zoned_school;

            $data_ary[] = $tmp;
        }

        // return $data_ary;
        return Excel::download(new ZoneAddressExport(collect($data_ary)), 'ZoneedSchools.xlsx');
    }

    public function importZonedSchoolGet()
    {
        return view('ZonedSchool::import_zoned_school_address');
    }

    public function importZonedSchool(Request $request)
    {
        // return 'test';
        $rules = [
            // 'upload_csv'=>'required',
            'upload_csv' => 'required|mimes:xlsx',
        ];
        $message = [
            // 'upload_csv.required'=>'File is required',
            'upload_csv.required' => 'File is required',
            'upload_csv.mimes' => 'Invalid file format | File format must be xlsx.',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_csv');
            $import = new ZonedSchoolImport;
            $import->import($file);

            Session::flash('success', 'Zoned Address Imported successfully');
        }
        return  redirect()->back();
    }
}
