<?php

namespace App\Modules\ZonedSchool\Controllers;

use Illuminate\Http\Request;
use App\Modules\ZonedSchool\Models\ZonedSchool;
use App\Modules\ZonedSchool\Models\ZonedAddressMaster;
use App\Modules\ZonedSchool\Export\ZoneAddressExport;
use App\Modules\ZonedSchool\Export\ZonedSchoolImport;
use App\Modules\ZonedSchool\Models\NoZonedSchool;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function masterindex()
    {
        $masterAddress = ZonedAddressMaster::get();
        return view('ZonedSchool::master_index', compact('masterAddress'));
    }

    public function index($master_id)
    {
        $master_address_id = $master_id;
        return view('ZonedSchool::index', compact('master_address_id'));
    }

    public function addressOverrideAddress()
    {
        $zonedSchool = ZonedSchool::where('added_by', 'manual')->get();
        return view('ZonedSchool::address_override_index', compact('zonedSchool'));
    }

    public function getZonedSchool(Request $request, $master_address_id)
    {
        $send_arr = $data_arr = array();
        // $zoneData = ZonedSchool::take(1000)->get();
        $zoneData = ZonedSchool::getZonedSchoolList($request->all(), 1, $master_address_id);
        $total = ZonedSchool::getZonedSchoolList($request->all(), 0, $master_address_id);
        // dd($zoneData);
        foreach ($zoneData as $value) {
            $send_arr[] = [
                $value->bldg_num,
                $value->street_name,
                $value->street_type,
                //$value->suffix_dir_full,
                $value->unit_info,
                $value->city,
                $value->zip,
                $value->elementary_school,
                $value->intermediate_school,
                $value->middle_school,
                $value->high_school,
                "<div class='text-center'><a href=" . url('admin/ZonedSchool/edit', $value->id) . " title='Edit' class='font-18'><i class='far fa-edit'></i></a></div>"
            ];
        }

        $data_arr['recordsTotal'] = $total;
        $data_arr['recordsFiltered'] = $total;
        $data_arr['data'] = $send_arr;
        return json_encode($data_arr);
        // dd($zoneData);
    }

    public function create()
    {
        $mag_el = array("Eichold-Mertz Magnet School (K-5)", "Just 4 (K-5)", "Old Shell Rd Magnet Elementary (K-5)");
        $mag_middle = array("Clark-Shaw Magnet School (6-8)", "Phillips Preparatory School (6-8)", "Dunbar Magnet School (6-8)", "Denton Magnet School of Technology (6-8)", "The Barton Academy for Advanced World Studies (6-9)");

        $common_data = DB::select("SELECT
            (SELECT group_concat(DISTINCT street_type) FROM zone_address) as street_type,
            (SELECT group_concat(DISTINCT prefix_dir) FROM zone_address) as prefix_dir,
            (SELECT group_concat(DISTINCT city) FROM zone_address) as city,
            (SELECT group_concat(DISTINCT zip) FROM zone_address) as zip,
            (SELECT group_concat(DISTINCT elementary_school) FROM zone_address) as elementary_school,
            (SELECT group_concat(DISTINCT middle_school) FROM zone_address) as middle_school,
            (SELECT group_concat(DISTINCT high_school) FROM zone_address) as high_school,
            (SELECT group_concat(DISTINCT intermediate_school) FROM zone_address) as intermediate_school");
        $street_type = explode(",", $common_data[0]->street_type);
        $prefix_dir = explode(",", $common_data[0]->prefix_dir);
        $city = explode(",", $common_data[0]->city);
        $zip = explode(",", $common_data[0]->zip);

        $elementary_school = explode(",", $common_data[0]->elementary_school);
        foreach ($mag_el as $val) {
            if (!in_array($val, $elementary_school)) {
                $elementary_school[] = $val;
            }
        }

        $middle_school = explode(",", $common_data[0]->middle_school);
        foreach ($mag_middle as $val) {
            if (!in_array($val, $middle_school)) {
                $middle_school[] = $val;
            }
        }

        $high_school = explode(",", $common_data[0]->high_school);
        $intermediate_school = explode(",", $common_data[0]->intermediate_school);
        sort($street_type);
        sort($prefix_dir);
        sort($city);
        sort($zip);
        sort($elementary_school);
        sort($high_school);
        sort($middle_school);
        sort($intermediate_school);

        return view('ZonedSchool::create', compact("street_type", "prefix_dir", "city", "zip", "elementary_school", "middle_school", "high_school", "intermediate_school"));
    }

    public function store(Request $request)
    {
        $district_id = Session::get("district_id");
        $msg = [
            'street_name.required' => 'Street Name is required.',
            /*'zip.required'=>'Zip code is required.',
            'zip.regex'=>'The Zip code must be an integer.',
            'zip.max'=>'The Zip code may not be grater than 6 characters.',
            'zip.min'=>'The Zip code must be at least 5 characters.',*/
        ];

        $validateData = $request->validate([
            'street_name' => 'required|max:255',
            //'zip' =>'required|regex:/^\d+$/|max:6|min:5',
        ], $msg);

        if ($request->elementary_school == "" && $request->intermediate_school == "" && $request->middle_school == "" && $request->high_school == "") {
            Session::flash('error', "At least one school is required.");
            return redirect()->back()->withInput();
        }

        $suffix_dir_full = "";
        if (isset($request->suffix_dir)) {
            $suffixArr = ['S' => 'South', 'E' => 'East', 'N' => 'North', 'W' => 'West'];
            (isset($suffixArr[$request->suffix_dir])) ? $suffix_dir_full = $suffixArr[$request->suffix_dir] : "";
        }

        $data = array();

        $data['bldg_num'] = $request->bldg_num;
        $data['street_name'] = $request->street_name;
        if ($request->street_type == "Other")
            $data['street_type'] = $request->street_type_other;
        else
            $data['street_type'] = $request->street_type;
        $data['unit_info'] = $request->unit_info;
        $data['suffix_dir'] = $request->suffix_dir;
        $data['suffix_dir_full'] = $suffix_dir_full;
        $data['state'] = $request->state;
        $data['prefix_dir'] = $request->prefix_dir;
        $data['user_id'] = Auth::user()->id;

        if ($request->city == "Other")
            $data['city'] = $request->city_other;
        else
            $data['city'] = $request->city;

        if ($request->zip == "Other")
            $data['zip'] = $request->zip_other;
        else
            $data['zip'] = $request->zip;

        if ($request->elementary_school == "Other")
            $data['elementary_school'] = $request->elementary_school_other;
        else
            $data['elementary_school'] = $request->elementary_school;

        if ($request->intermediate_school == "Other")
            $data['intermediate_school'] = $request->intermediate_school_other;
        else
            $data['intermediate_school'] = $request->intermediate_school;

        if ($request->middle_school == "Other")
            $data['middle_school'] = $request->middle_school_other;
        else
            $data['middle_school'] = $request->middle_school;

        if ($request->high_school == "Other")
            $data['high_school'] = $request->high_school_other;
        else
            $data['high_school'] = $request->high_school;

        $data['added_by'] = 'manual';
        $data['user_id'] = Auth::user()->id;
        $data['district_id'] = $district_id;
        $data['zone_master_id'] = 1;

        //dd($data);
        $zoned_id = ZonedSchool::create($data)->id;

        if (isset($zoned_id)) {
            Session::flash("success", "Zone Address added successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            return redirect('admin/ZonedSchool/overrideAddress');
        }
        return redirect('admin/ZonedSchool/create');
    }

    public function edit($id)
    {
        // dd($id);
        $common_data = DB::select("SELECT
            (SELECT group_concat(DISTINCT street_type) FROM zone_address) as street_type,
            (SELECT group_concat(DISTINCT prefix_dir) FROM zone_address) as prefix_dir,
            (SELECT group_concat(DISTINCT city) FROM zone_address) as city,
            (SELECT group_concat(DISTINCT zip) FROM zone_address) as zip,
            (SELECT group_concat(DISTINCT elementary_school) FROM zone_address) as elementary_school,
            (SELECT group_concat(DISTINCT middle_school) FROM zone_address) as middle_school,
            (SELECT group_concat(DISTINCT high_school) FROM zone_address) as high_school,
            (SELECT group_concat(DISTINCT intermediate_school) FROM zone_address) as intermediate_school");
        $street_type = explode(",", $common_data[0]->street_type);
        $prefix_dir = explode(",", $common_data[0]->prefix_dir);
        $city = explode(",", $common_data[0]->city);
        $zip = explode(",", $common_data[0]->zip);
        $elementary_school = explode(",", $common_data[0]->elementary_school);
        $middle_school = explode(",", $common_data[0]->middle_school);
        $high_school = explode(",", $common_data[0]->high_school);
        $intermediate_school = explode(",", $common_data[0]->intermediate_school);
        sort($street_type);
        sort($prefix_dir);
        sort($city);
        sort($zip);
        sort($elementary_school);
        sort($high_school);
        sort($middle_school);
        sort($intermediate_school);

        $zonedschool = ZonedSchool::where('id', $id)->first();
        return view('ZonedSchool::edit', compact('zonedschool', "street_type", "prefix_dir", "city", "zip", "elementary_school", "middle_school", "high_school", "intermediate_school"));
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        $msg = [
            'street_name.required' => 'Street Name is required.',
            'zip.required' => 'Zip code is required.',
            'zip.regex' => 'The Zip code must be an integer.',
            'zip.max' => 'The Zip code may not be grater than 6 characters.',
            'zip.min' => 'The Zip code must be at least 5 characters.',
        ];

        $validateData = $request->validate([
            'street_name' => 'required|max:255',
            'zip' => 'required|regex:/^\d+$/|max:6|min:5',
        ], $msg);

        $suffix_dir_full = "";
        if (isset($request->suffix_dir)) {
            $suffixArr = ['S' => 'South', 'E' => 'East', 'N' => 'North', 'W' => 'West'];
            (isset($suffixArr[$request->suffix_dir])) ? $suffix_dir_full = $suffixArr[$request->suffix_dir] : "";
        }



        $data['bldg_num'] = $request->bldg_num;
        $data['street_name'] = $request->street_name;
        if ($request->street_type == "Other")
            $data['street_type'] = $request->street_type_other;
        else
            $data['street_type'] = $request->street_type;
        $data['unit_info'] = $request->unit_info;
        $data['suffix_dir'] = $request->suffix_dir;
        $data['suffix_dir_full'] = $suffix_dir_full;
        $data['state'] = $request->state;
        $data['prefix_dir'] = $request->prefix_dir;

        if ($request->city == "Other")
            $data['city'] = $request->city_other;
        else
            $data['city'] = $request->city;

        if ($request->zip == "Other")
            $data['zip'] = $request->zip_other;
        else
            $data['zip'] = $request->zip;

        if ($request->elementary_school == "Other")
            $data['elementary_school'] = $request->elementary_school_other;
        else
            $data['elementary_school'] = $request->elementary_school;

        if ($request->intermediate_school == "Other")
            $data['intermediate_school'] = $request->intermediate_school_other;
        else
            $data['intermediate_school'] = $request->intermediate_school;

        if ($request->middle_school == "Other")
            $data['middle_school'] = $request->middle_school_other;
        else
            $data['middle_school'] = $request->middle_school;

        if ($request->high_school == "Other")
            $data['high_school'] = $request->high_school_other;
        else
            $data['high_school'] = $request->high_school;

        // $data['user_id'] = Auth::user()->id;
        $data['district_id'] = Session::get('district_id'); //$district_id;


        $result = ZonedSchool::where('id', $id)->update($data);

        if (isset($result)) {
            Session::flash("success", "Zoned Address Updated successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            if ($request->added_by == 'manual') {
                return redirect('admin/ZonedSchool/overrideAddress');
            } else {
                return redirect('admin/ZonedSchool');
            }
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
        $tmp = explode("-", $zip);
        $zip = $tmp[0];
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
                    $q->where(DB::raw("LOWER(replace(street_name,' ', ''))"), $word);
                else
                    $q->orWhere(DB::raw("LOWER(replace(street_name,' ', ''))"), $word);
                $count++;
            }

            foreach ($arr as $word) {
                $q->orWhere(DB::raw("LOWER(replace(street_name,' ', ''))"), $word);
            }
        });


        $streetMatch = clone ($zoneData);

        $zoneData->where(function ($q) use ($arr, $withoutspace) {
            $count = 0;
            foreach ($withoutspace as $word) {
                if ($count == 0)
                    $q->where(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), $word);
                else
                    $q->orWhere(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), $word);
                $count++;
            }

            foreach ($arr as $word) {
                $q->orWhere(DB::raw("LOWER(replace(concat(bldg_num),' ', ''))"), $word);
            }
        });
        $streetBldgMatch = clone ($zoneData);
        $streetPlusBldg = $zoneData->where(function ($q) use ($arr, $withoutspace, $tmpaddress) {
            $q->where(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), $tmpaddress)
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir),' ', ''))"), $tmpaddress)
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir, unit_info),' ', ''))"), $tmpaddress)
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir_full),' ', ''))"), $tmpaddress)
                ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir_full, unit_info),' ', ''))"), $tmpaddress);
        })->get();

        $addressDiv = $zoned_school = $exportData = "";

        if (count($streetPlusBldg) > 0) {
            $count = 0;
            $exportData = $streetPlusBldg;
            $str = "<select onchange='selectAddress(this.value)' class='form-control' id='addoptions'>";
            $str .= "<option value=''>Select any address</option>";
            $add = "";
            $exatmatch = $matched = "";
            foreach ($streetPlusBldg as $key => $value) {

                $add = $value->bldg_num . " " . $value->street_name . " " . $value->street_type . " " . $value->suffix_dir . " " . $value->unit_info;
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


            if (count($exportData) <= 0) {
                $exportData = $cityMatchData->where(function ($q) use ($arr, $withoutspace) {
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
                })->get();
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
                $add = $value->bldg_num . " " . $value->street_name . " " . $value->street_type . " " . $value->suffix_dir . " " . $value->unit_info;
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

    public function importZonedSchoolGet($master_address_id = 0)
    {
        return $master_address_id;
        return view('ZonedSchool::import_zoned_school_address');
    }

    public function importZonedSchool(Request $request)
    {
        // return 'test';
        $rules = [
            // 'upload_csv'=>'required',
            'upload_csv' => 'required|mimes:xlsx',
            'group_name' => 'required'
        ];
        $message = [
            // 'upload_csv.required'=>'File is required',
            'upload_csv.required' => 'File is required',
            'upload_csv.mimes' => 'Invalid file format | File format must be xlsx.',
            'group_name.required' => 'The Address Group Name field is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_csv');
            // dd($file);
            $master_address = ZonedAddressMaster::where('group_name', $request->group_name)->first();
            if (isset($master_address) && !empty($master_address)) {
                $zone_master_id = $master_address->id;
            } else {
                $address = [
                    'group_name' => $request->group_name,
                    'district_id' => Session::get('district_id'),
                    'user_id' => Auth::user()->id,
                    'status' => 'N'
                ];

                $zone_master_id = ZonedAddressMaster::create($address)->id;
            }

            $import = new ZonedSchoolImport($zone_master_id);
            $import->import($file);
            $invalidArr = $import->invalidArr;
            $addedArr = $import->addedArr;

            Session::flash('success', 'Zoned Address Imported successfully');
            return view('ZonedSchool::after_import', compact("invalidArr", "addedArr"));
        }
        return  redirect()->back();
    }

    public function masterChangeStatus($id)
    {
        ZonedAddressMaster::where('id', '!=', $id)->update(['status' => 'N']);
        ZonedAddressMaster::where('id', $id)->update(['status' => 'Y']);

        return redirect('admin/ZonedSchool');
    }
}
