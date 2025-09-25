<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (!function_exists('checkPermission')) {
    function checkPermission($role_id, $slug)
    {
        $flag  = 0;
        $roles = new App\Modules\Role\Controllers\RoleController;
        $permission = $roles->getRolesPermssion($role_id, $slug)->count();
        if ($permission > 0 || in_array(Auth::user()->role_id, [1])) {
            $flag = 1;
        }

        return $flag;
    }
}
if (!function_exists('getDistrictLogo')) {
    function getDistrictLogo($logo_type = "")
    {
        if (Session::has('district_id')) {
            if (Session::get('district_id') > 0) {
                $rsStores = DB::table("district")->where("id", Session::get('district_id'))->get();
                if (!empty($rsStores)) {
                    if ($logo_type == "")
                        $logo = $rsStores[0]->district_logo;
                    else
                        $logo = $rsStores[0]->{$logo_type};
                    return url('/') . '/resources/filebrowser/' . $rsStores[0]->district_slug . "/logo/" . $logo; //url('/resources/filebrowser/').'/'.$rsStores[0]->display_name.'/Logo/'.$rsStores[0]->logo_file;
                }
            }
            //print_r($rsStores);
        }
        return url('/resources/assets/admin/images/login.png');
    }
}
if (!function_exists('get_form_name')) {
    function get_form_name($form_id)
    {
        $FormControl = new \App\Modules\Form\Models\Form;
        $form = $FormControl::where("id", $form_id)->first();
        if (!empty($form))
            return $form->name;
        else
            return "";
    }
}
if (!function_exists('get_district_combo')) {
    function get_district_combo()
    {
        $districtList = "";
        $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        $tmp = explode("@", $controller);
        if ($tmp[0] == "DistrictController")
            return "";

        if (Session::get('super_admin') == "Y") {
            $rsStores = DB::table("district")->where("status", "Y")->get();

            if (!empty($rsStores)) {
                $districtList .= '<select class="form-control custom-select custom-select-sm w-120 theme-changer" autocomplete="off" id="district" onchange="changeDistrict(this.value)">';
                //$districtList .= '<option value="0">All</option>';
                foreach ($rsStores as $key => $value) {
                    $districtList .= '<option value="' . $value->id . '"' . (Session::get('district_id') == $value->id ? "selected" : "") . '>' . $value->short_name . '</option>';
                }
                $districtList .= '</select>';
            }
        }
        //        echo $districtList;exit;
        return $districtList;
    }
}
if (!function_exists('get_enrollment_combo')) {
    function get_enrollment_combo()
    {
        $district_id = Session::get('district_id');
        $enrollments = DB::table("enrollments")->where('district_id', $district_id)->where("status", "Y")->get();
        $enrollList = '';
        if (isset($enrollments) && !empty($enrollments->toArray())) {
            $enrollList .= '<select class="form-control custom-select custom-select-sm w-120 theme-changer" autocomplete="off" id="enrollment" onchange="changeEnrollment(this.value)">';
            //$districtList .= '<option value="0">All</option>';
            foreach ($enrollments as $key => $value) {
                $enrollList .= '<option value="' . $value->id . '"' . (Session::get('enrollment_id') == $value->id ? "selected" : "") . '>' . $value->school_year . '</option>';
            }
            $enrollList .= '</select>';
        }
        return $enrollList;
    }
}
if (!function_exists('theme_css')) {
    function theme_css()
    {
        $css = "<style>
        ";
        $css .= "header {background-color: " . Session::get("theme_color") . " !important;}";
        $css .= ".logo-box {background-color: " . Session::get("theme_color") . " !important;}";
        $css .= "</style>";
        return $css;
    }
}
if (!function_exists('getTopLinks')) {
    function getTopLinks()
    {
        $topLinks = DB::table("landing_links")->where("district_id", Session::get("district_id"))->where('status', 'Y')->orderBy('sort_order')->get();
        $rsStores = DB::table("district")->where("id", Session::get('district_id'))->first();

        $class = "top_links_" . count($topLinks);
        $linkStr = '<div class="mt-20 d-flex flex-wrap justify-content-around top-links ' . $class . '">';
        foreach ($topLinks as $key => $value) {
            $linkStr .= '<div class="">';
            if ($value->link_filename != "") {
                $link = url('/resources/filebrowser/' . $rsStores->district_slug . '/documents/' . $value->link_filename);
                $linkStr .= "<a href='" . $link . "' title='" . $value->link_title . "' target='_blank'>" . $value->link_title . "</a>";
            } elseif ($value->link_url != "") {
                $linkStr .= "<a href='" . $value->link_url . "' title='" . $value->link_title . "' target='_blank'>" . $value->link_title . "</a>";
            } else {
                $linkStr .= "<a href='" . url('/showinfo/' . $value->link_id) . "' data-toggle='modal' data-target='#infopopup' class='ls-modal' title='" . $value->link_title . "'>" . $value->link_title . "</a>";
            }
            $linkStr .= '</div>';
        }
        $linkStr .= "</div>";
        return $linkStr;
    }
}
if (!function_exists('getConfig')) {
    function getConfig()
    {
        if (session('district_id')) {
            $conf   = DB::table('district_config')->where("district_id", Session::get("district_id"))->get();
        } else {
            $conf   = DB::table('district_config')->get();
        }

        $return = [];
        foreach ($conf as $key => $value) {
            $return[$value->config_name] = $value->config_value;
        }
        return $return;
    }
}
if (!function_exists('getFormbyId')) {
    function getFormbyId($form_id)
    {
        $FrontControl = new \App\Http\Controllers\HomeController;
        $data  = $FrontControl->getFormbyId($form_id);
        return $data;
    }
}
if (!function_exists('getPreviewFieldByTypeandId')) {
    function getPreviewFieldByTypeandId($type_id, $form_field_id, $form_id = '')
    {
        $FrontControl = new \App\Http\Controllers\HomeController;
        $data  = $FrontControl->getPreviewFieldByTypeandId($type_id, $form_field_id, $form_id);
        return $data;
    }
}
if (!function_exists('getFieldByTypeandId')) {
    function getFieldByTypeandId($type_id, $form_field_id, $form_id = '')
    {
        $FrontControl = new \App\Http\Controllers\HomeController;
        $data  = $FrontControl->getFieldByTypeandId($type_id, $form_field_id, $form_id);
        return $data;
    }
}
if (!function_exists('getOnlyFieldProperty')) {
    function getOnlyFieldProperty($type_id, $form_field_id, $form_id = '')
    {
        $property = DB::table('form_content')->join('form_build', 'form_build.id', 'form_content.build_id')->where('build_id', $form_field_id)->select('form_content.*', 'form_build.type')->orderBy('sort_option')->get();

        $data = [];
        foreach ($property as $key => $value) {
            $data[$value->field_property] = $value->field_value;
            $data['type'] = $value->type;
        }
        //    print_r($data);
        return $data;
    }
}
if (!function_exists('get_form_pages')) {
    function get_form_pages($form_id)
    {
        $rsForm = DB::table("form")->where('id', $form_id)->first();
        return $rsForm->no_of_pages;
    }
}
if (!function_exists('getFormElementLabel')) {
    function getFormElementLabel($field_id)
    {
        $rsField = DB::table("form_content")->where('build_id', $field_id)->where('field_property', 'label')->first();
        if (!empty($rsField)) {
            return $rsField->field_value;
        } else
            return "";
    }
}
if (!function_exists('getFormElementType')) {
    function getFormElementType($field_id)
    {
        $rsField = DB::table("form_build")->where('id', $field_id)->first();
        if (!empty($rsField)) {
            return $rsField->type;
        } else
            return "";
    }
}
if (!function_exists('get_district_slug')) {
    function get_district_slug()
    {
        $rsDistrict = DB::table("district")->where("id", Session::get("district_id"))->first();
        return $rsDistrict->district_slug;
    }
}
if (!function_exists('fetch_student_field_id')) {
    function fetch_student_field_id($form_id, $field = 'student_id')
    {
        $rs_field = DB::table("form_content")->where('field_property', 'db_field')->where('field_value', $field)->join('form_build', 'form_build.id', 'form_content.build_id')->where('form_content.form_id', $form_id)->first();
        if (!empty($rs_field)) {
            return $rs_field->build_id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getFrontProgramDropdown')) {
    function getFrontProgramDropdown()
    {
        $application_id = Session::get("application_id");
        $form_id = Session::get("form_id");

        $rs_program_grade = DB::table("application_programs")->where("application_id", $application_id)->where("program.parent_submission_form", $form_id)->join("program", "program.id", "application_programs.program_id")->join("grade", "grade.id", "application_programs.grade_id")->select("program.name as program_name", "grade.name as grade_name", "application_programs.id", "application_programs.program_id")->get();
        return $rs_program_grade;
    }
}
if (!function_exists('getProgramDropdown')) {
    function getProgramDropdown($application_id = 0)
    {
        if ($application_id > 0) {
            $rs_program_grade = DB::table("application_programs")->where("application_id", $application_id)->join("program", "program.id", "application_programs.program_id")->join("grade", "grade.id", "application_programs.grade_id")->select("program.name as program_name", "program.zoned_schools", "grade.name as grade_name", "application_programs.id", "application_programs.program_id")->get();
        } else {
            $rs_program_grade = DB::table("application_programs")->where("application_id", Session::get("application_id"))->join("program", "program.id", "application_programs.program_id")->join("grade", "grade.id", "application_programs.grade_id")->select("program.name as program_name", "program.zoned_schools", "grade.name as grade_name", "application_programs.id", "application_programs.program_id")->get();
        }
        //dd($rs_program_grade);
        return $rs_program_grade;
    }
}
if (!function_exists('getSchoolMasterName')) {
    function getSchoolMasterName($zoned_school_name)
    {
        $rs = \App\Modules\School\Models\School::where("zoning_api_name", $zoned_school_name)->orWhere("sis_name", $zoned_school_name)->orWhere("name", $zoned_school_name)->select("name")->first();
        if (!empty($rs))
            return $rs->name;
        else
            return "";
    }
}
if (!function_exists('getSchoolId')) {
    function getSchoolId($school_name)
    {
        $rs = \App\Modules\School\Models\School::where("name", $school_name)->first();
        if (!empty($rs))
            return $rs->id;
        else
            return 0;
    }
}
if (!function_exists('getSchoolName')) {
    function getSchoolName($id)
    {
        $rs = \App\Modules\School\Models\School::where("id", $id)->first();
        if (!empty($rs))
            return $rs->name;
        else
            return 0;
    }
}
if (!function_exists('getEnrolmentConfirmationStyle')) {
    function getEnrolmentConfirmationStyle($application_id)
    {
        $confirm_style = DB::table("enrollments")->join("application", "application.enrollment_id", "enrollments.id")->where("application.id", $application_id)->first();
        return $confirm_style->confirmation_style;
    }
}
if (!function_exists('checkDuplicateFields')) {
    function checkDuplicateFields($form_id)
    {
        $duplication_build = DB::table("form_content")->where('form_id', $form_id)->where('field_property', 'check_unique')->where('field_value', 'yes')->get();
        return $duplication_build;
    }
}
if (!function_exists('get_field_value')) {
    function get_field_value($build_id, $form_id, $field_property)
    {
        $field_value = DB::table("form_content")->where("build_id", $build_id)->where("form_id", $form_id)->where('field_property', $field_property)->first();
        if (!empty($field_value)) {
            return $field_value->field_value;
        } else {
            return "";
        }
    }
}
if (!function_exists('generate_lottery_number')) {
    function generate_lottery_number()
    {
        $lotteryNumbers = array(0, 0, 0, 0, 0, 0);
        $lottery_number = 1;
        $uniqueLottoFlag = true;
        $breakOutIfInfiniteLoopCounter = 0;

        while ($uniqueLottoFlag) {

            //create an array of 6 random numbers that range in value from 1 to 64
            for ($i = 0; $i < 6; $i++) {
                $lotteryNumbers[$i] = rand(1, 64);
            }

            //concatenate all lottery numbers into one lottery number
            $lottery_number = intval(implode($lotteryNumbers));
            //check to see if the lottery number has already been assigned during another submission
            $submission = DB::table("submissions")->where("lottery_number", $lottery_number)->first();
            //if not already assigned, break out of loop
            if (empty($submission)) {
                $uniqueLottoFlag = false;
            }

            //make sure we do not get in an infinite loop; report error if so
            $breakOutIfInfiniteLoopCounter++;
            if ($breakOutIfInfiniteLoopCounter == 500) {
                sleep(1);
            }
            if ($breakOutIfInfiniteLoopCounter == 1000) {
                sleep(1);
            }
            if ($breakOutIfInfiniteLoopCounter == 1250) {
                sleep(1);
            }
            if ($breakOutIfInfiniteLoopCounter == 1400) {
                sleep(1);
            }
            if ($breakOutIfInfiniteLoopCounter > 1500) {
                return 0;
                throw new \Exception("We are sorry, there was an error with your submission. Please visit Student Support Services for assistance, or try your submission again later.");
            }
        }
        return $lottery_number;
    }
}
if (!function_exists('getNextGradeField')) {
    function getNextGradeField($form_field_id)
    {
        $form = DB::table("form_build")->where('id', $form_field_id)->first();

        $rs_field = DB::table("form_content")->where('field_property', 'db_field')->where('field_value', 'next_grade')->where('form_id', $form->form_id)->first();
        if (!empty($rs_field)) {
            return $rs_field->build_id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getZonedSchoolField')) {
    function getZonedSchoolField($form_field_id)
    {
        $form = DB::table("form_build")->where('id', $form_field_id)->first();

        $rs_field = DB::table("form_content")->where('field_property', 'db_field')->where('field_value', 'zoned_school')->where('form_id', $form->form_id)->first();
        if (!empty($rs_field)) {
            return $rs_field->build_id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getCurrentGradeField')) {
    function getCurrentGradeField($form_field_id)
    {
        $form = DB::table("form_build")->where('id', $form_field_id)->first();

        $rs_field = DB::table("form_content")->where('field_property', 'db_field')->where('field_value', 'current_grade')->where('form_id', $form->form_id)->first();
        if (!empty($rs_field)) {
            return $rs_field->build_id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getCurrentSchoolField')) {
    function getCurrentSchoolField($form_field_id)
    {
        $form = DB::table("form_build")->where('id', $form_field_id)->first();

        $rs_field = DB::table("form_content")->where('field_property', 'db_field')->where('field_value', 'current_school')->where('form_id', $form->form_id)->first();
        if (!empty($rs_field)) {
            return $rs_field->build_id;
        } else {
            return 0;
        }
    }
}
if (!function_exists('getStudentGradeData')) {
    function getStudentGradeData($state_id)
    {
        $grade_data = DB::table("studentgrade")->where('stateID', $state_id)->get();

        if (count($grade_data) > 0) {
            return $grade_data;
        } else {
            return array();
        }
    }
}
if (!function_exists('getStudentGradeDataYear')) {
    function getStudentGradeDataYear($state_id, $term_arr, $term_arr1, $subjects)
    {
        $sub = array();
        $configSubject = Config::get('variables.subjects');
        foreach ($subjects as $key => $value) {
            if (isset($configSubject[$value]))
                $sub[] = $configSubject[$value];
        }

        /* $grade_data = DB::table("studentgrade")->where('stateID', $state_id)->where(function ($query) use ($term_arr1, $term_arr) {
        $query->where('academicYear', $term_arr)
              ->orWhere('academicYear', $term_arr1);
    })->whereIn('courseType', $sub)->orderBy('academicTerm')->get();*/
        $grade_data = DB::table("studentgrade")->where('stateID', $state_id)->where(function ($query) use ($term_arr1, $term_arr) {
            $query->where('academicYear', $term_arr)
                ->orWhere('academicYear', $term_arr1);
        })->whereIn('courseTypeID', array(11, 30, 35, 39, 18))->orderBy('academicTerm')->get();


        $tmp_data = $tmp_store = array();
        $type = "";
        foreach ($grade_data as $key => $value) {
            $tmpstr = $value->academicTerm . "-" . $value->courseTypeID;
            if (strstr(strtolower($value->academicTerm) . "-", "wk") || strstr(strtolower($value->academicTerm) . "-", "week")) {
                $type = "9W";
            } elseif (strstr(strtolower($value->academicTerm) . "-", "sem") || strstr(strtolower($value->academicTerm) . "-", "semester")) {
                $type = "SEM";
            } else {
                $type = "YE";
            }
            $type = "9W";
            if (!in_array($tmpstr, $tmp_store)) {
                $tmp_data[] = $value;
                //$tmp_store[] = $value->academicTerm."-".$value->courseTypeID;
            }
        }

        return array("type" => $type, "data" => $tmp_data);
    }
}
if (!function_exists('getStudentGradeDataYearLate')) {
    function getStudentGradeDataYearLate($state_id, $term_calc, $academic_years, $subjects)
    {
        $sub = array();
        $configSubject = Config::get('variables.subjects');
        foreach ($subjects as $key => $value) {
            if (isset($configSubject[$value]))
                $sub[] = $configSubject[$value];
        }

        /* $grade_data = DB::table("studentgrade")->where('stateID', $state_id)->where(function ($query) use ($term_arr1, $term_arr) {
        $query->where('academicYear', $term_arr)
              ->orWhere('academicYear', $term_arr1);
    })->whereIn('courseType', $sub)->orderBy('academicTerm')->get();*/
        //print_r($term_calc);exit;
        $grade_data = DB::table("studentgrade")->where('stateID', $state_id)->whereIn("academicYear", $academic_years)->whereIn("GradeName", $term_calc)->whereIn('courseTypeID', array(11, 30, 35, 39, 18))->orderBy('academicYear')->get();


        $tmp_data = $tmp_store = array();
        $type = "";
        foreach ($grade_data as $key => $value) {
            $tmpstr = $value->academicTerm . "-" . $value->courseTypeID;
            if (strstr(strtolower($value->academicTerm) . "-", "wk") || strstr(strtolower($value->academicTerm) . "-", "week")) {
                $type = "9W";
            } elseif (strstr(strtolower($value->academicTerm) . "-", "sem") || strstr(strtolower($value->academicTerm) . "-", "semester")) {
                $type = "SEM";
            } else {
                $type = "YE";
            }
            $type = "9W";
            if (!in_array($tmpstr, $tmp_store)) {
                $tmp_data[] = $value;
                //$tmp_store[] = $value->academicTerm."-".$value->courseTypeID;
            }
        }

        return array("type" => $type, "data" => $tmp_data);
    }
}
if (!function_exists('getConsolidatedGradeWeekData')) {
    function getConsolidatedGradeWeekData($grade_data, $course_type_id, $conversion)
    {
        if ($conversion == "SEM") {
            $first_sem = $second_sem = $third_sem = $fourth_sem = 0;
            foreach ($grade_data as $key => $value) {
                if ($course_type_id == $value->courseTypeID) {
                    if (strtolower(str_replace(" ", "", $value->academicTerm)) == "1-9wk" || strtolower(str_replace(" ", "", $value->academicTerm)) == "1-9week") {
                        $first_sem = $value->numericGrade;
                    } elseif (strtolower(str_replace(" ", "", $value->academicTerm)) == "2-9wk" || strtolower(str_replace(" ", "", $value->academicTerm)) == "2-9week") {
                        $second_sem = $value->numericGrade;
                    } elseif (strtolower(str_replace(" ", "", $value->academicTerm)) == "3-9wk" || strtolower(str_replace(" ", "", $value->academicTerm)) == "3-9week") {
                        $third_sem = $value->numericGrade;
                    } elseif (strtolower(str_replace(" ", "", $value->academicTerm)) == "4-9wk" || strtolower(str_replace(" ", "", $value->academicTerm)) == "4-9week") {
                        $fourth_sem = $value->numericGrade;
                    }
                }
            }
            return array("Semester 1" => number_format(($first_sem + $second_sem) / 2, 0), "Semester 2" => number_format(($third_sem + $fourth_sem) / 2, 0));
        }

        if ($conversion == "YE") {
            $total = $count = 0;
            foreach ($grade_data as $key => $value) {
                if ($course_type_id == $value->courseTypeID) {
                    $total += $value->numericGrade;
                    $count++;
                }
            }
            return array("Yearly" => number_format($total / $count, 0));
        }
    }
}

/*function getStudentConductData($state_id)
{
    $conduct_data = DB::table("student_conduct_disciplinary")->where('stateID', $state_id)->first();
    //    print_r($conduct_data);exit;
    if(!empty($conduct_data) > 0)
    {
        return $conduct_data;
    }
    else
    {
        return array();
    }
}*/

if (!function_exists('getStudentStandardData')) {
    function getStudentStandardData($state_id)
    {
        $standard_data = DB::table("student_standard_testing")->where('stateID', $state_id)->first();
        //    print_r($standard_data);exit;
        if (!empty($standard_data) > 0) {
            return $standard_data;
        } else {
            return array();
        }
    }
}
if (!function_exists('getAcademicYearGrade')) {
    function getAcademicYearGrade($grade_data)
    {
        $academic_year = array();
        foreach ($grade_data as $key => $value) {
            if (!in_array($value->academicYear, $academic_year)) {
                $academic_year[] = $value->academicYear;
            }
        }
        return $academic_year;
    }
}
if (!function_exists('getAcademicTerms')) {
    function getAcademicTerms($grade_data)
    {
        //    print_r()
        $academic_terms = array();
        foreach ($grade_data as $key => $value) {
            if (!in_array($value['academicTerm'], $academic_terms)) {
                $academic_terms[$value['academicTerm']] = $value['academicTerm'];
            }
        }
        //print_r($academic_terms);exit;
        return $academic_terms;
    }
}
if (!function_exists('getEligibilities')) {
    function getEligibilities($choice_id, $type = '')
    {
        $program = DB::table("application_programs")->where("id", $choice_id)->first();
        if (!empty($program)) {
            if ($type == '') {
                $eligibilities = DB::table("program_eligibility")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->where("program_eligibility.status", 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            } else {
                $eligibilities = DB::table("program_eligibility")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->where("program_eligibility.status", 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("eligibility_template.name", $type)->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            }
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilitiesDynamic')) {
    function getEligibilitiesDynamic($choice_id, $type = '')
    {
        $program = DB::table("application_programs")->where("id", $choice_id)->first();
        if (!empty($program)) {
            // echo $program->program_id;exit;
            if ($type == '') {
                $eligibilities = DB::table("program_eligibility")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->where("program_eligibility.status", 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("program_eligibility.application_id", $program->application_id)->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibiility.alias_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            } else {
                $eligibilities = DB::table("program_eligibility")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->where("program_eligibility.status", 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("program_eligibility.application_id", $program->application_id)->where("eligibility_template.name", $type)->select("program_eligibility.*", "eligibiility.alias_name", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            }
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilitiesLateSubmission')) {
    function getEligibilitiesLateSubmission($choice_id, $type = '')
    {
        $program = DB::table("application_programs")->where("id", $choice_id)->first();
        if (!empty($program)) {
            //        echo $program->program_id;exit;
            if ($type == '') {
                $eligibilities = DB::table("program_eligibility_late_submission")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility_late_submission.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->select("program_eligibility_late_submission.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            } else {
                $eligibilities = DB::table("program_eligibility_late_submission")->where("program_id", $program->program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility_late_submission.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("eligibility_template.name", $type)->select("program_eligibility_late_submission.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibiility.override", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
            }
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getSubmissionEligibilities')) {
    function getSubmissionEligibilities($submission)
    {
        $choice_id = array();
        if ($submission->first_choice != '')
            $choice_id[] = $submission->first_choice;
        if ($submission->second_choice != '')
            $choice_id[] = $submission->second_choice;
        $program = DB::table("application_programs")->whereIn("id", $choice_id)->select("program_id")->get();
        // dd($program, $choice_id);
        $arr = array();
        foreach ($program as $value) {
            $arr[] = $value->program_id;
        }

        if (count($arr) > 0) {
            $eligibilities = \App\Modules\Program\Models\ProgramEligibility::join('eligibility_template', 'eligibility_template.id', 'program_eligibility.eligibility_type')->whereIn("program_id", $arr)->whereRaw("FIND_IN_SET('" . $submission->next_grade . "', grade_lavel_or_recommendation_by)")->where('program_eligibility.status', 'Y')->get();
            $arr = array();
            foreach ($eligibilities as $value) {
                $arr[] = $value->name;
            }
            return $arr;
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getLateSubmissionEligibilities')) {
    function getLateSubmissionEligibilities($submission)
    {
        $choice_id = array();
        if ($submission->first_choice != '')
            $choice_id[] = $submission->first_choice;
        if ($submission->second_choice != '')
            $choice_id[] = $submission->second_choice;
        $program = DB::table("application_programs")->whereIn("id", $choice_id)->select("program_id")->get();
        $arr = array();
        foreach ($program as $value) {
            $arr[] = $value->program_id;
        }

        if (count($arr) > 0) {
            $eligibilities = \App\Modules\Program\Models\ProgramEligibilityLateSubmission::join('eligibility_template', 'eligibility_template.id', 'program_eligibility_late_submission.eligibility_type')->whereIn("program_id", $arr)->whereRaw("FIND_IN_SET('" . $submission->next_grade . "', grade_lavel_or_recommendation_by)")->where('program_eligibility_late_submission.status', 'Y')->get();
            $arr = array();
            foreach ($eligibilities as $value) {
                $arr[] = $value->name;
            }
            return $arr;
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilitiesByProgram')) {

    function getEligibilitiesByProgram($program_id, $type = '')
    {
        if ($type == '') {
            $eligibilities = DB::table("program_eligibility")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        } else {
            $eligibilities = DB::table("program_eligibility")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("eligibility_template.name", $type)->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilitiesByProgramDynamic')) {
    function getEligibilitiesByProgramDynamic($program_id, $type = '', $application_id = '')
    {
        if ($type == '') {
            $eligibilities = DB::table("program_eligibility")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("program_eligibility.application_id", $application_id)->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        } else {
            $eligibilities = DB::table("program_eligibility")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("program_eligibility.application_id", $application_id)->where("eligibility_template.name", $type)->select("program_eligibility.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilitiesByProgramLateSubmission')) {
    function getEligibilitiesByProgramLateSubmission($program_id, $type = '')
    {
        if ($type == '') {
            $eligibilities = DB::table("program_eligibility_late_submission")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility_late_submission.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->select("program_eligibility_late_submission.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        } else {
            $eligibilities = DB::table("program_eligibility_late_submission")->where("program_id", $program_id)->where('eligibiility.status', 'Y')->join("eligibiility", "eligibiility.id", "program_eligibility_late_submission.assigned_eigibility_name")->join("eligibility_template", "eligibility_template.id", "eligibiility.template_id")->where("eligibility_template.name", $type)->select("program_eligibility_late_submission.*", "eligibiility.name as eligibility_name", "eligibility_template.name as eligibility_ype", "eligibility_template.sort")->orderBy('eligibility_template.sort')->get();
        }
        return $eligibilities;
    }
}
if (!function_exists('getEligibilityContent1')) {
    function getEligibilityContent1($eligibility_id)
    {
        $eligibiility_data = DB::table("eligibility_content")->where("eligibility_id", $eligibility_id)->first();
        return json_decode($eligibiility_data->content);
    }
}
if (!function_exists('getFieldSequence')) {
    function getFieldSequence($field_id)
    {
        $form = DB::table("form_build")->where("id", $field_id)->first();
        if (!empty($form)) {
            $fields = DB::table("form_build")->where("form_id", $form->form_id)->orderBy('sort', 'asc')->get();
            return $fields;
        } else
            return array();
    }
}
if (!function_exists('getStudentName')) {
    function getStudentName($student_id)
    {
        $student = DB::table("student")->where('stateID', $student_id)->first();
        if (!empty($student)) {
            return " - " . $student->first_name . " " . $student->last_name;
        } else
            return "";
    }
}
if (!function_exists('findSubmissionForm')) {
    function findSubmissionForm($application_id)
    {
        $form_data = DB::table("form")->join("application", "application.form_id", "form.id")->where("application.id", $application_id)->select("form.name")->first();
        if (!empty($form_data))
            return $form_data->name;
        else
            return "";
    }
}
if (!function_exists('findFormName')) {
    function findFormName($form_id)
    {
        $form_data = DB::table("form")->where("id", $form_id)->first();
        if (!empty($form_data))
            return $form_data->name;
        else
            return "";
    }
}
if (!function_exists('getApplicationDetailsById')) {
    function getApplicationDetailsById($application_id)
    {
        $enrollment_id = DB::table("application")->where("id", $application_id)->pluck("enrollment_id")->first();
        $birthday_cut_off = DB::table("enrollments")->where("id", $enrollment_id)->pluck("perk_birthday_cut_off")->first();
        return $birthday_cut_off;
        // SELECT * FROM enrollments WHERE STATUS = 'Y' AND id = 5
        return $enrollment_id;
        // SELECT * FROM APPLICATION WHERE ID=59
    }
}
if (!function_exists('getProgramName')) {
    function getProgramName($program_id)
    {
        $program = DB::table("program")->where("id", $program_id)->first();
        if (!empty($program))
            return $program->name;
        else
            return "";
    }
}
if (!function_exists('findGPA')) {
    function findGPA($score, $gpa_limit)
    {
        $gpa_grade = "0";
        $count = 4;
        foreach ($gpa_limit as $key => $value) {
            $tmp = explode("-", $value);
            if ($score >= $tmp[0] && $score <= $tmp[1]) {
                $gpa_grade = $count;
                break;
            }
            $count--;
        }
        return $gpa_grade;
    }
}
if (!function_exists('getSubmissionGradeData')) {
    function getSubmissionGradeData($submission_id, $term_calc, $academic_years)
    {
        $term1 = (date("Y") - 1) . "-" . (date("Y"));
        $term2 = (date("Y") - 1) . "-" . (date("y"));
        $term1 = "2019-2020";

        // dd($term_calc);
        $submission_data = DB::table("submission_grade")->where("submission_id", $submission_id)->whereIn('academicYear', $academic_years)->whereIn('courseTypeID', array(3, 4, 9, 7))->where(function ($query) use ($term_calc) {
            $query->whereIn('academicTerm', $term_calc)
                ->orWhere('GradeName', $term_calc);
        })->get();

        if (!empty($submission_data)) {
            $tmpdata = array();
            foreach ($submission_data as $key => $value) {
                $tmp = array();
                $tmp['submission_id'] = $value->submission_id;
                $tmp['academicYear'] = trim($value->academicYear);
                $tmp['sAcademicYear'] = trim($value->academicYear);
                $tmp['academicTerm'] = trim($value->academicTerm);
                $tmp['GradeName'] = trim($value->academicTerm);
                $tmp['courseType'] = $value->courseType;
                $tmp['courseTypeID'] = $value->courseTypeID;
                $tmp['actual_numeric_grade'] = $value->actual_numeric_grade;
                $tmp['advanced_course_bonus'] = $value->advanced_course_bonus;
                $tmp['courseName'] = $value->courseName;
                $tmp['sectionNumber'] = $value->sectionNumber;
                $tmp['numericGrade'] = $value->numericGrade;
                $tmpdata[] = $tmp;
            }
            return $tmpdata;
        } else {
            return array();
        }
    }
}
if (!function_exists('getLateSubmissionGradeData')) {
    function getLateSubmissionGradeData($submission_id, $term_calc, $academic_years)
    {
        $term1 = (date("Y") - 1) . "-" . (date("Y"));
        $term2 = (date("Y") - 1) . "-" . (date("y"));
        $term1 = "2019-2020";

        $tmpdata = array();
        foreach ($academic_years as $avalue) {
            $term1 = $term2 = $avalue;
            foreach ($term_calc as $tvalue) {
                $submission_data = DB::table("submission_grade")->where("submission_id", $submission_id)->where(function ($query) use ($term1, $term2) {
                    $query->where('academicYear', $term1)
                        ->orWhere('academicYear', $term2);
                })->whereIn('courseTypeID', array(11, 30, 35, 18))->where("GradeName", $tvalue)->get();

                if (!empty($submission_data)) {
                    //$tmpdata = array();
                    foreach ($submission_data as $key => $value) {
                        $tmp = array();
                        $tmp['submission_id'] = $value->submission_id;
                        $tmp['academicYear'] = $value->academicYear;
                        $tmp['sAcademicYear'] = $value->academicYear;
                        $tmp['academicTerm'] = $value->GradeName;
                        $tmp['GradeName'] = $value->GradeName;
                        $tmp['courseType'] = $value->courseType;
                        $tmp['courseTypeID'] = $value->courseTypeID;
                        $tmp['courseName'] = $value->courseName;
                        $tmp['sectionNumber'] = $value->sectionNumber;
                        $tmp['numericGrade'] = $value->numericGrade;
                        $tmpdata[] = $tmp;
                    }
                }
            }
        }
        return $tmpdata;
    }
}
if (!function_exists('getSubmissionGradeCalculationData')) {
    function getSubmissionGradeCalculationData($submission_id)
    {
        $submission_grade_data = DB::table("submission_academic_grade_calculation")->where("submission_id", $submission_id)->first();
        if (!empty($submission_grade_data) > 0) {
            return $submission_grade_data;
        } else {
            return array();
        }
    }
}
if (!function_exists('getViewEnableFields')) {
    function getViewEnableFields($form_id)
    {
        $form = DB::table("form_content")->where("form_id", $form_id)->where("field_property", "display_view")->where("field_value", "yes")->get();

        if (!empty($form)) {
            $field_ids = array();
            foreach ($form as $key => $value) {
                $field_ids[] = $value->build_id;
            }
            //print_r($field_ids);exit;

            $fields = DB::table("form_content")->whereIn("build_id", $field_ids)->where('form_id', $form_id)->where('field_property', 'label')->get();
            return $fields;
        } else
            return array();
    }
}
if (!function_exists('getViewEnable')) {
    function getViewEnable($build_id)
    {
        $form = DB::table("form_content")->where("build_id", $build_id)->where("field_property", "display_view")->where("field_value", "yes")->first();

        if (!empty($form))
            return true;
        else
            return false;
    }
}
if (!function_exists('getAcademicScore')) {
    function getAcademicScore($student_id, $courseType, $GradeName, $term1, $term2, $submission_id = 0)
    {
        $data = DB::table("submission_grade")->where("submission_id", $submission_id)->where("courseType", $courseType)->where("GradeName", $GradeName)->where(function ($query) use ($term1, $term2) {
            $query->where('academicYear', $term1)
                ->orWhere('academicYear', $term2);
        })->first();

        if (empty($data) && $student_id != '') {
            $data = DB::table("studentgrade")->where("stateID", $student_id)->where("courseType", $courseType)->where("GradeName", $GradeName)->where(function ($query) use ($term1, $term2) {
                $query->where('academicYear', $term1)
                    ->orWhere('academicYear', $term2);
            })->first();
        }


        if (!empty($data)) {
            return $data->numericGrade;
        } else
            return 0;
    }
}
if (!function_exists('getSubmissionAcademicScore')) {
    function getSubmissionAcademicScore($submission_id, $courseType, $GradeName, $term1, $term2)
    {
        $data = DB::table("submission_grade")->where("submission_id", $submission_id)->where("courseType", $courseType)->where("academicTerm", $GradeName)->where(function ($query) use ($term1, $term2) {
            $query->where('academicYear', $term1)
                ->orWhere('academicYear', $term2);
        })->first();
        if (!empty($data)) {
            return $data->numericGrade;
        } else {
            $student_id = DB::table("submissions")->where("id", $submission_id)->where("student_id", "<>", "")->select('student_id')->first();
            if (!empty($student_id)) {
                /*$data = DB::table("studentgrade")->where("stateID", $student_id->student_id)->get();
            foreach($data as $key=>$value)
            {
                $grade_data = [
                    'submission_id' => $submission_id,
                    'academicYear' => $value->academicYear,
                    'academicTerm' => $value->academicTerm,
                    'courseTypeID' => $value->courseTypeID,
                    'courseName' => $value->courseName,
                    'numericGrade' => $value->numericGrade,
                    'sectionNumber' => $value->sectionNumber,
                    'courseType' => $value->courseType,
                    'stateID' => $value->stateID,
                    'GradeName' => $value->GradeName,
                    'sequence' => $value->sequence,
                    'courseFullName' => $value->courseFullName,
                    'fullsection_number' => $value->fullsection_number,
                ];
                DB::table("submission_grade")->insert($grade_data);
            }*/

                $data = DB::table("studentgrade")->where("stateID", $student_id->student_id)->where("courseType", $courseType)->where("GradeName", $GradeName)->where(function ($query) use ($term1, $term2) {
                    $query->where('academicYear', $term1)
                        ->orWhere('academicYear', $term2);
                })->first();

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $grade_data = [
                            'submission_id' => $submission_id,
                            'academicYear' => $value->academicYear ?? null,
                            'academicTerm' => $value->academicTerm ?? null,
                            'courseTypeID' => $value->courseTypeID ?? null,
                            'courseName' => $value->courseName ?? null,
                            'numericGrade' => $value->numericGrade ?? null,
                            'sectionNumber' => $value->sectionNumber ?? null,
                            'courseType' => $value->courseType ?? null,
                            'stateID' => $value->stateID ?? null,
                            'GradeName' => $value->GradeName ?? null,
                            'sequence' => $value->sequence ?? null,
                            'courseFullName' => $value->courseFullName ?? null,
                            'fullsection_number' => $value->fullsection_number ?? null,
                        ];
                        if ($grade_data['academicYear'] != null)
                            DB::table("submission_grade")->insert($grade_data);
                    }
                    return $data->numericGrade;
                }
            }
            return 0;
        }
    }
}
if (!function_exists('getSubmissionAcademicScoreMissing')) {
    function getSubmissionAcademicScoreMissing($submission_id, $courseType, $GradeName, $term1, $term2)
    {
        $data = DB::table("submission_grade")->where("submission_id", $submission_id)->where("courseType", $courseType)->where("GradeName", $GradeName)->where(function ($query) use ($term1, $term2) {
            $query->where('academicYear', $term1)
                ->orWhere('academicYear', $term2);
        })->first();
        if (!empty($data)) {
            return $data->numericGrade;
        } else {
            $student_id = DB::table("submissions")->where("id", $submission_id)->where("student_id", "<>", "")->select('student_id')->first();
            if (!empty($student_id)) {
                $data = DB::table("studentgrade")->where("stateID", $student_id->student_id)->where("courseType", $courseType)->where("GradeName", $GradeName)->where(function ($query) use ($term1, $term2) {
                    $query->where('academicYear', $term1)
                        ->orWhere('academicYear', $term2);
                })->first();

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $grade_data = [
                            'submission_id' => $submission_id,
                            'academicYear' => $value->academicYear ?? null,
                            'academicTerm' => $value->academicTerm ?? null,
                            'courseTypeID' => $value->courseTypeID ?? null,
                            'courseName' => $value->courseName ?? null,
                            'numericGrade' => $value->numericGrade ?? null,
                            'sectionNumber' => $value->sectionNumber ?? null,
                            'courseType' => $value->courseType ?? null,
                            'stateID' => $value->stateID ?? null,
                            'GradeName' => $value->GradeName ?? null,
                            'sequence' => $value->sequence ?? null,
                            'courseFullName' => $value->courseFullName ?? null,
                            'fullsection_number' => $value->fullsection_number ?? null,
                        ];
                        if ($grade_data['academicYear'] != null)
                            DB::table("submission_grade")->insert($grade_data);
                    }
                    return $data->numericGrade;
                }
            }
            return "NA";
        }
    }
}
if (!function_exists('getApplicationProgramName')) {

    function getApplicationProgramName($program_id)
    {
        $rs = DB::table("program")->join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $program_id)->select("name")->first();

        if (!empty($rs)) {
            return $rs->name;
        } else
            return "";
    }
}
if (!function_exists('getApplicationProgramId')) {

    function getApplicationProgramId($program_id)
    {
        $rs = DB::table("application_programs")->where("application_programs.id", $program_id)->select("program_id")->first();

        if (!empty($rs)) {
            return $rs->program_id;
        } else
            return 0;
    }
}
if (!function_exists('getSetEligibilityData')) {
    function getSetEligibilityData($application_program_id, $type)
    {
        $rs = DB::table("program")->join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $application_program_id)->select("program.id")->first();
        if (!empty($rs)) {
            $program_id = $rs->id;

            $data = DB::table('seteligibility_extravalue')->where('program_id', $program_id)->where('eligibility_type', $type)->first();
            if (!empty($data))
                return json_decode($data->extra_values);
            else
                return array();
        } else {
            return array();
        }
    }
}
if (!function_exists('getSetEligibilityDataDynamic')) {
    function getSetEligibilityDataDynamic($application_program_id, $type)
    {
        $rs = DB::table("program")->join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $application_program_id)->select("program.id", "application_programs.application_id")->first();
        if (!empty($rs)) {
            $program_id = $rs->id;

            $data = DB::table('seteligibility_extravalue')->where("application_id", $rs->application_id)->where('program_id', $program_id)->where('eligibility_type', $type)->first();
            if (!empty($data))
                return json_decode($data->extra_values);
            else
                return array();
        } else {
            return array();
        }
    }
}
if (!function_exists('getSetEligibilityDataLS')) {
    function getSetEligibilityDataLS($application_program_id, $type)
    {
        $rs = DB::table("program")->join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $application_program_id)->select("program.id")->first();
        if (!empty($rs)) {
            $program_id = $rs->id;

            $data = DB::table('seteligibility_extravalue_late_submission')->where('program_id', $program_id)->where('eligibility_type', $type)->first();
            if (!empty($data))
                return json_decode($data->extra_values);
            else
                return array();
        } else {
            return array();
        }
    }
}
if (!function_exists('getSetEligibilityDataByProgramID')) {
    function getSetEligibilityDataByProgramID($program_id, $type)
    {
        $data = DB::table('seteligibility_extravalue')->where('program_id', $program_id)->where('eligibility_type', $type)->first();
        if (!empty($data))
            return json_decode($data->extra_values);
        else
            return array();
    }
}
if (!function_exists('getStudentConductData')) {
    function getStudentConductData($state_id, $submission_id = 0)
    {
        $conduct_data = DB::table("submission_conduct_discplinary_info")->where('submission_id', $submission_id)->first();
        if (empty($conduct_data)) {
            $conduct_data = DB::table("student_conduct_disciplinary")->where('stateID', $state_id)->first();
        }
        if (!empty($conduct_data) > 0) {
            return $conduct_data;
        } else {
            return array();
        }
    }
}
if (!function_exists('getDateTimeFormat')) {
    function getDateTimeFormat($date)
    {
        if ($date != '')
            return date('m/d/Y h:i:s A', strtotime($date));
        else {
            return date('m/d/Y');
        }
    }
}
if (!function_exists('getDateFormat')) {
    function getDateFormat($date)
    {
        return date('m/d/Y', strtotime($date));
    }
}
if (!function_exists('findArrayKey')) {
    function findArrayKey($arr, $value)
    {
        foreach ($arr as $key => $val) {
            if ($val == $value)
                return $key;
        }
        return "";
    }
}
if (!function_exists('getUserName')) {
    function getUserName($user_id)
    {
        $username = "";
        if (isset($user_id)) {
            $user = DB::table("users")->where("id", $user_id)->first();
            if (!empty($user)) {
                $username = ucwords($user->first_name . ' ' . $user->last_name);
            }
        }
        return $username;
    }
}
if (!function_exists('getAlertMsg')) {
    function getAlertMsg($msg_title)
    {
        $data = DB::table("common_alert_msg")->where("msg_title", $msg_title)->first();
        if (!empty($data))
            return $data->msg_txt;
        else
            return "";
    }
}
if (!function_exists('fetch_conduct_details')) {
    function fetch_conduct_details($student_id)
    {
        $data = DB::table("student_cdi_details")->where("stateID", $student_id)->orderByDesc('datetime')->get();
        if (!empty($data)) {
            return $data;
        } else {
            return array();
        }
    }
}
if (!function_exists('getMagnetSchool')) {
    function getMagnetSchool($program_id)
    {
        $data = DB::table("program")->where("id", $program_id)->where("existing_magnet_program_alert", "Y")->select("magnet_school")->first();
        if (!empty($data)) {
            $rs = DB::table("school")->where("name", $data->magnet_school)->orWhere("sis_name", $data->magnet_school)->first();
            if (!empty($rs)) {
                return array($data->magnet_school, $rs->sis_name);
            } else {
                return array($data->magnet_school);
            }
            //return $data->magnet_school;
        } else {
            return array();
        }
    }
}
if (!function_exists('getFieldLabel')) {
    function getFieldLabel($field)
    {
        $content = DB::table("form_content")->where("field_value", $field)->first(['build_id']);
        if (isset($content)) {
            return getContentValue($content->build_id, 'label');
        } else {
            return null;
        }
    }
}
if (!function_exists('sendMail')) {
    function sendMail($emailArr, $log = false)
    {
        $msg = $emailArr['msg'];
        // dd($emailArr);
        $msg = str_replace("{student_name}", (isset($emailArr['first_name']) ? $emailArr['first_name'] . " " . $emailArr['last_name'] : ""), $msg);
        $msg = str_replace("{parent_name}", (isset($emailArr['parent_first_name']) ? $emailArr['parent_first_name'] . " " . $emailArr['parent_last_name'] : ""), $msg);

        $msg = str_replace("{confirm_number}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
        $msg = str_replace("{confirmation_no}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
        $msg = str_replace("{transcript_due_date}", (isset($emailArr['transcript_due_date']) ? $emailArr['transcript_due_date'] : ""), $msg);

        $msg = str_replace("{offer_program}", (isset($emailArr['awarded_school']) ? $emailArr['awarded_school'] : ""), $msg);

        $emailArr['subject'] = str_replace("{confirm_number}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $emailArr['subject']);
        $emailArr['subject'] = str_replace("{confirmation_no}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $emailArr['subject']);


        $emailArr['email_text'] = $msg;
        $emailArr['logo'] = getDistrictLogo();
        $data = array();
        $data['submission_id'] = (isset($emailArr['id']) ? $emailArr['id'] : 0);
        $data['email_to'] = (isset($emailArr['parent_email']) ? $emailArr['parent_email'] : $emailArr['email']);
        $data['email_subject'] = $emailArr['subject'];
        $data['email_body'] = $msg;
        $data['logo'] = getDistrictLogo();
        $data['module'] = (isset($emailArr['module']) ? $emailArr['module'] : 'Edit Communication');

        try {
            Mail::send('emails.index', ['data' => $emailArr], function ($message) use ($emailArr) {
                $message->to($emailArr['email']);
                $message->subject($emailArr['subject']);
            });
            $data['status'] = "success";
        } catch (\Exception $e) {

            $data['status'] = "Error";
            createEmailActivityLog($data);
            Session::flash('error', 'Something went wrong.');
            return false;
        }
        if ($log) {
            createEmailActivityLog($data);
        }

        return true;
    }
}
if (!function_exists('getMaxGrade')) {
    function getMaxGrade($program_id)
    {
        $data = \App\Modules\Program\Models\Program::where("id", $program_id)->first();
        $grade_level = $data->grade_lavel;
        $grades = explode(",", $grade_level);
        $cgrade = $grades[count($grades) - 1];
        if ($cgrade == "PreK" || $cgrade == "K")
            return 0;
        else
            return $cgrade;
    }
}
if (!function_exists('getEnrollmentYear')) {
    function getEnrollmentYear($enrollment_id)
    {
        $enrollment = DB::table("enrollments")->where('id', $enrollment_id)->first();
        if (!empty($enrollment))
            return $enrollment->school_year;
        else
            return "";
    }
}
if (!function_exists('getApplicationName')) {
    function getApplicationName($application_id)
    {
        $application = DB::table("application")->where('id', $application_id)->first();
        if (!empty($application))
            return $application->application_name;
        else
            return "";
    }
}
if (!function_exists('get_signature')) {
    function get_signature($type)
    {
        $dist_config = App\Modules\DistrictConfiguration\Models\DistrictConfiguration::where('district_id', \Session('district_id'))
            ->where('name', $type)
            ->first();
        $signature = url('/resources/filebrowser/signature/common_signature.png');
        if (!empty($dist_config)) {
            $signature = $dist_config->value;
        }
        return str_replace("https://", "http://", $signature);
    }
}
if (!function_exists('changeTimezone')) {
    function changeTimezone()
    {
        $dist_config = App\Modules\District\Models\District::where('id', \Session('district_id'))
            ->select('district_timezone')
            ->first();
        file_put_contents("resources/filebrowser/Timezone.txt", ($dist_config->district_timezone ?? 'US/Central'));
    }
}
if (!function_exists('find_replace_string')) {
    function find_replace_string($str, $arr)
    {
        foreach ($arr as $key => $value) {
            //echo "{".$key."} - ".$value."<BR>";
            $str = str_replace("{" . $key . "}", $value, $str);
        }
        return $str;
    }
}
if (!function_exists('fetch_individual_custom_communication')) {
    function fetch_individual_custom_communication($id)
    {
        $data = DB::table("custom_communication_data")->where('type', 'Letter')->where("submission_id", $id)->select('custom_communication_data.*')->orderByDesc('created_at')->get();
        $download_data = array();
        foreach ($data as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['template_name'] = "";
            $tmp['program'] = "";
            $tmp['grade'] = "";
            $tmp['status'] = "";
            $tmp['generated_by'] = $value->generated_by;
            $tmp['total_count'] = 1;
            $tmp['file_name'] = $value->file_name;
            $tmp['created_at'] = getDateTimeFormat($value->created_at);
            $download_data[] = $tmp;
        }
        return $download_data;
    }
}
if (!function_exists('fetch_individual_email_log')) {
    function fetch_individual_email_log($id)
    {
        $data = DB::table("custom_communication_data")->where('type', 'Email')->where("submission_id", $id)->where('email_body', '<>', '')->select('custom_communication_data.*')->orderByDesc('created_at')->get();
        $download_data = array();
        foreach ($data as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['template_name'] = "";
            $tmp['program'] = "";
            $tmp['grade'] = "";
            $tmp['status'] = "";
            $tmp['generated_by'] = $value->generated_by;
            $tmp['total_count'] = 1;
            $tmp['file_name'] = $value->file_name;
            $tmp['created_at'] = getDateTimeFormat($value->created_at);
            $tmp['email_body'] = $value->email_body;
            $tmp['email_subject'] = $value->email_subject;
            $tmp['email'] = $value->email;
            $download_data[] = $tmp;
        }
        return $download_data;
    }
}
if (!function_exists('getSubmissionStudentName')) {
    function getSubmissionStudentName($id)
    {
        $data = DB::table("submissions")->where("id", $id)->first();
        if (!empty($data)) {
            return $data->first_name . " " . $data->last_name;
        } else
            return "";
    }
}
if (!function_exists('generateShortCode')) {
    function generateShortCode($value)
    {
        $application_data1 = \App\Modules\Application\Models\Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();
        $tmp = array();
        $tmp['id'] = $value->id;
        $tmp['student_id'] = $value->student_id;
        $tmp['confirmation_no'] = $value->confirmation_no;
        $tmp['name'] = $value->first_name . " " . $value->last_name;
        $tmp['first_name'] = $value->first_name;
        $tmp['last_name'] = $value->last_name;
        $tmp['current_grade'] = $value->current_grade;
        $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
        $tmp['current_school'] = $value->current_school;
        $tmp['zoned_school'] = $value->zoned_school;
        $tmp['created_at'] = getDateFormat($value->created_at);
        $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
        $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
        $tmp['program_name'] = getProgramName($value->first_choice_program_id);
        $tmp['birth_date'] = getDateFormat($value->birthday);
        $tmp['student_name'] = $value->first_name . " " . $value->last_name;
        $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
        $tmp['parent_email'] = $value->parent_email;
        $tmp['student_id'] = $value->student_id;
        $tmp['parent_email'] = $value->parent_email;
        $tmp['student_id'] = $value->student_id;
        $tmp['submission_date'] = getDateTimeFormat($value->created_at);
        $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
        $tmp['application_url'] = url('/');
        $tmp['signature'] = get_signature('email_signature');
        $tmp['school_year'] = $application_data1->school_year;
        $tmp['enrollment_period'] = $tmp['school_year'];
        $t1 = explode("-", $tmp['school_year']);
        $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
        $tmp['next_year'] = date("Y") + 1;
        return $tmp;
    }
}
if (!function_exists('getLocationInfoByIp')) {
    function getLocationInfoByIp($ip_address = '')
    {
        if (empty($ip_address)) {
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $server  = @$_SERVER['SERVER_ADDR'];
            $remote  = @$_SERVER['REMOTE_ADDR'];
            if (!empty($client) && filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif (!empty($forward) && filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } elseif (!empty($server) && filter_var($server, FILTER_VALIDATE_IP)) {
                $ip = $server;
            } else {
                $ip = $remote;
            }
        } else {
            $ip = $ip_address;
        }

        $ip_data = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=" . $ip));
        $result  = ['country' => '', 'city' => ''];

        if ($ip_data && $ip_data['geoplugin_countryCode'] != null) {
            $result['country'] = $ip_data['geoplugin_countryCode'];
            $result['city'] = $ip_data['geoplugin_city'];
        }
        return $result;
    }
}
if (!function_exists('getContratIPInfo')) {
    function getContratIPInfo($submission_id)
    {
        $data = DB::table("contract_logs")->where("submission_id", $submission_id)->first();
        $str = "";
        if (!empty($data)) {
            $str .= "<div>";
            $str .= "<strong>IP: </strong>" . $data->ip_address;
            if ($data->city != '')
                $str .= "<br><strong>City: </strong>" . $data->city;
            /*if($data->country != '')
            $str .= "<br><strong>Country: </strong>".$data->country;*/
            $str .= "</div>";
        }
        return $str;
    }
}
if (!function_exists('get_district_global_setting')) {
    function get_district_global_setting($type)
    {
        $dist_config = App\Modules\DistrictConfiguration\Models\DistrictConfiguration::where('name', $type)
            ->first();
        $config_value = '';
        if (!empty($dist_config)) {
            $config_value = $dist_config->value;
        }
        return $config_value;
    }
}
if (!function_exists('getGradeUploadDocs')) {
    function getGradeUploadDocs($submission_id, $type)
    {

        $docs = App\Modules\UploadGrade\Models\Grade::where('submission_id', $submission_id)->where('file_type', $type)->get(['file_name', 'created_at']);
        return $docs;
    }
}
if (!function_exists('getCDIUploadDocs')) {
    function getCDIUploadDocs($submission_id)
    {

        $docs = App\SubmissionDocuments::where('submission_id', $submission_id)->where('doc_cdi', '!=', '')->get();
        // dd($docs, $submission_id);

        if ($docs != '') {
            return $docs;
        }

        return '';
    }
}
if (!function_exists('getSubmissionEligibilitiesIndividual')) {
    function getSubmissionEligibilitiesIndividual($submission, $choice = '')
    {
        $choice_id = array();
        if ($choice != '') {
            if ($submission->{$choice . '_choice'} != '')
                $choice_id[] = $submission->{$choice . '_choice'};
        } else {
            if ($submission->first_choice != '')
                $choice_id[] = $submission->first_choice;
            if ($submission->second_choice != '')
                $choice_id[] = $submission->second_choice;
        }
        $program = DB::table("application_programs")->whereIn("id", $choice_id)->select("program_id")->get();
        $arr = array();
        foreach ($program as $value) {
            $arr[] = $value->program_id;
        }

        if (count($arr) > 0) {
            $eligibilities = \App\Modules\Program\Models\ProgramEligibility::join('eligibility_template', 'eligibility_template.id', 'program_eligibility.eligibility_type')->whereIn("program_id", $arr)->whereRaw("FIND_IN_SET('" . $submission->next_grade . "', grade_lavel_or_recommendation_by)")->where("application_id", $submission->application_id)->where('program_eligibility.status', 'Y')->get();
            $arr = array();
            foreach ($eligibilities as $value) {
                $arr[] = $value->name;
            }
            return $arr;
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getLateSubmissionEligibilitiesIndividual')) {
    function getLateSubmissionEligibilitiesIndividual($submission, $choice = '')
    {
        $choice_id = array();
        if ($choice != '') {
            if ($submission->{$choice . '_choice'} != '')
                $choice_id[] = $submission->{$choice . '_choice'};
        } else {
            if ($submission->first_choice != '')
                $choice_id[] = $submission->first_choice;
            if ($submission->second_choice != '')
                $choice_id[] = $submission->second_choice;
        }
        $program = DB::table("application_programs")->whereIn("id", $choice_id)->select("program_id")->get();
        $arr = array();
        foreach ($program as $value) {
            $arr[] = $value->program_id;
        }

        if (count($arr) > 0) {
            $eligibilities = \App\Modules\Program\Models\ProgramEligibilityLateSubmission::join('eligibility_template', 'eligibility_template.id', 'program_eligibility_late_submission.eligibility_type')->whereIn("program_id", $arr)->whereRaw("FIND_IN_SET('" . $submission->next_grade . "', grade_lavel_or_recommendation_by)")->where('program_eligibility_late_submission.status', 'Y')->get();
            $arr = array();
            foreach ($eligibilities as $value) {
                $arr[] = $value->name;
            }
            return $arr;
        } else {
            $eligibilities = array();
        }
        return $eligibilities;
    }
}
if (!function_exists('getWritingPromptDetails')) {
    function getWritingPromptDetails($submission_id, $eligibility, $late_submission = 'N')
    {
        // function getWritingPromptDetails($submission_id, $eligibility, $late_submission='N', $choice=''){
        // $table = 'writing_prompt_detail';
        // if ($choice == 'first') {
        //     $choice = 1;
        // }else {
        //     $choice = 2;
        // }
        $wp = DB::table('writing_prompt')->where('submission_id', $submission_id)->where('program_id', $eligibility->program_id)->first();
        $wp_data = [];
        if (isset($wp)) {
            $wp_data = DB::table('writing_prompt_detail')->where('wp_id', $wp->id)->get();
        }

        if (count($wp_data) <= 0) {
            $wp_data = [];
            $table = 'seteligibility_extravalue';
            if ($late_submission == 'Y') {
                $table = 'seteligibility_extravalue_late_submission';
            }
            $extraValue = DB::table($table)->where('program_id', $eligibility->program_id)->where('eligibility_type', $eligibility->eligibility_type)->first();
            if (isset($extraValue->id)) {
                $extraValue = json_decode($extraValue->extra_values, 1);
                if (isset($extraValue['wp_question'])) {
                    foreach ($extraValue['wp_question'] as $key => $value) {
                        $wp_data[$key]['writing_prompt'] = $value;
                    }
                }
            }
        }
        return json_encode($wp_data);
    }
}
if (!function_exists('getSortCodeValues')) {
    function getSortCodeValues($value, $choice = 'first')
    {

        $application_data1 = \App\Modules\Application\Models\Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();
        $tmp = array();
        $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;

        $choice1_program = getProgramName($value->first_choice_program_id);
        $choice2_program = (($value->second_choice_program_id > 0) ? getProgramName($value->second_choice_program_id) : "");
        if ($choice == 'first') {
            $tmp['program_name'] = $choice1_program;
            $tmp['program_name_with_grade'] = $choice1_program . " - Grade " . $tmp['next_grade'];
        } else {
            $tmp['program_name'] = $choice2_program;
            $tmp['program_name_with_grade'] = $choice2_program . " - Grade " . $tmp['next_grade'];
        }

        $tmp['id'] = $value->id;
        $tmp['student_id'] = $value->student_id;
        $tmp['confirmation_no'] = $value->confirmation_no;
        $tmp['name'] = $value->first_name . " " . $value->last_name;
        $tmp['current_grade'] = $value->current_grade;
        $tmp['first_name'] = $value->first_name;
        $tmp['last_name'] = $value->last_name;
        $tmp['current_school'] = $value->current_school;
        $tmp['zoned_school'] = $value->zoned_school;
        $tmp['created_at'] = getDateFormat($value->created_at);
        $tmp['first_choice'] = $choice1_program;
        $tmp['second_choice'] = $choice2_program;
        $tmp['birth_date'] = getDateFormat($value->birthday);
        $tmp['student_name'] = $value->first_name . " " . $value->last_name;
        $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
        $tmp['parent_email'] = $value->parent_email;
        $tmp['student_id'] = $value->student_id;
        $tmp['submission_date'] = getDateTimeFormat($value->created_at);
        $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
        $tmp['application_url'] = url('/');
        $tmp['signature'] = get_signature('letter_signature');

        $tmp['choice_program_1_with_grade'] = $choice1_program . " - Grade " . $tmp['next_grade'];
        $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? $choice2_program . " - Grade " . $tmp['next_grade'] : "");
        $tmp['school_year'] = $application_data1->school_year;
        $tmp['enrollment_period'] = $tmp['school_year'];
        $t1 = explode("-", $tmp['school_year']);
        $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
        $tmp['next_year'] = date("Y") + 1;

        $tmp['offer_program'] = $choice1_program;
        $tmp['offer_program_with_grade'] = $choice1_program . " - Grade " . $value->next_grade;
        $tmp['accepted_program_name_with_grade'] = $choice1_program . " - Grade " . $value->next_grade;

        // For Offered
        if (($value->submission_status == "Offered" || $value->submission_status == "Offered and Waitlisted") && $value->offer_slug != "") {
            $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
        } else {
            $tmp['offer_link'] = "";
        }

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = App\Modules\DistrictConfiguration\Models\DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = App\Modules\DistrictConfiguration\Models\DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);
        $tmp['online_offer_last_date'] = $last_date_online_acceptance;
        $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;

        // For waitlisted
        if ($value->submission_status == "Waitlisted") {
            $tmp['waitlist_program_1'] = $choice1_program;
            $tmp['waitlist_program_1_with_grade'] = $choice1_program . " - Grade " . $value->next_grade;

            if ($value->second_choice_program_id != 0) {
                $tmp['waitlist_program_2'] = $choice2_program;
                $tmp['waitlist_program_2_with_grade'] = $choice2_program . " - Grade " . $value->next_grade;
            } else {
                $tmp['waitlist_program_2'] = "";
                $tmp['waitlist_program_2_with_grade'] = "";
            }
        } else {
            $tmp['waitlist_program_1'] = "";
            $tmp['waitlist_program_1_with_grade'] = "";
            $tmp['waitlist_program_2'] = "";
            $tmp['waitlist_program_2_with_grade'] = "";
        }

        // Writing Prompt link
        $link = \App\Modules\Submissions\Models\SubmissionData::where('submission_id', $value->id)
            ->where('config_name', 'wp_' . $choice . '_choice_link')
            ->first(['config_value'])->config_value ?? '';
        $tmp['writing_prompt_link'] = url('/WritingPrompt/' . $link);
        // Writing Prompt duration
        $tmp['writing_prompt_duration'] = 0;
        $e_temp = \App\Modules\Eligibility\Models\EligibilityTemplate::where('name', 'Writing Prompt')->first();
        $duration = 0;
        if (isset($e_temp->id)) {
            $wp_email_config = DB::table('set_eligibility_configuration')
                ->where('district_id', session('district_id'))
                ->where('program_id', $value[$choice . '_choice_program_id'])
                ->where('eligibility_type', $e_temp->id)
                ->get();
            if (!empty($wp_email_config)) {
                $duration = $wp_email_config->where('configuration_type', 'prompt_timer')->first()->configuration_value ?? 0;
                $res = ($duration / 60);
                $hours = intval($res);
                $minutes = ($res - $hours) * 60;
                $extra = '';
                if ($hours > 0) {
                    $extra = $hours . ' hour(s)';
                    $extra .= ($minutes > 0) ? ' & ' : '';
                }
                if ($minutes > 0) {
                    $extra .= $minutes . ' minute(s) ';
                }
                $tmp['writing_prompt_duration'] = $extra;
            }
        }

        return $tmp;
    }
}
if (!function_exists('getRecommendationLinks')) {
    function getRecommendationLinks($submission_id)
    {
        $recomm_links = \App\Modules\Submissions\Models\SubmissionData::where('submission_id', $submission_id)->where('config_name', 'LIKE', '%recommendation%')->where('config_name', 'LIKE', '%url%')->get();

        if (isset($recomm_links) && $recomm_links != '') {
            return $recomm_links;
        } else {
            return "";
        }
    }
}
if (!function_exists('getEligibilityConfig')) {
    function getEligibilityConfig($program_id, $eligibility_id, $config_name)
    {
        $data = \App\Modules\SetEligibility\Models\SetEligibilityConfiguration::where("program_id", $program_id)->where("eligibility_id", $eligibility_id)->where("configuration_type", $config_name)->first();
        if (!empty($data)) {
            return $data->configuration_value;
        } else {
            return "";
        }
    }
}
if (!function_exists('getEligibilityConfigDynamic')) {
    function getEligibilityConfigDynamic($program_id, $eligibility_id, $config_name, $application_id)
    {
        $data = \App\Modules\SetEligibility\Models\SetEligibilityConfiguration::where("program_id", $program_id)->where("eligibility_id", $eligibility_id)->where("configuration_type", $config_name)->where("application_id", $application_id)->first();
        if (!empty($data)) {
            return $data->configuration_value;
        } else {
            return "";
        }
    }
}
if (!function_exists('getRecommendationFormData')) {
    function getRecommendationFormData($submission_id)
    {
        $data = \App\Modules\Submissions\Models\SubmissionRecommendation::where('submission_id', $submission_id)->get();

        if (isset($data) && !empty($data->toArray())) {
            return $data;
        } else {
            return "";
        }
    }
}
if (!function_exists('createEmailActivityLog')) {
    function createEmailActivityLog($data = [])
    {
        $fields = ['submission_id', 'program_id', 'email_body', 'email_subject', 'email_to', 'module', 'status'];
        if (Auth::check())
            $create_data['user_id'] = Auth::user()->id;
        else
            $create_data['user_id'] = 0;
        $create_data['district_id'] = session('district_id');

        foreach ($fields as $key => $field) {
            if (isset($data[$field])) {
                $create_data[$field] = $data[$field];
            } else {
                $create_data[$field] = NULL;
            }
        }
        App\Modules\WritingPrompt\Models\EmailActivityLog::create($create_data);
    }
}
if (!function_exists('getTestScoreData')) {
    function getTestScoreData($submission_id, $eligibility, $late_submission = 'N')
    {
        $ts_data = DB::table('submission_test_score')->where('submission_id', $submission_id)->where('program_id', $eligibility->program_id)->get();
        $score = $scorerank = [];
        if (count($ts_data) > 0) {
            foreach ($ts_data as $key => $value) {
                $score[$value->test_score_name] = $value->test_score_value;
                $scorerank[$value->test_score_name] = $value->test_score_rank;
            }
        }

        $data = [];
        $table = 'seteligibility_extravalue';
        $extraValue = DB::table($table)->where('program_id', $eligibility->program_id)->where('eligibility_type', $eligibility->eligibility_type)->first();
        if (isset($extraValue->id)) {
            $extraValue = json_decode($extraValue->extra_values, true);
            if (isset($extraValue['ts_scores'])) {
                foreach ($extraValue['ts_scores'] as $key => $value) {
                    $data[$value] = array("score" => $score, "scorerank" => $scorerank);
                }
            }
        }

        return $data;
    }
}
if (!function_exists('getProgramByEligibility')) {
    function getProgramByEligibility($type)
    {

        $programs = DB::table("program_eligibility")->join("eligibility_template", "program_eligibility.eligibility_type", "eligibility_template.id")->join('program', 'program.id', 'program_eligibility.program_id')->where("eligibility_template.name", $type)->select('program.name', 'program.id')->orderBy('program.id', 'asc')->get();

        return $programs;
    }
}
if (!function_exists('checkCheckedProgram')) {
    function checkCheckedProgram($program_id, $current_grade, $next_grade, $application_id = 0)
    {
        $rs = \App\Modules\Submissions\Models\ProgramChoiceException::where("program_id", $program_id)->where("grade", $current_grade)->first();
        if (!empty($rs)) {
            $str = $rs->display_name;
            if (Session::has("application_id") || $application_id > 0) {
                if ($application_id == 0)
                    $application_id = Session::get("application_id");
                $application_data = \App\Modules\Application\Models\Application::where("application.id", $application_id)->join("enrollments", "enrollments.id", "application.id")->select("school_year")->first();
                if (!empty($application_data)) {
                    $year = $application_data->school_year;
                } else {
                    $year = (date("Y") + 1) . "-" . (date("Y") + 2);
                }
            } else {
                $year = (date("Y") + 1) . "-" . (date("Y") + 2);
            }
            $str = str_replace("##NEXT_YEAR##", $year, $str);
            return $str;
        } else {
            return "";
        }
    }
}
if (!function_exists('getMajorityRace')) {
    function getMajorityRace($submission)
    {
        $rsSchool = \App\Modules\School\Models\School::where("zoning_api_name", $submission->zoned_school)->whereRaw("FIND_IN_SET('" . $submission->next_grade . "', grade_id)")->first();
        if (!empty($rsSchool)) {
            $school_name = $rsSchool->name;
            $rsAdm = \App\Modules\Enrollment\Models\ADMData::where("enrollment_id", Session::get("enrollment_id"))->where("school_id", $rsSchool->id)->first();
            if (!empty($rsAdm)) {
                if ($rsAdm->black >= 50 || $rsAdm->white >= 50 || $rsAdm->other >= 50) {
                    $race = strtolower($submission->calculated_race);
                    if (isset($rsAdm->{$race})) {
                        if ($rsAdm->{$race} > 50)
                            return true;
                    }
                }
            }
        }
        return false;
    }
}
if (!function_exists('getTestScoreDataIndividual')) {
    function getTestScoreDataIndividual($submission_id, $eligibility, $late_submission = 'N')
    {
        $ts_data = DB::table('submission_test_score')->where('submission_id', $submission_id)->where('program_id', $eligibility->program_id)->get();
        $score = $scorerank = [];
        if (count($ts_data) > 0) {
            foreach ($ts_data as $key => $value) {
                $score[$value->test_score_name] = $value->test_score_value;
                $scorerank[$value->test_score_name] = $value->test_score_rank;
            }
        }

        $data = [];
        $table = 'seteligibility_extravalue';
        if ($late_submission == 'Y') {
            $table = 'seteligibility_extravalue_late_submission';
        }
        $extraValue = DB::table($table)->where('program_id', $eligibility->program_id)->where('eligibility_type', $eligibility->eligibility_type)->first();
        if (isset($extraValue->id)) {
            $extraValue = json_decode($extraValue->extra_values, true);
            if (isset($extraValue['ts_scores'])) {
                foreach ($extraValue['ts_scores'] as $key => $ts_name) {
                    $data[$ts_name]['score'] = $score[$ts_name] ?? 0;
                    $data[$ts_name]['scorerank'] = $scorerank[$ts_name] ?? 0;
                    // $data[$ts_name] = array("score"=>$tmp_score, "scorerank"=>$tmp_scorerank);
                    // $data[$ts_name] = array("score"=>$score, "scorerank"=>$scorerank);
                }
            }
        }
        return $data;
    }
}
if (!function_exists('build_sorter_asc')) {
    function build_sorter_asc($key)
    {
        return function ($x, $y) use ($key) {
            // If $x is equal to $y it returns 0
            if ($x[$key] == $y[$key])
                return 0;

            // if x is less than y then it returns -1
            // else it returns 1    
            if ($x[$key] < $y[$key])
                return -1;
            else
                return 1;
        };
    }
}
if (!function_exists('build_sorter_desc')) {
    function build_sorter_desc($key)
    {
        return function ($x, $y) use ($key) {
            // If $x is equal to $y it returns 0
            if ($x[$key] == $y[$key])
                return 0;

            // if x is less than y then it returns -1
            // else it returns 1    
            if ($x[$key] > $y[$key])
                return -1;
            else
                return 1;
        };
    }
}
if (!function_exists('getPreliminaryScoreLateSubmissionCount')) {
    function getPreliminaryScoreLateSubmissionCount($application_id = 0)
    {
        $count = 0;
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program_eligibility.application_id", $application_id)->where("program.enrollment_id", Session::get("enrollment_id"))->where("eligibility_template.name", "Test Score")->where('program_eligibility.status', 'Y')->pluck('program_id')->toArray();


        $submissions = \App\Modules\Submissions\Models\Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where('application_id', $application_id)
            ->where('late_submission', "Y")
            ->whereIn("submission_status", array('Active', 'Pending'))
            ->where(function ($q) use ($avail_program_ary) {
                $q->whereIn('first_choice_program_id', $avail_program_ary)->orWhereIn('second_choice_program_id', $avail_program_ary);
            })
            ->get();


        if (isset($submissions)) {
            foreach ($submissions as $key => $value) {

                $flag = true;

                $grade_average_data = \App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation::where('submission_id', $value->id)->first();

                $preliminary_score_data = \App\Modules\ProcessSelection\Models\PreliminaryScore::where('submission_id', $value->id)->first();

                if (!isset($grade_average_data->given_score) || $grade_average_data->given_score == '') {
                    continue;
                }

                $choice_ary = ['first', 'second'];

                foreach ($choice_ary as $choice) {
                    $use_calc = $tmpAr = array();
                    $program_id = $value->{$choice . '_choice_program_id'};

                    if ($program_id != '' && $program_id != '0') {
                        $eligibility = getEligibilitiesDynamic($value->{$choice . '_choice'}, 'Test Score');


                        $committee_score = getSubmissionCommitteeScore($value->id, $program_id);

                        if (!isset($committee_score) || $committee_score == '') {
                            $flag = false;
                            break;
                        }

                        if (isset($eligibility) && !empty($eligibility)) {
                            $tdata = getEligibilityConfigDynamic($program_id, $eligibility[0]->assigned_eigibility_name, 'use_calculation', $value->application_id);

                            $use_calc = explode(",", $tdata);


                            foreach ($use_calc as $tk => $tv) {
                                $tScore = \App\Modules\Submissions\Models\SubmissionTestScore::where("program_id", $program_id)->where("submission_id", $value->id)->where("test_score_name", $tv)->first();
                                if (empty($tScore)) {

                                    $flag = false;
                                    break;
                                }
                            }
                        } else {
                            $flag = false;
                            break;
                        }
                    }
                }

                if ($flag) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
if (!function_exists('getPreliminaryScoreSubmissionCount')) {
    function getPreliminaryScoreSubmissionCount($application_id = 0)
    {
        $count = 0;
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program_eligibility.application_id", $application_id)->where("program.enrollment_id", Session::get("enrollment_id"))->where("eligibility_template.name", "Test Score")->where('program_eligibility.status', 'Y')->pluck('program_id')->toArray();


        $submissions = \App\Modules\Submissions\Models\Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where('application_id', $application_id)
            ->whereIn("submission_status", array('Active', 'Pending'))
            ->where(function ($q) use ($avail_program_ary) {
                $q->whereIn('first_choice_program_id', $avail_program_ary);
                $q->orWhereIn('second_choice_program_id', $avail_program_ary);
            })
            ->get();

        if (isset($submissions)) {
            foreach ($submissions as $key => $value) {

                $flag = true;

                $grade_average_data = \App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation::where('submission_id', $value->id)->first();
                $preliminary_score_data = \App\Modules\ProcessSelection\Models\PreliminaryScore::where('submission_id', $value->id)->first();

                if (!isset($grade_average_data->given_score) || $grade_average_data->given_score == '') {
                    continue;
                }

                $choice_ary = ['first', 'second'];

                foreach ($choice_ary as $choice) {
                    $use_calc = $tmpAr = array();
                    $program_id = $value->{$choice . '_choice_program_id'};

                    if ($program_id != '' && $program_id != '0') {
                        $eligibility = getEligibilitiesDynamic($value->{$choice . '_choice'}, 'Test Score');

                        $committee_score = getSubmissionCommitteeScore($value->id, $program_id);
                        if (!isset($committee_score) || $committee_score == '') {
                            $flag = false;
                            break;
                        }

                        if (isset($eligibility) && !empty($eligibility)) {
                            $tdata = getEligibilityConfigDynamic($program_id, $eligibility[0]->assigned_eigibility_name, 'use_calculation', $value->application_id);
                            $use_calc = explode(",", $tdata);
                            foreach ($use_calc as $tk => $tv) {
                                $tScore = \App\Modules\Submissions\Models\SubmissionTestScore::where("program_id", $program_id)->where("submission_id", $value->id)->where("test_score_name", $tv)->first();
                                if (empty($tScore)) {

                                    $flag = false;
                                    break;
                                }
                            }
                        } else {
                            $flag = false;
                            break;
                        }
                    }
                }

                if ($flag) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
if (!function_exists('generateCompositeScore')) {
    function generateCompositeScore($submission_id)
    {
        $submission = \App\Modules\Submissions\Models\Submissions::where("id", $submission_id)->first();
        $committee_score_1 = getSubmissionCommitteeScore($submission->id, $submission->first_choice_program_id);
        $committee_score_2 = getSubmissionCommitteeScore($submission->id, $submission->second_choice_program_id);
        $preliminary_score = 0;
        if (is_numeric($committee_score_1))
            $preliminary_score += $committee_score_1;
        if (is_numeric($committee_score_2))
            $preliminary_score += $committee_score_2;
        //echo "Committee Scores : ".($preliminary_score)."<br>";

        $test_scores_titles = [];
        $data = getSetEligibilityDataDynamic($submission->first_choice, 12);
        if (isset($data->ts_scores)) {
            foreach ($data->ts_scores as $ts => $tv) {
                if (!in_array($tv, $test_scores_titles)) {
                    $test_scores_titles[] = $tv;
                }
            }
        }
        $data = getSetEligibilityDataDynamic($submission->first_choice, 12);
        if (isset($data->ts_scores)) {
            foreach ($data->ts_scores as $ts => $tv) {
                if (!in_array($tv, $test_scores_titles)) {
                    $test_scores_titles[] = $tv;
                }
            }
        }
        $rcontroller  = new App\Modules\Reports\Controllers\ReportsController;

        $data = $rcontroller->getProgramTestScores($submission->first_choice_program_id, $submission->id, $test_scores_titles);
        $test_score = 0;
        foreach ($data as $key => $value) {
            $preliminary_score += $value['score'];
            $test_score += $value['score'];
        }
        //echo "Test Scores : ".$test_score."<br>";

        $grade_average_data = \App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation::where('submission_id', $submission->id)->first();
        if (!empty($grade_average_data)) { //
            if (is_numeric($grade_average_data->given_score)) {
                $preliminary_score += $grade_average_data->given_score;
                //echo "Grade Average Scores : ".$grade_average_data->given_score."<br>";
            }
        }

        $data = \App\Modules\Submissions\Models\SubmissionInterviewScore::where('submission_id', $submission_id)->first();
        if (!empty($data)) {
            $composite_score = $preliminary_score + $data->data;
            //echo "Interview  Scores : ".$data->data."<br>";
            $data = array();
            $data['submission_id'] = $submission_id;
            $data['score'] = $composite_score;
            $rs = \App\Modules\Submissions\Models\SubmissionCompositeScore::updateOrCreate(["submission_id" => $submission_id], $data);
        }
    }
}
if (!function_exists('checkApplicationStatus')) {
    function checkApplicationStatus($id)
    {
        $submission = \App\Modules\Submissions\Models\Submissions::where('application_id', $id)->first();
        if (!empty($submission)) {
            return false;
        }
        return true;
    }
}
if (!function_exists('getChoiceAry')) {
    function getChoiceAry($count = NULL)
    {
        if (!isset($count)) {
            $count = config('variables.choice_count');
        }
        $choice_ary = config('variables.choice_ary');
        $fltr_choice_ary = array_slice($choice_ary, 0, $count);
        return $fltr_choice_ary;
    }
}
if (!function_exists('getSubmissionAcademicGradeData')) {
    function getSubmissionAcademicGradeData($submission)
    {
        $current_grade = $submission->current_grade;
        $gradeArr = [];
        $gradeArr[] = $current_grade;
        if ($current_grade != "PreK" && $current_grade != "K") {
            if ($current_grade == 1) {
                $gradeArr[] = "PreK";
                $gradeArr[] = "K";
            } elseif ($current_grade == 2) {
                $gradeArr[] = 1;
                $gradeArr[] = "K";
            } else {
                $gradeArr[] = $current_grade - 1;
                $gradeArr[] = $current_grade - 2;
            }
        }
        $data = DB::table("submission_grade")->where("submission_id", $submission->id)->whereIn('grade_level', $gradeArr)->orderBy("academicYear", "Desc")->get();
        return $data;
    }
}
if (!function_exists('getCalculatedRace')) {
    function getCalculatedRace($race)
    {
        $rs = \App\Modules\Submissions\Models\RaceDivision::where("actual_race", $race)->first();
        if (!empty($rs)) {
            if ($rs->calculated_race == "Black")
                return "black";
            else
                return "non_black";
            //        return $rs->calculated_race;
        } else
            return "";
    }
}
if (!function_exists('check_last_process')) {
    function check_last_process($submission_id)
    {
        $rsMain = \App\Modules\Submissions\Models\SubmissionsFinalStatus::where("submission_id", $submission_id)->first();
        $rsWait = \App\Modules\Submissions\Models\SubmissionsWaitlistFinalStatus::where("submission_id", $submission_id)->orderBy("id", "DESC")->first();
        $rsLate = \App\Modules\Submissions\Models\LateSubmissionFinalStatus::where("submission_id", $submission_id)->orderBy("id", "DESC")->first();

        if (!empty($rsLate)) {
            if (!empty($rsWait)) {
                if ($rsWait->updated_at > $rsLate->updated_at) {
                    return array("id" => $rsWait->id, "finalObj" => "waitlist", "version" => $rsWait->version);
                } else {
                    return array("id" => $rsLate->id, "finalObj" => "late_submission", "version" => $rsLate->version);
                }
            } else {
                if (!empty($rsMain)) {
                    if ($rsMain->updated_at > $rsLate->updated_at) {
                        return array("id" => $rsMain->id, "finalObj" => "regular", "version" => 0);
                    } else {
                        return array("id" => $rsLate->id, "finalObj" =>  "late_submission", "version" => $rsLate->version);
                    }
                } else {
                    return array("id" => $rsLate->id, "finalObj" =>  "late_submission", "version" => $rsLate->version);
                }
            }
        } else {
            if (!empty($rsWait) && !empty($rsMain)) {
                if ($rsWait->updated_at > $rsMain->updated_at) {
                    return array("id" => $rsWait->id, "finalObj" => "waitlist", "version" => $rsWait->version);
                } else {
                    return array("id" => $rsMain->id, "finalObj" => "regular", "version" => 0);
                }
            } elseif (!empty($rsWait)) {
                return array("id" => $rsWait->id, "finalObj" => "waitlist", "version" => $rsWait->version);
            } elseif (!empty($rsMain)) {
                return array("id" => $rsMain->id, "finalObj" => "regular", "version" => 0);
            }
        }
        return array("id" => 0, "finalObj" => "", "version" => 0);
    }
}

if (!function_exists('sendMessage')) {
    function sendMessage($submission_data, $emailArr, $messageKey = 'app_confirmed', $type = 'sms')
    {
        try {
            $twilioSid = config('variables.twilio.sid');
            $twilioToken = config('variables.twilio.token');

            if ($type === 'whatsapp') {
                $twilioNumber = config('variables.twilio.whatsapp_number');
            } else {
                $twilioNumber = config('variables.twilio.phone_number');
            }

            if (!$twilioSid || !$twilioToken || !$twilioNumber) {
                Log::warning("Twilio credentials not configured. Skipping {$type} message.");
                return false;
            }

            if (empty($submission_data->phone_number)) {
                Log::warning("No phone number found for submission ID: " . $submission_data->id);
                return false;
            }

            $phoneNumber = formatPhoneNumberForMessaging($submission_data->phone_number);
            $message = createDynamicMessageContent($emailArr, $messageKey);
            $recipient = ($type === 'whatsapp') ? "whatsapp:" . $phoneNumber : $phoneNumber;

            $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json";
            $response = Http::withBasicAuth($twilioSid, $twilioToken)
                ->withOptions(['verify' => base_path('resources/certs/cacert.pem')])
                ->asForm()
                ->post($url, [
                    'To' => $recipient,
                    'From' => $twilioNumber,
                    'Body' => ($type === 'whatsapp') ? strip_tags($message) : $message
                ]);

            if ($response->successful()) {
                return true;
            } else {
                throw new \Exception('Twilio API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Failed to send {$messageKey} {$type} message", [
                'submission_id' => $submission_data->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type' => $type,
                'message_key' => $messageKey
            ]);
            return false;
        }
    }
}

if (!function_exists('formatPhoneNumberForMessaging')) {
    function formatPhoneNumberForMessaging($phoneNumber)
    {
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strlen($cleanNumber) == 10) {
            $cleanNumber = '1' . $cleanNumber;
        }
        return '+' . $cleanNumber;
    }
}

if (!function_exists('createDynamicMessageContent')) {
    function createDynamicMessageContent($emailArr, $messageKey = 'app_confirmed')
    {
        $selected_language = Session::get('default_language', 'english');
        switch ($messageKey) {
            case 'app_confirmed':
                return createAppConfirmedMessage($emailArr, $selected_language);

            case 'custom_communication':
                return createCustomCommunicationMessage($emailArr, $selected_language);

            default:
                return createAppConfirmedMessage($emailArr, $selected_language);
        }
    }
}

if (!function_exists('createAppConfirmedMessage')) {
    function createAppConfirmedMessage($emailArr, $language = 'english')
    {
        if (!empty($emailArr['app_sms_text'])) {
            $message = $emailArr['app_sms_text'];
            $message = str_replace('{confirmation_no}', $emailArr['confirm_number'], $message);
            $message = str_replace('{parent_name}', $emailArr['parent_name'], $message);
            $message = str_replace('{student_name}', $emailArr['student_name'], $message);
            $message = str_replace('{submission_date}', $emailArr['submission_date'], $message);
            $message = str_replace('{next_grade}', $emailArr['next_grade'], $message);
            $message = str_replace('{transcript_due_date}', $emailArr['transcript_due_date'], $message);
            return $message;
        }

        if ($language == 'spanish') {
            $message = "Confirmacion de Solicitud\n\n";
            $message .= "Estimado/a {$emailArr['parent_name']},\n\n";
            $message .= "Su solicitud para {$emailArr['student_name']} ha sido enviada exitosamente!\n\n";
            $message .= "Numero de Confirmacion: {$emailArr['confirm_number']}\n";
            $message .= "Fecha de Envio: {$emailArr['submission_date']}\n";
            $message .= "Grado Solicitado: {$emailArr['next_grade']}\n\n";

            if (!empty($emailArr['transcript_due_date'])) {
                $message .= "Importante: Fecha limite para transcripciones: {$emailArr['transcript_due_date']}\n\n";
            }
            $message .= "Recibira un correo electronico de confirmacion detallado en breve.\n\n";
            $message .= "Gracias por su solicitud!";
        } else {
            $message = "Application Confirmation\n\n";
            $message .= "Dear {$emailArr['parent_name']},\n\n";
            $message .= "Your application for {$emailArr['student_name']} has been successfully submitted!\n\n";
            $message .= "Confirmation Number: {$emailArr['confirm_number']}\n";
            $message .= "Submission Date: {$emailArr['submission_date']}\n";
            $message .= "Grade Applied For: {$emailArr['next_grade']}\n\n";

            if (!empty($emailArr['transcript_due_date'])) {
                $message .= "Important: Transcript due date: {$emailArr['transcript_due_date']}\n\n";
            }
            $message .= "You will receive a detailed confirmation email shortly.\n\n";
            $message .= "Thank you for your application!";
        }
        return $message;
    }
}

if (!function_exists('createCustomCommunicationMessage')) {
    function createCustomCommunicationMessage($emailArr, $language = 'english')
    {
        if (!empty($emailArr['custom_sms_text'])) {
            $message = $emailArr['custom_sms_text'];
            $message = str_replace('{confirmation_no}', $emailArr['confirm_number'], $message);
            $message = str_replace('{parent_name}', $emailArr['parent_name'], $message);
            $message = str_replace('{student_name}', $emailArr['student_name'], $message);
            return $message;
        }
        $message = "Confirmation Number: {$emailArr['confirm_number']}\n";
        $message .= "Please check your email. JCS has sent you communication email regarding your submission";

        return $message;
    }
}
