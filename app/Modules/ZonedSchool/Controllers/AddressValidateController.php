<?php

namespace App\Modules\ZonedSchool\Controllers;

use Illuminate\Http\Request;
use App\Modules\ZonedSchool\Models\ZonedSchool;
use App\Modules\ZonedSchool\Models\ZonedAddressMaster;
use App\Modules\ZonedSchool\Export\ZoneAddressExport;
use App\Modules\ZonedSchool\Export\ZonedSchoolImport;
use App\Modules\ZonedSchool\Models\NoZonedSchool;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Import\Models\NonJCSStudent;
use App\Modules\Form\Models\FormContent;
use App\ZoneAPI;
use App\Modules\School\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AddressValidateController extends Controller
{

    private $end_points = [
        'schools' => "https://maps.huntsvilleal.gov/ArcGIS/rest/services/Layers/Addresses/MapServer/3/query?returnCountOnly=false&returnIdsOnly=false&returnGeometry=false&outFields=elem_sch_distr%2Cmid_sch_distr%2Chigh_sch_distr%2Caddress_full&f=json&where=address_full+LIKE+",
        //        'schools' => "https://maps.huntsvilleal.gov/ArcGIS/rest/services/Layers/Addresses/MapServer/1/query?returnCountOnly=false&returnIdsOnly=false&returnGeometry=false&outFields=elem_sch_distr%2Cmid_sch_distr%2Chigh_sch_distr%2Caddress_full&f=json&where=address_full+LIKE+",
        'possible_addresses' => "https://maps.huntsvilleal.gov/arcgis/rest/services/Locators/CompositeLocator/GeocodeServer/findAddressCandidates?Street=&category=&outFields=*&maxLocations=5&outSR=&searchExtent=&location=&distance=&magicKey=&f=json&SingleLine=",
    ];

    public function __construct()
    {
        // session()->put('district_id', 1);  //26-6-20
    }

    public function prepareAddress($address)
    {
        //HSV City System only used Unit, it changes Apt and Suite over to Unit.
        //We need to do the same. PREG_REPLACE Replaces either words with Unit.
        $address = trim($address);
        $address = preg_replace('/(\bSuite\b)|(\bLot\b)|(\bApt\b)/i', 'Unit', $address);
        $address = preg_replace("/(\.)|(,)|(')|(#)/", '', $address);
        $address = preg_replace('/(\bDrive\b)/i', 'DR', $address);
        $address = preg_replace('/(\bCr\b)/i', 'CIR', $address);
        //$address = preg_replace( '/(\bmc)/i' , 'Mc ' , $address );
        $address = preg_replace('/(\bBlvd\b)/i', 'BLV', $address);
        $address = preg_replace('/(\bAvenue\b)/i', 'AVE', $address);
        $addressArray = explode(' ', $address);

        //Does the index:1 contain an number street. Example: 8th Street.
        if (isset($addressArray[1]) && preg_match('/\d+/', $addressArray[1], $matches) !== false) {
            //Index:1 contains an number. Need to replace.
            //Add in switch statement to handle converting 1st - 17th to First - Seventeenth
            switch (strtoupper($addressArray[1])) {
                case '1ST':
                    $addressArray[1] = 'FIRST';
                    break;
                case '2ND':
                    $addressArray[1] = 'SECOND';
                    break;
                case '3RD':
                    $addressArray[1] = 'THIRD';
                    break;
                case '4TH':
                    $addressArray[1] = 'FOURTH';
                    break;
                case '5TH':
                    $addressArray[1] = 'FIFTH';
                    break;
                case '6TH':
                    $addressArray[1] = 'SIXTH';
                    break;
                case '7TH':
                    $addressArray[1] = 'SEVENTH';
                    break;
                case '8TH':
                    $addressArray[1] = 'EIGHTH';
                    break;
                case '9TH':
                    $addressArray[1] = 'NINTH';
                    break;
                case '10TH':
                    $addressArray[1] = 'TENTH';
                    break;
                case '11TH':
                    $addressArray[1] = 'ELEVENTH';
                    break;
                case '12TH':
                    $addressArray[1] = 'TWELFTH';
                    break;
                case '13TH':
                    $addressArray[1] = 'THIRTEENTH';
                    break;
                case '14TH':
                    $addressArray[1] = 'FOURTEENTH';
                    break;
                case '15TH':
                    $addressArray[1] = 'FIFTEENTH';
                    break;
                case '17TH':
                    $addressArray[1] = 'SEVENTEENTH';
                    break;
                default:
                    break;
            }
        }
        return implode(' ', $addressArray);
    }

    public function getSuggestion(Request $request)
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

    /**
     * Retrieve response from API
     *
     * @param $end_point
     * @return array|mixed
     */
    public function getResponse($end_point)
    {

        $curl = curl_init($end_point);

        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1');    // mPDF 5.7.4
        $data = curl_exec($curl);
        curl_close($curl);

        if (!$data) {
            return [];
        }
        $decoded_data = json_decode($data);

        if (json_last_error() != JSON_ERROR_NONE) {

            Log::error('JSON error: ' . json_last_error_msg());
            return [];
        }

        return $decoded_data;
    }

    public function getAddressCandidates($address, $zip = null, $maxAddresses = null)
    {
        // dd($address, $zip, $maxAddresses);

        // Get possible addresses from API
        $response = $this->getResponse($this->end_points['possible_addresses'] . urlencode($this->prepareAddress($address)));
        // dd($response);
        if (!isset($response->candidates)) {
            return false;
        }

        $possible_addresses = [];
        $scoredList = [];

        //Build list of addresses with scores
        foreach ($response->candidates as $candidate) {
            $scoredList[] = [
                'score' => $candidate->score,
                'addressBound' => $candidate->address
            ];
        }
        //Sort scored list by score descending
        usort($scoredList, function ($a, $b) {
            if ($a['score'] == $b['score']) {
                return 0;
            }
            return ($a['score'] > $b['score']) ? -1 : 1;
        });

        //Remove duplicate addresses
        $final_match = false;
        foreach ($scoredList as $index => $scoredAddress) {
            /*if($scoredAddress['score'] == 100 && !$final_match)
            {
                $final_match = true;
                if( !in_array( $scoredAddress['addressBound'], $possible_addresses ) ) {
                    $possible_addresses[] = $scoredAddress['addressBound'];
                }
            }

            if(!$final_match)
            {*/
            if (!in_array($scoredAddress['addressBound'], $possible_addresses)) {
                $possible_addresses[] = $scoredAddress['addressBound'];
            } else {
                unset($scoredList[$index]);
            }
            //}
        }
        $returnAddresses = [];
        foreach ($scoredList as $address) {
            $returnAddresses[] = $address['addressBound'];
        }

        return $returnAddresses;
    }


    public function getSuggestionCurrent($form_id)
    {
        if (Session::has("form_data")) {
            $dataArray =  Session::get("form_data")[0];
        }
        $formdata = $dataArray['formdata'];

        $address_id = fetch_student_field_id($form_id, "address");
        $zip_id = fetch_student_field_id($form_id, "zip");
        $city_id = fetch_student_field_id($form_id, "city");
        $next_grade_id = fetch_student_field_id($form_id, "next_grade");
        $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
        $student_id = fetch_student_field_id($form_id, "student_id");

        if (isset($formdata[$student_id])) {
            $non_jcs_exist = NonJCSStudent::where("ssid", $formdata[$student_id])->where("enrollment_id", Session::get("enrollment_id"))->first();

            if (!empty($non_jcs_exist) && $non_jcs_exist->approved_zoned_school != '') {
                $formdata[$zoned_field_id] = $non_jcs_exist->approved_zoned_school;
                Session::forget("form_data");
                $dataArray['formdata'] = $formdata;
                Session::push("form_data", $dataArray);
                return $non_jcs_exist->approved_zoned_school;
            }
        }

        $val = $address = Session::get("form_data")[0]["formdata"][$address_id];
        $zip_code = Session::get("form_data")[0]["formdata"][$zip_id];
        $next_grade = Session::get("form_data")[0]["formdata"][$next_grade_id];
        $city = Session::get("form_data")[0]["formdata"][$city_id];

        $addressParts = explode(' ', trim(strtolower($val)));


        $countParts = count($addressParts);

        $value = trim(preg_replace('/\s+/', ' ', strtoupper($val)));

        $results = null;
        $final_address = "";
        for ($useParts = $countParts; $useParts > 0; $useParts--) {
            $searchAddress = implode(' ', array_slice($addressParts, 0, $useParts));
            $zoned_school = $this->getZonedSchoolCurrent($searchAddress, $form_id);
            if ($zoned_school) {
                $formdata[$zoned_field_id] = $zoned_school;
                Session::forget("form_data");
                $dataArray['formdata'] = $formdata;
                Session::push("form_data", $dataArray);
                return $zoned_school;
            }
            return "NoMatch";
        }
        return "NoMatch";
    }

    public function getZonedSchool($form_id)
    {
        if (Session::has("form_data")) {
            $dataArray =  Session::get("form_data")[0];
        }
        $formdata = $dataArray['formdata'];
        $db_fields = FormContent::where('form_id', $form_id)->where('field_property', 'db_field')->get();

        $address = $city = $state = $zip = $zoned_school = "";
        $zoned_field_id = $next_grade = 0;
        foreach ($db_fields as $key => $value) {
            if ($value->field_value == "zoned_school") {
                $zoned_field_id = $value->build_id;
            } elseif (in_array($value->field_value, array("address", "city", "zip", "state", "next_grade"))) {
                if (isset($formdata[$value->build_id])) {
                    ${$value->field_value} = $formdata[$value->build_id];
                }
            }
        }
        if ($address != "") {
            $tmp = explode("-", $zip);
            $zip = $tmp[0];
            $insert = array();
            $insert['street_address'] = $address;
            $insert['city'] = $city;
            $insert['zip'] = $zip;
            $tmpaddress = strtolower(str_replace(" ", "", $address));
            $tmpaddress = str_replace(",", "", $tmpaddress);
            $withoutspace = explode(" ", strtolower($address));
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

            $zoneData = ZoneAPI::where("zip", $zip)->where(DB::raw("LOWER(replace(city, ' ',''))"), strtolower(str_replace(" ", "", $city)));
            $zoneData->where(function ($q) use ($arr, $withoutspace) {
                $q->whereIn(DB::raw("LOWER(replace(street_name,' ', ''))"), $withoutspace)
                    ->orWhereIn(DB::raw("LOWER(replace(street_name,' ', ''))"), $arr);
            });
            $streetMatch = clone ($zoneData); //->get();
            $streetPlusBldg = $zoneData->where(function ($q) use ($arr, $withoutspace, $tmpaddress) {
                $q->where(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir, unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orwhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), $tmpaddress)
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir),' ', ''))"), $tmpaddress)
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir, unit_info),' ', ''))"), $tmpaddress);
            })->first();

            if (!empty($streetPlusBldg)) {
                $zoned_school = $this->getZonedSchoolName($next_grade, $streetPlusBldg);
            } else {
                $result = $streetMatch->get();
                /* if(count($result) > 5)
                {
                    $nz = NoZonedSchool::create($insert);
                    return false;
                }*/
                $count = 0;
                foreach ($result as $key => $value) {
                    if ($count == 0) {
                        $zoned_school = $this->getZonedSchoolName($next_grade, $value);
                        break;
                    }
                    $count++;
                }
            }
            if ($zoned_school != '') {
                return $zoned_school;
            } else {
                $nz = NoZonedSchool::create($insert);
                return false;
            }
        }
        return true;
    }

    public function getZonedSchoolName($next_grade = '', $zone_address)
    {
        // $ZonedSchoolGrades_conf = config('variables.ZonedSchoolGrades');
        $key_schools = [];
        $zoned_school = '';
        array_push($key_schools, $zone_address->elementary_school);
        array_push($key_schools, $zone_address->middle_school);
        array_push($key_schools, $zone_address->high_school);
        foreach ($key_schools as $scl) {
            if ($scl != '') {
                $related_school = School::where(function ($qry) use ($scl) {
                    $qry->where('name', $scl);
                    $qry->orWhere('zoning_api_name', $scl);
                    $qry->orWhere('sis_name', $scl);
                })
                    ->whereRaw("FIND_IN_SET('" . $next_grade . "', grade_id)")
                    ->first();

                if (isset($related_school)) {
                    $zoned_school = $scl;
                    break;
                }
            }
        }

        return $zoned_school;
    }

    function getZonedSchoolCurrent($address1, $form_id)
    {
        if (Session::has("form_data")) {
            $dataArray =  Session::get("form_data")[0];
        }
        $formdata = $dataArray['formdata'];
        $db_fields = FormContent::where('form_id', $form_id)->where('field_property', 'db_field')->get();

        $address = $city = $state = $zip = $zoned_school = "";
        $zoned_field_id = $next_grade = 0;
        foreach ($db_fields as $key => $value) {
            if ($value->field_value == "zoned_school") {
                $zoned_field_id = $value->build_id;
            } elseif (in_array($value->field_value, array("city", "zip", "state", "next_grade"))) {
                if (isset($formdata[$value->build_id])) {
                    ${$value->field_value} = $formdata[$value->build_id];
                }
            }
        }
        $address = $address1;
        if ($address != "") {
            $tmp = explode("-", $zip);
            $zip = $tmp[0];
            $insert = array();
            $insert['street_address'] = $address;
            $insert['city'] = $city;
            $insert['zip'] = $zip;
            $tmpaddress = strtolower(str_replace(" ", "", $address));
            $tmpaddress = str_replace(",", "", $tmpaddress);
            $withoutspace = explode(" ", strtolower($address));
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


            $zoneData = ZoneAPI::where("zip", $zip);
            //->where(DB::raw("LOWER(replace(city, ' ',''))"), strtolower(str_replace(" ","", $city)));
            //dd($zip, $city, $zoneData->get());
            $zoneData->where(function ($q) use ($arr, $withoutspace) {
                $q->whereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name),' ', ''))"), $withoutspace)
                    ->orWhereIn(DB::raw("LOWER(replace(concat(bldg_num, street_name),' ', ''))"), $arr);
            });
            $streetMatch = clone ($zoneData); //->get();

            //echo $tmpaddress;exit;
            $streetPlusBldg = $zoneData->where(function ($q) use ($arr, $withoutspace, $tmpaddress) {
                $q->where(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(street_name, street_type),' ', ''))"), "LIKE", "" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), $tmpaddress)
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir, unit_info),' ', ''))"), "LIKE", "%" . $tmpaddress . "%")
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type),' ', ''))"), $tmpaddress)
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir),' ', ''))"), $tmpaddress)
                    ->orWhere(DB::raw("LOWER(replace(concat(bldg_num, street_name, street_type, suffix_dir, unit_info),' ', ''))"), $tmpaddress);
            })->first();



            if (!empty($streetPlusBldg)) {
                $zoned_school = $this->getZonedSchoolName($next_grade, $streetPlusBldg);
            } else {
                $nz = NoZonedSchool::create($insert);
                return false;
                $result = $streetMatch->get();
                /* if(count($result) > 5)
                {
                    $nz = NoZonedSchool::create($insert);
                    return false;
                }*/
                $count = 0;
                foreach ($result as $key => $value) {

                    if ($count == 0) {
                        $zoned_school = $this->getZonedSchoolName($next_grade, $value);
                        break;
                    }
                    $count++;
                }
            }
            if ($zoned_school != '') {
                return $zoned_school;
            } else {
                $nz = NoZonedSchool::create($insert);
                return false;
            }
        }
        return true;
    }
}
