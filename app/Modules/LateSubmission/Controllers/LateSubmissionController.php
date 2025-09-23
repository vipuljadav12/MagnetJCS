<?php

namespace App\Modules\LateSubmission\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\{Program, ProgramEligibility, ProgramGradeMapping};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\Enrollment\Models\{Enrollment, EnrollmentRaceComposition};
use App\Modules\ProcessSelection\Models\{Availability, ProgramSwingData, PreliminaryScore, ProcessSelection};
use App\Modules\SetAvailability\Models\{WaitlistAvailability, LateSubmissionAvailability};
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs, LateSubmissionAvailabilityLog, LateSubmissionAvailabilityProcessLog, LateSubmissionIndividualAvailability};
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionsStatusLog, SubmissionsWaitlistStatusUniqueLog, SubmissionsSelectionReportMaster, SubmissionsRaceCompositionReport, SubmissionsLatestFinalStatus, SubmissionsTmpFinalStatus, LateSubmissionFinalStatus, LateSubmissionsStatusUniqueLog, SubmissionInterviewScore, SubmprocesslaissionCompositeScore, SubmissionCommitteeScore};
use App\Modules\Waitlist\Models\{WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog, WaitlistIndividualAvailability};
use App\Modules\Enrollment\Models\ADMData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LateSubmissionController extends Controller
{

    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $group_racial_composition = [];
    public $program_group = [];
    public $enrollment_race_data = [];

    public function validateApplication($application_id)
    {
        $rs = Submissions::where("form_id", $application_id)->where("submission_status", "Offered")->count();
        if ($rs > 0)
            echo "Selected Applications has still open offered submissions.";
        else
            echo "OK";
    }


    public function selection_application_index()
    {
        $selection = "Y";
        $applications = Form::where("status", "y")->get();
        return view("LateSubmission::application_index", compact("applications", "selection"));
    }

    public function application_index()
    {
        $selection = "";
        $applications = Form::where("status", "y")->get();
        return view("LateSubmission::application_index", compact("applications", "selection"));
    }

    public function processRunAdminReport($application_id = 1) {}

    public function admin_run_selection($application_id = 1)
    {

        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');

        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();
        if ($process_selection)
            $version = $process_selection->version;
        else
            $version = 0;
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
            $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->count();
            $tmp['availability'] =  $value->available_seats - $rs1;

            if ($rising_composition && is_object($rising_composition)) {
                foreach ($rising_composition as $rkey => $rvalue) {
                $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->get();

                foreach ($rs1 as $rs1k => $rs1v) {
                    if (getSchoolMasterName($rs1v->zoned_school) == $rkey) {
                        $race = getCalculatedRace($rs1v->race);
                        if ($race)
                            $rvalue->{$race} = $rvalue->{$race} + 1;
                        //                        $rvalue++;
                    }
                }
                $tmp[$rkey] = $rvalue;
                }
            }
            $lateData = LateSubmissionProcessLogs::join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->where("process_selection.type", "late_submission")->where("program_id", $value->program_id)->select('zoned_schools', 'late_submission_process_logs.id')->get();
            foreach ($lateData as $lkey => $lvalue) {
                if ($lvalue->zoned_schools != '') {
                    foreach (json_decode($lvalue->zoned_schools) as $jlkey => $jlvalue) {
                        if (isset($tmp[$jlkey])) {
                            $tmp1 = $tmp[$jlkey];
                            if (isset($tmp1->black)) {
                                $tmp1->black = $tmp1->black - $jlvalue->black;
                            }


                            if (isset($tmp1->non_black)) {
                                $tmp1->non_black = $tmp1->non_black - $jlvalue->non_black;
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
                            if (isset($tmp1->black)) {
                                $tmp1->black = $tmp1->black - $jlvalue->black;
                            }


                            if (isset($tmp1->non_black)) {
                                $tmp1->non_black = $tmp1->non_black - $jlvalue->non_black;
                            }

                            $tmp[$jlkey] = $tmp1;
                        }
                    }
                }
            }
            $programAvailability[$value->program_id] = $tmp;
        }
        //                 echo "<pre>";
        // print_r($programAvailability);
        // exit;

        /* Here racial composition updated so code is pending from here */

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("commited", "Yes")->orderBy("created_at", "desc")->first();

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


        $offered_arr = $final_skipped_arr = $done_ids = $inavailable_arr = $skipped_unique = $final_data = $display_data = [];
        $choiceArr = ["first", "second", "third"];
        foreach ($choiceArr as $choice) {
            $done_choice[] = $choice;

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

                    $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                            $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                        })
                        ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                        ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".third_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status", $table_name . ".third_choice_final_status")
                        ->where("next_grade", $grade)
                        ->orderBy("lottery_number", "DESC")
                        ->get();

                    // $submissions=Submissions::
                    //     where('submissions.enrollment_id', Session::get('enrollment_id'))
                    //     ->whereIn("submission_status", ["Waitlisted", "Declined / Waitlisted for Other"])
                    //     ->where("form_id", $application_id)
                    //     ->whereIn($choice."_choice_program_id", $prc_program_id)
                    //     //->whereIn("id", [3737/*,3289*/])
                    //     ->where("next_grade", $grade)
                    //     ->orderBy("lottery_number", "DESC")
                    //     ->get();

                    //dd(Session::get("enrollment_id"),$submissions);
                    foreach ($submissions as $submission) { //1

                        $pid_field = $choice . "_choice_program_id";
                        // if($submission->$pid_field)
                        // {
                        // echo $submission->id." - ".$choice." - ".$round." -  ".$programAvailability[$submission->$pid_field]['availability']."<br>";  

                        // }
                        $offered = false;
                        $current_school = "";
                        $offer_program_id = 0;

                        $tmpSubmission = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($submission);
                        $tmpSubmission['first_choice_program'] = getProgramName($submission->first_choice_program_id);
                        $tmpSubmission['second_choice_program'] = getProgramName($submission->second_choice_program_id);
                        $tmpSubmission['third_choice_program'] = getProgramName($submission->third_choice_program_id);

                        if ($submission->{$choice . "_choice_final_status"} == "Waitlisted"  || $submission->{$choice . "_choice_final_status"} == "Declined / Waitlist for other") // New
                        {

                            if (!in_array($submission->id, $offered_arr) && !in_array($submission->id . "." . $choice, $inavailable_arr)) // && !in_array($choice."_".$submission->id, $skipped_unique)) // 
                            { //2

                                // echo $submission->id."<BR>";
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

                                            $tmpMinMaxStr = "(<strong>Min: " . $schoolAdmData[$school_id][$calculated_race]["min"] . "% Max: " . $schoolAdmData[$school_id][$calculated_race]["max"] . "%</strong>)";

                                            if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                            { //11

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

                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Main Offered");
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
                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "NOT IN RANGE");
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
                                    $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Not AVailable");
                                    //$display_data[] = $tmpSubmission;
                                }   // 10                          
                            }  // 2
                            else if (in_array($submission->id, $offered_arr) && !in_array($submission->id . "-" . $choice, $done_ids)) //  && in_array($submission->$pid_field, $prc_program_id)
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
                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Already Offered");
                                //$display_data[] = $tmpData;
                            }
                        } //new

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
                                                    // if($submission->id == 3555 && $skvalue['id'] == 3772)
                                                    // {
                                                    //     echo $newP . " >= " . $schoolAdmData[$school_id][$calculated_race]["min"] ." && " . $newP ." <= ".$schoolAdmData[$school_id][$calculated_race]["max"]."<br>";
                                                    // }

                                                    if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                                    { //1

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
                                                        $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission1, $display_data, "SKIPPED LOOP");
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
                                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpNew, $display_data, "SKIPPED LOOP");
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


        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
            $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
        })
            ->where('submissions.enrollment_id', Session::get("enrollment_id"))
            ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status")->get();


        $displayRecords = [];
        $count = 0;



        // echo "<pre>";
        // dd($display_data, "TTT");
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



                if (!empty($tmp) && $submission->{$choice . "_choice_final_status"} == "Waitlisted") // && $submission->{$choice."_offer_status"} == "Pending")
                {
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
                    } elseif (in_array($tmp['status'], ["No Availability", "Next Round", "Already Offered"])) {
                        $data[$choice . '_waitlist_for'] = $submission->{$choice . '_choice_program_id'};
                        $data[$choice . '_choice_final_status'] = "Waitlisted";
                        $msg = $tmp['msg'] ?? "";
                    }
                }
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

        $submission_data = [];
        $subids = [];
        $prc_program_id = []; //      $program_process_ids;//[];
        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->get();
        foreach ($programs as $pkey => $pvalue) {
            $prc_program_id[] = $pvalue->id;
        }
        $offered_arr = $final_skipped_arr = $done_ids = $inavailable_arr = $skipped_unique = $final_data = [];
        $choiceArr = ["first", "second", "third"];
        foreach ($choiceArr as $choice) {
            $done_choice[] = $choice;


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
                        ->where("late_submission", 'Y')
                        //                        ->whereIn("id", $subids)
                        //->whereIn($choice."_choice_program_id", $prc_program_id)
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

                                        $tmpMinMaxStr = "(<strong>Min: " . $schoolAdmData[$school_id][$calculated_race]["min"] . "% Max: " . $schoolAdmData[$school_id][$calculated_race]["max"] . "%</strong>)";

                                        if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                        { //11

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

                                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Main Offered");
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
                                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "NOT IN RANGE");
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
                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Not AVailable");
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
                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Already Offered");
                            //$display_data[] = $tmpData;
                        } else if (in_array($submission->$pid_field, $prc_program_id)) {

                            $tmpData = $tmpSubmission;
                            $tmpData['choice'] = $choice;
                            $tmpData['race'] = getCalculatedRace($submission->race);
                            $tmpData['first_choice_program_id'] = $submission->first_choice_program_id;
                            $tmpData['second_choice_program_id'] = $submission->second_choice_program_id;
                            $tmpData['round'] = $round + 1;

                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Last Condition");
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


                                                    if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                                    { //1

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
                                                        $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission1, $display_data, "SKIPPED LOOP");
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
                                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpNew, $display_data, "SKIPPED LOOP");
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
            ->where("late_submission", "Y")
            // ->where(function ($q) use ($prc_program_id) {
            //         $q->whereIn("first_choice_program_id", $prc_program_id)
            //         ->orWhereIn("second_choice_program_id", $prc_program_id)
            //         ->orWhereIn("third_choice_program_id", $prc_program_id);
            // })
            //->whereIn("id", [3284/*,3289*/])
            //->where("next_grade", $grade)
            ->orderBy("lottery_number", "DESC")
            ->get();
        //$displayRecords = [];
        $count = 0;



        // echo "<pre>";
        // dd($display_data);
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
        return view("ProcessSelection::test_index", compact("display_data", "final_skipped_arr"));
    }
    public function index($application_id = 0)
    {
        $displayother = 0;
        $district_id = Session::get("district_id");
        $rs = $exist_process = ProcessSelection::where("last_date_online_acceptance", ">", date("Y-m-d H:i:s"))->where("form_id", $application_id)->where('type', 'late_submission')->where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "DESC")->first();
        $display_outcome = $displayother = 0;

        $updated_id = 0;

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        if (!empty($rs)) {
            $displayother = 1;
            if ($rs->commited == "Yes") {
                $display_outcome = 1;
                $updated_id = $rs->id;
            } else {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";
            }
            $last_date_online_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_online_acceptance));
            $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_offline_acceptance));
        } else {
            $rs = $exist_process = ProcessSelection::where("form_id", $application_id)->where("enrollment_id", Session::get("enrollment_id"))->where('type', 'late_submission')->where("commited", "No")->orderBy("created_at", "DESC")->first();
            if (!empty($rs))
                $displayother = 1;
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();
        $prgGroupArr = [];
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
        $pvalues = array_unique(array_values($prgGroupArr));

        $af_programs = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->fetch_programs_group($application_id);

        $tmp = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->groupByRacism($af_programs);

        /* Fetch all program groups */
        $program_group = $program_group_array = $tmp['program_group'];


        /* Get Program Values by unique value and by sorting */
        $disp_arr = $program_arr = [];
        foreach ($pvalues as $key => $value) {
            $tmp_val_arr = [];
            foreach ($program_group as $pk => $pv) {
                if ($pv == $value) {
                    $program_name = getProgramName($pk);
                    $program_data = Program::where("id", $pk)->first();
                    $grade_lavel = explode(",", $program_data->grade_lavel);

                    $pdata = [];
                    $pdata['id'] = $pk;
                    $pdata['grade'] = "";
                    $rs_availability = Availability::where("program_id", $pk)->where("enrollment_id", Session::get("enrollment_id"))->first();
                    $pdata['withdrawn_allowed'] = "Yes";
                    $pdata['name'] = $program_name;

                    $rsZoned = Program::where("id", $pk)->first();

                    $zoned_schools = [];
                    if ($rsZoned->zoned_schools != "") {
                        $tmpZoned = explode(",", $rsZoned->zoned_schools);
                        $tstZoned = [];
                        for ($ti = 0; $ti < count($tmpZoned); $ti++) {
                            $tst1 = [];
                            $tst1['black'] = $tst1['non_black'] = 0;
                            $tstZoned[$tmpZoned[$ti]] = $tst1;
                        }
                        $zoned_schools = $tstZoned;
                    }
                    $pdata['zoned_schools'] = $zoned_schools;
                    $pdata['waitlist_count'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_waitlist_count($application_id, $pk, "");
                    $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $pk, "");



                    $pdata['available_count'] = $pdata['available_slot'] = $data['available_seats'] - $data['offered_seats'];

                    if ($pdata['available_slot'] < 0)
                        $pdata['available_slot'] = 0;


                    $additional = WaitlistProcessLogs::where("process_selection.enrollment_id", Session::get("enrollment_id"))->where("program_id", $pk)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum('additional_seats');
                    $pdata['total_seats'] = $data['available_seats'] + $additional;

                    $total_applicants = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("late_submission", "Y")->where(function ($query) use ($pk) {
                        $query->where('first_choice_program_id', $pk);
                        $query->orWhere('second_choice_program_id', $pk);
                    })->get()->count();

                    $pdata['late_application_count'] = $total_applicants;
                    $pdata['withdrawn_student'] = "No";

                    $pdata['black_withdrawn'] = 0;
                    $pdata['white_withdrawn'] = 0;
                    $pdata['other_withdrawn'] = 0;
                    $pdata['additional_seats'] = 0;
                    $pdata['visible'] = "N";
                    $black = $white = $other = $black1 = $white1 = $other1 = 0;
                    if (!empty($exist_process)) {
                        $tmp_data = LateSubmissionProcessLogs::where("process_log_id", $exist_process->id)->where("program_id", $pk)->first();
                        if (!empty($tmp_data)) {
                            $pdata['visible'] = "Y";
                            $pdata['withdrawn_student'] = $tmp_data->withdrawn_student;
                            $pdata['black_withdrawn'] = $tmp_data->black_withdrawn;
                            $pdata['white_withdrawn'] = $tmp_data->white_withdrawn;
                            $pdata['other_withdrawn'] = $tmp_data->other_withdrawn;
                            $pdata['available_slot'] = $tmp_data->slots_to_awards;
                            $pdata['additional_seats'] = $tmp_data->additional_seats;
                            $pdata['available_count'] = $tmp_data->available_slots;
                            $pdata['late_application_count'] = $tmp_data->late_application_count;
                            if ($tmp_data->zoned_schools != "") {
                                $jArr = json_decode($tmp_data->zoned_schools);

                                $zArray = [];
                                foreach ($jArr as $jkey => $jvalue) {
                                    $tmpJ = [];
                                    $tmpJ['black'] = $jvalue->black ?? 0;
                                    $tmpJ['non_black'] = $jvalue->non_black ?? 0;
                                    $zArray[$jkey] = $tmpJ;
                                }

                                $pdata['zoned_schools'] = $zArray;
                            }
                        }
                    } else {

                        $black = WaitlistProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                        $white = WaitlistProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                        $other = WaitlistProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                        $black1 = LateSubmissionProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                        $white1 = LateSubmissionProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                        $other1 = LateSubmissionProcessLogs::where("program_id", $pk)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                        $pdata['available_count']  += $black + $black1 + $white1 + $white + $other + $other1;
                        $pdata['available_slot'] = $pdata['available_count'];
                        ///
                    }

                    $pdata['application_program_id'] = $pk;
                    $tmp_val_arr[] = $pdata;
                }
            }
            // dd($value);
            $disp_arr[$value] = $tmp_val_arr;
        }



        //         $pvalues = array_unique(array_values($program_group));
        //         sort($pvalues);
        //         $disp_arr = $program_arr = [];
        //         foreach($pvalues as $key=>$value)
        //         {
        //             $tmp_val_arr = [];
        //             foreach($program_group as $pk=>$pv)
        //             {
        //                 if($pv == $value)
        //                 {
        //                     $program_name = getProgramName($pk);
        //                     $program_data = Program::where("id", $pk)->first();
        //                     $grade_lavel = explode(",", $program_data->grade_lavel);

        //                     foreach($grade_lavel as $gval)
        //                     {
        //                         $pdata = [];
        //                         $pdata['id'] = $pk;
        //                         $pdata['grade'] = $gval;
        //                         $pdata['withdrawn_allowed'] = "Yes";
        //                         $rs_availability = Availability::where("program_id", $pk)->where("enrollment_id", Session::get("enrollment_id"))->where("grade", $gval)->first();
        //                         if(!empty($rs_availability))
        //                         {
        //                             if($rs_availability->white_seats == 0 && $rs_availability->white_seats == 0 && $rs_availability->other_seats == 0)
        //                             {
        //                                 $pdata['withdrawn_allowed'] = "No";
        //                             }
        //                         }
        //                         $pdata['name'] = $program_name . " - Grade ".$gval;
        //                         $pdata['waitlist_count'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_waitlist_count($application_id, $pk, $gval);
        //                         $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $pk, $gval);



        //                         $pdata['available_count'] = $pdata['available_slot'] = $data['available_seats']-$data['offered_seats'];


        //                         if($pdata['available_slot'] < 0)
        //                             $pdata['available_slot'] = 0;


        //                         $additional = LateSubmissionProcessLogs::where("process_selection.enrollment_id", Session::get("enrollment_id"))->where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum('additional_seats');
        //                         $pdata['total_seats'] = $data['available_seats'] + $additional;

        //                         $total_applicants = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("late_submission", "Y")->where(function($query) use ($pk){
        //                                 $query->where('first_choice_program_id', $pk);
        //                                 $query->orWhere('second_choice_program_id', $pk);
        //                             })->where('next_grade', $gval)->get()->count();

        //                         $pdata['late_application_count'] = $total_applicants;
        //                         $pdata['withdrawn_student'] = "No";
        //                         $pdata['black_withdrawn'] = 0;
        //                         $pdata['white_withdrawn'] = 0;
        //                         $pdata['other_withdrawn'] = 0;
        //                         $pdata['additional_seats'] = 0;
        //                         $pdata['visible'] = "N";

        //                         $black = $white = $other = $black1 = $white1 = $other1 = 0;

        //                         if(!empty($exist_process))
        //                         {
        //                             $tmp_data = LateSubmissionProcessLogs::where("process_log_id", $exist_process->id)->where("program_id", $pk)->where("grade", $gval)->first();

        //                             if(!empty($tmp_data))
        //                             {
        //                                 $pdata['visible'] = "Y";
        //                                 $pdata['withdrawn_student'] = $tmp_data->withdrawn_student;
        //                                 $pdata['black_withdrawn'] = $tmp_data->black_withdrawn;
        //                                 $pdata['white_withdrawn'] = $tmp_data->white_withdrawn;
        //                                 $pdata['other_withdrawn'] = $tmp_data->other_withdrawn;
        //                                 $pdata['available_slot'] = $tmp_data->slots_to_awards;
        //                                 $pdata['additional_seats'] = $tmp_data->additional_seats;
        //                                 $pdata['late_application_count'] = $tmp_data->late_application_count;
        //                                 $pdata['available_count'] = $tmp_data->available_slots;
        //                             }
        //                         }
        //                         else
        //                         {


        //                             $black = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
        //                             $white = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
        //                             $other = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

        //                             $black1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
        //                             $white1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
        //                             $other1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");
        //                             $pdata['available_count']  += $black + $black1 + $white1 + $white + $other + $other1;
        //                             $pdata['available_slot'] = $pdata['available_count'];
        //                             ///
        //                         }




        //                         $application_program_id = ProgramGradeMapping::where("program_id", $pk)->where("grade", $gval)->first();
        // //                        echo $gval." - " .$pk;exit; 
        //                         if(!empty($application_program_id))
        //                         {
        //                             $pdata['application_program_id'] = $application_program_id->id;
        //                             $tmp_val_arr[] = $pdata;
        //                         }

        //                     }

        //                 }
        //             }
        //             $disp_arr[$value] = $tmp_val_arr;
        //         }

        /* echo "<pre>";
        print_r($disp_arr);
        exit;
        */
        $waitlist_process_logs = [];
        if ($display_outcome == 1) {
            $waitlist_process_logs = LateSubmissionProcessLogs::where("process_log_id", $updated_id)->orderBy("id", "DESC")->get();
        } else {
            //$last_date_online_acceptance = $last_date_offline_acceptance = "";
        }
        return view("LateSubmission::all_availability_index", compact("application_id", "disp_arr", "display_outcome", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance", "waitlist_process_logs"));
    }


    public function storeAllAvailability(Request $request, $application_id)
    {
        $req = $request->all();
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

        $version = 0;
        if (!empty($process_selection)) {
            if ($process_selection->commited == 'Yes') {
                $version = $process_selection->version + 1;
            } else {
                $version = $process_selection->version;
            }
        }
        $type = "";
        if (isset($req['type']))
            $type = $req['type'];

        $selected_programs = [];
        $process = false;
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {
                $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(" . $value . ", selected_programs)")->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();

                if (!empty($process_selection)) {
                    if ($process_selection->commited != 'Yes') {
                        $process = true;
                    }
                } else {
                    $process = true;
                }
                $selected_programs[] = $value;
            }
        }

        if ($req['last_date_online_acceptance'] != '' || $req['process_event'] == "saveonly") {
            if ($req['last_date_online_acceptance'] != '') {
                $data['last_date_online_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_online_acceptance']));
                $data['last_date_offline_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_offline_acceptance']));
            }

            $data['district_id'] = Session::get("district_id");
            $data['enrollment_id'] = Session::get("enrollment_id");
            $data['application_id'] = $application_id;
            $data['district_id'] = Session::get("district_id");
            $data['type'] = "late_submission";
            $data['version'] = $version;
            $data['selected_programs'] = implode(",", $selected_programs);
            $rs = ProcessSelection::updateOrCreate(['form_id' => $data['application_id'], "version" => $version, "type" => "late_submission", "enrollment_id" => Session::get("enrollment_id")], $data);

            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where('form_id', $data['application_id'])->where("version", $version)->where("type", "late_submission")->first();


            $t = LateSubmissionProcessLogs::where("process_log_id", $rs->id)->delete();
            foreach ($req['application_program_id'] as $key => $value) {
                if ($req['awardslot' . $value] > 0) {
                    $insert = [];
                    $insert['process_log_id'] = $rs->id;
                    $insert['program_id'] = $req['program_id' . $value];
                    $insert['grade'] = $req['grade' . $value];
                    $insert['application_id'] = $rs->application_id;
                    $insert['version'] = $version;
                    $insert['program_name'] = $req['program_name' . $value];
                    $insert['total_seats'] = $req['total_seats' . $value];
                    $insert['additional_seats'] = $req['additional_seats' . $value];
                    $insert['withdrawn_student'] = $req['withdrawn_student' . $value];
                    if (isset($req['zoned_schools'][$value]))
                        $insert['zoned_schools'] = json_encode($req['zoned_schools'][$value]);
                    if ($req['withdrawn_student' . $value] != "Yes") {
                        $insert['black_withdrawn'] = 0;
                        $insert['white_withdrawn'] = 0;
                        $insert['other_withdrawn'] = 0;
                    } else {
                        $insert['black_withdrawn'] = $req['black' . $value];
                        $insert['white_withdrawn'] = $req['white' . $value];
                        $insert['other_withdrawn'] = $req['other' . $value];
                    }
                    $insert['waitlisted'] = $req['waitlist_count' . $value];
                    $insert['late_application_count'] = $req['late_application_count' . $value];
                    $insert['available_slots'] = $req['available_slot' . $value];
                    $insert['slots_to_awards'] = $req['awardslot' . $value];
                    $insert['generated_by'] = Auth::user()->id;
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $rs1 = LateSubmissionProcessLogs::updateOrCreate(["process_log_id" => $rs->id, "program_name" => $insert['program_name']], $insert);
                }
            }
        }

        $data = array();

        if ($process && $req['process_event'] != "saveonly") {
            $rdel = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->delete();
            $test = $this->processLateSubmission($req, $application_id, $version, $type);
        }
        echo "done";
    }


    /* Seats Status Functions */
    public function seatStatusVersion($id = 0)
    {
        $rs = ProcessSelection::where("id", $id)->first();
        $application_id = $rs->application_id;
        $version = $rs->version;

        $version_data = $rs;
        $selected_programs = explode(",", $rs->selected_programs);

        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $program_ids[] = getApplicationProgramId($value);
        }

        $tmp_version_data = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();

        $parray = [];
        //$rs = WaitlistAvailabilityProcessLog::where("version", $version)->get();
        foreach ($tmp_version_data as $key => $value) {
            if (!isset($parray[$value->program_id])) {
                $parray[$value->program_id] = [];
            }
            array_push($parray[$value->program_id], $value->grade);
        }



        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $district_id = Session::get("district_id");
        $submissions = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        $prgCount = array();;
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if ($value->$choice != 0) {
                        if (!isset($programs[$value->$choice]) && in_array($value->$choice, array_keys($parray))) {
                            $programs[$value->$choice] = [];
                        }
                        if (isset($programs[$value->$choice]) && !in_array($value->next_grade, $programs[$value->$choice])) {
                            if (in_array($value->next_grade, $parray[$value->$choice])) {
                                array_push($programs[$value->$choice], $value->next_grade);
                            }
                        }
                    }
                }
            }
        }

        ksort($programs);
        $final_data = array();
        foreach ($programs as $key => $value) {
            foreach ($value as $ikey => $ivalue) {
                $tmp = array();
                $rs = Availability::where("program_id", $key)->where("grade", $ivalue)->where("enrollment_id", Session::get("enrollment_id"))->first();
                $available_seats = $rs->available_seats;

                $seat_data = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $key)->where("grade", $ivalue)->where("application_id", $application_id)->where("version", $version)->first();
                //echo $ivalue."<BR>";
                $tmp['original_capacity'] = $rs->total_seats;
                $tmp['total_seats'] = $rs->available_seats;
                $tmp['available_seats'] = $seat_data->available_slots;
                //echo $tmp['available_seats'];exit;
                $tmp['process_seats'] = $seat_data->slots_to_awards;
                $tmp['total_applicants'] = $seat_data->waitlisted;
                $tmp['program_name'] = $seat_data->program_name;
                $tmp['black_withdrawn'] = $seat_data->black_withdrawn;
                $tmp['white_withdrawn'] = $seat_data->white_withdrawn;
                $tmp['other_withdrawn'] = $seat_data->other_withdrawn;
                $tmp['additional_seats'] = $seat_data->additional_seats;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)->where("late_submissions_final_status.application_id", $application_id)->where("late_submissions_final_status.version", $version)
                    ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)->where("late_submissions_final_status.application_id", $application_id)->where("late_submissions_final_status.version", $version)
                    ->get()->count();
                $tmp['offered'] = $rs1 + $rs2;






                $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $key, $ivalue);
                $accepted = $data['offered_seats'];


                $current_accepted = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $ivalue)->where(function ($q1) use ($key) {
                    $q1->where(function ($q) use ($key) {
                        $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $key);
                    })->orWhere(function ($q) use ($key) {
                        $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $key);
                    });
                })->where("late_submissions_final_status.version", $version)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();
                $accepted = $accepted - $current_accepted;

                $tmp['accepted'] = $accepted;
                $tmp['remaining'] = $tmp['available_seats']  + $tmp['black_withdrawn'] + $tmp['white_withdrawn'] + $tmp['other_withdrawn'] + $tmp['additional_seats'];
                $final_data[] = $tmp;
            }
        }




        //print_r($final_data);exit;
        return view("LateSubmission::seats_status", compact("final_data", "version_data"));
    }


    /* Population Change */
    public function population_change_application($application_id = 1, $version = 0)
    {
        // Processing
        $pid = $application_id;
        $from = "form";

        $selected_programs = [];
        if ($version == 0) {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        $program_ids = [];



        $additional_data = $this->get_additional_info($application_id, $version);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];


        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();

        // Population Changes
        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        // $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($program_ids) {
        //                         $q->whereIn("first_choice_program_id", $program_ids)
        //                           ->orWhereIn("second_choice_program_id", $program_ids)
        //                            ->orWhereIn("third_choice_program_id", $program_ids);  
        //                     })
        //                     ->where('district_id', $district_id)
        //                     ->where("submissions.form_id", $application_id)
        //                     ->where("submissions.enrollment_id", Session::get('enrollment_id'))
        //                     ->where("late_submissions_final_status.version", $version)
        //                     ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
        //                     ->orderByRaw('FIELD(next_grade,'.implode(",",$ids).')')
        //     ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);


        $submissions = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where(function ($q) {
            $q->where("first_choice_final_status", "Offered")
                ->orWhere("second_choice_final_status", "Offered")
                ->orWhere("third_choice_final_status", "Offered");
        })
            ->where('submissions.enrollment_id', Session::get("enrollment_id"))->where("submissions.form_id", $application_id)->where("late_submissions_final_status.version", $version)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'third_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for', 'third_waitlist_for']);



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
        foreach ($programs as $program_id => $grades) {

            $availability = Availability::where("enrollment_id", Session::get("enrollment_id"))->where('program_id', $program_id)
                ->first(['total_seats', 'available_seats']);
            $race_count = [];
            if (!empty($availability)) {
                $offer_count = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($program_id))->count();
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

                $rsWait = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $program_id)->first();

                if (!empty($rsWait)) {
                    $availability->available_seats += $rsWait->additional_seats;
                }




                $data = [
                    'program_id' => $program_id,
                    'grade' => "",
                    'total_seats' => $availability->available_seats ?? 0,
                    'available_seats' => ($availability->available_seats - $offer_count) ?? 0,
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
        return view("LateSubmission::population_change", compact('data_ary', 'race_ary', 'pid', 'from', "display_outcome", "application_id", "last_date_online_acceptance", "last_date_offline_acceptance"));
    }


    public function population_change_version($application_id, $version = 0)
    {
        // Population Changes
        $selected_programs = [];
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();

        $version_data = $rs;
        $version = $rs->version;
        $selected_programs = explode(",", $rs->selected_programs);
        $program_ids = [];

        foreach ($selected_programs as $key => $value) {
            //$rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $value; //rs->program_id;//getApplicationProgramId($value);
        }

        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($program_ids) {
            $q->whereIn("first_choice_program_id", $program_ids)
                ->orWhereIn("second_choice_program_id", $program_ids)
                ->orWhereIn("third_choice_program_id", $program_ids);
        })
            ->where('district_id', $district_id)
            ->where("submissions.form_id", $application_id)
            ->where("submissions.enrollment_id", Session::get('enrollment_id'))
            ->where("late_submissions_final_status.version", $version)
            ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status', 'first_waitlist_for', 'second_waitlist_for', 'third_waitlist_for']);



        $choices = ['first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if (in_array($value->$choice, $program_ids)) {
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
        }
        ksort($programs);
        $data_ary = [];
        $race_ary = [];
        foreach ($programs as $program_id => $grades) {
            $availability = Availability::where("enrollment_id", Session::get("enrollment_id"))->where('program_id', $program_id)->first(['total_seats', 'available_seats']);

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
                $rsproc = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("version", $version)->where("application_id", $application_id)->where("program_id", $program_id)->first();
                if (!isset($race_ary['Black']))
                    $race_ary['Black'] = 0;
                if (!isset($race_ary['Other']))
                    $race_ary['Non-Black'] = 0;
                $data = [
                    'program_id' => $program_id,
                    'grade' => "",
                    'total_seats' => $rsproc->available_slots ?? 0,
                    'available_seats' => $rsproc->slots_to_awards ?? 0,
                    'race_count' => $race_count,
                ];
                $data_ary[] = $data;
                // sorting race in ascending
                ksort($race_ary);
            }
            // exit;
        }


        return view("LateSubmission::population_change_report", compact('data_ary', 'race_ary', "version", "version_data"));
    }


    /* sUBMISSION RESULTS */

    public function submissions_results_application($application_id = 1, $version = 0)
    {
        $selected_programs = [];
        if ($version == 0) {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $program_ids =  $selected_programs = explode(",", $rs->selected_programs);
            //            $selected_programs = explode(",", $rs->selected_programs);
        }
        // $program_ids = [];
        // foreach($selected_programs as $key=>$value)
        // {
        //     $rs = ProgramGradeMapping::where("id", $value)->first();

        //     $program_ids[] =   $rs->program_id;//getApplicationProgramId($value);
        // }


        $additional_data = $this->get_additional_info($application_id, $version);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];

        $pid = $application_id;
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("submissions.form_id", $application_id)->where("late_submissions_final_status.version", $version)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status']);

        //dd($submissions);
        $final_data = array();
        foreach ($submissions as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['school'] = $value->current_school;
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

            /* if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                        $final_data[] = $tmp;
                        */

            if (!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                $final_data[] = $tmp;

            if ($value->second_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
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

                if (!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }
            if ($value->third_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
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

                if (!in_array($value->third_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->third_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }
        }
        $grade = $outcome = array();
        foreach ($final_data as $key => $value) {
            $grade['grade'][] = $value['grade'];
            $outcome['outcome'][] = $value['outcome'];
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("LateSubmission::submissions_result", compact('final_data', 'pid', 'display_outcome', "application_id", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance"));
    }
    public function submissions_results_version($application_id, $version = 0)
    {
        $selected_programs = [];
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();
        $version_data = $rs;
        $version = $rs->version;
        $selected_programs = explode(",", $rs->selected_programs);

        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            //$rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $value; //rs->program_id;//getApplicationProgramId($value);
        }



        $pid = $application_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("submissions.form_id", $application_id)->where('late_submissions_final_status.version', $version)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status']);

        $final_data = array();
        foreach ($submissions as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['school'] = $value->current_school;
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

            /*if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                    $final_data[] = $tmp;  
                    */

            if (!in_array($value->first_choice_final_status, array("Pending")) && in_array($value->first_choice_program_id, $program_ids))
                $final_data[] = $tmp;

            if ($value->second_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
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
                /*if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;*/
                if (!in_array($value->second_choice_final_status, array("Pending")) && in_array($value->second_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }

            if ($value->third_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
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
                /*if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;*/
                if (!in_array($value->third_choice_final_status, array("Pending")) && in_array($value->third_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }
        }
        $grade = $outcome = array();
        foreach ($final_data as $key => $value) {
            $grade['grade'][] = $value['grade'];
            $outcome['outcome'][] = $value['outcome'];
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("LateSubmission::submissions_result_report", compact('final_data', "application_id",  "version", "version_data"));
    }


    public function processLateSubmission($req, $application_id, $actual_version, $type = "")
    {

        $process_program = $awardslot  = $program_process_ids = $availabilityArray = [];
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {

                $program_id = $value;
                $program_process_ids[] = $program_id;
                $availabilityArray[$program_id] = $req['awardslot' . $value];

                if (!isset($process_program[$program_id])) {
                    $process_program[$program_id][] = $req['awardslot' . $value];
                }
                $awardslot[$program_id] = $req['awardslot' . $value];
            }
        }

        $submission_data = [];
        $keys = array_keys($process_program);

        $subids = [];

        $selected_programs = [];
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {
                $rs = ProcessSelection::where('enrollment_id', Session::get('enrollment_id'))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(" . $value . ", selected_programs)")->where("commited", "Yes")->orderBy("created_at", "desc")->first();

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

                $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                        $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                    })
                    ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                    ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".third_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status", $table_name . ".third_choice_final_status")
                    ->get();

                foreach ($submissions as $sk => $sv) {

                    $insert = false;

                    if (in_array($sv->first_choice_program_id, $keys)) {

                        $insert = true;

                        //if(($sv->first_choice_final_status == "Waitlisted" && !in_array($sv->second_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) || $sv->first_choice_final_status == "Denied due to Ineligibility" || $sv->first_choice_final_status == "Denied due to Incomplete Records")
                        if ($sv->first_choice_final_status != "Waitlisted" && $sv->first_choice_final_status != "Pending") {
                            $insert = false;
                        }
                    }
                    if (in_array($sv->second_choice_program_id, $keys) && !in_array($sv->first_choice_program_id, $keys)) {

                        $insert = true;

                        // if($sv->second_choice_final_status == "Waitlisted" && $sv->second_offer_status == "Pending")
                        // {
                        //    $insert = false;
                        // }

                        if ($sv->second_choice_final_status != "Waitlisted" && $sv->second_choice_final_status != "Pending") {

                            $insert = false;
                        }
                    }
                    if (in_array($sv->third_choice_program_id, $keys) && !in_array($sv->first_choice_program_id, $keys) && !in_array($sv->second_choice_program_id, $keys)) {

                        $insert = true;

                        // if($sv->third_choice_final_status == "Waitlisted" &&  $sv->third_offer_status == "Pending")
                        // {
                        //    $insert = false;
                        // }

                        if ($sv->third_choice_final_status != "Waitlisted" && $sv->third_choice_final_status != "Pending") {

                            $insert = false;
                        }
                    }
                    if ($insert && !in_array($sv->id, $subids)) {
                        $submission_data[] = $sv;
                        $subids[] = $sv->id;
                    }
                }
            }
        }


        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');

        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();
        $version = $process_selection->version;

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
            $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->count();
            $tmp['availability'] =  $value->available_seats - $rs1 + $req['additional_seats' . $value->program_id];

            $tmArr = [];

            foreach ($rising_composition as $rkey => $rvalue) {
                $rs1 = Submissions::where("submission_status", "Offered and Accepted")->where("enrollment_id", Session::get("enrollment_id"))->where("awarded_school", getProgramName($value->program_id))->get();



                foreach ($rs1 as $rs1k => $rs1v) {

                    if (getSchoolMasterName($rs1v->zoned_school) == $rkey) {
                        $race = getCalculatedRace($rs1v->race);
                        if (isset($req['zoned_schools'][$value->program_id][getSchoolMasterName($rs1v->zoned_school)])) {
                            // echo "<pre>";
                            // echo $value->program_id . "  -   ".getSchoolMasterName($rs1v->zoned_school)."<br>";

                            if (isset($req['zoned_schools'][$value->program_id][getSchoolMasterName($rs1v->zoned_school)]['black']) && !in_array($rkey . "-" . $value->program_id . "-black", $tmArr)) {


                                $rvalue->black = $rvalue->black - $req['zoned_schools'][$value->program_id][getSchoolMasterName($rs1v->zoned_school)]['black'];
                                $tmArr[] = $rkey . "-" . $value->program_id . "-black";
                            }
                            if (isset($req['zoned_schools'][$value->program_id][getSchoolMasterName($rs1v->zoned_school)]['non_black']) && !in_array($rkey . "-" . $value->program_id . "-non_black", $tmArr)) {
                                $rvalue->non_black = $rvalue->non_black - $req['zoned_schools'][$value->program_id][getSchoolMasterName($rs1v->zoned_school)]['non_black'];
                                $tmArr[] = $rkey . "-" . $value->program_id . "-non_black";
                            }
                            if ($race)
                                $rvalue->{$race} = $rvalue->{$race} + 1;
                        }
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
                            if (isset($tmp1->black)) {
                                $tmp1->black = $tmp1->black - $jlvalue->black;
                            }


                            if (isset($tmp1->non_black)) {
                                $tmp1->non_black = $tmp1->non_black - $jlvalue->non_black;
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
                            if (isset($tmp1->black)) {
                                $tmp1->black = $tmp1->black - $jlvalue->black;
                            }


                            if (isset($tmp1->non_black)) {
                                $tmp1->non_black = $tmp1->non_black - $jlvalue->non_black;
                            }

                            $tmp[$jlkey] = $tmp1;
                        }
                    }
                }
            }
            $programAvailability[$value->program_id] = $tmp;
        }
        //         echo "<pre>";
        // print_r($programAvailability);
        // exit;

        /* Here racial composition updated so code is pending from here */

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("commited", "Yes")->orderBy("created_at", "desc")->first();

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


        $offered_arr = $final_skipped_arr = $done_ids = $inavailable_arr = $skipped_unique = $final_data = $display_data = [];
        $choiceArr = ["first", "second", "third"];
        foreach ($choiceArr as $choice) {
            $done_choice[] = $choice;

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

                    $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                            $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                        })
                        ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                        ->whereIn("submissions.id", $subids)
                        ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".third_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status", $table_name . ".third_choice_final_status")
                        ->where("next_grade", $grade)
                        ->orderBy("lottery_number", "DESC")
                        ->get();


                    // $submissions=Submissions::
                    //     where('submissions.enrollment_id', Session::get('enrollment_id'))
                    //     ->whereIn("submission_status", ["Waitlisted", "Declined / Waitlisted for Other"])
                    //     ->where("form_id", $application_id)
                    //     ->whereIn($choice."_choice_program_id", $prc_program_id)
                    //     //->whereIn("id", [3737/*,3289*/])
                    //     ->where("next_grade", $grade)
                    //     ->orderBy("lottery_number", "DESC")
                    //     ->get();

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

                        if ($submission->{$choice . "_choice_final_status"} == "Waitlisted" || $submission->{$choice . "_choice_final_status"} == "Declined / Waitlist for other") // New
                        {


                            if (!in_array($submission->id, $offered_arr) && !in_array($submission->id . "." . $choice, $inavailable_arr)) // && !in_array($choice."_".$submission->id, $skipped_unique)) // 
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

                                            $tmpMinMaxStr = "(<strong>Min: " . $schoolAdmData[$school_id][$calculated_race]["min"] . "% Max: " . $schoolAdmData[$school_id][$calculated_race]["max"] . "%</strong>)";

                                            if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                            { //11

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

                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Main Offered");
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
                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "NOT IN RANGE");
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
                                    $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Not AVailable");
                                    //$display_data[] = $tmpSubmission;
                                }   // 10                          
                            }  // 2
                            else if (in_array($submission->id, $offered_arr) && !in_array($submission->id . "-" . $choice, $done_ids)) //  && in_array($submission->$pid_field, $prc_program_id)
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
                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Already Offered");
                                //$display_data[] = $tmpData;
                            }
                        } //new

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
                                                    // if($submission->id == 3555 && $skvalue['id'] == 3772)
                                                    // {
                                                    //     echo $newP . " >= " . $schoolAdmData[$school_id][$calculated_race]["min"] ." && " . $newP ." <= ".$schoolAdmData[$school_id][$calculated_race]["max"]."<br>";
                                                    // }

                                                    if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) //$newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                                    { //1

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
                                                        $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission1, $display_data, "SKIPPED LOOP");
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
                                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpNew, $display_data, "SKIPPED LOOP");
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


        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
            $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
        })
            ->where('submissions.enrollment_id', Session::get("enrollment_id"))
            ->whereIn("submissions.id", $subids)
            ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".third_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status", $table_name . ".third_choice_final_status")->get();


        $displayRecords = [];
        if ($type == "update") {
            $rs = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("version", $actual_version)->where("application_id", $application_id)->delete();
            //$rs = SubmissionsLatestFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->delete();
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



                if (!empty($tmp) && $submission->{$choice . "_choice_final_status"} == "Waitlisted") // && $submission->{$choice."_offer_status"} == "Pending")
                {
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
                    } elseif (in_array($tmp['status'], ["No Availability", "Next Round", "Already Offered"])) {
                        $data[$choice . '_waitlist_for'] = $submission->{$choice . '_choice_program_id'};
                        $data[$choice . '_choice_final_status'] = "Waitlisted";
                        $msg = $tmp['msg'] ?? "";
                    }
                }
            }

            if ($type == "update") {
                //  echo $count."-".$submission->id."<BR>";
                $count++;

                // if($submission->id == 3500)
                // {
                //     dd("SS",$data);
                // }

                if (isset($data['second_choice_final_status']) && $data['second_choice_final_status'] == "Offered") {
                    $data['third_choice_final_status'] = 'Pending';
                    $data['first_choice_final_status'] = 'Waitlisted';
                }
                if (isset($data['third_choice_final_status']) && $data['third_choice_final_status'] == "Offered") {
                    $data['second_choice_final_status'] = 'Pending';
                    $data['first_choice_final_status'] = 'Waitlisted';
                }
                if (isset($data['first_choice_final_status']) && $data['first_choice_final_status'] == "Offered") {
                    $data['third_choice_final_status'] = 'Pending';
                    $data['second_choice_final_status'] = 'Pending';
                }
                $data['version'] = $actual_version;

                $rs = LateSubmissionFinalStatus::create($data);


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
        //        echo "" from here pending

        /* Here coding start for Late Submission With Active/Inactive Status */
        $submission_data = [];
        $subids = [];
        $prc_program_id = []; //$program_process_ids;//[];
        if (isset($req['application_program_id'])) {
            foreach ($req['application_program_id'] as $pid) {
                $prc_program_id[] = $pid;
            }
        }

        $choiceArr = ["first", "second", "third"];
        foreach ($choiceArr as $choice) {
            $done_choice[] = $choice;


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
                        ->where("late_submission", 'Y')
                        //                        ->whereIn("id", $subids)
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

                                        $tmpMinMaxStr = "(<strong>Min: " . $schoolAdmData[$school_id][$calculated_race]["min"] . "% Max: " . $schoolAdmData[$school_id][$calculated_race]["max"] . "%</strong>)";

                                        if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 
                                        { //11

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

                                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Main Offered");
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
                                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "NOT IN RANGE");
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
                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission, $display_data, "Not AVailable");
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
                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Already Offered");
                            //$display_data[] = $tmpData;
                        } else if (in_array($submission->$pid_field, $prc_program_id)) {

                            $tmpData = $tmpSubmission;
                            $tmpData['choice'] = $choice;
                            $tmpData['race'] = getCalculatedRace($submission->race);
                            $tmpData['first_choice_program_id'] = $submission->first_choice_program_id;
                            $tmpData['second_choice_program_id'] = $submission->second_choice_program_id;
                            $tmpData['round'] = $round + 1;

                            $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpData, $display_data, "Last Condition");
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

                                                    if ($newP <= $schoolAdmData[$school_id][$calculated_race]["max"]) // $newP >= $schoolAdmData[$school_id][$calculated_race]["min"] && 

                                                    { //1

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
                                                        $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpSubmission1, $display_data, "SKIPPED LOOP");
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
                                                                $display_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updateDisplayData($round + 1, $tmpNew, $display_data, "SKIPPED LOOP");
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
            ->where("late_submission", "Y")
            ->where(function ($q) use ($prc_program_id) {
                $q->whereIn("first_choice_program_id", $prc_program_id)
                    ->orWhereIn("second_choice_program_id", $prc_program_id)
                    ->orWhereIn("third_choice_program_id", $prc_program_id);
            })
            //->whereIn("id", [3284/*,3289*/])
            //->where("next_grade", $grade)
            ->orderBy("lottery_number", "DESC")
            ->get();
        //dd($submissions);
        $displayRecords = [];
        if ($type == "update") {
            //$rs = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->delete();
            //$rs = SubmissionsLatestFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->delete();
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
                $data['version'] = $actual_version;
                $rs = LateSubmissionFinalStatus::create($data);

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

        /*if($group_name == "Academy of Science and Foreign Language - K")
        {
            if($id == 1672)
            {
                echo "<pre>";
                print_r($tmp);
                echo "<pre>";
                print_r($tmp_enroll);
                echo "<Pre>";
                echo $new_percent;exit;
            }
        }*/


        if ($new_percent >= $tmp_enroll[$race]['min'] && $new_percent <= $tmp_enroll[$race]['max']) {
            $in_range = true;
            $max = 0;
            foreach ($tmp as $key => $value) {
                if ($key != $race && $key != "total" && $key != "no_previous") {
                    $total = $tmp['total'] + 1;
                    $new_percent = number_format($value * 100 / $total, 2);
                    if ($new_percent < $tmp_enroll[$key]['min']) {
                        $in_range = false;
                    } elseif ($new_percent > $tmp_enroll[$key]['max']) {
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
            } else {
                return true;
            }
        } else {
            if ($original_race_percent < $tmp_enroll[$race]['min'])
                return true;
            else
                return false;
        }
    }


    public function get_additional_info($application_id = 0, $version = 0)
    {
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->first();

        $display_outcome = 0;
        $displayother = 0;

        if (!empty($process_selection)) {
            $displayother = 1;

            if ($process_selection->commited == "Yes") {
                $display_outcome = 1;
                $last_date_online_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_online_acceptance));
                $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_offline_acceptance));
            } else {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";
            }
        } else {
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        return array("display_outcome" => $display_outcome, "displayother" => $displayother, "last_date_online_acceptance" => $last_date_online_acceptance, "last_date_offline_acceptance" => $last_date_offline_acceptance);
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


    public function selection_accept(Request $request, $application_id)
    {

        $form_id = 1;
        $district_id = \Session('district_id');

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("commited", "No")->where("type", "late_submission")->orderBy("created_at", "DESC")->first();
        $update_id = $rs->id;
        $version = $rs->version;

        $data = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();
        foreach ($data as $key => $value) {
            $status = $value->first_choice_final_status;
            if ($value->second_choice_final_status == "Offered")
                $status = "Offered";

            if ($value->first_choice_final_status == "Pending")
                $status = $value->second_choice_final_status;

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
                } else {
                    $program_name = "";
                }

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
            $rs = SubmissionsStatusLog::create(array("submission_id" => $submission_id, "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id, "comment" => "Waitlist Process :: " . $comment));
            $rs = LateSubmissionsStatusUniqueLog::updateOrCreate(["submission_id" => $submission_id], array("submission_id" => $submission_id, "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id, "version" => $version));
            $rs = Submissions::where("id", $submission_id)->update(["submission_status" => $status]);
        }

        $rs = SubmissionsTmpFinalStatus::get();
        foreach ($rs as $key => $value) {
            $rs1 = Submissions::where("id", $value->submission_id)->first();
            $var = $value->choice_type . "_choice_program_id";
            $program_id = $rs1->{$var};
            if ($value->offer_slug != '')
                $rsupdate = SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $value->submission_id], array("submission_id" => $value->submission_id,  "application_id" => $application_id, "enrollment_id" => Session::get("enrollment_id"), $value->choice_type . "_choice_final_status" => $value->status, $value->choice_type . "_choice_eligibility_reason" => $value->reason, $value->choice_type . "_waitlist_for" => $program_id, "offer_slug" => $value->offer_slug));
            else
                $rsupdate = SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $value->submission_id], array("submission_id" => $value->submission_id, "application_id" => $application_id, "enrollment_id" => Session::get("enrollment_id"), $value->choice_type . "_choice_final_status" => $value->status, $value->choice_type . "_choice_eligibility_reason" => $value->reason,  $value->choice_type . "_waitlist_for" => $program_id,));
        }
        $rs = ProcessSelection::where("id", $update_id)->update(array("commited" => "Yes"));
        echo "Done";
        exit;
    }

    public function checkWailistOpen()
    {
        $rs = LateSubmissionProcessLogs::where("last_date_online", ">", date("Y-m-d H:i:s"))->first();
        if (!empty($rs))
            return 1;
        else
            return 0;
    }


    public function selection_revert()
    {
        $version = $this->checkWailistOpen();
        $quotations = LateSubmissionsStatusUniqueLog::orderBy('created_at', 'ASC')->where("version", $version)
            ->get()
            ->unique('submission_id');

        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_late_submission_online_acceptance")->delete();
        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_late_submission_offline_acceptance")->delete();


        foreach ($quotations as $key => $value) {
            $rs = Submissions::where("id", $value->submission_id)->update(array("submission_status" => $value->old_status));
        }
        LateSubmissionsStatusUniqueLog::where("version", $version)->delete();
        LateSubmissionFinalStatus::where("version", $version)->delete();
        LateSubmissionProcessLogs::where("version", $version)->delete();
        LateSubmissionAvailabilityLog::truncate();
        LateSubmissionAvailabilityProcessLog::where("version", $version)->where("type", "Late Submission")->delete();
        //SubmissionsStatusUniquesLog::truncate();

    }


    public function get_offer_count($program_id, $grade, $district_id, $form_id)
    {
        $offer_count = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


        $offer_count1 = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->count();

        $offer_count2 = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")->count();
        return $offer_count + $offer_count1 + $offer_count2;
    }
}
