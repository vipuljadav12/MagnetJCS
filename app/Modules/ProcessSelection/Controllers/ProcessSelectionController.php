<?php

namespace App\Modules\ProcessSelection\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\ProcessSelection\Models\{Availability, ProgramSwingData, PreliminaryScore, ProcessSelection};
use App\Modules\setEligibility\Models\setEligibility;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\{Program, ProgramEligibility, ProgramGradeMapping};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Enrollment\Models\{Enrollment, EnrollmentRaceComposition};
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs, LateSubmissionAvailabilityLog, LateSubmissionAvailabilityProcessLog, LateSubmissionIndividualAvailability};
use App\Modules\Waitlist\Models\{WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog, WaitlistIndividualAvailability};
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionsFinalStatus, SubmissionsStatusLog, SubmissionsStatusUniqueLog, SubmissionCommitteeScore, SubmissionCompositeScore, SubmissionsSelectionReportMaster, SubmissionsRaceCompositionReport, LateSubmissionFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionInterviewScore, SubmissionsLatestFinalStatus};
use App\Modules\Enrollment\Models\ADMData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProcessSelectionController extends Controller
{
    /* This function will generate Racial composition according to program group 
    set in Applicaiton Filter level at "Selection" tab of program */

    public $group_racial_composition = array();
    public $program_group = array();
    public $enrollment_race_data = array();
    public $waitlistRaceArr = array();

    public function currentStateReport()
    {
        $enrollment_id = Session::get('enrollment_id');

        $rs = Availability::where("enrollment_id", Session::get("enrollment_id"))->get();
        $programAvailability = [];
        foreach ($rs as $key => $value) {
            $tmp = [];
            $rising_composition = json_decode($value->rising_composition);
            $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->count();
            $tmp['availability'] =  $value->available_seats;
            $tmp['offered_accepted'] = $rs1;


            foreach ($rising_composition as $rkey => $rvalue) {
                $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->get();

                foreach ($rs1 as $rs1k => $rs1v) {
                    if (getSchoolMasterName($rs1v->zoned_school) == $rkey) {
                        $race = getCalculatedRace($rs1v->race);
                        if ($race)
                            $rvalue->{$race . "_added"} = (isset($rvalue->{$race . "_added"}) ? $rvalue->{$race . "_added"} : 0)  + 1;
                        //                        $rvalue++;
                    }
                }


                $tmp[$rkey] = $rvalue;
            }

            $lateData = LateSubmissionProcessLogs::join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->where("process_selection.type", "late_submission")->where("program_id", $value->program_id)->select('zoned_schools', 'late_submission_process_logs.id')->get();
            foreach ($lateData as $lkey => $lvalue) {
                if ($lvalue->zoned_schools != '') {
                    foreach (json_decode($lvalue->zoned_schools) as $jlkey => $jlvalue) {
                        if (isset($tmp[$jlkey])) {
                            $tmp1 = $tmp[$jlkey];
                            if (isset($tmp1->{'black_withdrawn'})) {
                                $tmp1->{'black_withdrawn'} = $tmp1->{'black_withdrawn'} + $jlvalue->black;
                            } else {
                                $tmp1->{'black_withdrawn'} = $jlvalue->black;
                            }

                            if (isset($tmp1->{'non_black_withdrawn'})) {
                                $tmp1->{'non_black_withdrawn'} = $tmp1->{'non_black_withdrawn'} + $jlvalue->non_black;
                            } else {
                                $tmp1->{'non_black_withdrawn'} = $jlvalue->non_black;
                            }

                            $tmp[$jlkey] = $tmp1;
                        }
                    }
                }
            }

            $waitData = WaitlistProcessLogs::join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->where("process_selection.type", "waitlist")->where("program_id", $value->program_id)->get();
            foreach ($waitData as $lkey => $lvalue) {
                if ($lvalue->zoned_schools != '') {
                    foreach (json_decode($lvalue->zoned_schools) as $jlkey => $jlvalue) {

                        if (isset($tmp[$jlkey])) {
                            $tmp1 = $tmp[$jlkey];
                            if (isset($tmp1->{'black_withdrawn'})) {
                                $tmp1->{'black_withdrawn'} = $tmp1->{'black_withdrawn'} + $jlvalue->black;
                            } else {
                                $tmp1->{'black_withdrawn'} = $jlvalue->black;
                            }

                            if (isset($tmp1->{'non_black_withdrawn'})) {
                                $tmp1->{'non_black_withdrawn'} = $tmp1->{'non_black_withdrawn'} + $jlvalue->non_black;
                            } else {
                                $tmp1->{'non_black_withdrawn'} = $jlvalue->non_black;
                            }

                            $tmp[$jlkey] = $tmp1;
                        }
                    }
                }
            }
            $programAvailability[$value->program_id] = $tmp;
        }
        //dd($programAvailability);
        $schoolAdmData = [];
        $rs = ADMData::where("enrollment_id", Session::get("enrollment_id"))->get();
        foreach ($rs as $key => $value) {
            $tmp = [];
            $tmp['black'] = ["value" => $value->black];
            $tmp['non_black'] = ["value" => $value->non_black];
            $tmp['majority_race'] = $value->majority_race;

            $schoolAdmData[getSchoolName($value->school_id)] = $tmp;
        }
        //dd($schoolAdmData);
        return view("ProcessSelection::state_report", compact("schoolAdmData", "programAvailability"));
    }

    public function validateAllNecessity($application_id)
    {
        $error_msg = "";
        $rs = Submissions::where("submission_status", "Offered")->where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->count();
        if ($rs > 0)
            $error_msg .= "<li>Current " . $rs . " submissions are in Offered Stage.</li>";
        else
            $error_msg = "OK";
        return $error_msg;
    }

    public function groupByRacism($af_programs)
    {
        $af = [
            'application_filter_1' => 'applicant_filter1',
            'application_filter_2' => 'applicant_filter2',
            'application_filter_3' => 'applicant_filter3'
        ];
        $seat_type = [
            'black_seats' => 'Black',
            'white_seats' => 'White',
            'other_seats' => 'Other'
        ];
        $group_race_array = $program_group = [];
        foreach ($af_programs as $key => $value) {
            $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where('status', '!=', 'T')->where(function ($q) use ($value) {
                $q->where('applicant_filter1', $value);
                $q->orWhere('applicant_filter2', $value);
                $q->orWhere('applicant_filter3', $value);
                $q->orWhere('name', $value);
            })->get();
            $filtered_programs = [];
            $avg_data = [];
            if (count($programs) <= 0) {
                $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where('status', '!=', 'T')->where("name", $value)->get();
            }
            if (!empty($programs)) {
                $programs_avg = [];
                foreach ($programs as $pkey => $program) {
                    if ($program->selection_by == "Program Name")
                        $selection_by = "name";
                    else
                        $selection_by = strtolower(str_replace(' ', '_', $program->selection_by));
                    if (
                        isset($af[$selection_by]) &&
                        ($program->{$af[$selection_by]} != '') &&
                        $program->{$af[$selection_by]} == $value
                    ) {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                        $program_group[$program->id] = $value;
                    } elseif ($selection_by == "name" || $selection_by == "program_name" || $selection_by == "") {
                        if ($program->applicant_filter1 == $value) {
                            $filtered_programs[] = $program;
                            array_push($programs_avg, $program->id);
                            $program_group[$program->id] = $value;
                        }
                    }
                }

                if (!empty($programs_avg)) {
                    $total = 0;
                    $availabilities =  Availability::whereIn("program_id", $programs_avg)->where('district_id', Session('district_id'))->get(array_keys($seat_type));

                    foreach ($seat_type as $stype => $svalue) {
                        $sum = $availabilities->sum($stype);
                        $total += $sum;
                        if ($sum == 0)
                            $avg_data['no_previous'] = "Y";
                        else
                            $avg_data['no_previous'] = "N";
                        $avg_data[strtolower($svalue)] = $sum;
                    }
                    $avg_data['total'] = $total;
                }
                $group_race_array[$value] = $avg_data;
            }
        }
        return array("group_race" => $group_race_array, "program_group" => $program_group);
    }

    public function application_index($type = '')
    {
        /* 

        $rs = SubmissionRaw::get();
        foreach($rs as $key=>$value)
        {
            $data = json_decode($value->formdata);

            foreach($data as $k=>$v)
            {
                if($k == "13")
                    echo $v."^";
                elseif($k=="second_sibling")
                    echo "Secod--".$v."^";
                elseif($k=="first_sibling")
                    echo "first--".$v."^";

            }
            echo "<br>";
        }
        exit;
        */
        $applications = Form::where("status", "y")->get();
        return view("ProcessSelection::application_index", compact("applications"));
    }

    public function validateApplication($application_id)
    {
        //echo "OK";exit;
        $error_msg = $this->validateAllNecessity($application_id);
        if ($error_msg == "")
            $error_msg = "OK";
        echo $error_msg;
    }

    public function fetch_programs_group($application_id)
    {
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->where("program.parent_submission_form", $application_id)->get();

        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if ($application_data->preliminary_processing == "Y")
            $preliminary_score = true;

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                $cnt = 0;
                foreach ($af as $key => $af_field) {
                    if ($program->$af_field == '')
                        $cnt++;
                    if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                        array_push($af_programs, $program->$af_field);
                    }
                }
                if ($cnt == count($af)) {
                    array_push($af_programs, $program->name);
                }
            }
        }
        return $af_programs;
    }

    public function selectProcessProgram($application_id)
    {
        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->get();
        return view("ProcessSelection::program_selection", compact("programs", "application_id"));
    }

    public function processSelectionReport(Request $request)
    {
        $req = $request->all();
        $application_id = $req['application_id'];
        $prc_program_id = [];
        if (isset($req['program_id'])) {
            foreach ($req['program_id'] as $pid) {
                $prc_program_id[] = $pid;
            }
        }
        return $this->processTest($application_id, $prc_program_id, "");
    }

    public function updateDisplayData($round, $tmpSubmission, $display_data1, $from)
    {
        $display_data = [];
        $new_data = [];
        $added = false;


        // echo "<pre>";
        //  print_r($display_data1);
        //  echo "<BR>AFTER----------------<br>";

        foreach ($display_data1 as $key => $value) {


            if (isset($value['round'])) {
                if ($value['status'] == "Offered") {
                    $rd = $value['round'];
                } else {
                    $tmp = explode(",", $value['round']);
                    $rd = "";
                    foreach ($tmp as $t) {
                        if ($t != $round)
                            $rd .= $t . ",";
                    }
                    $rd .= $round;
                }
            } else {
                $rd = $round;
            }
            $value['round'] = $tmpSubmission['round'] = $rd;
            if ($value['id'] == $tmpSubmission['id'] && $value['choice'] == $tmpSubmission['choice']) {
                // if($value['id'] == 3574 && $value['choice'] == "first")
                // {
                //     echo "<pre>";
                //     print_r($value);
                //     print_r($tmpSubmission);
                //     exit;
                // }

                if (isset($tmpSubmission['status']) && $tmpSubmission['status'] == "")
                    $tmpSubmission['status'] = $value['status'];
                if (!isset($tmpSubmission['msg']))
                    $tmpSubmission['msg'] = $value['msg'] ?? "";
                else {
                    $str = $value['msg'] . "<br>" . $tmpSubmission['msg'];
                    $tmpSubmission['msg'] = $value['msg'] = $str;
                }
                if (!isset($tmpSubmission['status'])) {
                    $new_data[] = $value;
                } else {
                    if ($tmpSubmission['status'] == "Already Offered") {
                        // if($tmpSubmission['id'] == 3457)
                        // {
                        //     echo "Round : ".$round."  ".$tmpSubmission['id']."<br>Old : ".$value['choice']." - Status : ".$value['status']."  <b>".$from."</b><br>";
                        //     echo "Round : ".$round."  ".$tmpSubmission['id']."<br>New : ".$tmpSubmission['choice']." - tatus : ".$tmpSubmission['status']."  <b>".$from."</b><br>-------------------------------------------<br><br>";
                        // }
                        //$value['status'] = $tmpSubmission['choice'];
                        $new_data[] = $value;
                    } else {
                        $msg = $value['msg'] . "<br>" . ($tmpSubmission['msg'] ?? "");
                        $new_data[] = $tmpSubmission;
                    }
                    //$new_data[] = $tmpSubmission;
                }

                $added = true;
            } else {
                $new_data[] = $value;
            }

            // if($value['id'] == 3457)
            // {
            //     echo "<pre>";
            //     print_r($value);
            // }

        }

        if (!$added) {
            $new_data[] = $tmpSubmission;
        }


        $display_data = $new_data;

        //print_r($display_data);exit;
        return $display_data;
    }

    public function processTest($application_id, $prc_program_id, $type = "")
    {

        /* Test Code Ends */
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "regular")->first();


        if (!empty($process_selection) && $process_selection->commited == "Yes") {
            $final_data = $incomplete_arr = $failed_arr = [];
            return view("ProcessSelection::test_index", compact("final_data", "incomplete_arr", "failed_arr"));
        }
        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');

        if ($type == "9999") {
            $error_msg = $this->validateAllNecessity($application_id);
            if ($error_msg != '') {
                return view("ProcessSelection::error_msg", compact("error_msg", "type"));
            }
        }

        /* save enrollment wise race composition with swing % Data */
        $rsSwing = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->first();
        //dd($application_id, $rsSwing);
        $swingPercent = $rsSwing->swing_percentage;
        $swingArray = explode(",", $swingPercent); //$enrollment_racial->swing;







        /* Program wise Availability and Rising capacity */
        $rs = Availability::where("enrollment_id", Session::get("enrollment_id"))->get();
        $programAvailability = [];
        foreach ($rs as $key => $value) {
            $tmp = [];
            $rising_composition = json_decode($value->rising_composition);
            $tmp['availability'] =  $value->available_seats;
            foreach ($rising_composition as $rkey => $rvalue) {
                $tmp[$rkey] = $rvalue;
            }
            $programAvailability[$value->program_id] = $tmp;
        }

        $offered_arr = $final_skipped_arr = $done_ids = $inavailable_arr = $skipped_unique = $final_data = $display_data = [];
        $choiceArr = ["first", "second", "third"];
        foreach ($choiceArr as $choice) {
            $done_choice[] = $choice;

            /* Fetch ADM Data from Enrollment ID */

            // $crace = "non_black";
            // $tmpblack = 2;
            // $tmpnon_black=4;
            // ${"tmp".$crace}++;
            // echo $tmpnon_black;exit;
            // // $test = $programAvailability[34]["Corner High School"];
            // // dd($test->black);


            /* from here */






            /* Get CDI Data */
            $gradeArr = [9, 10];


            foreach ($gradeArr as $grade) { // -1
                $skipped_arr = [];
                //    $done_choice = [];    
                for ($round = 0; $round <= 2; $round++) { // 0
                    $roundChanged = true;
                    $roundHTML = '<span class="badge badge-danger">(' . ($round + 1) . ')</span>';
                    //echo $roundHTML."<BR>";
                    $schoolAdmData = [];
                    $rs = ADMData::where("enrollment_id", Session::get("enrollment_id"))->get();
                    // echo "<pre>Round : ".$round."<BR>";
                    //print_r($swingArray);
                    foreach ($rs as $key => $value) {
                        $tmp = [];
                        $tmp['black'] = ["value" => $value->black, "min" => number_format($value->black - $swingArray[$round], 2), "max" => number_format($value->black + $swingArray[$round], 2)];
                        $tmp['non_black'] = ["value" => $value->non_black, "min" => number_format($value->non_black - $swingArray[$round], 2), "max" => number_format($value->non_black + $swingArray[$round], 2)];
                        $tmp['majority_race'] = $value->majority_race;

                        $schoolAdmData[$value->school_id] = $tmp;
                    }

                    $submissions = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))
                        ->whereIn('submission_status', array("Active"))
                        ->where("form_id", $application_id)
                        ->whereIn($choice . "_choice_program_id", $prc_program_id)
                        //->whereIn("id", [3737/*,3289*/])
                        ->where("next_grade", $grade)
                        ->orderBy("lottery_number", "DESC")
                        ->get();

                    //dd(Session::get("enrollment_id"),$submissions);
                    foreach ($submissions as $submission) { //1
                        //echo $submission->id." - ".$choice." - ".$round."<br>";
                        $pid_field = $choice . "_choice_program_id";
                        $offered = false;
                        $current_school = "";
                        $offer_program_id = 0;

                        $tmpSubmission = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($submission);
                        $tmpSubmission['first_choice_program'] = getProgramName($submission->first_choice_program_id);
                        $tmpSubmission['second_choice_program'] = getProgramName($submission->second_choice_program_id);
                        $tmpSubmission['third_choice_program'] = getProgramName($submission->third_choice_program_id);
                        // if($choice == "first" && $submission->id == 3772)
                        //                                 {
                        //                                     echo $submission->$pid_field."<BR>";
                        //                                     echo $programAvailability[$submission->$pid_field]['availability'];
                        //                                     echo "<pre>";
                        //                                     print_r($inavailable_arr);
                        //                                     print_r($prc_program_id);

                        //                                     //exit;
                        //                                 }

                        if (!in_array($submission->id, $offered_arr) && !in_array($submission->id . "." . $choice, $inavailable_arr) && in_array($submission->$pid_field, $prc_program_id)) // && !in_array($choice."_".$submission->id, $skipped_unique)) // 
                        { //2


                            // Check availability of the program
                            if ($submission->$pid_field > 0 && isset($programAvailability[$submission->$pid_field]['availability']) && $programAvailability[$submission->$pid_field]['availability'] > 0) { // 15

                                // Here we need to write logic for processing

                                $zoned_school = getSchoolMasterName($submission->zoned_school);
                                $calculated_race = getCalculatedRace($submission->race);

                                $school_id = getSchoolId($zoned_school);



                                if ($calculated_race != "") { // 14
                                    if (isset($schoolAdmData[$school_id])) { // 13


                                        $tmpblack = $programAvailability[$submission->$pid_field][$zoned_school]->black ?? 0;
                                        $tmpnon_black = $programAvailability[$submission->$pid_field][$zoned_school]->non_black ?? 0;

                                        if ($tmpblack + $tmpnon_black > 0) {
                                            $oldPBlack = number_format($tmpblack * 100 / ($tmpblack + $tmpnon_black), 2);
                                            $oldPNBlack = number_format($tmpnon_black * 100 / ($tmpblack + $tmpnon_black), 2);
                                        } else {
                                            $oldPNBlack = 0;
                                            $oldPNBlack = 0;
                                        }

                                        ${"tmp" . $calculated_race}++;
                                        $tmpTotal = $tmpblack + $tmpnon_black;
                                        $newP = number_format(${"tmp" . $calculated_race} * 100 / $tmpTotal, 2);



                                        $newPBlack = number_format($tmpblack * 100 / ($tmpblack + $tmpnon_black), 2);
                                        $newPNBlack = number_format($tmpnon_black * 100 / ($tmpblack + $tmpnon_black), 2);

                                        $tmpMinMaxStr = "(<strong>Min: " . $schoolAdmData[$school_id][$calculated_race]["min"] . "% Max: " . $schoolAdmData[$school_id][$calculated_race]["max"] . "%</strong>) " . $programAvailability[$submission->$pid_field]['availability'];

                                        if ($newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && $newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) { //11

                                            $tmpObj1 = $programAvailability[$submission->$pid_field][$zoned_school];
                                            $tmpObj1->black = $tmpblack;
                                            $tmpObj1->non_black = $tmpnon_black;
                                            $programAvailability[$submission->$pid_field][$zoned_school] = $tmpObj1;

                                            $programAvailability[$submission->$pid_field]["availability"] = $programAvailability[$submission->$pid_field]["availability"] - 1;
                                            $done_ids[] = $tmpSubmission['id'] . "-" . $choice;

                                            $tmpSubmission['status'] = "Offered";
                                            $tmpSubmission['choice'] = $choice;
                                            $tmpSubmission['race'] = ucfirst($calculated_race);

                                            if ($roundChanged) {
                                                $tmpMsg = $roundHTML . "<br><div>";
                                                $roundChanged = false;
                                            } else
                                                $tmpMsg = "<div>";
                                            $tmpMsg .= "<span>" . $zoned_school . "</span><br>--------<br>";
                                            $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span class='text-success'>" . $newPBlack . "%</span>" : $newPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span class='text-success'>" . $newPNBlack . "%</span>" : $newPNBlack . "%") . "<br>" . $tmpMinMaxStr;


                                            $tmpMsg .= "<div>";

                                            $tmpSubmission['msg'] = $tmpMsg;
                                            $offered_arr[] = $submission->id;



                                            $tmpSkip = [];
                                            foreach ($final_skipped_arr as $svalue) {
                                                if ($svalue['id'] != $submission->id)
                                                    $tmpSkip[] = $svalue;
                                            }
                                            $final_skipped_arr = $tmpSkip;

                                            $tmpSkip = [];
                                            foreach ($skipped_arr as $svalue) {
                                                if ($svalue['id'] != $submission->id)
                                                    $tmpSkip[] = $svalue;
                                            }
                                            $skipped_arr = $tmpSkip;

                                            $final_data[] = $tmpSubmission;
                                            $tmpSubmission['round'] = $round + 1;

                                            $display_data = $this->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Main Offered");
                                            //$display_data[] = $tmpSubmission;

                                            $offered = true;
                                            $current_school = $zoned_school;
                                            $offer_program_id = $submission->$pid_field;
                                        } //11
                                        else { //12
                                            // if($choice == "first" && $submission->id == 3772)
                                            // {
                                            //     echo "first";
                                            //     echo $calculated_race;

                                            //     exit;
                                            // }
                                            if ($roundChanged) {
                                                $tmpMsg = $roundHTML . "<br><div>";
                                                $roundChanged = false;
                                            } else
                                                $tmpMsg = "<div>";
                                            $tmpMsg .= "<span>" . $zoned_school . "</span><br>--------<br>";
                                            $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span>" . $oldPBlack . "%</span>" : $oldPBlack . "%") . "<br>Non-Black:" . ($calculated_race == "non_black" ? "<span>" . $oldPNBlack . "%</span>" : $oldPNBlack . "%") . "<br><br>--------<br>";
                                            $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span class='text-danger'>" . $newPBlack . "%</span>" : $newPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span class='text-danger'>" . $newPNBlack . "%</span>" : $newPNBlack . "%") . "<br>" . $tmpMinMaxStr;


                                            $tmpMsg .= "</div>";

                                            $tmpData = $tmpSubmission;
                                            $tmpData['choice'] = $choice;
                                            $tmpData['status'] = "Next Round";
                                            $tmpData['race'] = getCalculatedRace($submission->race);
                                            $tmpData['first_choice_program_id'] = $submission->first_choice_program_id;
                                            $tmpData['second_choice_program_id'] = $submission->second_choice_program_id;
                                            $tmpData['third_choice_program_id'] = $submission->third_choice_program_id;
                                            $tmpData['msg'] = $tmpMsg;
                                            $tmpData['round'] = $round + 1;
                                            //echo "ddd";
                                            $display_data = $this->updateDisplayData($round + 1, $tmpData, $display_data, "NOT IN RANGE");
                                            if (!in_array($choice . "_" . $submission->id, $skipped_unique)) {
                                                $skipped_unique[] = $choice . "_" . $submission->id;
                                                $skipped_arr[] = $tmpData;
                                            }
                                        }    //12
                                    } // 13
                                } // 14


                            } // 15
                            else if ($submission->$pid_field > 0) { // 10

                                $tmpSubmission['choice'] = $choice;
                                $tmpSubmission['race'] = getCalculatedRace($submission->race);
                                //$tmpSubmission['next_grade'] = $submission->next_grade;
                                //$tmpSubmission['program_id'] = $submission->$pid_field;
                                $tmpSubmission['status'] = "No Availability";
                                $tmpSubmission['msg'] = "(" . $round . ")";
                                // FROM HERE PENDING 2nd JAN 
                                $inavailable_arr[] = $submission->id . "." . $choice;
                                $final_data[] = $tmpSubmission;
                                $tmpSubmission['round'] = $round + 1;
                                $display_data = $this->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Not AVailable");
                                //$display_data[] = $tmpSubmission;
                            }   // 10                          
                        }  // 2
                        else if (in_array($submission->id, $offered_arr) && !in_array($submission->id . "-" . $choice, $done_ids) && in_array($submission->$pid_field, $prc_program_id)) //  && in_array($submission->$pid_field, $prc_program_id)
                        {

                            // if($tmpSubmission['id'] == 3266)
                            // {
                            //     echo "<pre>";
                            //     print_r($offered_arr);
                            //     print_r($final_data);
                            //     exit;
                            // }
                            $tmpData = $tmpSubmission;
                            $tmpData['choice'] = $choice;
                            $tmpData['status'] = "Already Offered";
                            $tmpData['race'] = getCalculatedRace($submission->race);
                            $tmpData['first_choice_program_id'] = $submission->first_choice_program_id;
                            $tmpData['second_choice_program_id'] = $submission->second_choice_program_id;
                            $tmpData['third_choice_program_id'] = $submission->third_choice_program_id;
                            $tmpData['msg'] = "";
                            $tmpData['round'] = $round + 1;
                            // echo "<pre>";
                            // print_r($display_data);
                            // print_r($tmpSubmission);
                            // echo $choice;
                            // exit;
                            $done_ids[] = $tmpSubmission['id'] . "-" . $choice;
                            $tmpData['srno'] = count($display_data);
                            $display_data = $this->updateDisplayData($round + 1, $tmpData, $display_data, "Already Offered");
                            //$display_data[] = $tmpData;
                        } else if (in_array($submission->$pid_field, $prc_program_id)) {

                            $tmpData = $tmpSubmission;
                            $tmpData['choice'] = $choice;
                            $tmpData['race'] = getCalculatedRace($submission->race);
                            $tmpData['first_choice_program_id'] = $submission->first_choice_program_id;
                            $tmpData['second_choice_program_id'] = $submission->second_choice_program_id;
                            $tmpData['round'] = $round + 1;

                            $display_data = $this->updateDisplayData($round + 1, $tmpData, $display_data, "Last Condition");
                        }

                        if ($offered) { // 9

                            foreach ($skipped_arr as $skvalue) { // 8

                                $tmpSubmission1 = $skvalue;
                                // if($submission->id == 3555 && $offered)
                                // {
                                //     echo "<pre>";
                                //     echo $choice."<br>";
                                //     print_r($skipped_arr);
                                //     exit;
                                // }
                                // if($submission->id == 3555)
                                // {
                                //     echo $skvalue['id'] ." - ".$offer_program_id."<BR>".$current_school." -- ".$skvalue['zoned_school']." - ".getSchoolMasterName($skvalue['zoned_school'])."<BR>";
                                // }
                                if ($offer_program_id > 0 && getSchoolMasterName($skvalue['zoned_school']) == $current_school && !in_array($skvalue['id'], $offered_arr)) { // 7
                                    // $tmpSubmission1 = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($skvalue);
                                    $tmpSubmission1['first_choice_program'] = getProgramName($skvalue['first_choice_program_id']);
                                    $tmpSubmission1['second_choice_program'] = getProgramName($skvalue['second_choice_program_id']);
                                    $tmpSubmission1['third_choice_program'] = getProgramName($skvalue['third_choice_program_id']);
                                    $schoice = "";

                                    foreach ($done_choice as $dchoice) {
                                        if ($skvalue[$dchoice . '_choice_program_id'] == $offer_program_id)
                                            $schoice = $dchoice;
                                    }
                                    // else if($skvalue['second_choice_program_id'] > 0 && $skvalue['second_choice_program_id'] == $offer_program_id)
                                    //     $schoice = "second";
                                    // else if($skvalue['third_choice_program_id'] > 0 && $skvalue['third_choice_program_id'] == $offer_program_id)
                                    //     $schoice = "third";


                                    if ($schoice != "") { // 6
                                        $pid_field = $schoice . "_choice_program_id";



                                        if ($submission->$pid_field > 0 && isset($programAvailability[$skvalue[$pid_field]]['availability']) && $programAvailability[$skvalue[$pid_field]]['availability'] > 0) { //5



                                            // Here we need to write logic for processing

                                            $zoned_school = getSchoolMasterName($skvalue['zoned_school']);
                                            $calculated_race = $skvalue['race'];
                                            $school_id = getSchoolId($zoned_school);

                                            // if($submission->id == 3555 && $skvalue['id'] == 3772)
                                            //                                                     {

                                            //                                                         echo "--<pre>".$calculated_race;
                                            //                                                         print_r($programAvailability[$skvalue[$pid_field]]['availability']);
                                            //                                                         exit;
                                            //                                                     }
                                            if ($calculated_race != "") { //4
                                                if (isset($schoolAdmData[$school_id])) { //3


                                                    $tmpblack = $programAvailability[$skvalue[$pid_field]][$zoned_school]->black ?? 0;
                                                    $tmpnon_black = $programAvailability[$skvalue[$pid_field]][$zoned_school]->non_black ?? 0;
                                                    if ($tmpblack + $tmpnon_black > 0) {
                                                        $oldPBlack = number_format($tmpblack * 100 / ($tmpblack + $tmpnon_black), 2);
                                                        $oldPNBlack = number_format($tmpnon_black * 100 / ($tmpblack + $tmpnon_black), 2);
                                                    } else {
                                                        $oldPBlack = 0;
                                                        $oldPNBlack = 0;
                                                    }

                                                    ${"tmp" . $calculated_race}++;
                                                    $tmpTotal = $tmpblack + $tmpnon_black;
                                                    $newP = number_format(${"tmp" . $calculated_race} * 100 / $tmpTotal, 2);

                                                    $newPBlack = number_format($tmpblack * 100 / ($tmpblack + $tmpnon_black), 2);
                                                    $newPNBlack = number_format($tmpnon_black * 100 / ($tmpblack + $tmpnon_black), 2);
                                                    if ($submission->id == 3555 && $skvalue['id'] == 3772) {
                                                        echo $newP . " >= " . $schoolAdmData[$school_id][$calculated_race]["min"] . " && " . $newP . " <= " . $schoolAdmData[$school_id][$calculated_race]["max"] . "<br>";
                                                    }

                                                    if ($newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && $newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) { //1

                                                        $tmpObj1 = $programAvailability[$submission->$pid_field][$zoned_school];
                                                        $tmpObj1->black = $tmpblack;
                                                        $tmpObj1->non_black = $tmpnon_black;
                                                        $programAvailability[$submission->$pid_field][$zoned_school] = $tmpObj1;


                                                        $programAvailability[$skvalue[$pid_field]]["availability"] = $programAvailability[$skvalue[$pid_field]]["availability"] - 1;


                                                        $tmpSubmission1['status'] =  "Offered";
                                                        $done_ids[] = $tmpSubmission1['id'] . "-" . $schoice;

                                                        $tmpSubmission1['race'] = ucfirst($calculated_race);
                                                        $tmpSubmission1['choice'] = $schoice;
                                                        $offered_arr[] = $skvalue['id'];

                                                        $tmpSkip = [];
                                                        foreach ($final_skipped_arr as $svalue) {
                                                            if ($svalue['id'] != $skvalue['id'])
                                                                $tmpSkip[] = $svalue;
                                                        }
                                                        $final_skipped_arr = $tmpSkip;

                                                        $tmpSkip = [];
                                                        foreach ($skipped_arr as $svalue) {
                                                            /// echo $svalue['id'] . " - ". $skvalue['id']."<BR>";
                                                            if ($svalue['id'] != $skvalue['id'])
                                                                $tmpSkip[] = $svalue;
                                                        }
                                                        $skipped_arr = $tmpSkip;
                                                        if ($roundChanged) {
                                                            $tmpMsg = $roundHTML . "<br><div>";
                                                            $roundChanged = false;
                                                        } else
                                                            $tmpMsg = "<div>";
                                                        $tmpMsg .= "<br>--------<br>";
                                                        $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span>" . $oldPBlack . "%</span>" : $oldPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span>" . $oldPNBlack . "%</span>" : $oldPNBlack . "%") . "<br><br>--------<br>";
                                                        $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span class='text-danger'>" . $newPBlack . "%</span>" : $newPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span class='text-danger'>" . $newPNBlack . "%</span>" : $newPNBlack . "%");


                                                        $tmpMsg .= "<div>";
                                                        $tmpSubmission1['round'] = $round + 1;

                                                        $tmpSubmission1['msg'] = $skvalue['msg'] . "<BR>---------<br>" . $tmpMsg;
                                                        $final_data[] = $tmpSubmission1;
                                                        $tmpSubmission1['srno'] = count($display_data);
                                                        $display_data = $this->updateDisplayData($round + 1, $tmpSubmission1, $display_data, "SKIPPED LOOP");
                                                        //$display_data[] = $tmpSubmission1;
                                                    }  //1
                                                    else { //2
                                                        $tmpSkip = [];
                                                        foreach ($skipped_arr as $svalue) {
                                                            /// echo $svalue['id'] . " - ". $skvalue['id']."<BR>";
                                                            $tmpNew = $svalue;
                                                            if ($svalue['id'] == $skvalue['id'] && $svalue['choice'] == $choice) {

                                                                if ($roundChanged) {
                                                                    $tmpMsg = $roundHTML . "<br><div>";
                                                                    $roundChanged = false;
                                                                } else
                                                                    $tmpMsg = "<div>";
                                                                $tmpMsg .= "<br>--------<br>";
                                                                $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span>" . $oldPBlack . "%</span>" : $oldPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span>" . $oldPNBlack . "%</span>" : $oldPNBlack . "%") . "%<br><br>--------<br>";
                                                                $tmpMsg .= "Black: " . ($calculated_race == "black" ? "<span class='text-danger'>" . $newPBlack . "%</span>" : $newPBlack . "%") . "<br>Non-Black: " . ($calculated_race == "non_black" ? "<span class='text-danger'>" . $newPNBlack . "%</span>" : $newPNBlack . "%");


                                                                $tmpMsg .= "</div>";
                                                                $tmpNew['msg'] .= $tmpMsg;
                                                                $display_data = $this->updateDisplayData($round + 1, $tmpNew, $display_data, "SKIPPED LOOP");
                                                            }
                                                            $tmpSkip[] = $tmpNew;
                                                        }
                                                        $skipped_arr = $tmpSkip;
                                                    } //2


                                                } //3
                                            } //4


                                        } //5
                                    } // 6
                                } // 7
                            } // 8
                        } // 9

                        /* Offered condition ends */
                    } //1



                }

                $tmpskip = [];
                foreach ($skipped_arr as $svalue) {
                    if (!in_array($svalue['id'], $offered_arr))
                        $tmpskip[] = $svalue;
                }
                // echo "<pre>";
                //print_r($final_skipped_arr);
                $final_skipped_arr = array_merge($final_skipped_arr, $skipped_arr);
            }

            /* Main logic ends */
        }

        //exit;
        // echo "<pre>";
        // print_r($display_data);
        // exit;
        //exit;
        //dd($final_skipped_arr);
        //echo count($final_skipped_arr)."<BR>".count($final_data);exit;
        //dd(Session::get('enrollment_id'));
        //exit;

        $submissions = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))
            ->whereIn('submission_status', array("Active"))
            ->where("form_id", $application_id)
            ->where(function ($q) use ($prc_program_id) {
                $q->whereIn("first_choice_program_id", $prc_program_id)
                    ->orWhereIn("second_choice_program_id", $prc_program_id)
                    ->orWhereIn("third_choice_program_id", $prc_program_id);
            })
            //->whereIn("id", [3284/*,3289*/])
            //->where("next_grade", $grade)
            ->orderBy("lottery_number", "DESC")
            ->get();

        $displayRecords = [];
        if ($type == "update") {
            $rs = SubmissionsFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->delete();
            $rs = SubmissionsLatestFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->delete();
        }
        $count = 0;



        // echo "<pre>";
        // print_r($display_data);
        // exit;


        foreach ($submissions as $submission) {
            $data = [];

            $data['submission_id'] = $submission->id;
            $data['application_id'] = $application_id;
            $data['enrollment_id'] = Session::get("enrollment_id");
            $msg = "";
            $final_status = "";


            foreach ($choiceArr as $choice) {
                $tmp = array_filter($display_data, function ($data) use ($submission, $choice) {
                    return $data['id'] === $submission->id && $data['choice'] === $choice;
                });



                if (!empty($tmp)) {
                    $tmp = $tmp[array_keys($tmp)[0]];
                    //dd($tmp);
                    // if($submission->id == 3749 && !empty($tmp))
                    // {
                    //     echo "<pre>".$choice."<BR>";
                    //     print_r($tmp);

                    // }

                    $final_status = $tmp['status'];
                    if ($tmp['status'] == "Offered") {
                        do {
                            $code = mt_rand(100000, 999999);
                            $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                            $user_code1 = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                            $user_code2 = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code) && !empty($user_code1) && !empty($user_code2));
                        $data['offer_slug'] = $code;

                        $data[$choice . '_waitlist_for'] = $submission->{$choice . '_choice_program_id'};
                        $data[$choice . '_choice_final_status'] = "Offered";
                        $msg = $tmp['msg'] ?? "";
                    } elseif (in_array($tmp['status'], ["No Availability", "Next Round", "Already Offered"]) && in_array($submission->{$choice . '_choice_program_id'}, $prc_program_id)) {
                        $data[$choice . '_waitlist_for'] = $submission->{$choice . '_choice_program_id'};
                        $data[$choice . '_choice_final_status'] = "Waitlisted";
                        $msg = $tmp['msg'] ?? "";
                    }
                }


                // else
                // {
                //     $tmp = array_filter($final_skipped_arr, function($data) use ($submission, $choice) {
                //         return $data['id'] === $submission->id && $data['choice'] === $choice;
                //     });
                //     // if($submission->id == 3749 && !empty($tmp))
                //     // {
                //     //     echo "<pre>".$choice."<BR>";
                //     //     print_r($tmp);

                //     // }
                // //     if($submission->id == 3540)
                // // {
                // //     dd($tmp);
                // // }
                // //     if($submission->id == 3280 && !empty($tmp))
                // // {
                // //     echo "<pre>".$choice."<BR>";
                // //     print_r($tmp);
                // //     exit;
                // // }
                //     if(!empty($tmp))
                //     {
                //         $tmp = $tmp[array_keys($tmp)[0]];
                //         //dd($tmp);
                //         $final_status = $tmp['status'];
                //         $pid_field = $choice."_choice_program_id";
                //         if($submission->$pid_field > 0)
                //         {
                //             $data[$choice.'_waitlist_for'] = $submission->$pid_field; 
                //             $data[$choice.'_choice_final_status'] = "Waitlisted";
                //         }
                //         $msg = $tmp['msg'] ?? "";

                //     }
                // }
            }
            //exit;
            // if($submission->id == 3280)
            //                     {
            //                         dd($data);
            //                     }
            if ($type == "update") {
                //  echo $count."-".$submission->id."<BR>";
                $count++;

                // if($submission->id == 3500)
                // {
                //     dd("SS",$data);
                // }

                if (isset($data['second_choice_final_status']) && $data['second_choice_final_status'] == "Offered") {
                    $data['third_choice_final_status'] = 'Pending';
                    if (in_array($submission->first_choice_program_id, $prc_program_id)) {
                        $data['first_choice_final_status'] = 'Waitlisted';
                    } else {
                        $data['first_choice_final_status'] = 'Pending';
                    }
                }
                if (isset($data['first_choice_final_status']) && $data['first_choice_final_status'] == "Offered") {
                    $data['third_choice_final_status'] = 'Pending';
                    $data['second_choice_final_status'] = 'Pending';
                }

                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $submission->id], $data);

                // if($submission->id == 3500)
                // {
                //     echo "<pre>";
                //     print_r($data);
                //     exit;
                // }
                //SubmissionsLatestFinalStatus::create($data);
            }

            $dataNew = $data;
            $dataNew['id'] = $submission->id;
            $dataNew['race'] = ucfirst(getCalculatedRace($submission->race));
            $dataNew['student_id'] = $submission->student_id;
            $dataNew['first_name'] = $submission->first_name;
            $dataNew['last_name'] = $submission->last_name;
            $dataNew['next_grade'] = $submission->next_grade;
            $dataNew['first_program'] = getProgramName($submission->first_choice_program_id);
            $dataNew['second_program'] = getProgramName($submission->second_choice_program_id);
            $dataNew['third_program'] = getProgramName($submission->third_choice_program_id);
            $dataNew['first_sibling'] = $submission->first_sibling;
            $dataNew['second_sibling'] = $submission->second_sibling;
            $dataNew['third_sibling'] = $submission->third_sibling;
            $dataNew['lottery_number'] = $submission->lottery_number;
            $dataNew['final_status'] = $final_status;
            $dataNew['current_school'] = $submission->current_school;
            $dataNew['zoned_school'] = $submission->zoned_school;
            $dataNew['msg'] = $msg;
            $displayRecords[] = $dataNew;
        }
        //exit;
        if ($type == "update") {
            // $rs = SubmissionsFinalStatus::where("enrollment_id", Session::get('enrollment_id'))->where("second_choice_final_status", "Offered")->update(["first_choice_final_status"=>"Waitlisted", "third_choice_final_status"=>"Pending"]);//->where("first_choice_final_status", "Pending")
            // $rs = SubmissionsFinalStatus::where("enrollment_id", Session::get('enrollment_id'))->where("first_choice_final_status", "Offered")->update(["second_choice_final_status"=>"Pending", "third_choice_final_status"=>"Pending"]);
            return true;
        } else {
            //dd($displayRecords);
            //return view("ProcessSelection::test_index",compact("displayRecords"));
            return view("ProcessSelection::test_index", compact("display_data", "final_skipped_arr"));
        }





        //echo "<pre>Failed<BR><BR>";
        //print_r($failed_arr);
        //echo "<pre>Incomplete<BR><BR>";
        //print_r($incomplete_arr);
        //exit;
        /*
echo "<pre>First Data<br><br>";
print_r($firstdata);

echo "<pre>Second Data<br><br>";
print_r($seconddata);
exit;*/
        /* Update failed and income statuses to database */




        //exit;

    }


    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $applications = Form::where("status", "y")->get();

        $displayother = SubmissionsFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->count();
        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_online_acceptance")->first();

        return view("ProcessSelection::index", compact("applications"));
    }

    public function index_step2($application_id = 0)
    {

        //$this->updated_racial_composition($application_id);exit;
        //echo "T";exit;
        $programs = Program::where("enrollment_id", Session::get('enrollment_id'))->where("parent_submission_form", $application_id)->get();
        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $additional_data = $this->get_additional_info($application_id);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $enrollment_racial = $additional_data['enrollment_racial'];
        $swingData = $additional_data['swingData'];
        $prgGroupArr = $additional_data['prgGroupArr'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];
        $processed_programs = [];

        return view("ProcessSelection::index_step2", compact("application_id", "last_date_online_acceptance", "last_date_offline_acceptance", "applications", "displayother", "display_outcome", "enrollment_racial", "swingData", "prgGroupArr", "programs", "processed_programs"));
    }

    public function get_additional_info($application_id = 0)
    {
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->first();

        $display_outcome = 0;
        $displayother = 0;

        $processed_programs = "";

        if (!empty($process_selection)) {
            $displayother = 1;
            $last_date_online_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_online_acceptance));
            $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_offline_acceptance));
            $processed_programs = $process_selection->processed_programs;
            if ($process_selection->commited == "Yes") {
                $display_outcome = 1;
            }
            // else
            // {
            //     $last_date_online_acceptance = "";
            //     $last_date_offline_acceptance = "";

            // }

        } else {
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        /* Swing Data Calculation */
        //$application_data = Application::where("form_id", $application_id)->first();
        $prgGroupArr = $swingData = [];


        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();
        foreach ($programs as $key => $value) {
            if ($value->applicant_filter1 != '')
                $prgGroupArr[] = $value->applicant_filter1;
            if ($value->applicant_filter2 != '')
                $prgGroupArr[] = $value->applicant_filter2;
            if ($value->applicant_filter3 != '')
                $prgGroupArr[] = $value->applicant_filter3;
            if ($value->applicant_filter1 == '' && $value->applicant_filter2 == '' && $value->applicant_filter3 == '') {
                $prgGroupArr[] = $value->name;
            }
        }
        $prgGroupArr = array_unique($prgGroupArr);
        foreach ($prgGroupArr as $key => $value) {
            $rs = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("program_name", $value)->where("district_id", Session::get("district_id"))->first();
            if (!empty($rs)) {
                $swingData[$value] = $rs->swing_percentage;
            }
        }


        $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();
        return array("display_outcome" => $display_outcome, "displayother" => $displayother, "enrollment_racial" => $enrollment_racial, "prgGroupArr" => $prgGroupArr, "swingData" => $swingData, "last_date_online_acceptance" => $last_date_online_acceptance, "last_date_offline_acceptance" => $last_date_offline_acceptance, "processed_programs" => $processed_programs);
    }

    public function settings_index()
    {
        $applications = Form::where("status", "y")->get();

        return view("ProcessSelection::settings_index", compact("applications"));
    }

    public function settings_step_two($application_id = 0)
    {
        // Fetch All Forms - Applications
        $display_outcome = SubmissionsStatusUniqueLog::where("enrollment_id", Session::get("enrollment_id"))->count();

        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        //$application_data = Application::where("id", $application_id)->first();

        $prgGroupArr = $swingData = [];

        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();
        foreach ($programs as $key => $value) {
            if ($value->applicant_filter1 != '')
                $prgGroupArr[] = $value->applicant_filter1;
            if ($value->applicant_filter2 != '')
                $prgGroupArr[] = $value->applicant_filter2;
            if ($value->applicant_filter3 != '')
                $prgGroupArr[] = $value->applicant_filter3;
            if ($value->applicant_filter1 == '' && $value->applicant_filter2 == '' && $value->applicant_filter3 == '') {
                $prgGroupArr[] = $value->name;
            }
        }
        $prgGroupArr = array_unique($prgGroupArr);
        foreach ($prgGroupArr as $key => $value) {
            $rs = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("program_name", $value)->where("district_id", Session::get("district_id"))->first();
            if (!empty($rs)) {
                $swingData[$value] = $rs->swing_percentage;
            }
        }


        $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();



        return view("ProcessSelection::settings_step_two", compact("prgGroupArr", "enrollment_racial", "applications", "application_id", "swingData"));
    }


    public function storeSettings(Request $request)
    {
        $req = $request->all();

        $swing_data = $req['swing_data'];
        foreach ($swing_data as $key => $value) {
            if ($req['swing_value'][$key] != '') {
                $data = array();
                $data['application_id'] = $req['application_id'];
                $data['enrollment_id'] = Session::get('enrollment_id');
                $data['district_id'] = Session::get('district_id');
                $data['program_name'] = $value;
                $data['swing_percentage'] = $req['swing_value'][$key];
                $data['user_id'] = Auth::user()->id;
                $rs = ProgramSwingData::updateOrCreate(["application_id" => $data['application_id'], "enrollment_id" => $data['enrollment_id'], "program_name" => $data['program_name']], $data);
            } else {
                $rs = ProgramSwingData::where("application_id", $req['application_id'])->where("enrollment_id", Session::get('enrollment_id'))->where("program_name", $value)->delete();
            }
        }
        Session::flash("success", "Submission Updated successfully.");

        return redirect("/admin/Process/Selection/settings/" . $req['application_id']);
    }

    public function store(Request $request)
    {
        $req = $request->all();


        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $req['application_id'])->first();
        $process = true;
        if (!empty($process_selection) && $process_selection->commited == 'Yes') {
            $process = false;
        }

        $prc_program_id = [];
        if (isset($req['program_id'])) {
            foreach ($req['program_id'] as $pid) {
                $prc_program_id[] = $pid;
            }
        }

        if ($process) {
            /* Store Program Swing Data only when Processed Selection is not accepted */
            // $swing_data = $req['swing_data'];
            // foreach($swing_data as $key=>$value)
            // {
            //     if($req['swing_value'][$key] != '')
            //     {
            //         $data = array();
            //         $data['application_id'] = $req['application_id'];
            //         $data['enrollment_id'] = Session::get('enrollment_id');
            //         $data['district_id'] = Session::get('district_id');
            //         $data['program_name'] = $value;
            //         $data['swing_percentage'] = $req['swing_value'][$key];
            //         $data['user_id'] = Auth::user()->id;
            //         $rs = ProgramSwingData::updateOrCreate(["application_id"=>$data['application_id'], "enrollment_id" => $data['enrollment_id'], "program_name" => $data['program_name']], $data);
            //     }
            //     else
            //     {
            //         $rs = ProgramSwingData::where("application_id", $req['application_id'])->where("enrollment_id", Session::get('enrollment_id'))->where("program_name", $value)->delete();   
            //     }
            // }
            $test = $this->processTest($req['application_id'], $prc_program_id, "update");
        }


        $data = array();
        if ($req['last_date_online_acceptance'] != '') {
            $data['last_date_online_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_online_acceptance']));
            $data['last_date_offline_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_offline_acceptance']));
            $data['district_id'] = Session::get("district_id");
            $data['enrollment_id'] = Session::get("enrollment_id");
            $data['form_id'] = $req['application_id'];
            $data['district_id'] = Session::get("district_id");
            $data['processed_programs'] = implode(",", $prc_program_id);

            $rs = ProcessSelection::updateOrCreate(['application_id' => $data['form_id']], $data);
        }
        echo "done";
    }

    public function generate_race_composition_update($group_data, $total_seats, $race, $type = "S")
    {
        $update = "";
        $tst = $group_data;
        $total_seats = $tst['total'];
        foreach ($tst as $tstk => $tstv) {
            if ($tstk != "total" && $tstk != "no_previous") {
                if ($tstv > 0)
                    $tst_percent = number_format($tstv * 100 / $total_seats, 2);
                else
                    $tst_percent = 0;
                if ($tstk == $race) {
                    if ($type == "W")
                        $clname = "text-danger";
                    elseif ($type == "S")
                        $clname = "text-success";
                    else
                        $clname = "";
                } else
                    $clname = "";
                $update .= "<div><span><strong>" . ucfirst($tstk) . "</strong> :</span> <span class='" . $clname . "'>" . $tst_percent . "% (" . $tstv . ")</span></div>";
            }
        }
        return $update;
    }

    // from here pending

    public function population_change_application($application_id = 1)
    {
        // Processing
        $pid = $application_id;
        $from = "form";

        $additional_data = $this->get_additional_info($application_id);
        $processed_programs = $additional_data['processed_programs'];
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $enrollment_racial = $additional_data['enrollment_racial'];
        $swingData = $additional_data['swingData'];
        $prgGroupArr = $additional_data['prgGroupArr'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];



        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();

        // Population Changes
        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        $submissions = Submissions::where('district_id', $district_id)->where(function ($q) {
            $q->where("first_choice_final_status", "Offered")
                ->orWhere("second_choice_final_status", "Offered")
                ->orWhere("third_choice_final_status", "Offered");
        })
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where('district_id', $district_id)->where("submissions.form_id", $application_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status', 'first_waitlist_for', 'second_waitlist_for', 'third_waitlist_for']);


        $choices = ['first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if (!isset($programs[$value->$choice])) {
                        if ($value->$choice != 0)
                            $programs[$value->$choice] = [];
                    }
                    if ($value->$choice != 0 && !in_array($value->next_grade, $programs[$value->$choice])) {
                        array_push($programs[$value->$choice], $value->next_grade);
                    }
                }
            }
        }
        ksort($programs);
        $data_ary = [];
        $race_ary = [];

        // echo "<pre>";
        // print_r($programs);
        // exit;

        foreach ($programs as $program_id => $grades) {

            $availability = Availability::where("enrollment_id", Session::get("enrollment_id"))->where('program_id', $program_id)
                ->first(['total_seats', 'available_seats']);

            $race_count = [];
            if (!empty($availability)) {
                foreach ($choices as $choice) {
                    if ($choice == "first_choice_program_id") {
                        $submission_race_data = $submissions->where($choice, $program_id)->where('first_choice_final_status', "Offered");
                    } elseif ($choice == "second_choice_program_id") {
                        $submission_race_data = $submissions->where($choice, $program_id)->where('second_choice_final_status', "Offered");
                    } else {
                        $submission_race_data = $submissions->where($choice, $program_id)->where('third_choice_final_status', "Offered");
                    }

                    $race = $submission_race_data->groupBy('calculated_race')->map->count();
                    //echo "<pre>";
                    //print_r($race);
                    if (count($race) > 0) {
                        $race_ary = array_merge($race_ary, $race->toArray());

                        if (count($race_count) > 0) {
                            foreach ($race as $key => $value) {

                                if (isset($race_count[$key])) {
                                    $race_count[$key] = $race_count[$key] + $value;
                                } else {
                                    $race_count[$key] = $value;
                                }
                            }
                        } else {


                            $race_count = $race;
                        }
                    }
                }

                $data = [
                    'program_id' => $program_id,
                    'grade' => "",
                    'total_seats' => $availability->total_seats ?? 0,
                    'available_seats' => $availability->available_seats ?? 0,
                    'race_count' => $race_count,
                ];
                $data_ary[] = $data;
                // sorting race in ascending
                ksort($race_ary);
            }
            // exit;
        }

        //exit;
        // Submissions Result
        $processed_programs = explode(",", $processed_programs);
        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->get();
        return view("ProcessSelection::population_change", compact('data_ary', 'race_ary', 'pid', 'from', "display_outcome", "application_id", "enrollment_racial", "last_date_online_acceptance", "last_date_offline_acceptance", "prgGroupArr", "swingData", "processed_programs", "programs"));
    }


    public function submissions_results_application($application_id = 1)
    {
        $pid = $application_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where('submissions.enrollment_id', SESSION::get('enrollment_id'))
            ->where("submissions.form_id", $application_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'zoned_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id',  'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status']);

        // echo "<pre>";
        // print_r($submissions);
        // exit;
        $final_data = array();
        foreach ($submissions as $key => $value) {


            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['school'] = $value->zoned_school;
            $tmp['choice'] = 1;
            $tmp['race'] = $value->calculated_race;
            $tmp['program'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
            $tmp['program_name'] = getProgramName($value->first_choice_program_id);
            $tmp['offered_status'] = $value->first_choice_final_status;
            if ($value->first_choice_final_status == "Offered")
                $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
            elseif ($value->first_choice_final_status == "Denied due to Ineligibility")
                $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
            elseif ($value->first_choice_final_status == "Waitlisted")
                $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
            elseif ($value->first_choice_final_status == "Denied due to Incomplete Records")
                $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
            else
                $tmp['outcome'] = "";

            $final_data[] = $tmp;

            if ($value->second_choice_program_id != 0) {
                if ($value->id == "3302") {
                    //dd($value);
                }
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->zoned_school;
                $tmp['race'] = $value->calculated_race;
                $tmp['choice'] = 2;
                $tmp['program'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                $tmp['offered_status'] = $value->second_choice_final_status;

                if ($value->second_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif ($value->second_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif ($value->second_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif ($value->second_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";
                $final_data[] = $tmp;
            }

            if ($value->third_choice_program_id != 0) {
                if ($value->id == "3302") {
                    //dd($value);
                }
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->zoned_school;
                $tmp['race'] = $value->calculated_race;
                $tmp['choice'] = 3;
                $tmp['program'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['program_name'] = getProgramName($value->third_choice_program_id);
                $tmp['offered_status'] = $value->third_choice_final_status;

                if ($value->third_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif ($value->third_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif ($value->third_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif ($value->third_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";


                $final_data[] = $tmp;
            }
        }
        $grade = $outcome = array();
        foreach ($final_data as $key => $value) {
            $grade['grade'][] = $value['grade'];
            $outcome['outcome'][] = $value['outcome'];
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);

        $additional_data = $this->get_additional_info($application_id);
        $processed_programs = $additional_data['processed_programs'];
        $processed_programs = explode(",", $processed_programs);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $enrollment_racial = $additional_data['enrollment_racial'];
        $swingData = $additional_data['swingData'];
        $prgGroupArr = $additional_data['prgGroupArr'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];

        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->get();

        return view("ProcessSelection::submissions_result", compact('final_data', 'pid', 'from', 'display_outcome', "application_id", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance", "prgGroupArr", "swingData", "enrollment_racial", "programs", "processed_programs"));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selection_accept(Request $request)
    {

        $application_id = $request->application_id;
        $data = SubmissionsFinalStatus::where("submissions_final_status.application_id", $application_id)->where("submissions.enrollment_id", Session::get("enrollment_id"))->join("submissions", "submissions.id", "submissions_final_status.submission_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->whereIn("submissions.submission_status", array('Active'))->get();


        foreach ($data as $key => $value) {

            $insert = [];
            $insert['submission_id'] = $value->submission_id;
            $insert['enrollment_id'] = $value->enrollment_id;
            $insert['application_id'] = $value->application_id;
            $insert['first_choice_final_status'] = $value->first_choice_final_status;
            $insert['second_choice_final_status'] = $value->second_choice_final_status;
            $insert['first_waitlist_number'] = $value->first_waitlist_number;
            $insert['second_waitlist_number'] = $value->second_waitlist_number;
            $insert['incomplete_reason'] = $value->incomplete_reason;
            $insert['first_choice_eligibility_reason'] = $value->first_choice_eligibility_reason;
            $insert['second_choice_eligibility_reason'] = $value->second_choice_eligibility_reason;
            $insert['first_offered_rank'] = $value->first_offered_rank;
            $insert['second_offered_rank'] = $value->second_offered_rank;
            $insert['first_waitlist_for'] = $value->first_waitlist_for;
            $insert['second_waitlist_for'] = $value->second_waitlist_for;
            $insert['third_waitlist_for'] = $value->third_waitlist_for;
            $insert['offer_slug'] = $value->offer_slug;
            $insert['first_offer_update_at'] = $value->first_offer_update_at;
            $insert['second_offer_update_at'] = $value->second_offer_update_at;
            $insert['third_offer_update_at'] = $value->third_offer_update_at;
            $insert['contract_status'] = $value->contract_status;
            $insert['contract_signed_on'] = $value->contract_signed_on;
            $insert['contract_name'] = $value->contract_name;
            $insert['offer_status_by'] = $value->offer_status_by;
            $insert['contract_status_by'] = $value->contract_status_by;
            $insert['contract_mode'] = $value->contract_mode;
            $insert['first_offer_status'] = $value->first_offer_status;
            $insert['second_offer_status'] = $value->second_offer_status;
            $insert['third_offer_status'] = $value->third_offer_status;
            $insert['manually_updated'] = $value->manually_updated;
            $insert['communication_sent'] = $value->communication_sent;
            $insert['communication_text'] = $value->communication_text;
            $insert['version'] = $value['version'];
            $rs = SubmissionsLatestFinalStatus::create($insert);

            $status = $value->first_choice_final_status;
            if ($value->second_choice_final_status == "Offered")
                $status = "Offered";
            elseif ($value->third_choice_final_status == "Offered")
                $status = "Offered";
            elseif ($status != "Offered" && ($value->second_choice_final_status == "Waitlisted" || $value->third_choice_final_status == "Waitlisted"))
                $status = "Waitlisted";
            $submission_id = $value->submission_id;
            $rs = Submissions::where("id", $submission_id)->select("submission_status")->first();
            $old_status = $rs->submission_status;

            $comment = "By Accept and Commit Event";
            if ($status == "Offered") {
                $submission = Submissions::where("id", $value->submission_id)->first();
                if ($value->first_choice_final_status == "Offered") {
                    $program_name = getProgramName($submission->first_choice_program_id);
                } else if ($value->second_choice_final_status == "Offered") {
                    $program_name = getProgramName($submission->second_choice_program_id);
                } else if ($value->third_choice_final_status == "Offered") {
                    $program_name = getProgramName($submission->third_choice_program_id);
                } else {
                    $program_name = "";
                }
                $rsS = Submissions::where("id", $submission_id)->update(["awarded_school" => $program_name]);
                $program_name .= " - Grade " . $submission->next_grade;
                $comment = "System has Offered " . $program_name . " to Parent";
            } else if ($status == "Denied due to Ineligibility") {
                if ($value->first_choice_eligibility_reason != '') {
                    if ($value->first_choice_eligibility_reason == "Both") {
                        $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    } else if ($value->first_choice_eligibility_reason == "Grade") {
                        $comment = "System has denied the application because of Grades Ineligibility";
                    } else {
                        $comment = $value->first_choice_eligibility_reason;
                    }
                }
            } else if ($status == "Denied due to Incomplete Records") {
                if ($value->incomplete_reason != '') {
                    if ($value->incomplete_reason == "Both") {
                        $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    } else if ($value->incomplete_reason == "Grade") {
                        $comment = "System has denied the application because of Incomplete Grades";
                    } else {
                        $comment = "System has denied the application because of Incomplete Records";
                    }
                }
            }

            if ($status != 'Pending') {
                $rs = SubmissionsStatusLog::create(array("submission_id" => $submission_id, "enrollment_id" => Session::get("enrollment_id"), "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id, "comment" => $comment));
                $rs = SubmissionsStatusUniqueLog::updateOrCreate(["submission_id" => $submission_id], array("submission_id" => $submission_id, "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id));
                $rs = Submissions::where("id", $submission_id)->update(["submission_status" => $status]);
            }
        }
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->update(array("commited" => "Yes"));
        echo "Done";
        exit;
    }

    public function selection_revert(Request $request)
    {
        $req = $request->all();
        $quotations = SubmissionsStatusLog::join("submissions", "submissions.id", "submissions_status_log.submission_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("submissions.form_id", $req['application_id'])->where('old_status', 'Active')->orderBy('submissions_status_log.id', 'ASC')
            ->get()
            ->unique('submission_id');

        //dd($quotations);exit;

        $sub_id = [];
        foreach ($quotations as $key => $value) {
            $sub_id[] = $value->submission_id;
            $rs = Submissions::where("id", $value->submission_id)->update(array("submission_status" => $value->old_status, "awarded_school" => ""));
        }
        SubmissionsStatusUniqueLog::whereIn("submission_id", $sub_id)->delete();
        SubmissionsFinalStatus::whereIn("submission_id", $sub_id)->delete();
        //SubmissionsRaceCompositionReport::where("application_id", $req['application_id'])->delete();
        //SubmissionsSelectionReportMaster::where("application_id", $req['application_id'])->delete();

        $rs = ProcessSelection::where("application_id", $req['application_id'])->delete(); //update(["commited"=>"No"]);

        //SubmissionsStatusUniquesLog::truncate();

    }

    public function check_race_previous_data($group_name, $race)
    {
        $data = $this->group_racial_composition[$group_name];

        $zero = 0;

        foreach ($data as $key => $value) {
            if ($key != 'total' && $key != "no_previous" && $key != $race) {
                if ($value == 0) {
                    $zero++;
                }
            }
        }
        if ($zero > 0) {
            return "OnlyThisOffered";
        } else {
            if ($data[$race] == 0) {
                return "OnlyThisOffered";
            } else {
                $is_lower = $is_self_lower = false;
                foreach ($data as $key => $value) {
                    if ($key != 'total' && $key != "no_previous") {
                        if ($key != $race)
                            $tmp = $value;
                        else
                            $tmp = $value + 1;
                        $total = $data['total'] + 1;
                        $new_percent = number_format($tmp * 100 / $total, 2);
                        if ($new_percent < $this->enrollment_race_data[$group_name][$key]['min'] || $new_percent > $this->enrollment_race_data[$group_name][$key]['max'])
                            $is_lower = true;
                    }
                }
                //                exit;
                if ($is_lower)
                    return "SkipOffered";
                elseif ($is_self_lower)
                    return "OnlyThisOffered";
                else {
                    //$this->group_racial_composition[$group_name]['no_previous'] = 'N'; 
                    return "OfferedWaitlisted";
                }
            }
        }
    }


    public function check_all_race_range($group_name, $race, $id)
    {

        $tmp_enroll = $this->enrollment_race_data[$group_name];
        $tmp = $this->group_racial_composition[$group_name];
        $total_seats = $tmp['total'];
        $race_percent = $tmp[$race];
        if ($total_seats > 0)
            $original_race_percent = number_format($race_percent * 100 / $total_seats, 2);
        else
            $original_race_percent = 0;
        $total_seats++;
        $race_percent++;
        $new_percent = number_format($race_percent * 100 / $total_seats, 2);

        if ($new_percent >= $tmp_enroll[$race]['min'] && $new_percent <= $tmp_enroll[$race]['max']) {
            $in_range = true;
            $max = 0;
            foreach ($tmp as $key => $value) {
                if ($key != $race && $key != "total" && $key != "no_previous") {
                    $total = $tmp['total'] + 1;
                    $new_percent = number_format($value * 100 / $total, 2);
                    if ($new_percent < $tmp_enroll[$key]['min'])
                        $in_range = false;
                    elseif ($new_percent > $tmp_enroll[$key]['max']) {
                        $in_range = false;
                        $max++;
                    }
                }
            }
            if (!$in_range) {
                if ($max > 0)
                    return true;
                else
                    return false;
            } else
                return true;
        } else {
            if ($original_race_percent < $tmp_enroll[$race]['min'])
                return true;
            else
                return false;
        }
    }

    /* Function to find out updated Racial Commposition */
    public function updated_racial_composition($application_id, $from = "")
    {
        /* Create Application Filter Group Array for Program */
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->join("application_programs", "application_programs.program_id", "program.id")->where("program.parent_submission_form", $application_id)->get();

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                $cnt = 0;
                foreach ($af as $key => $af_field) {
                    if ($program->$af_field == '')
                        $cnt++;
                    if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                        array_push($af_programs, $program->$af_field);
                    }
                }
                if ($cnt == count($af)) {
                    array_push($af_programs, $program->name);
                }
            }
        }

        $tmp = $this->groupByRacism($af_programs);


        $this->group_racial_composition = $group_race_array = $tmp['group_race'];
        $this->program_group = $program_group_array = $tmp['program_group'];



        $group_racial_composition = [];
        foreach ($this->program_group as $key => $value) {
            $program_id = $key;
            $group_racial_composition[$value] = $this->calculate_offered_from_all($key, $value);
            //print_r($$group_racial_composition[$value]);exit;
        }


        /* Get Withdraw Student Count */
        $tmp = $this->program_group;
        $tmp_group = $this->group_racial_composition;


        foreach ($tmp as $k => $v) {
            if ($from == "desktop" && !is_int($from)) {
                $black = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                $white = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                $other = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                $black1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                $white1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                $other1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");
            } else {
                $black = WaitlistProcessLogs::where("program_id", $k)->sum("black_withdrawn");
                $white = WaitlistProcessLogs::where("program_id", $k)->sum("white_withdrawn");
                $other = WaitlistProcessLogs::where("program_id", $k)->sum("other_withdrawn");

                $black1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("black_withdrawn");
                $white1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("white_withdrawn");
                $other1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("other_withdrawn");
            }


            $tmp_data = $tmp_group[$v];
            $black_data = $tmp_data['black'] - $black - $black1;
            $white_data = $tmp_data['white'] - $white - $white1;
            $other_data = $tmp_data['other'] - $other - $other1;

            if ($black_data < 0)
                $black_data = 0;
            if ($white_data < 0)
                $white_data = 0;
            if ($other_data < 0)
                $other_data = 0;

            $tmp_data['black'] = $black_data;
            $tmp_data['white'] = $white_data;
            $tmp_data['other'] = $other_data;
            $tmp_data['total'] = $black_data + $white_data + $other_data;


            $tmp_group[$v] = $tmp_data;
        }
        $this->group_racial_composition = $tmp_group;
        return $this->group_racial_composition;
    }

    public function calculate_offered_from_all($program_id, $group_name)
    {

        $group_data = $this->group_racial_composition[$group_name];

        /* From regular submissions Results */
        $submission = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
            $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        })->orWhere(function ($q) use ($program_id) {
            $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();

        $total = $group_data['total'];


        foreach ($submission as $sk => $sv) {
            if (getCalculatedRace($sv->race) != "")
                $group_data[getCalculatedRace($sv->race)]  = $group_data[getCalculatedRace($sv->race)] + $sv->CNT;
            $total += $sv->CNT;
        }


        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $submission = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
            $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        })->orWhere(function ($q) use ($program_id) {
            $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();

        foreach ($submission as $sk => $sv) {
            if (getCalculatedRace($sv->race) != "")
                $group_data[getCalculatedRace($sv->race)]  = $group_data[getCalculatedRace($sv->race)] + $sv->CNT;
            //$group_data[strtolower($sv->calculated_race)]  = $group_data[strtolower($sv->calculated_race)] + $sv->CNT; 
            $total += $sv->CNT;
        }

        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $submission = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
            $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        })->orWhere(function ($q) use ($program_id) {
            $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();

        foreach ($submission as $sk => $sv) {
            if (getCalculatedRace($sv->race) != "")
                $group_data[getCalculatedRace($sv->race)]  = $group_data[getCalculatedRace($sv->race)] + $sv->CNT;
            //$group_data[strtolower($sv->calculated_race)]  = $group_data[strtolower($sv->calculated_race)] + $sv->CNT; 
            $total += $sv->CNT;
        }



        $group_data['total'] = $total;
        $this->group_racial_composition[$group_name] = $group_data;
        return $group_data;
    }

    public function get_waitlist_count($application_id, $program_id, $grade)
    {

        $application_program_id = $program_id;

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("commited", "Yes")->whereRaw("FIND_IN_SET(" . $application_program_id . ", selected_programs)")->orderBy("created_at", "desc")->first();

        $table_name = "submissions_final_status";

        $version = 0;
        if (!empty($rs)) {
            if ($rs->type == "regular") {
                $table_name = "submissions_final_status";
                $version = 0;
            } elseif ($rs->type == "waitlist") {
                $table_name = "submissions_waitlist_final_status";
                $version = $rs->version;
            } elseif ($rs->type == "late_submission") {
                $table_name = "late_submissions_final_status";
                $version = $rs->version;
            }
        }
        //$table_name = "submissions_latest_final_status";

        $waitlist_count1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('first_choice_final_status', 'Waitlisted')->where('first_offer_status', 'Pending')->where($table_name . ".version", $version)->join($table_name, $table_name . ".submission_id", "submissions.id")->whereIn("submissions.submission_status", ["Waitlisted", "Declined / Waitlist for other"])->where("first_choice_program_id", $program_id)->count();

        $waitlist_count2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('second_choice_final_status', 'Waitlisted')->where('second_offer_status', 'Pending')->where($table_name . ".version", $version)->join($table_name, $table_name . ".submission_id", "submissions.id")->whereIn("submissions.submission_status", ["Waitlisted", "Declined / Waitlist for other"])->where("second_choice_program_id", $program_id)->count();

        $waitlist_count3 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('third_choice_final_status', 'Waitlisted')->where('third_offer_status', 'Pending')->where($table_name . ".version", $version)->join($table_name, $table_name . ".submission_id", "submissions.id")->whereIn("submissions.submission_status", ["Waitlisted", "Declined / Waitlist for other"])->where("third_choice_program_id", $program_id)->count();





        $waitlist_count4 = 0; //Submissions::where('district_id', Session::get("district_id"))->where('submissions.application_id', $application_id)->where('first_choice_final_status', 'Waitlisted')->where('second_choice_final_status', 'Pending')->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("first_choice_program_id", $program_id)->count();


        return $waitlist_count1 + $waitlist_count2 + $waitlist_count3 + $waitlist_count4;
    }

    public function get_available_count($application_id, $program_id, $grade)
    {
        $total_offered = $this->get_offered_count_programwise($program_id, $grade);
        $rs = Availability::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $program_id)->first();

        return array("total_seats" => 0, "available_seats" => $rs->available_seats ?? 0, "offered_seats" => $total_offered);
    }

    public function get_offered_count_programwise($program_id, $grade)
    {
        /* From regular submissions Results */
        $count1 = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('submission_status', 'Offered and Accepted')->where(function ($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("third_offer_status", "Accepted")->where("third_waitlist_for", $program_id);
            });
        })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->count();


        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count2 = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('submission_status', 'Offered and Accepted')->where(function ($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("third_offer_status", "Accepted")->where("third_waitlist_for", $program_id);
            });
        })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();

        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count3 = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('submission_status', 'Offered and Accepted')->where(function ($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                $q->where("third_offer_status", "Accepted")->where("third_waitlist_for", $program_id);
            });
        })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->count();

        return $count1 + $count2 + $count3;
    }
}
