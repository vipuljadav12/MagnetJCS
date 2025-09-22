<?php

namespace App\Modules\Reports\Controllers;

use App\Modules\School\Models\School;
use App\Modules\District\Models\District;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\ProgramGradeMapping;

use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\Program\Models\Program;
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\SetEligibility\Models\SetEligibilityConfiguration;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionsStatusUniqueLog, LateSubmissionFinalStatus, SubmissionRecommendation, SubmissionData, SubmissionCommitteeScore, SubmissionTestScore, SubmissionInterviewScore, SubmissionAcademicGradeCalculation, SubmissionCompositeScore, SubmissionsRaceCompositionReport, SubmissionsSelectionReportMaster, SubmissionsStatusLog};
use App\Modules\Waitlist\Models\{WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog, WaitlistIndividualAvailability};
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs, LateSubmissionAvailabilityLog, LateSubmissionAvailabilityProcessLog, LateSubmissionIndividualAvailability};
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Reports\Export\{SubmissionExport, MissingGradesExport, MissingCDIExport, GradeImport, CDIImport, MissingRecommendationExport, MissingWritingPromptExport, MissingCommitteeScoreExport, CommitteeScoreImport, MissingEligibilityExport, MissingCommitteeScoreErrorExport, CourtReportExport};
use App\Modules\Eligibility\Models\SubjectManagement;
use App\Modules\SetAvailability\Models\{Availability, WaitlistAvailability, LateSubmissionAvailability};
use Maatwebsite\Excel\HeadingRowImport;
use App\Traits\AuditTrail;
use Illuminate\Support\Str;
use App\Modules\ProcessSelection\Models\ProcessSelection;
use App\Modules\Import\Models\AgtToNch;
use App\Modules\WritingPrompt\Models\WritingPrompt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{
    use AuditTrail;

    public $eligibility_grade_pass = array();
    public $academic_year_arr = array();
    public $group_racial_composition = array();
    public $program_group = array();
    public $submission;

    public function selectionReport($enrollment_id, $application_id)
    {

        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if ($application_data->preliminary_processing == "Y")
            $preliminary_score = true;

        $process_selecton = ProcessSelection::where("application_id", $application_id)->where("commited", "Yes")->first();
        if (!empty($process_selecton)) {
            $data = SubmissionsSelectionReportMaster::where("application_id", $application_id)->where("type", "regular")->get();
            $group_racial_composition = [];
            $rs = SubmissionsRaceCompositionReport::where("application_id", $application_id)->where("type", "regular")->get();
            foreach ($rs as $k => $v) {
                $tmp = [];
                $tmp['program_group'] = $v->program_group;
                $tmp['black'] = $v->black;
                $tmp['white'] = $v->white;
                $tmp['other'] = $v->other;
                $tmp['total'] = $v->black + $v->white + $v->other;
                $group_racial_composition[] = $tmp;
            }
        } else {
            $data = [];
            $group_racial_composition = [];
        }
        return view("Reports::selection_report_master", compact("data", "application_id", "preliminary_score", "group_racial_composition"));
    }


    public function selectionReportVersion($enrollment_id, $application_id, $version = 0, $type)
    {
        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if ($application_data->preliminary_processing == "Y")
            $preliminary_score = true;

        $process_selecton = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", $type)->where("version", $version)->where("commited", "Yes")->first();
        if (!empty($process_selecton)) {
            $data = SubmissionsSelectionReportMaster::where("application_id", $application_id)->where("type", $type)->where("version", $version)->get();
            $group_racial_composition = [];
            $rs = SubmissionsRaceCompositionReport::where("application_id", $application_id)->where("type", $type)->where("version", $version)->get();
            foreach ($rs as $k => $v) {
                $tmp = [];
                $tmp['program_group'] = $v->program_group;
                $tmp['black'] = $v->black;
                $tmp['white'] = $v->white;
                $tmp['other'] = $v->other;
                $tmp['total'] = $v->black + $v->white + $v->other;
                $group_racial_composition[] = $tmp;
            }
        } else {
            $data = [];
            $group_racial_composition = [];
        }
        return view("Reports::selection_report_master", compact("data", "application_id", "preliminary_score", "group_racial_composition"));
    }

    public function late_submission_index($grade = 0)
    {
        $settings = DB::table("reports_hide_option")->first();

        $grade_data = Submissions::distinct()->where('next_grade', '<>', '')->orderBy('next_grade', 'DESC')->get(["next_grade"]);
        $gradeArr = array("K", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");
        $fgradeTab = [];
        foreach ($grade_data as $key => $value) {
            $fgradeTab[] = $value->next_grade;
        }
        $gradeTab = [];
        foreach ($gradeArr as $key => $value) {
            if (in_array($value, $fgradeTab))
                $gradeTab[] = $value;
        }

        if ($grade == 0)
            $existGrade = $gradeTab[0];
        else
            $existGrade = $grade;



        $rs = LateSubmissionProcessLogs::count();
        $version = $rs + 1;

        $rsWt = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->count();
        if ($rsWt > 0) {
            $id = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "DESC")->first()->version;
        } else {
            $id = 0;
        }

        $availabilityArray = array();
        $parray = $garray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $offer_count = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($value, $gvalue) {
                    $q->where(function ($q1)  use ($value, $gvalue) {
                        $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    })->orWhere(function ($q1) use ($value, $gvalue) {
                        $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    });
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


                $rs = WaitlistAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $garray[] = $gvalue->grade;
                    $parray[] = $value->program_id;
                    $wt_count = $rs->withdrawn_seats;
                    //$availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $rs->withdrawn_seats - $offer_count;
                } else {
                    $wt_count = 0;
                    //$availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats - $offer_count;
                }

                $rs = LateSubmissionAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $garray[] = $gvalue->grade;
                    $parray[] = $value->program_id;
                    $lt_count = $rs->withdrawn_seats;
                } else {
                    $lt_count = 0;
                }
                $c[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $wt_count + $lt_count - $offer_count;
            }
        }

        LateSubmissionFinalStatus::where("version", $version)->delete();

        $firstData = Submissions::distinct()->get(["first_choice"]);

        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
        */
        $subjects = $terms = array();
        $eligibilityArr = array();
        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            if ($content->scoring->type == "DD") {
                                $tmp = array();

                                foreach ($content->subjects as $value) {
                                    if (!in_array($value, $subjects)) {
                                        $subjects[] = $value;
                                    }
                                }

                                foreach ($content->terms_calc as $value) {
                                    if (!in_array($value, $terms)) {
                                        $terms[] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $secondData = Submissions::distinct()->get(["second_choice"]);
        foreach ($secondData as $value) {
            if ($value->second_choice != "") {
                $eligibilityData = getEligibilities($value->second_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    if (!empty($content)) {
                        if ($content->scoring->type == "DD") {
                            $tmp = array();

                            foreach ($content->subjects as $value) {
                                if (!in_array($value, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            foreach ($content->terms_calc as $value) {
                                if (!in_array($value, $terms)) {
                                    $terms[] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 3);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue})) {
                            $setEligibilityData[$value->first_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 3);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue})) {
                            $setEligibilityData[$value->second_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*   else
                            $setEligibilityData[$value->second_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }
        }


        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->first_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->first_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->first_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->first_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->first_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->first_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->second_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->second_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->second_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->second_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->second_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->second_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }

        if ($id == 0) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->where('next_grade', $existGrade)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_final_status.first_offer_status", "submissions_final_status.second_offer_status")
                ->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->where('next_grade', $existGrade)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_waitlist_final_status.first_offer_status", "submissions_waitlist_final_status.second_offer_status")
                ->get();
        }



        $decWtArry = array();
        $firstdata = $seconddata = array();

        foreach ($submissions as $key => $value) {
            $failed = false;
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgram($value->first_choice_program_id, 'Academic Grade Calculation');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgram($value->second_choice_program_id, 'Academic Grade Calculation');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (!in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReport($value, $subjects, $terms, $value->next_grade, $skip, $setEligibilityData);
            if (count($score) <= 0) {
                $failed = true;
                $score = array();
                foreach ($subjects as $svalue) {
                    foreach ($terms as $svalue1) {
                        $score[$svalue][$svalue1] = "";
                    }
                }
            }

            if ($skip) {
                $cdiArr = array();
                $cdiArr['b_info'] = "NA";
                $cdiArr['c_info'] = "NA";
                $cdiArr['d_info'] = "NA";
                $cdiArr['e_info'] = "NA";
                $cdiArr['susp'] = "NA";
                $cdiArr['susp_days'] = "NA";
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = $cdi_data->b_info;
                    $cdiArr['c_info'] = $cdi_data->c_info;
                    $cdiArr['d_info'] = $cdi_data->d_info;
                    $cdiArr['e_info'] = $cdi_data->e_info;
                    $cdiArr['susp'] = $cdi_data->susp;
                    $cdiArr['susp_days'] = $cdi_data->susp_days;
                } elseif ($value->cdi_override == "Y") {
                    $cdiArr = array();
                    $cdiArr['b_info'] = 0;
                    $cdiArr['c_info'] = 0;
                    $cdiArr['d_info'] = 0;
                    $cdiArr['e_info'] = 0;
                    $cdiArr['susp'] = 0;
                    $cdiArr['susp_days'] = 0;
                } else {
                    $failed = true;
                    $cdiArr = array();
                    $cdiArr['b_info'] = "";
                    $cdiArr['c_info'] = "";
                    $cdiArr['d_info'] = "";
                    $cdiArr['e_info'] = "";
                    $cdiArr['susp'] = "";
                    $cdiArr['susp_days'] = "";
                }
            }
            if ($value->first_choice != "" && $value->second_choice != "") {

                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                }

                $choice = getApplicationProgramName($value->first_choice);
                $tmp['late_submission'] = "No";
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;

                $tmp['second_program'] = "";
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                }


                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['late_submission'] = "No";
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                }
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
            } else {
                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['late_submission'] = "No";
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                }
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            }
        }

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        if (count($firstdata) > 0) {
            $final_first_data = $firstdata;
        } else {
            $final_first_data = array();
        }
        if (count($seconddata) > 0) {
            $final_second_data = $seconddata;
        } else {
            $final_second_data = array();
        }





        /* Code for Late Submissions */
        $firstdata = $seconddata = array();

        // $subjects = $terms = array();
        $eligibilityArr = array();

        $firstData = Submissions::distinct()->where("late_submission", "Y")->get(["first_choice"]);
        //print_r($parray);
        //            $secondData = Submissions::distinct()->where("late_submission", "Y")->whereIn("second_choice_program_id", $parray)->whereIn("next_grade", $garray)->get(["first_choice"]);

        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                //echo "FC".$value->first_choice."<BR>";
                $eligibilityData = getEligibilitiesLateSubmission($value->first_choice, 'Academic Grade Calculation');

                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        //      print_r($content);
                        //     echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";
                        if (!empty($content)) {
                            if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                                $tmp = array();

                                foreach ($content->subjects as $svalue) {
                                    if (!in_array($svalue, $subjects)) {
                                        $subjects[] = $svalue;
                                    }
                                }

                                $str = "";
                                foreach ($content->terms_calc as $svalue) {
                                    $str .= $svalue . "-";
                                }
                                $str = trim($str, "-");

                                if (!in_array($str, $terms)) {
                                    $terms[] = $str;
                                }
                            }
                        }
                    }
                }
            }
        }

        $secondData = Submissions::distinct()->where("late_submission", "Y")->get(["second_choice"]);
        foreach ($secondData as $value) {
            if ($value->second_choice != "") {
                $eligibilityData = getEligibilitiesLateSubmission($value->second_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    //print_r($content);
                    //echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";

                    if (!empty($content)) {
                        if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            $str = "";
                            foreach ($content->terms_calc as $svalue) {
                                $str .= $svalue . "-";
                            }
                            $str = trim($str, "-");
                            if (!in_array($str, $terms)) {
                                $terms[] = $str;
                            }
                        }
                    }
                }
            }
        }
        //exit;
        //print_r($parray);exit;

        /* Get Set Eligibility Data Set for first choice program and second choice program
             */

        $setEligibilityData1 = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData1))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 3);
                foreach ($subjects as $svalue) {
                    $str = "";
                    foreach ($terms as $tvalue) {
                        if ($tvalue != "Q4.4 Final Grade") {
                            $str .= $tvalue . "-";
                        }
                        /*                        else
                                $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                    $str = trim($str, "-");
                    $str = str_replace(" Qtr Grade", "", $str);


                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData1[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData1))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 3);
                foreach ($subjects as $svalue) {
                    $str = "";
                    if ($tvalue != "Q4.4 Final Grade") {
                        $str .= $tvalue . "-";
                    }

                    $str = trim($str, "-");
                    $str = str_replace(" Qtr Grade", "", $str);

                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData1[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }
        //print_r($setEligibilityData1);exit;

        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
             */

        $setCDIEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->first_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->first_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->first_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->first_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->first_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->first_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->second_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->second_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->second_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->second_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->second_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->second_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        /* Get CDI Data */
        if ($version == 1) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where("late_submission", "Y")->where('next_grade', $existGrade)->select("submissions.*")->where('submission_status', '<>', 'Application Withdrawn')
                ->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where("late_submission", "Y")->where('next_grade', $existGrade)->join("late_submission_final_status", "late_submission_final_status.submission_id", "submissions.id")->select("submissions.*", "late_submission_final_status.first_offer_status", "late_submission_final_status.second_offer_status")->where('submission_status', '<>', 'Application Withdrawn')->where("version", $version)->get();
        }

        /* $submissions=Submissions::
                where('submissions.district_id', Session::get('district_id'))
                ->where("late_submission", "Y")
                ->where("next_grade", $existGrade)
                ->where('submission_status', '<>', 'Application Withdrawn')
                //            ->limit(5)
                ->get();*/

        $firstdata = $seconddata = array();
        $programGrades = array();
        foreach ($submissions as $key => $value) {
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgramLateSubmission($value->first_choice_program_id, 'Academic Grade Calculation');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgramLateSubmission($value->second_choice_program_id, 'Academic Grade Calculation');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (!in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReportLateSubmission($value, $subjects, $terms, $value->next_grade, $skip, $setEligibilityData1);
            //print_r($terms);exit;
            if (count($score) <= 0) {
                $failed = true;
                $score = array();
                foreach ($subjects as $svalue) {
                    foreach ($terms as $svalue1) {
                        $score[$svalue][$svalue1] = "";
                    }
                }
            }

            if ($skip) {
                $cdiArr = array();
                $cdiArr['b_info'] = "NA";
                $cdiArr['c_info'] = "NA";
                $cdiArr['d_info'] = "NA";
                $cdiArr['e_info'] = "NA";
                $cdiArr['susp'] = "NA";
                $cdiArr['susp_days'] = "NA";
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = $cdi_data->b_info;
                    $cdiArr['c_info'] = $cdi_data->c_info;
                    $cdiArr['d_info'] = $cdi_data->d_info;
                    $cdiArr['e_info'] = $cdi_data->e_info;
                    $cdiArr['susp'] = $cdi_data->susp;
                    $cdiArr['susp_days'] = $cdi_data->susp_days;
                } elseif ($value->cdi_override == "Y") {
                    $cdiArr = array();
                    $cdiArr['b_info'] = 0;
                    $cdiArr['c_info'] = 0;
                    $cdiArr['d_info'] = 0;
                    $cdiArr['e_info'] = 0;
                    $cdiArr['susp'] = 0;
                    $cdiArr['susp_days'] = 0;
                } else {
                    $failed = true;
                    $cdiArr = array();
                    $cdiArr['b_info'] = "";
                    $cdiArr['c_info'] = "";
                    $cdiArr['d_info'] = "";
                    $cdiArr['e_info'] = "";
                    $cdiArr['susp'] = "";
                    $cdiArr['susp_days'] = "";
                }
            }
            if ($value->first_choice != "" && $value->second_choice != "") {

                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                }

                $choice = getApplicationProgramName($value->first_choice);
                $tmp['late_submission'] = "Yes";
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;

                $tmp['second_program'] = "";
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if (isset($this->eligibility_grade_pass[$value->id]['first']) && $this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $firstdata[] = $tmp;
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                }


                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if (isset($this->eligibility_grade_pass[$value->id]['second']) && $this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                $seconddata[] = $tmp;
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['late_submission'] = "Yes";
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if (isset($this->eligibility_grade_pass[$value->id]['first']) && $this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                }
                $firstdata[] = $tmp;
            } else {
                $tmp = $this->convertToArray($value);
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['late_submission'] = "Yes";
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if (isset($this->eligibility_grade_pass[$value->id]['second']) && $this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                if ($value->cdi_override == "Y")
                    $tmp['cdi_status'] = "Pass";
                else {
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                }
                $seconddata[] = $tmp;
            }
        }

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }
        $final_first_data = array_merge($final_first_data, $firstdata);
        $final_second_data = array_merge($final_second_data, $seconddata);

        $firstdata = $final_first_data;
        $seconddata = $final_second_data;

        if (str_contains(request()->url(), '/export')) {
            return $this->exportSubmissions($firstdata, $seconddata, $subjects, $terms);
        } else {
            return view("Reports::late_submission_index", compact("firstdata", "seconddata", "existGrade", "gradeTab", "subjects", "terms", "setEligibilityData", "setCDIEligibilityData", "settings"));
        }
    }

    public function processingLogsReport()
    {
        $process_selecton = ProcessSelection::orderBy("created_at", "desc")->where("enrollment_id", Session::get("enrollment_id"))->where("commited", "Yes")->get();

        $regular = $waitlist = $late_submission = [];
        foreach ($process_selecton as $key => $value) {
            if ($value->type == "regular")
                $regular[] = $value;
            elseif ($value->type == "waitlist")
                $waitlist[] = $value;
            elseif ($value->type == "late_submission")
                $late_submission[] = $value;
        }
        return view("Reports::log_index", compact("regular", "waitlist", "late_submission"));
    }

    public function processingRealLogsReport()
    {
        $process_selecton = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "desc")->first();
        $versions_lists = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "desc")->first();
        $late_lists = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "desc")->first();

        if (!empty($late_lists)) {
            return redirect(url('/admin/LateSubmission/SeatsStatus/Version/' . $late_lists->version));
        } elseif (!empty($versions_lists)) {
            return redirect(url('/admin/Waitlist/SeatsStatus/Version/' . $versions_lists->version));
        } else {
            return redirect(url('/admin/Reports/missing/' . $process_selecton->enrollment_id . '/seatstatus'));
        }
        //return view("Reports::real_log_index",compact("versions_lists", "process_selecton", "late_lists"));
    }

    public function generateWaitlistStatus()
    {
        $rs = WaitlistProcessLogs::count();
        $version = $rs + 1;

        $availabilityArray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $offer_count = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($value, $gvalue) {
                    $q->where(function ($q1)  use ($value, $gvalue) {
                        $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    })->orWhere(function ($q1) use ($value, $gvalue) {
                        $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    });
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


                $rs = WaitlistAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $rs->withdrawn_seats - $offer_count;
                } else {
                    $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats - $offer_count;
                }
            }
        }

        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
            })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_final_status.first_offer_status", "submissions_final_status.second_offer_status")
            ->get();

        $decWtArry = array();
        foreach ($submissions as $key => $value) {
            if ($value->first_choice != "" && $value->second_choice != "") {

                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }

                $tmp['rank'] = $this->priorityCalculate($value, "second");
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
            } else {
                $tmp = $this->convertToArray($value);
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            }
        }

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0) {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $firstOffered[] = $value['id'];
                    if (isset($offeredRank[$value['first_choice_program_id']])) {
                        $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['first_choice_program_id']] = 1;
                    }

                    $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));
                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['first_choice_program_id']])) {
                    $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['first_choice_program_id']] = 1;
                }
                $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
            }
        }

        foreach ($seconddata as $key => $value) {

            if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)) {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                    if (isset($offeredRank[$value['second_choice_program_id']])) {
                        $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['second_choice_program_id']] = 1;
                    }
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['second_choice_program_id']])) {
                    $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['second_choice_program_id']] = 1;
                }
                $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
            }
        }
        echo "Done";
    }

    /* Late Submission Individual Submissions */
    public function generateLateSubmissionIndividualStatus()
    {
        $rs = LateSubmissionProcessLogs::count();
        $version = $rs + 1;

        $rsWt = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->count();
        if ($rsWt > 0) {
            $id = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "DESC")->first()->version;
        } else {
            $id = 0;
        }

        $availabilityArray = array();
        $parray = $garray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $offer_count = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($value, $gvalue) {
                    $q->where(function ($q1)  use ($value, $gvalue) {
                        $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    })->orWhere(function ($q1) use ($value, $gvalue) {
                        $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    });
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


                $rs = WaitlistAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $garray[] = $gvalue->grade;
                    $parray[] = $value->program_id;
                    $wt_count = $rs->withdrawn_seats;
                    //$availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $rs->withdrawn_seats - $offer_count;
                } else {
                    $wt_count = 0;
                    //$availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats - $offer_count;
                }

                $rs = LateSubmissionAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $garray[] = $gvalue->grade;
                    $parray[] = $value->program_id;
                    $lt_count = $rs->withdrawn_seats;
                } else {
                    $lt_count = 0;
                }
                $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $wt_count + $lt_count - $offer_count;
            }
        }

        LateSubmissionFinalStatus::where("version", $version)->delete();

        if ($id == 0) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->where(function ($q) use ($parray) {
                    $q->whereIn("first_choice_program_id", $parray)->orWhereIn("second_choice_program_id", $parray);
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_final_status.first_offer_status", "submissions_final_status.second_offer_status")
                ->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->where(function ($q) use ($parray) {
                    $q->whereIn("first_choice_program_id", $parray)->orWhereIn("second_choice_program_id", $parray);
                })->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_waitlist_final_status.first_offer_status", "submissions_waitlist_final_status.second_offer_status")
                ->get();
        }
        $decWtArry = array();
        foreach ($submissions as $key => $value) {
            if (in_array($value->next_grade, $garray)) {
                if ($value->first_choice != "" && $value->second_choice != "") {

                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['second_program'] = "";
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->first_offer_status != "Declined & Waitlisted") {
                        $firstdata[] = $tmp;
                    }

                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_program'] = "";
                    if ($value->second_offer_status != "Declined & Waitlisted") {
                        $seconddata[] = $tmp;
                    }
                } elseif ($value->first_choice != "") {
                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_program'] = "";
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->first_offer_status != "Declined & Waitlisted") {
                        $firstdata[] = $tmp;
                    }
                } else {
                    $tmp = $this->convertToArray($value);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['first_program'] = "";
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($value->second_offer_status != "Declined & Waitlisted") {
                        $seconddata[] = $tmp;
                    }
                }
            }
        }

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }


        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0 && in_array($value['first_choice_program_id'], $parray)) {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $firstOffered[] = $value['id'];
                    if (isset($offeredRank[$value['first_choice_program_id']])) {
                        $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['first_choice_program_id']] = 1;
                    }

                    $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['first_choice_program_id']])) {
                    $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['first_choice_program_id']] = 1;
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
            }
        }

        foreach ($seconddata as $key => $value) {

            if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)  && in_array($value['second_choice_program_id'], $parray)) {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                    if (isset($offeredRank[$value['second_choice_program_id']])) {
                        $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['second_choice_program_id']] = 1;
                    }
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['second_choice_program_id']])) {
                    $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['second_choice_program_id']] = 1;
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
            }
        }


        /* Code for Late Submissions */
        $firstdata = $seconddata = array();

        $subjects = $terms = array();
        $eligibilityArr = array();

        $firstData = Submissions::distinct()->where("late_submission", "Y")->whereIn("first_choice_program_id", $parray)->whereIn("next_grade", $garray)->get(["first_choice"]);
        //print_r($parray);
        //            $secondData = Submissions::distinct()->where("late_submission", "Y")->whereIn("second_choice_program_id", $parray)->whereIn("next_grade", $garray)->get(["first_choice"]);

        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                //echo "FC".$value->first_choice."<BR>";
                $eligibilityData = getEligibilitiesLateSubmission($value->first_choice, 'Academic Grade Calculation');

                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        //      print_r($content);
                        //     echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";
                        if (!empty($content)) {
                            if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                                $tmp = array();

                                foreach ($content->subjects as $svalue) {
                                    if (!in_array($svalue, $subjects)) {
                                        $subjects[] = $svalue;
                                    }
                                }

                                $str = "";
                                foreach ($content->terms_calc as $svalue) {
                                    $str .= $svalue . "-";
                                }
                                $str = trim($str, "-");

                                if (!in_array($str, $terms)) {
                                    $terms[] = $str;
                                }
                            }
                        }
                    }
                }
            }
        }

        $secondData = Submissions::distinct()->where("late_submission", "Y")->whereIn("second_choice_program_id", $parray)->whereIn("next_grade", $garray)->get(["second_choice"]);
        foreach ($secondData as $value) {
            if ($value->second_choice != "") {
                $eligibilityData = getEligibilitiesLateSubmission($value->second_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    //print_r($content);
                    //echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";

                    if (!empty($content)) {
                        if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            $str = "";
                            foreach ($content->terms_calc as $svalue) {
                                $str .= $svalue . "-";
                            }
                            $str = trim($str, "-");
                            if (!in_array($str, $terms)) {
                                $terms[] = $str;
                            }
                        }
                    }
                }
            }
        }
        //exit;
        //print_r($parray);exit;

        /* Get Set Eligibility Data Set for first choice program and second choice program
             */

        $setEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 3);
                foreach ($subjects as $svalue) {
                    $str = "";
                    foreach ($terms as $tvalue) {
                        $str .= $tvalue . "-";
                        /*                        else
                                $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                    $str = trim($str, "-");
                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 3);
                foreach ($subjects as $svalue) {
                    $str = "";
                    foreach ($terms as $tvalue) {
                        $str .= $tvalue . "-";
                        /*                        else
                                $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                    $str = trim($str, "-");
                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }

        //print_r($terms);exit;

        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
             */

        $setCDIEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->first_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->first_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->first_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->first_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->first_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->first_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->second_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->second_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->second_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->second_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->second_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->second_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        /* Get CDI Data */
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where("late_submission", "Y")
            ->where(function ($q) use ($parray) {
                $q->whereIn("first_choice_program_id", $parray)->orWhereIn("second_choice_program_id", $parray);
            })
            ->whereIn("next_grade", $garray)
            ->where('submission_status', '<>', 'Application Withdrawn')
            //            ->limit(5)
            ->get();

        $firstdata = $seconddata = array();
        $programGrades = array();
        foreach ($submissions as $key => $value) {
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgramLateSubmission($value->first_choice_program_id, 'Academic Grade Calculation');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgramLateSubmission($value->second_choice_program_id, 'Academic Grade Calculation');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (!in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReportLateSubmission($value, $subjects, $terms, $value->next_grade, $skip, $setEligibilityData);

            if (count($score) > 0) {

                if ($skip) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = "NA";
                    $cdiArr['c_info'] = "NA";
                    $cdiArr['d_info'] = "NA";
                    $cdiArr['e_info'] = "NA";
                    $cdiArr['susp'] = "NA";
                    $cdiArr['susp_days'] = "NA";
                } else {
                    $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                    if (!empty($cdi_data)) {
                        $cdiArr = array();
                        $cdiArr['b_info'] = $cdi_data->b_info;
                        $cdiArr['c_info'] = $cdi_data->c_info;
                        $cdiArr['d_info'] = $cdi_data->d_info;
                        $cdiArr['e_info'] = $cdi_data->e_info;
                        $cdiArr['susp'] = $cdi_data->susp;
                        $cdiArr['susp_days'] = $cdi_data->susp_days;
                    } elseif ($value->cdi_override == "Y") {
                        $cdiArr = array();
                        $cdiArr['b_info'] = 0;
                        $cdiArr['c_info'] = 0;
                        $cdiArr['d_info'] = 0;
                        $cdiArr['e_info'] = 0;
                        $cdiArr['susp'] = 0;
                        $cdiArr['susp_days'] = 0;
                    } else {
                        $incomplete_reason = "CDI";
                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value->id, "version" => $version], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason, "version" => $version]);
                        continue;
                    }
                }
                if ($value->first_choice != "" && $value->second_choice != "") {

                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else {
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;

                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    $seconddata[] = $tmp;
                } elseif ($value->first_choice != "") {
                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;
                } else {
                    $tmp = $this->convertToArray($value);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $seconddata[] = $tmp;
                }
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $incomplete_reason = "Grade";
                } else {
                    $incomplete_reason = "Both";
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value->id, "version" => $version], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason, "version" => $version]);
            }
        }
        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                    if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0) {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $firstOffered[] = $value['id'];
                        if (isset($offeredRank[$value['first_choice_program_id']])) {
                            $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['first_choice_program_id']] = 1;
                        }

                        $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                        do {
                            $code = Str::random(10);
                            $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));
                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                    } else {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['first_choice_program_id']])) {
                            $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['first_choice_program_id']] = 1;
                        }

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                    }
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Eligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $first_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $first_choice_eligibility_reason = "CDI";
                } else {
                    $first_choice_eligibility_reason = "Grade";
                }

                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Denied due to Ineligibility", "first_waitlist_for" => $value['first_choice_program_id'], "first_choice_eligibility_reason" => $first_choice_eligibility_reason, "version" => $version]);
            }
        }

        foreach ($seconddata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                    if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)) {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                        if (isset($offeredRank[$value['second_choice_program_id']])) {
                            $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['second_choice_program_id']] = 1;
                        }
                        do {
                            $code = Str::random(10);
                            $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                    } else {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['second_choice_program_id']])) {
                            $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['second_choice_program_id']] = 1;
                        }

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                    }
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Ineligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $second_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $second_choice_eligibility_reason = "CDI";
                } else {
                    $second_choice_eligibility_reason = "Grade";
                }

                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Denied due to Ineligibility", "second_waitlist_for" => $value['second_choice_program_id'], "second_choice_eligibility_reason" => $second_choice_eligibility_reason, "version" => $version]);
            }
        }
        echo "Done";
    }
    /* Late Submissions Individual Submissions Ends */
    public function generateWaitlistIndividualStatus()
    {
        $rs = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->count();
        $version = $rs + 1;

        $availabilityArray = array();
        $parray = $garray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $offer_count = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($value, $gvalue) {
                    $q->where(function ($q1)  use ($value, $gvalue) {
                        $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    })->orWhere(function ($q1) use ($value, $gvalue) {
                        $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    });
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


                $rs = WaitlistAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $garray[] = $gvalue->grade;
                    $parray[] = $value->program_id;
                    $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $rs->withdrawn_seats - $offer_count;
                } else {
                    $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats - $offer_count;
                }
            }
        }

        SubmissionsWaitlistFinalStatus::where("version", $version)->delete();
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) use ($value, $gvalue) {
                $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
            })->where(function ($q) use ($parray) {
                $q->whereIn("first_choice_program_id", $parray)->orWhereIn("second_choice_program_id", $parray);
            })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_final_status.first_offer_status", "submissions_final_status.second_offer_status")
            ->get();
        $decWtArry = array();


        foreach ($submissions as $key => $value) {
            if (in_array($value->next_grade, $garray)) {
                if ($value->first_choice != "" && $value->second_choice != "") {

                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['second_program'] = "";
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->first_offer_status != "Declined & Waitlisted") {
                        $firstdata[] = $tmp;
                    }

                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_program'] = "";
                    if ($value->second_offer_status != "Declined & Waitlisted") {
                        $seconddata[] = $tmp;
                    }
                } elseif ($value->first_choice != "") {
                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_program'] = "";
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->first_offer_status != "Declined & Waitlisted") {
                        $firstdata[] = $tmp;
                    }
                } else {
                    $tmp = $this->convertToArray($value);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['first_program'] = "";
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($value->second_offer_status != "Declined & Waitlisted") {
                        $seconddata[] = $tmp;
                    }
                }
            }
        }


        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0 && in_array($value['first_choice_program_id'], $parray)) {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $firstOffered[] = $value['id'];
                    if (isset($offeredRank[$value['first_choice_program_id']])) {
                        $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['first_choice_program_id']] = 1;
                    }

                    $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));
                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['first_choice_program_id']])) {
                    $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['first_choice_program_id']] = 1;
                }
                $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
            }
        }

        foreach ($seconddata as $key => $value) {

            if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)  && in_array($value['second_choice_program_id'], $parray)) {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                    if (isset($offeredRank[$value['second_choice_program_id']])) {
                        $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['second_choice_program_id']] = 1;
                    }
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }

                    $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['second_choice_program_id']])) {
                    $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['second_choice_program_id']] = 1;
                }
                $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
            }
        }
        echo "Done";
    }

    /* Code for Late Submissions Process Selection */
    public function late_submission_wailist_calculate()
    {
        $rsWt = WaitlistProcessLogs::count();
        if ($rsWt > 0) {
            $id = WaitlistProcessLogs::orderBy("created_at", "DESC")->first()->version;
        } else {
            $id = 0;
        }
        $firstdata = $seconddata = array();
        if ($id == 0) {

            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where('late_submission', "N")->where(function ($q) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_final_status.first_offer_status", "submissions_final_status.second_offer_status")
                ->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where('late_submission', "N")->where(function ($q) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_waitlist_final_status.first_offer_status", "submissions_waitlist_final_status.second_offer_status")
                ->get();
        }

        $decWtArry = array();
        foreach ($submissions as $key => $value) {
            if ($value->first_choice != "" && $value->second_choice != "") {

                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
            } else {
                $tmp = $this->convertToArray($value);
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            }
        }


        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where('late_submission', "N")->where(function ($q) {
                $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
            })->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->select("submissions.*", "submissions_waitlist_final_status.first_offer_status", "submissions_waitlist_final_status.second_offer_status")
            ->get();

        $decWtArry = array();
        foreach ($submissions as $key => $value) {
            if ($value->first_choice != "" && $value->second_choice != "") {

                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                if ($value->first_offer_status != "Declined & Waitlisted") {
                    $firstdata[] = $tmp;
                }
            } else {
                $tmp = $this->convertToArray($value);
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['org_rank'] = 1;
                $tmp['rank'] = $this->priorityCalculate($value, "second");
                if ($value->second_offer_status != "Declined & Waitlisted") {
                    $seconddata[] = $tmp;
                }
            }
        }
        return array("firstdata" => $firstdata, "seconddata" => $seconddata);
    }

    public function generateLateSubmissionStatus()
    {
        $rs = LateSubmissionProcessLogs::count();
        $version = $rs + 1;

        $rsWt = WaitlistProcessLogs::count();
        if ($rsWt > 0) {
            $id = WaitlistProcessLogs::orderBy("created_at", "DESC")->first()->version;
        } else {
            $id = 0;
        }

        $availabilityArray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $offer_count = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($value, $gvalue) {
                    $q->where(function ($q1)  use ($value, $gvalue) {
                        $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    })->orWhere(function ($q1) use ($value, $gvalue) {
                        $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $value->program_id)->where('next_grade', $gvalue->grade);
                    });
                })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


                $rs = WaitlistAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $wt_count = $rs->withdrawn_seats;
                } else {
                    $wt_count = 0;
                }
                ///---
                $rs = LateSubmissionAvailability::where("program_id", $value->program_id)->where("grade", $gvalue->grade)->first();
                if (!empty($rs)) {
                    $lt_count = $rs->withdrawn_seats;
                } else {
                    $lt_cout = 0;
                }
                $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats + $wt_count + $lt_count - $offer_count;
            }
        }

        //print_r($availabilityArray);exit;
        $tstArray = $this->late_submission_wailist_calculate();
        $firstdata = $tstArray['firstdata'];
        $seconddata = $tstArray['seconddata'];

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0) {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $firstOffered[] = $value['id'];
                    if (isset($offeredRank[$value['first_choice_program_id']])) {
                        $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['first_choice_program_id']] = 1;
                    }

                    $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['first_choice_program_id']])) {
                    $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['first_choice_program_id']] = 1;
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
            }
        }

        foreach ($seconddata as $key => $value) {

            if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)) {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                    $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                    if (isset($offeredRank[$value['second_choice_program_id']])) {
                        $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                    } else {
                        $offeredRank[$value['second_choice_program_id']] = 1;
                    }
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }

                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                if (isset($waitlistArr[$value['second_choice_program_id']])) {
                    $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                } else {
                    $waitlistArr[$value['second_choice_program_id']] = 1;
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
            }
        }

        $firstdata = $seconddata = array();

        $subjects = $terms = array();
        $eligibilityArr = array();
        $firstData = Submissions::distinct()->where("late_submission", "Y")->get(["first_choice"]);
        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                //echo "FC".$value->first_choice."<BR>";
                $eligibilityData = getEligibilitiesLateSubmission($value->first_choice, 'Academic Grade Calculation');

                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        //      print_r($content);
                        //     echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";
                        if (!empty($content)) {
                            if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                                $tmp = array();

                                foreach ($content->subjects as $svalue) {
                                    if (!in_array($svalue, $subjects)) {
                                        $subjects[] = $svalue;
                                    }
                                }

                                $str = "";
                                foreach ($content->terms_calc as $svalue) {
                                    $str .= $svalue . "-";
                                }
                                $str = trim($str, "-");

                                if (!in_array($str, $terms)) {
                                    $terms[] = $str;
                                }
                            }
                        }
                    }
                }
            }
        }

        $secondData = Submissions::distinct()->where("late_submission", "Y")->get(["second_choice"]);
        foreach ($secondData as $value) {
            if ($value->second_choice != "") {
                $eligibilityData = getEligibilitiesLateSubmission($value->second_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    //print_r($content);
                    //echo "<BR><BR><BR>---------------------------------------------------------<BR><BR><BR>";

                    if (!empty($content)) {
                        if ($content->scoring->type == "DD" || $content->scoring->type == "GA") {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            $str = "";
                            foreach ($content->terms_calc as $svalue) {
                                $str .= $svalue . "-";
                            }
                            $str = trim($str, "-");
                            if (!in_array($str, $terms)) {
                                $terms[] = $str;
                            }
                        }
                    }
                }
            }
        }
        //exit;
        //print_r($terms);exit;

        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = array();

        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData))) {

                $data = getSetEligibilityDataDynamic($value->first_choice, 3);
                foreach ($subjects as $svalue) {
                    $str = "";
                    foreach ($terms as $tvalue) {
                        $str .= $tvalue . "-";
                        /*                        else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                    $str = trim($str, "-");
                    $str = str_replace(" Qtr Grade", "", $str);

                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }

        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 3);

                foreach ($subjects as $svalue) {
                    $str = "";
                    foreach ($terms as $tvalue) {
                        $str .= $tvalue . "-";
                        /*                        else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                    $str = trim($str, "-");
                    $str = str_replace(" Qtr Grade", "", $str);

                    if (isset($data->{$svalue . "-" . $str})) {
                        $setEligibilityData[$value->first_choice][$svalue . "-" . $str] = $data->{$svalue . "-" . $str}[0];
                    }
                }
            }
        }

        //print_r($terms);exit;

        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->first_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->first_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->first_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->first_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->first_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->first_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->second_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->second_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->second_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->second_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->second_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->second_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        /* Get CDI Data */
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where("late_submission", "Y")
            ->where('submission_status', '<>', 'Application Withdrawn')
            //->where("id", 3067)
            //            ->limit(5)
            ->get();


        $firstdata = $seconddata = array();
        $programGrades = array();
        foreach ($submissions as $key => $value) {
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgramLateSubmission($value->first_choice_program_id, 'Academic Grade Calculation');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgramLateSubmission($value->second_choice_program_id, 'Academic Grade Calculation');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (!in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReportLateSubmission($value, $subjects, $terms, $value->next_grade, $skip, $setEligibilityData);
            // print_r($this->eligibility_grade_pass);exit;
            if (count($score) > 0) {

                if ($skip) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = "NA";
                    $cdiArr['c_info'] = "NA";
                    $cdiArr['d_info'] = "NA";
                    $cdiArr['e_info'] = "NA";
                    $cdiArr['susp'] = "NA";
                    $cdiArr['susp_days'] = "NA";
                } else {
                    $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                    if (!empty($cdi_data)) {
                        $cdiArr = array();
                        $cdiArr['b_info'] = $cdi_data->b_info;
                        $cdiArr['c_info'] = $cdi_data->c_info;
                        $cdiArr['d_info'] = $cdi_data->d_info;
                        $cdiArr['e_info'] = $cdi_data->e_info;
                        $cdiArr['susp'] = $cdi_data->susp;
                        $cdiArr['susp_days'] = $cdi_data->susp_days;
                    } elseif ($value->cdi_override == "Y") {
                        $cdiArr = array();
                        $cdiArr['b_info'] = 0;
                        $cdiArr['c_info'] = 0;
                        $cdiArr['d_info'] = 0;
                        $cdiArr['e_info'] = 0;
                        $cdiArr['susp'] = 0;
                        $cdiArr['susp_days'] = 0;
                    } else {
                        $incomplete_reason = "CDI";
                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value->id, "version" => $version], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason, "version" => $version]);
                        continue;
                    }
                }
                if ($value->first_choice != "" && $value->second_choice != "") {

                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else {
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;

                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    $seconddata[] = $tmp;
                } elseif ($value->first_choice != "") {
                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;
                } else {
                    $tmp = $this->convertToArray($value);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $seconddata[] = $tmp;
                }
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $incomplete_reason = "Grade";
                } else {
                    $incomplete_reason = "Both";
                }
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value->id, "version" => $version], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason, "version" => $version]);
            }
        }
        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();

        foreach ($firstdata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                    if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0) {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $firstOffered[] = $value['id'];
                        if (isset($offeredRank[$value['first_choice_program_id']])) {
                            $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['first_choice_program_id']] = 1;
                        }

                        $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                        do {
                            $code = Str::random(10);
                            $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));
                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                    } else {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['first_choice_program_id']])) {
                            $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['first_choice_program_id']] = 1;
                        }

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                    }
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']], "version" => $version]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Eligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $first_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $first_choice_eligibility_reason = "CDI";
                } else {
                    $first_choice_eligibility_reason = "Grade";
                }

                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["first_choice_final_status" => "Denied due to Ineligibility", "first_waitlist_for" => $value['first_choice_program_id'], "first_choice_eligibility_reason" => $first_choice_eligibility_reason, "version" => $version, "second_choice_final_status" => "Denied due to Ineligibility", "second_waitlist_for" => $value['second_choice_program_id']]);
            }
        }

        foreach ($seconddata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                    if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)) {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                        if (isset($offeredRank[$value['second_choice_program_id']])) {
                            $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['second_choice_program_id']] = 1;
                        }
                        do {
                            $code = Str::random(10);
                            $user_code = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code, "version" => $version]);
                    } else {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['second_choice_program_id']])) {
                            $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['second_choice_program_id']] = 1;
                        }

                        $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                    }
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']], "version" => $version]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Ineligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $second_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $second_choice_eligibility_reason = "CDI";
                } else {
                    $second_choice_eligibility_reason = "Grade";
                }

                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $version], ["second_choice_final_status" => "Denied due to Ineligibility", "second_waitlist_for" => $value['second_choice_program_id'], "second_choice_eligibility_reason" => $second_choice_eligibility_reason, "version" => $version, "first_choice_final_status" => "Denied due to Ineligibility", "first_waitlist_for" => $value['first_choice_program_id']]);
            }
        }
    }

    /* Code Ends for Late Submission Process Selection */
    public function generateStatus()
    {
        $availabilityArray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats;
            }
        }


        $firstData = Submissions::distinct()->where("late_submission", "N")->get(["first_choice"]);

        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = array();
        $eligibilityArr = array();
        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            if ($content->scoring->type == "DD") {
                                $tmp = array();

                                foreach ($content->subjects as $value) {
                                    if (!in_array($value, $subjects)) {
                                        $subjects[] = $value;
                                    }
                                }

                                foreach ($content->terms_calc as $value) {
                                    if (!in_array($value, $terms)) {
                                        $terms[] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $secondData = Submissions::distinct()->where("late_submission", "N")->get(["second_choice"]);
        foreach ($secondData as $value) {
            if ($value->second_choice != "") {
                $eligibilityData = getEligibilities($value->second_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    if (!empty($content)) {
                        if ($content->scoring->type == "DD") {
                            $tmp = array();

                            foreach ($content->subjects as $value) {
                                if (!in_array($value, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            foreach ($content->terms_calc as $value) {
                                if (!in_array($value, $terms)) {
                                    $terms[] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 3);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue})) {
                            $setEligibilityData[$value->first_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*                        else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 3);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue})) {
                            $setEligibilityData[$value->second_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*   else
                            $setEligibilityData[$value->second_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }
        }


        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->first_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->first_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->first_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->first_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->first_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->first_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCDIEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 8);
                if (!empty($data)) {
                    $setCDIEligibilityData[$value->second_choice]['b_info'] = $data->B[0];
                    $setCDIEligibilityData[$value->second_choice]['c_info'] = $data->C[0];
                    $setCDIEligibilityData[$value->second_choice]['d_info'] = $data->D[0];
                    $setCDIEligibilityData[$value->second_choice]['e_info'] = $data->E[0];
                    $setCDIEligibilityData[$value->second_choice]['susp'] = $data->Susp[0];
                    $setCDIEligibilityData[$value->second_choice]['susp_days'] = $data->SuspDays[0];
                }
            }
        }
        /* Get CDI Data */
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where('submission_status', '<>', 'Application Withdrawn')
            ->where("late_submission", "N")
            //            ->limit(5)
            ->get();


        $firstdata = $seconddata = array();
        $programGrades = array();
        foreach ($submissions as $key => $value) {
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgram($value->first_choice_program_id, 'Academic Grade Calculation');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgram($value->second_choice_program_id, 'Academic Grade Calculation');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (!in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReport($value, $subjects, $terms, $value->next_grade, $skip, $setEligibilityData);
            if (count($score) > 0) {

                if ($skip) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = "NA";
                    $cdiArr['c_info'] = "NA";
                    $cdiArr['d_info'] = "NA";
                    $cdiArr['e_info'] = "NA";
                    $cdiArr['susp'] = "NA";
                    $cdiArr['susp_days'] = "NA";
                } else {
                    $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                    if (!empty($cdi_data)) {
                        $cdiArr = array();
                        $cdiArr['b_info'] = $cdi_data->b_info;
                        $cdiArr['c_info'] = $cdi_data->c_info;
                        $cdiArr['d_info'] = $cdi_data->d_info;
                        $cdiArr['e_info'] = $cdi_data->e_info;
                        $cdiArr['susp'] = $cdi_data->susp;
                        $cdiArr['susp_days'] = $cdi_data->susp_days;
                    } elseif ($value->cdi_override == "Y") {
                        $cdiArr = array();
                        $cdiArr['b_info'] = 0;
                        $cdiArr['c_info'] = 0;
                        $cdiArr['d_info'] = 0;
                        $cdiArr['e_info'] = 0;
                        $cdiArr['susp'] = 0;
                        $cdiArr['susp_days'] = 0;
                    } else {
                        $incomplete_reason = "CDI";
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value->id], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason]);
                        continue;
                    }
                }
                if ($value->first_choice != "" && $value->second_choice != "") {

                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else {
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;

                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['cdi'] = $cdiArr;
                    $seconddata[] = $tmp;
                } elseif ($value->first_choice != "") {
                    $tmp = $this->convertToArray($value);
                    $choice = getApplicationProgramName($value->first_choice);
                    $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['second_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "first");
                    if ($value->cdi_override == "Y")
                        $tmp['cdi_status'] = "Pass";
                    else
                        $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->first_choice);
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $firstdata[] = $tmp;
                } else {
                    $tmp = $this->convertToArray($value);
                    $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                    $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                    $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                    $tmp['first_program'] = "";
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $tmp['cdi'] = $cdiArr;
                    $tmp['rank'] = $this->priorityCalculate($value, "second");
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                    $tmp['cdi_status'] = $this->checkCDIStatus($setCDIEligibilityData, $cdiArr, $value->second_choice);
                    $seconddata[] = $tmp;
                }
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $incomplete_reason = "Grade";
                } else {
                    $incomplete_reason = "Both";
                }
                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value->id], ["first_choice_final_status" => "Denied Due To Incomplete Records", "first_offered_rank" => 0, "first_waitlist_for" => $value['first_choice_program_id'], "second_choice_final_status" => "Denied Due To Incomplete Records", "second_offered_rank" => 0, "second_waitlist_for" => $value['second_choice_program_id'], 'incomplete_reason' => $incomplete_reason]);
            }
        }

        if (!empty($firstdata)) {
            $f_siblings = $s_siblings = $f_lottery_numbers = $s_lottery_numbers = $f_status = $s_status = array();
            foreach ($firstdata as $key => $value) {
                $f_siblings['rank'][] = $value['rank'];
                $f_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($f_siblings['rank'], SORT_ASC, $f_lottery_numbers['lottery_number'], SORT_DESC, $firstdata);
        }

        if (!empty($seconddata)) {
            foreach ($seconddata as $key => $value) {
                $s_siblings['rank'][] = $value['rank'];
                $s_lottery_numbers['lottery_number'][] = $value['lottery_number'];
            }
            array_multisort($s_siblings['rank'], SORT_ASC, $s_lottery_numbers['lottery_number'], SORT_DESC, $seconddata);
        }

        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();
        foreach ($firstdata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']])) {

                    if ($tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] > 0) {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $firstOffered[] = $value['id'];
                        if (isset($offeredRank[$value['first_choice_program_id']])) {
                            $offeredRank[$value['first_choice_program_id']] = $offeredRank[$value['first_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['first_choice_program_id']] = 1;
                        }

                        $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['first_choice_program_id']][$value['next_grade']] - 1;
                        do {
                            $code = Str::random(10);
                            $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["first_choice_final_status" => "Offered", "first_offered_rank" => $offeredRank[$value['first_choice_program_id']], "first_waitlist_for" => $value['first_choice_program_id'], 'offer_slug' => $code]);
                    } else {
                        $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['first_choice_program_id']])) {
                            $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['first_choice_program_id']] = 1;
                        }

                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']]]);
                    }
                } else {
                    $firstdata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['first_choice_program_id']])) {
                        $waitlistArr[$value['first_choice_program_id']] = $waitlistArr[$value['first_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['first_choice_program_id']] = 1;
                    }
                    $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["first_choice_final_status" => "Waitlisted", "first_waitlist_for" => $value['first_choice_program_id'], "first_waitlist_number" => $waitlistArr[$value['first_choice_program_id']]]);
                }
            } else {
                $firstdata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Eligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $first_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $first_choice_eligibility_reason = "CDI";
                } else {
                    $first_choice_eligibility_reason = "Grade";
                }

                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["first_choice_final_status" => "Denied due to Ineligibility", "first_waitlist_for" => $value['first_choice_program_id'], "first_choice_eligibility_reason" => $first_choice_eligibility_reason]);
            }
        }

        foreach ($seconddata as $key => $value) {
            if ($value['grade_status'] == "Pass" && $value['cdi_status'] == "Pass") {
                if (isset($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']])) {
                    if ($tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] > 0 && !in_array($value['id'], $firstOffered)) {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-success'>Offered</div>";
                        $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] = $tmpAvailability[$value['second_choice_program_id']][$value['next_grade']] - 1;

                        if (isset($offeredRank[$value['second_choice_program_id']])) {
                            $offeredRank[$value['second_choice_program_id']] = $offeredRank[$value['second_choice_program_id']] + 1;
                        } else {
                            $offeredRank[$value['second_choice_program_id']] = 1;
                        }
                        do {
                            $code = Str::random(10);
                            $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code));

                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Offered", "second_offered_rank" => $offeredRank[$value['second_choice_program_id']], "second_waitlist_for" => $value['second_choice_program_id'], 'offer_slug' => $code]);
                    } else {
                        $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                        if (isset($waitlistArr[$value['second_choice_program_id']])) {
                            $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                        } else {
                            $waitlistArr[$value['second_choice_program_id']] = 1;
                        }

                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']]]);
                    }
                } else {
                    $seconddata[$key]['final_status'] = "<div class='alert1 alert-warning'>Wait Listed</div>";
                    if (isset($waitlistArr[$value['second_choice_program_id']])) {
                        $waitlistArr[$value['second_choice_program_id']] = $waitlistArr[$value['second_choice_program_id']] + 1;
                    } else {
                        $waitlistArr[$value['second_choice_program_id']] = 1;
                    }
                    $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Waitlisted", "second_waitlist_for" => $value['second_choice_program_id'], "second_waitlist_number" => $waitlistArr[$value['second_choice_program_id']]]);
                }
            } else {
                $seconddata[$key]['final_status'] = "<div class='alert1 alert-danger'>Declined due to Ineligibility</div>";
                if ($value['cdi_status'] == "Fail" && $value['grade_status'] == "Fail") {
                    $second_choice_eligibility_reason = "Both";
                } elseif ($value['cdi_status'] == "Fail") {
                    $second_choice_eligibility_reason = "CDI";
                } else {
                    $second_choice_eligibility_reason = "Grade";
                }

                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], ["second_choice_final_status" => "Denied due to Ineligibility", "second_waitlist_for" => $value['second_choice_program_id'], "second_choice_eligibility_reason" => $second_choice_eligibility_reason]);
            }
        }
    }

    public function application_index()
    {
        //$applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $applications = Form::where("status", "y")->get();

        return view("Reports::application_index", compact("applications"));
    }

    public function waitlist_application_index()
    {
        //$applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $applications = Form::where("status", "y")->get();

        return view("Reports::waitlist_application_index", compact("applications"));
    }

    public function index($application_id, $grade = 0)
    {
        $processType = Config::get('variables.process_separate_first_second_choice');
        $settings = DB::table("reports_hide_option")->first();
        $availabilityArray = array();
        $allProgram = Availability::distinct()->where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->get(['program_id']);
        foreach ($allProgram as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where("program_id", $value->program_id)->get();
            foreach ($avail_grade as $gkey => $gvalue) {
                $availabilityArray[$value->program_id][$gvalue->grade] = $gvalue->available_seats;
            }
        }

        /* Get Next Grade Unique for Tabbing */
        $grade_data = Submissions::distinct()->whereIn("submission_status", array('Active', 'Pending'))->where("form_id", $application_id)->where("submissions.enrollment_id", Session::get("enrollment_id"))->where('next_grade', '<>', '')->orderBy('next_grade', 'DESC')->get(["next_grade"]);
        $gradeArr = array("PreK", "K", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");
        $fgradeTab = [];
        foreach ($grade_data as $key => $value) {
            $fgradeTab[] = $value->next_grade;
        }
        $gradeTab = [];
        foreach ($gradeArr as $key => $value) {
            if (in_array($value, $fgradeTab))
                $gradeTab[] = $value;
        }

        if ($grade == "K")
            $existGrade = "K";
        elseif ($grade == "PreK")
            $existGrade = "PreK";
        elseif ($grade == 0)
            $existGrade = $gradeTab[0];
        else
            $existGrade = $grade;

        $preliminary_score = false;
        $application_data = Application::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->first();
        if ($application_data->preliminary_processing == "Y")
            $preliminary_score = true;


        $firstData = Submissions::distinct()->whereIn("submission_status", array('Active', 'Pending'))->where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("next_grade", $existGrade)->get(["first_choice"]);
        $secondData = Submissions::distinct()->whereIn("submission_status", array('Active', 'Pending'))->where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("next_grade", $existGrade)->get(["second_choice"]);

        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = $programArr = $test_scores_titles = array();
        $eligibilityArr = array();

        foreach ($firstData as $key => $value) {
            if ($value->first_choice != "" && !in_array($value->first_choice, $programArr)) {
                $programArr[] = $value->first_choice;
                // $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                $eligibilityData = getEligibilitiesDynamic($value->first_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            // if($content->scoring->type=="DD")
                            // {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $svalue;
                                }
                            }

                            // foreach($content->terms_calc as $tvalue)
                            // {
                            //     if(!in_array($tvalue, $terms))
                            //     {
                            //         $terms[] = $tvalue;
                            //     }
                            // }
                            if (isset($content->terms_calc)) {
                                foreach ($content->terms_calc as $tkey => $tvalue) {
                                    if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                        $terms[$tkey] = $tvalue;
                                    }
                                }
                            }
                            // }
                        }
                    }
                }
                $data = getSetEligibilityDataDynamic($value->first_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }
        foreach ($secondData as $key => $value) {
            if ($value->second_choice != "" && !in_array($value->second_choice, $programArr)) {
                $programArr[] = $value->second_choice;
                // $eligibilityData = getEligibilities($value->second_choice, 'Academic Grade Calculation');
                /* Code for Academic Grades Eligibility Data */
                $eligibilityData = getEligibilitiesDynamic($value->second_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    if (!empty($content)) {
                        // if($content->scoring->type=="DD")
                        // {
                        $tmp = array();

                        foreach ($content->subjects as $svalue) {
                            if (!in_array($svalue, $subjects)) {
                                $subjects[] = $value;
                            }
                        }

                        // foreach($content->terms_calc as $tvalue)
                        // {
                        //     if(!in_array($tvalue, $terms))
                        //     {
                        //         $terms[] = $tvalue;
                        //     }
                        // }
                        if (isset($content->terms_calc)) {
                            foreach ($content->terms_calc as $tkey => $tvalue) {
                                if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                    $terms[$tkey] = $tvalue;
                                }
                            }
                        }
                        // }
                    }
                }

                $data = getSetEligibilityDataDynamic($value->second_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }



        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = $setCommitteScoreEligibility = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 2);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue[0]})) {
                            $setEligibilityData[$value->first_choice][$svalue . "-" . $tvalue[0]] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*                        else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }

            if (!in_array($value->first_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->first_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->first_choice] = 2;
            }
        }

        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setEligibilityData))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 2);
                foreach ($subjects as $svalue) {
                    foreach ($terms as $tvalue) {
                        if (isset($data->{$svalue . "-" . $tvalue[0]})) {
                            $setEligibilityData[$value->second_choice][$svalue . "-" . $tvalue[0]] = $data->{$svalue . "-" . $tvalue}[0];
                        }
                        /*                        else
                            $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
                    }
                }
            }
            if (!in_array($value->second_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->second_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->second_choice] = 2;
            }
        }

        /* Code to fetch all selection properties of each program
        and Create array after soring. So we can get idea what is 
        selection ranking for each program */
        $programSortArr = [];
        foreach ($programArr as $key => $val) {
            $rsProgram = Program::where("id", getApplicationProgramId($val))->first();
            if (!empty($rsProgram)) {
                $tmp = array();
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->committee_score != '')
                    $tmp['committee_score'] = $rsProgram->committee_score;
                if ($rsProgram->audition_score != '')
                    $tmp['audition_score'] = $rsProgram->audition_score;
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->combine_score != '')
                    $tmp['combine_score'] = $rsProgram->combine_score;
                if ($rsProgram->lottery_number != '')
                    $tmp['lottery_number'] = $rsProgram->lottery_number;
                if ($rsProgram->final_score != '')
                    $tmp['final_score'] = $rsProgram->final_score;
                asort($tmp);
                $programSortArr[$rsProgram->id] = $tmp;
            }
        }

        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();


        $committee_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program.parent_submission_form", $application_id)->where("program.enrollment_id", Session::get("enrollment_id"))->where("eligibility_template.name", "Committee Score")->where("program_eligibility.status", "Y")->select("program.id")->get()->toArray();
        $committee_program_id = [];
        foreach ($committee_eligibility as $key => $value) {
            $committee_program_id[] = $value['id'];
        }

        /* Get CDI Data */
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where('submissions.enrollment_id', Session::get('enrollment_id'))
            ->where('next_grade', $existGrade)
            ->whereIn('submission_status', array("Active", "Pending"))
            ->where("form_id", $application_id)
            ->where("late_submission", "N")
            ->get();

        $firstdata = $seconddata = array();

        $programGrades = array();
        $committee_count = 0;

        foreach ($submissions as $key => $value) {
            $failed = false;
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgram($value->first_choice_program_id, 'Academic Grades');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && isset($programGrades[$value->first_choice_program_id]) && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgram($value->second_choice_program_id, 'Academic Grades');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (isset($programGrades[$value->second_choice_program_id]) && !in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReportYearwise($value->id, $subjects, $terms, $value->next_grade);

            if (count($score) <= 0) {
                $failed = true;
                $score = array();
                foreach ($subjects as $svalue) {
                    foreach ($terms as $svalue1) {
                        $score[$svalue][$svalue1] = "";
                    }
                }
            }

            $composite_score = 0;
            if ($preliminary_score) {
                generateCompositeScore($value->id);
                $rs = SubmissionCompositeScore::where("submission_id", $value->id)->first();
                if (!empty($rs)) {
                    $composite_score = $rs->score;
                }
            }


            if ($value->first_choice != "" && $value->second_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['score'] = $score;
                $tmp['composite_score'] = $composite_score;


                $tmp['test_scores'] = $this->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['rank'] = $this->priorityCalculate($value, "first");

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);

                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['first'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['choice'] = 1;
                $firstdata[$value->first_choice_program_id][] = $tmp;

                $tmp['test_scores'] = $this->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);

                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                }

                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['second'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['rank'] = $this->priorityCalculate($value, "second");


                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                $tmp['score'] = $score;
                $tmp['choice'] = 2;
                $seconddata[$value->second_choice_program_id][] = $tmp;
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['score'] = $score;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                $tmp['test_scores'] = $this->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['composite_score'] = $composite_score;


                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }


                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['first'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['choice'] = 1;
                $firstdata[$value->first_choice_program_id][] = $tmp;
            } else {
                $tmp = $this->convertToArray($value);
                // $tmp['test_scores'] = $test_scores;
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['score'] = $score;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores'] = $this->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);
                $tmp['composite_score'] = $composite_score;


                $tmp['rank'] = $this->priorityCalculate($value, "second");
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);
                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }


                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['second'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }

                $tmp['choice'] = 2;
                $seconddata[$value->second_choice_program_id][] = $tmp;
            }
        }

        /* Sort all submission based on selection rank set 
        by each program array */

        $firstPrgData = $secondPrgData = [];
        $loopArr = array("first", "second");

        if (!$processType) {
            $dataStoreArr = array("first", "first");
        } else {
            $dataStoreArr = array("first", "second");
        }


        $append_second = $append_first = [];

        foreach ($loopArr as $lkey => $lvalue) {
            $str = $lvalue . "data";
            $arrvar = ${$str};
            foreach ($arrvar as $key => $value) {

                $parray = $value;
                if (isset($programSortArr[$key])) {
                    $array = $value;
                    $sortingParams = [];
                    $first_column = "";
                    $i = 0;
                    foreach ($programSortArr[$key] as $pk => $pv) {
                        if ($i == 0)
                            $first_column = $pk;
                        $i++;
                    }
                    //echo $first_column;exit;

                    $first_col_arr = array_column($array, $first_column);

                    $tmpStr = $dataStoreArr[$lkey] . "PrgData";

                    if ($first_column != "committee_score" && $first_column != "combine_score" && (in_array($key, $committee_program_id) || $key == 19)) {
                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${"append_" . $lvalue}[] = $pv;
                        }

                        foreach ($programSortArr[$key] as $pk => $pv) {
                            if ($pk == "rating_priority") {
                                $sort_field = "rank";
                                $sort_type = SORT_ASC;
                            } else {
                                $sort_field = $pk;
                                $sort_type = SORT_DESC;
                            }


                            $sortingParams[] = array_column(${"append_" . $dataStoreArr[$lkey]}, $sort_field);
                            $sortingParams[] = $sort_type;
                        }
                        $sortingParams[] = &${"append_" . $dataStoreArr[$lkey]};
                        array_multisort(...$sortingParams);
                    } else {

                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${$tmpStr}[] = $pv;
                        }
                    }
                }
            }
        }


        $firstdata = $firstPrgData;
        if (!empty($firstdata)) {
            $committee_score  = $priority = $lottery_number = $choices = $next_grade = array();
            foreach ($firstdata as $key => $value) {
                try {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                } catch (\Exception $e) {
                    echo $value['id'];
                    exit;
                }
                $priority['rank'][] = $value['rank'];
                $lottery_number['lottery_number'][] = $value['lottery_number'];
                $choices['choice'][] = $value['choice'];
                //$next_grade['next_grade'][] = $value['next_grade'];

            }
            if (!$processType) {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $choices['choice'], SORT_ASC, $firstdata);
            } else {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $firstdata);
            }
        }
        if ($committee_count > 0) {
            $firstdata = array_merge($append_first, $firstdata);
            if (!$processType) {
                $firstdata = array_merge($firstdata, $append_second);
            }
        }

        if ($processType) {
            $seconddata = $secondPrgData;
            if (!empty($seconddata)) {
                $committee_score  = $priority = $lottery_number = array();
                foreach ($seconddata as $key => $value) {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                    $priority['rank'][] = $value['rank'];
                    $lottery_number['lottery_number'][] = $value['lottery_number'];
                }
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $seconddata);
            }
            $seconddata = array_merge($seconddata, $append_second);
        } else {
            $seconddata = [];
        }
        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();



        /*
echo "<pre>";
print_r($firstdata);
print_r($seconddata);

exit;*/
        $year = $this->academic_year_arr;

        /*echo "<pre>";
        print_r($firstdata);exit;
        print_r($subjects);
        print_r($terms);exit;*/
        if (str_contains(request()->url(), '/export')) {
            return $this->exportSubmissions($firstdata, $seconddata, $subjects, $terms);
        } else {
            return view("Reports::index", compact("firstdata", "seconddata", "existGrade", "gradeTab", "subjects", "terms", "setEligibilityData", "application_id", "settings", "year", "test_scores_titles", "preliminary_score"));
        }
    }

    public function waitlist_index($application_id, $grade = 0)
    {
        $group_racial_composition = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updated_racial_composition($application_id);
        foreach ($group_racial_composition as $key => $value) {
            $group_racial_composition[$key]['no_previous'] = 'N';
        }


        $af_programs = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->fetch_programs_group($application_id);

        $this->group_racial_composition = $group_race_array = $group_racial_composition;

        $tmp = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->groupByRacism($af_programs);

        $this->program_group = $program_group_array = $tmp['program_group'];

        $rs = ApplicationProgram::where("application_programs.application_id", $application_id)->join("grade", "grade.id", "application_programs.grade_id")->select("program_id", "grade.name", "grade.id")->get();

        $availabilityArray = array();
        foreach ($rs as $key => $value) {
            $offer_count = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_offered_count_programwise($value->program_id, $value->name);


            $rs1 = Availability::where("enrollment_id", Session::get("enrollment_id"))->where("grade", $value->name)->where("program_id", $value->program_id)->first();
            if (!empty($rs1))
                $total = $rs1->available_seats;
            else
                $total = 0;
            $availabilityArray[$value->program_id][$value->name] = $total - $offer_count;
        }



        $processType = Config::get('variables.process_separate_first_second_choice');
        $settings = DB::table("reports_hide_option")->first();



        /* Get Next Grade Unique for Tabbing */
        $grade_data = Submissions::distinct()->whereNotIn("submission_status", array('Application Withdrawn'))->where("form_id", $application_id)->where('next_grade', '<>', '')->orderBy('next_grade', 'DESC')->get(["next_grade"]);
        $gradeArr = array("PreK", "K", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");
        $fgradeTab = [];
        foreach ($grade_data as $key => $value) {
            $fgradeTab[] = $value->next_grade;
        }
        $gradeTab = [];
        foreach ($gradeArr as $key => $value) {
            if (in_array($value, $fgradeTab))
                $gradeTab[] = $value;
        }

        if ($grade == "K")
            $existGrade = "K";
        elseif ($grade == "PreK")
            $existGrade = "PreK";
        elseif ($grade == 0)
            $existGrade = $gradeTab[0];
        else
            $existGrade = $grade;


        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if ($application_data->preliminary_processing == "Y")
            $preliminary_score = true;


        $firstData = Submissions::distinct()->where("submission_status", "<>",  "Applicaation Withdrawn")->where("form_id", $application_id)->get(["first_choice"]);
        $secondData = Submissions::distinct()->where("submission_status", "<>",  "Applicaation Withdrawn")->where("form_id", $application_id)->get(["second_choice"]);
        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = $programArr = $test_scores_titles = array();
        $eligibilityArr = array();


        foreach ($firstData as $key => $value) {
            if ($value->first_choice != "" && !in_array($value->first_choice, $programArr)) {
                $programArr[] = $value->first_choice;
                $data = getSetEligibilityDataDynamic($value->first_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }
        foreach ($secondData as $key => $value) {
            if ($value->second_choice != "" && !in_array($value->second_choice, $programArr)) {
                $programArr[] = $value->second_choice;
                $data = getSetEligibilityDataDynamic($value->second_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }


        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = $setCommitteScoreEligibility = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->first_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->first_choice] = 2;
            }
        }

        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->second_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->second_choice] = 2;
            }
        }

        /* Code to fetch all selection properties of each program
        and Create array after soring. So we can get idea what is 
        selection ranking for each program */


        $programSortArr = [];
        foreach ($programArr as $key => $val) {
            $rsProgram = Program::where("id", getApplicationProgramId($val))->first();
            if (!empty($rsProgram)) {
                $tmp = array();
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->committee_score != '')
                    $tmp['committee_score'] = $rsProgram->committee_score;
                if ($rsProgram->audition_score != '')
                    $tmp['audition_score'] = $rsProgram->audition_score;
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->combine_score != '')
                    $tmp['combine_score'] = $rsProgram->combine_score;
                if ($rsProgram->lottery_number != '')
                    $tmp['lottery_number'] = $rsProgram->lottery_number;
                if ($rsProgram->final_score != '')
                    $tmp['final_score'] = $rsProgram->final_score;
                asort($tmp);
                $programSortArr[$rsProgram->id] = $tmp;
            }
        }
        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();

        $committee_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program.parent_submission_form", $application_id)->where("program.enrollment_id", Session::get("enrollment_id"))->where("eligibility_template.name", "Committee Score")->where("program_eligibility.status", "Y")->select("program.id")->get()->toArray();
        $committee_program_id = [];
        foreach ($committee_eligibility as $key => $value) {
            $committee_program_id[] = $value['id'];
        }

        $program_list = [];
        $submission_data = $subids = [];
        $rs = ProgramGradeMapping::where("grade", $existGrade)->where("enrollment_id", Session::get("enrollment_id"))->get();
        foreach ($rs as $key => $value) {
            $id = $value->id;
            $rsExist = ProcessSelection::where('enrollment_id', Session::get('enrollment_id'))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(" . $id . ", selected_programs)")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
            $table_name = "submissions_final_status";
            $version = 0;
            if (!empty($rsExist)) {
                if ($rsExist->type == "regular") {
                    $table_name = "submissions_final_status";
                    $version = 0;
                } elseif ($rsExist->type == "waitlist") {
                    $table_name = "submissions_waitlist_final_status";
                    $version = $rsExist->version;
                } elseif ($rsExist->type == "late_submission") {
                    $table_name = "late_submissions_final_status";
                    $version = $rsExist->version;
                }
            }
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where("next_grade", $existGrade)
                ->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status")
                ->get();

            foreach ($submissions as $sk => $sv) {

                $insert = true;

                if ($sv->first_choice_final_status == "Waitlisted" && !in_array($sv->second_offer_status, array("Pending", "Waitlisted", "Declined & Waitlisted", "Declined"))) {
                    $insert = false;
                }
                if ($insert && !in_array($sv->id, $subids)) {
                    $submission_data[] = $sv;
                    $subids[] = $sv->id;
                }

                if ($sv->second_choice_final_status == "Waitlisted" && !in_array($sv->first_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) {
                    $insert = false;
                }

                if ($sv->second_choice_final_status != "Waitlisted") {

                    $insert = false;
                }
                if ($insert && !in_array($sv->id, $subids)) {
                    $submission_data[] = $sv;
                    $subids[] = $sv->id;
                }
            }
        }

        $submissions = $submission_data;

        $firstdata = $seconddata = array();

        $programGrades = array();
        $committee_count = 0;

        foreach ($submissions as $key => $value) {
            $failed = false;
            if (!isset($programGrades[$value->first_choice_program_id])) {
                $availableGrades = array();
                $eligibilityData = getEligibilitiesByProgram($value->first_choice_program_id, 'Academic Grades');
                if (isset($eligibilityData[0])) {
                    $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                    $programGrades[$value->first_choice_program_id] = $availableGrades;
                }
            }
            $skip = false;
            if ($value->first_choice_program_id != 0 && isset($programGrades[$value->first_choice_program_id]) && !in_array($value->next_grade, $programGrades[$value->first_choice_program_id])) {
                $skip = true;
            }

            if ($value->second_choice_program_id != '' && $value->second_choice_program_id != '0') {
                if (!isset($programGrades[$value->second_choice_program_id])) {
                    $availableGrades = array();
                    $eligibilityData = getEligibilitiesByProgram($value->second_choice_program_id, 'Academic Grades');
                    if (isset($eligibilityData[0])) {
                        $availableGrades = explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by);
                        $programGrades[$value->second_choice_program_id] = $availableGrades;
                    }
                }
                if (isset($programGrades[$value->second_choice_program_id]) && !in_array($value->next_grade, $programGrades[$value->second_choice_program_id])) {
                    $skip = true;
                }
            }

            $score = $this->collectionStudentGradeReportYearwise($value->id, $subjects, $terms, $value->next_grade);

            if (count($score) <= 0) {
                $failed = true;
                $score = array();
                foreach ($subjects as $svalue) {
                    foreach ($terms as $svalue1) {
                        $score[$svalue][$svalue1] = "";
                    }
                }
            }

            $composite_score = 0;
            if ($preliminary_score) {
                generateCompositeScore($value->id);
                $rs = SubmissionCompositeScore::where("submission_id", $value->id)->first();
                if (!empty($rs)) {
                    $composite_score = $rs->score;
                }
            }


            if ($value->first_choice != "" && $value->second_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['score'] = $score;
                $tmp['race'] = $value->calculated_race;
                $tmp['composite_score'] = $composite_score;


                $tmp['test_scores'] = $this->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['rank'] = $this->priorityCalculate($value, "first");

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);

                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['first'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['choice'] = 1;

                if ($value->first_offer_status != "Declined & Waitlisted" && $value->first_choice_final_status != "Denied due to Ineligibility"  && $value->first_choice_final_status != "Denied due to Incomplete Records") {
                    $firstdata[$value->first_choice_program_id][] = $tmp;
                }
                //$firstdata[$value->first_choice_program_id][] = $tmp;

                $tmp['test_scores'] = $this->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);

                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                }

                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['second'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['rank'] = $this->priorityCalculate($value, "second");


                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                $tmp['score'] = $score;
                $tmp['choice'] = 2;
                if ($value->second_offer_status != "Declined & Waitlisted"  && $value->second_choice_final_status != "Denied due to Ineligibility"  && $value->second_choice_final_status != "Denied due to Incomplete Records") {
                    $seconddata[$value->second_choice_program_id][] = $tmp;
                }
            } elseif ($value->first_choice != "") {
                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['score'] = $score;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = $this->priorityCalculate($value, "first");
                $tmp['race'] = $value->calculated_race;

                $tmp['test_scores'] = $this->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['composite_score'] = $composite_score;


                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }


                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['first'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }
                $tmp['choice'] = 1;
                if ($value->first_offer_status != "Declined & Waitlisted"  && $value->first_choice_final_status != "Denied due to Ineligibility"  && $value->first_choice_final_status != "Denied due to Incomplete Records") {
                    $firstdata[$value->first_choice_program_id][] = $tmp;
                }
            } else {
                $tmp = $this->convertToArray($value);
                // $tmp['test_scores'] = $test_scores;
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['score'] = $score;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores'] = $this->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);
                $tmp['composite_score'] = $composite_score;

                $tmp['race'] = $value->calculated_race;

                $tmp['rank'] = $this->priorityCalculate($value, "second");
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);
                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }


                if ($tmp['committee_score'] == null || $tmp['committee_score'] == "NA")
                    $tmp['committee_score_status'] = "Pending";
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                    $tmp['committee_score_status'] = 'Pass';
                else
                    $tmp['committee_score_status'] = 'Fail';
                if (!isset($this->eligibility_grade_pass[$value->id]['second'])) {
                    $tmp['grade_status'] = "NA";
                } else {
                    if ($this->eligibility_grade_pass[$value->id]['second'] == "Pass") {
                        $tmp['grade_status'] = "Pass";
                    } else {
                        $tmp['grade_status'] = "Fail";
                    }
                }

                $tmp['choice'] = 2;
                if ($value->second_offer_status != "Declined & Waitlisted"  && $value->second_choice_final_status != "Denied due to Ineligibility"  && $value->second_choice_final_status != "Denied due to Incomplete Records") {
                    $seconddata[$value->second_choice_program_id][] = $tmp;
                }
            }
        }


        /* Sort all submission based on selection rank set 
        by each program array */

        $firstPrgData = $secondPrgData = [];
        $loopArr = array("first", "second");

        if (!$processType) {
            $dataStoreArr = array("first", "first");
        } else {
            $dataStoreArr = array("first", "second");
        }


        $append_second = $append_first = [];

        foreach ($loopArr as $lkey => $lvalue) {
            $str = $lvalue . "data";
            $arrvar = ${$str};
            foreach ($arrvar as $key => $value) {

                $parray = $value;
                if (isset($programSortArr[$key])) {
                    $array = $value;
                    $sortingParams = [];
                    $first_column = "";
                    $i = 0;
                    foreach ($programSortArr[$key] as $pk => $pv) {
                        if ($i == 0)
                            $first_column = $pk;
                        $i++;
                    }
                    //echo $first_column;exit;

                    $first_col_arr = array_column($array, $first_column);

                    $tmpStr = $dataStoreArr[$lkey] . "PrgData";

                    if ($first_column != "committee_score" && $first_column != "combine_score" && (in_array($key, $committee_program_id) || $key == 19)) {
                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${"append_" . $lvalue}[] = $pv;
                        }

                        foreach ($programSortArr[$key] as $pk => $pv) {
                            if ($pk == "rating_priority") {
                                $sort_field = "rank";
                                $sort_type = SORT_ASC;
                            } else {
                                $sort_field = $pk;
                                $sort_type = SORT_DESC;
                            }


                            $sortingParams[] = array_column(${"append_" . $dataStoreArr[$lkey]}, $sort_field);
                            $sortingParams[] = $sort_type;
                        }
                        $sortingParams[] = &${"append_" . $dataStoreArr[$lkey]};
                        array_multisort(...$sortingParams);
                    } else {

                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${$tmpStr}[] = $pv;
                        }
                    }
                }
            }
        }


        $firstdata = $firstPrgData;
        if (!empty($firstdata)) {
            $committee_score  = $priority = $lottery_number = $choices = $next_grade = array();
            foreach ($firstdata as $key => $value) {
                try {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                } catch (\Exception $e) {
                    echo $value['id'];
                    exit;
                }
                $priority['rank'][] = $value['rank'];
                $lottery_number['lottery_number'][] = $value['lottery_number'];
                $choices['choice'][] = $value['choice'];
                //$next_grade['next_grade'][] = $value['next_grade'];

            }
            if (!$processType) {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $choices['choice'], SORT_ASC, $firstdata);
            } else {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $firstdata);
            }
        }
        if ($committee_count > 0) {
            $firstdata = array_merge($append_first, $firstdata);
            if (!$processType) {
                $firstdata = array_merge($firstdata, $append_second);
            }
        }

        if ($processType) {
            $seconddata = $secondPrgData;
            if (!empty($seconddata)) {
                $committee_score  = $priority = $lottery_number = array();
                foreach ($seconddata as $key => $value) {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                    $priority['rank'][] = $value['rank'];
                    $lottery_number['lottery_number'][] = $value['lottery_number'];
                }
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $seconddata);
            }
            $seconddata = array_merge($seconddata, $append_second);
        } else {
            $seconddata = [];
        }
        $tmpAvailability = $availabilityArray;
        $waitlistArr = $offeredRank = $firstOffered = array();



        /*
echo "<pre>";
print_r($firstdata);
print_r($seconddata);

exit;*/
        $year = $this->academic_year_arr;

        /*echo "<pre>";
        print_r($firstdata);exit;
        print_r($subjects);
        print_r($terms);exit;*/
        if (str_contains(request()->url(), '/export')) {
            return $this->exportSubmissions($firstdata, $seconddata, $subjects, $terms);
        } else {
            return view("Reports::waitlist_index", compact("firstdata", "seconddata", "existGrade", "gradeTab", "subjects", "terms", "setEligibilityData", "application_id", "settings", "year", "test_scores_titles", "preliminary_score"));
        }
    }

    public function getProgramTestScores($program_id, $submission_id, $test_scores_titles)
    {
        $test_scores = [];
        foreach ($test_scores_titles as $tk => $tv) {
            $rsTestScores = SubmissionTestScore::where("submission_id", $submission_id)->where("program_id", $program_id)->where("test_score_name", $tv)->first();
            if (!empty($rsTestScores)) {
                $test_scores[$tv] = array("value" => $rsTestScores->test_score_value, "score" => $rsTestScores->test_score_rank);
            }
        }
        return $test_scores;
    }

    public function collectionStudentGradeReportYearwise($submission_id, $subjects, $terms, $next_grade = 0)
    {
        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();

        $config_subjects = Config::get('variables.subjects');
        $config_type = Config::get('variables.courseType');
        $score = array();
        $missing = false;
        $grade_year = [];
        if (isset($gradeInfo)) {
            $grade_year = explode(',', $gradeInfo->year);
        }
        foreach ($grade_year as $acy => $acvy) {
            if (isset($terms[$acvy])) {
                foreach ($terms[$acvy] as $tkey => $tvalue) {
                    foreach ($subjects as $skey => $svalue) {
                        $tmp = [];
                        $marks = getSubmissionAcademicScore($submission_id, $config_subjects[$svalue], $tvalue, $acvy, '');

                        $tmp['subject'] = $config_subjects[$svalue];
                        $tmp['courseType'] = array_search($config_subjects[$svalue], $config_type);
                        $tmp['academic_year'] = $acvy;
                        $tmp['academic_term'] = $tvalue;
                        $field = strtolower(str_replace(" ", "_", $config_subjects[$svalue]));
                        if ($gradeInfo->{$field} == "N") {
                            $tmp['grade'] = "NA";
                        } else {
                            $tmp['grade'] = $marks;
                        }

                        $score[] = $tmp;
                    }
                }
                if (!in_array($acvy, $this->academic_year_arr)) {
                    $this->academic_year_arr[] = $acvy;
                }
            }
        }
        return $score;
    }

    public function missing_index()
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        return view("Reports::missing_index", compact('enrollment'));
    }

    public function admin_index()
    {
        $selection = "";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        return view("Reports::admin_review_index", compact('enrollment', 'selection'));
    }

    /* Missing Grade Report */
    public function missing($program_id = 0)
    {

        $application_program = ApplicationProgram::get();
        $aprogram = $programs = array();
        $firstdata = $seconddata = array();
        foreach ($application_program as $key => $value) {
            if (!in_array($value->program_id, $programs)) {
                $programs[] = $value->program_id;
            }
            if ($value->program_id == $program_id) {
                $aprogram[] = $value->id;
            }
        }
        $setEligibilityData = array();
        $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))
            ->where(function ($q) use ($aprogram) {
                $q->whereIn("first_choice", $aprogram)
                    ->orWhereIn("second_choice", $aprogram);
            })->get();
        $subjects = $terms = array();
        //print_r($submissions);exit;
        $eligibilityArr = array();
        foreach ($submissions as $value) {
            $eligibilityData = getEligibilitiesByProgram($program_id, 'Academic Grade Calculation');
            if (count($eligibilityData) > 0) {
                if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                    $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                    // echo $eligibilityData[0]->id;exit;
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                    if (!empty($content)) {
                        if ($content->scoring->type == "DD") {
                            $tmp = array();

                            foreach ($content->subjects as $value) {
                                if (!in_array($value, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            foreach ($content->terms_calc as $value) {
                                if (!in_array($value, $terms)) {
                                    $terms[] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }




        /* Get CDI Data */


        foreach ($submissions as $key => $value) {

            $score = $this->collectionStudentGrade($value->id, $subjects, $terms, "missing", $value->next_grade);

            $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
            if (!empty($cdi_data)) {
                $cdiArr = array();
                $cdiArr['b_info'] = $cdi_data->b_info;
                $cdiArr['c_info'] = $cdi_data->c_info;
                $cdiArr['d_info'] = $cdi_data->d_info;
                $cdiArr['e_info'] = $cdi_data->e_info;
                $cdiArr['susp'] = $cdi_data->susp;
                $cdiArr['susp_days'] = $cdi_data->susp_days;
            } else {
                $cdiArr = array();

                $data = DB::table("student_conduct_disciplinary")->where("stateID", $value->student_id)->first();
                if (!empty($data)) {
                    $cdi_data = [
                        'submission_id' => $value->id,
                        'b_info' => $data->b_info,
                        'c_info' => $data->c_info,
                        'd_info' => $data->d_info,
                        'e_info' => $data->e_info,
                        'susp' => $data->susp,
                        'susp_days' => $data->susp_days
                    ];
                    DB::table("submission_conduct_discplinary_info")->insert($cdi_data);
                    $cdiArr = array();
                    $cdiArr['b_info'] = $data->b_info;
                    $cdiArr['c_info'] = $data->c_info;
                    $cdiArr['d_info'] = $data->d_info;
                    $cdiArr['e_info'] = $data->e_info;
                    $cdiArr['susp'] = $data->susp;
                    $cdiArr['susp_days'] = $data->susp_days;
                } else {
                    $cdiArr['b_info'] = $cdiArr['c_info'] = $cdiArr['d_info'] = $cdi_data['d_info'] = $cdiArr['e_info'] = $cdiArr['susp'] = $cdiArr['susp_days'] = '<i class="fas fa-exclamation-circle text-danger"></i>';
                }
            }
            $tmp = $this->convertToArray($value);
            $tmp['submission_id'] = $value->id;
            $tmp['first_program'] = getApplicationProgramName($value->first_choice);
            $tmp['second_program'] = getApplicationProgramName($value->second_choice);
            $tmp['score'] = $score;
            $tmp['first_choice'] = $value->first_choice;
            $tmp['second_choice'] = $value->second_choice;
            $tmp['cdi'] = $cdiArr;
            $firstdata[] = $tmp;
        }

        $seconddata = array();
        if (str_contains(request()->url(), '/export')) {
            return $this->exportSubmissions($firstdata, $seconddata, $subjects, $terms);
        } else {
            return view("Reports::missing", compact("firstdata", "seconddata", "subjects", "terms", "programs", "program_id"));
        }
    }

    /* Missing Grade Report */
    public function missingGradeMain($enrollment_id, $late_submission = 0)
    {
        $selection = "grade";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $display_outcome = SubmissionsStatusUniqueLog::join("submissions", "submissions.id", "submissions_status_unique_log.submission_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->count();
        return view("Reports::missing_grade", compact('enrollment_id', 'enrollment', 'display_outcome', 'late_submission', 'selection'));
    }
    public function missingGrade($enrollment_id = 0, $late_submission = 0)
    {
        $program_id = 0;

        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $aprogram = $programs = array();
        $firstdata = $seconddata = array();
        /*$application_program = ApplicationProgram::join('application', 'application.id', 'application_programs.application_id')->where('application.district_id', Session::get('district_id'))->select('application_programs.id', 'application_programs.program_id')->get();
        
        
        foreach($application_program as $key=>$value)
        {
            if(!in_array($value->program_id, $programs))
            {
                $programs[] = $value->program_id;
            }
                $aprogram[] = $value->id;
        
        }*/

        $setEligibilityData = array();

        if (is_string($enrollment_id) && $enrollment_id == "Non-Current") {
            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->where("enrollment_id", $enrollment_id)->where('grade_override', 'N')->whereIn('submission_status', array('Active', 'Pending'))->whereNull('student_id')->get();
        } else if (is_string($enrollment_id) && $enrollment_id == "Current") {
            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->where("enrollment_id", $enrollment_id)->where('grade_override', 'N')->whereIn('submission_status', array('Active', 'Pending'))->whereNotNull('student_id')->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->where("enrollment_id", $enrollment_id)->where('grade_override', 'N')->whereIn('submission_status', array('Active', 'Pending'))->get();
        }
        //            print_r($submissions);exit;

        $subjects = $terms = array();
        $eligibilityArr = array();

        $availableGrades = array();
        // $program_id = $programs[0];


        foreach ($submissions as $value) {
            $eligibilityData = getEligibilitiesByProgramDynamic($value->first_choice_program_id, 'Academic Grades', $value->application_id);
            // dd($eligibilityData);
            if (count($eligibilityData) > 0) {
                $availableGrades = array_merge(explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by), $availableGrades);
                if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                    $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    // dd($content);
                    if (!empty($content)) {
                        // $tmp = array();
                        foreach ($content->academic_year_calc as $ayc_val) {
                            if (isset($content->terms_calc->{$ayc_val})) {
                                if (!isset($terms[$ayc_val])) {
                                    $terms[$ayc_val] = [];
                                }
                                foreach ($content->terms_calc->{$ayc_val} as $tc_value) {
                                    if (!in_array($tc_value, $terms[$ayc_val])) {
                                        array_push($terms[$ayc_val], $tc_value);
                                    }
                                }
                            }
                        }
                        foreach ($content->subjects as $svalue) {
                            if (!in_array($svalue, $subjects)) {
                                $subjects[] = $svalue;
                            }
                        }
                    }
                }
            }
            // dd($subjects, $terms);
            if ($value->second_choice_program_id > 0) {
                $eligibilityData = getEligibilitiesByProgramDynamic($value->second_choice_program_id, 'Academic Grades', $value->application_id);
                if (count($eligibilityData) > 0) {
                    $availableGrades = array_merge(explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by), $availableGrades);
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;

                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            // $tmp = array();
                            foreach ($content->academic_year_calc as $ayc_val) {
                                if (isset($content->terms_calc->{$ayc_val})) {
                                    if (!isset($terms[$ayc_val])) {
                                        $terms[$ayc_val] = [];
                                    }
                                    foreach ($content->terms_calc->{$ayc_val} as $tc_value) {
                                        if (!in_array($tc_value, $terms[$ayc_val])) {
                                            array_push($terms[$ayc_val], $tc_value);
                                        }
                                    }
                                }
                            }
                            foreach ($content->subjects as $value) {
                                if (!in_array($value, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }
                        }
                    }
                }
            }
            // dd($subjects, $terms);
        }


        /* Get CDI Data */
        foreach ($submissions as $key => $value) {
            if (in_array($value->next_grade, $availableGrades)) {
                $score = $this->collectionStudentGrade($value->id, $subjects, $terms, "missing", $value->next_grade);
                // dd($score);
                if (!empty($score)) {
                    $tmp = $this->convertToArray($value);
                    $tmp['score'] = $score;
                    $tmp['submission_id'] = $value->id;
                    $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                    $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                    $tmp['score'] = $score;
                    $tmp['first_choice'] = $value->first_choice;
                    $tmp['second_choice'] = $value->second_choice;
                    $firstdata[] = $tmp;
                }
            }
        }

        $seconddata = $programs = array();
        if (str_contains(request()->url(), '/export/missinggrade')) {
            return $this->exportMissingGrade($firstdata, $subjects, $terms);
        } else {
            $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_grade_response", compact("firstdata", "seconddata", "subjects", "terms", "programs", "program_id", "enrollment_id", "enrollment", "display_outcome", "late_submission"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }
    }
    public function mcpssSubmissions($enrollment_id = 0)
    {
        $display_outcome = SubmissionsStatusUniqueLog::count();

        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        //$firstdata = Submissions::where("submissions.district_id", Session::get("district_id"))->where("mcp_employee", "Yes")->join("application", "application.id","submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $enrollment_id)->get();

        $firstdata = Submissions::join('application', 'application.id', 'submissions.application_id')
            ->join('enrollments', 'enrollments.id', 'application.enrollment_id')
            ->where('submissions.district_id', Session::get('district_id'))
            ->whereIn('submission_status', array('Active', 'Pending'))
            ->where("mcp_employee", "Yes")
            ->select('submissions.*', 'enrollments.school_year')
            ->orderBy('created_at', 'desc')
            ->get();
        return view("Reports::mcpss_employee", compact("firstdata", "enrollment_id", "enrollment", "display_outcome"));
    }

    /* Missing CDI Report */
    public function missingCDIMain($enrollment_id, $late_submission = 0)
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $display_outcome = SubmissionsStatusUniqueLog::count();
        return view("Reports::missing_cdi", compact('enrollment_id', 'enrollment', 'display_outcome', 'late_submission'));
    }
    public function missingCDI($enrollment_id = 0, $late_submission = 0)
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $program_id = 0;
        /*$application_program = ApplicationProgram::join('application', 'application.id', 'application_programs.application_id')->where('application.district_id', Session::get('district_id'))->select('application_programs.id', 'application_programs.program_id')->get();
        
        
        foreach($application_program as $key=>$value)
        {
            if(!in_array($value->program_id, $programs))
            {
                $programs[] = $value->program_id;
            }
                $aprogram[] = $value->id;
        
        }*/

        $aprogram = $programs = array();
        $firstdata = $seconddata = array();
        $setEligibilityData = $availableGrades = array();
        $setEligibilityData = array();

        if ($late_submission == 1)
            $late = "Y";
        else
            $late = "N";
        if (is_string($enrollment_id) && $enrollment_id == "Non-Current") {

            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->where('cdi_override', 'N')->whereIn('submission_status', array("Active", "Pending"))->whereNull('student_id')->get();
        } else if (is_string($enrollment_id) && $enrollment_id == "Current") {
            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->where('cdi_override', 'N')->whereIn('submission_status', array("Active", "Pending"))->whereNotNull('student_id')->get();
        } else {
            $submissions = Submissions::where('submissions.district_id', (Session::get('district_id') != 0 ? Session::get('district_id') : 3))->whereIn('submission_status', array("Active", "Pending"))->where('cdi_override', 'N')->get();
        }


        foreach ($submissions as $value) {
            $eligibilityData = getEligibilitiesByProgram($value->first_choice_program_id, 'Conduct Disciplinary Info');
            if (count($eligibilityData) > 0) {
                $availableGrades = array_merge(explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by), $availableGrades);
            }
            if ($value->second_choice_program_id > 0) {
                $eligibilityData = getEligibilitiesByProgram($value->second_choice_program_id, 'Conduct Disciplinary Info');
                if (count($eligibilityData) > 0) {
                    $availableGrades = array_merge(explode(",", $eligibilityData[0]->grade_lavel_or_recommendation_by), $availableGrades);
                }
            }
        }
        $subjects = $terms = array();

        /* Get CDI Data */


        foreach ($submissions as $key => $value) {
            if (in_array($value->next_grade, $availableGrades)) {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (empty($cdi_data)) {
                    $cdiArr = array();

                    $data = DB::table("student_conduct_disciplinary")->where("stateID", $value->student_id)->where('stateID', '<>', '')->first();
                    if (!empty($data)) {
                        $cdi_data = [
                            'submission_id' => $value->id,
                            'b_info' => $data->b_info,
                            'c_info' => $data->c_info,
                            'd_info' => $data->d_info,
                            'e_info' => $data->e_info,
                            'susp' => $data->susp,
                            'susp_days' => $data->susp_days
                        ];
                        DB::table("submission_conduct_discplinary_info")->insert($cdi_data);
                    } else {
                        $cdiArr['b_info'] = $cdiArr['c_info'] = $cdiArr['d_info'] = $cdi_data['d_info'] = $cdiArr['e_info'] = $cdiArr['susp'] = $cdiArr['susp_days'] = '<i class="fas fa-exclamation-circle text-danger"></i>';
                        $tmp = $this->convertToArray($value);
                        $tmp['submission_id'] = $value->id;
                        $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                        $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                        $tmp['first_choice'] = $value->first_choice;
                        $tmp['second_choice'] = $value->second_choice;
                        $tmp['cdi'] = $cdiArr;
                        $firstdata[] = $tmp;
                    }
                }
            }
        }
        $seconddata = $programs = array();


        if (str_contains(request()->url(), '/export/missingcdi')) {
            return $this->exportMissingCDI($firstdata);
        } else {
            $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_cdi_response", compact("firstdata", "seconddata", "subjects", "terms", "programs", "program_id", "enrollment_id", "enrollment", "display_outcome", "late_submission"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }
    }


    public function collectionStudentGrade($submission_id, $subjects, $terms, $type = "", $next_grade = 0)
    {
        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;

        // $sub = Submissions::where("id", $submission_id)->first();
        // if($sub->late_submission == "Y")
        //     $yr = "2020-2021";
        // else
        //     $yr = "2019-2020";

        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();

        foreach ($terms as $tyear => $tvalue) {
            $yr = $tyear;
            foreach ($subjects as $value) {
                foreach ($tvalue as $value1) {
                    $marks = getSubmissionAcademicScore($submission_id, $config_subjects[$value], $value1, $yr, $yr);

                    if ($type == "missing") {
                        if ($marks == 0) {
                            if (!empty($gradeInfo)) {
                                $grade_yrs = $gradeInfo->year ?? '';
                                $yrs_ary = explode(',', $grade_yrs);

                                $field = strtolower(str_replace(" ", "_", $config_subjects[$value]));
                                if ($gradeInfo->{$field} == "N") {
                                    $score[$yr][$value][$value1] = "NA";
                                } else if (!in_array($yr, $yrs_ary)) {
                                    $score[$yr][$value][$value1] = "NA";
                                } else {
                                    $score[$yr][$value][$value1] = '<i class="fas fa-exclamation-circle text-danger"></i>';
                                    $missing = true;
                                }
                            } else {
                                $score[$yr][$value][$value1] = '<i class="fas fa-exclamation-circle text-danger"></i>';
                                $missing = true;
                            }
                        } else
                            $score[$yr][$value][$value1] = $marks;
                    } else
                        $score[$yr][$value][$value1] = $marks;
                }
            }
        }
        // dd($score);
        if ($type == "missing") {
            if ($missing == true) {
                return $score;
            } else
                return array();
        } else
            return $score;
    }


    public function collectionStudentGradeReport($submission, $subjects, $terms, $next_grade = 0, $skip = false, $setEligibilityData)
    {
        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;
        /*
         if($next_grade == "K" || $next_grade == "PreK")
            $gd = "0";
        elseif($next_grade == "1")
            $gd = "PreK";
        elseif($next_grade == "2")
            $gd = "K";
        else
            $gd = $next_grade-2;
            */
        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();

        $import_academic_year = Config::get('variables.import_academic_year');
        $first_failed = $second_failed = 0;

        foreach ($subjects as $value) {
            foreach ($terms as $value1) {

                $marks = getSubmissionAcademicScoreMissing($submission->id, $config_subjects[$value], $value1, $import_academic_year, $import_academic_year);

                /* Here copy above function if condition  for NA */

                if ($marks == "NA") {
                    if ($skip || $submission->grade_override == "Y") {
                        $score[$value][$value1] = "NA";
                    } else {
                        if (!empty($gradeInfo)) {
                            $field = strtolower(str_replace(" ", "_", $config_subjects[$value]));
                            if ($gradeInfo->{$field} == "N") {
                                $score[$value][$value1] = "NA";
                            } else {
                                return array();
                            }
                        } else {
                            return array();
                        }
                    }
                } else {
                    if (isset($setEligibilityData[$submission->first_choice][$value . "-" . $value1])) {
                        if ($setEligibilityData[$submission->first_choice][$value . "-" . $value1] > $marks) {
                            $first_failed++;
                        }
                    }

                    if (isset($setEligibilityData[$submission->second_choice][$value . "-" . $value1])) {
                        if ($setEligibilityData[$submission->second_choice][$value . "-" . $value1] > $marks) {
                            $second_failed++;
                        }
                    }
                    $score[$value][$value1] = $marks;
                }
            }
        }

        if ($first_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Pass";
        }

        if ($second_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Pass";
        }


        return $score;
    }


    public function collectionStudentGradeReportLateSubmission($submission, $subjects, $terms, $next_grade = 0, $skip = false, $setEligibilityData)
    {
        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;
        /*
         if($next_grade == "K" || $next_grade == "PreK")
            $gd = "0";
        elseif($next_grade == "1")
            $gd = "PreK";
        elseif($next_grade == "2")
            $gd = "K";
        else
            $gd = $next_grade-2;
            */
        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();
        $import_academic_year = Config::get('variables.import_academic_year');
        $first_failed = $second_failed = 0;
        foreach ($subjects as $value) {
            $avgcnt = $avgmarks = 0;
            $str = "";
            foreach ($terms as $value1) {
                $tt = explode("-", $value1);
                foreach ($tt as $tv) {
                    if ($tv != "Q4.4 Final Grade") {
                        $marks = getSubmissionAcademicScoreMissing($submission->id, $config_subjects[$value], $tv, "2020-2021", "2020-2021");
                        //echo $submission->id."-".$config_subjects[$value]." - ".$tv."-".$marks."<BR>";exit;
                        /* Here copy above function if condition  for NA */
                        $str .= $tv . "-";
                        if ($marks == "NA") {
                            if ($skip || $submission->grade_override == "Y") {
                                $score[$value][$tv] = "NA";
                            } else {
                                if (!empty($gradeInfo)) {
                                    $field = strtolower(str_replace(" ", "_", $config_subjects[$value]));
                                    if ($gradeInfo->{$field} == "N") {
                                        $score[$value][$tv] = "NA";
                                    } else {
                                        return array();
                                    }
                                } else {
                                    return array();
                                }
                            }
                        } else {
                            $avgmarks += $marks;
                        }
                        $avgcnt++;
                    }
                }
            }
            $marks = number_format($avgmarks / $avgcnt, 2);
            $str = trim($str, "-");


            if (isset($setEligibilityData[$submission->first_choice][$value . "-" . str_replace(" Qtr Grade", "", $str)])) {
                if ($marks != "NA" && $marks > 0 && $setEligibilityData[$submission->first_choice][$value . "-" . str_replace(" Qtr Grade", "", $str)] > $marks) {
                    //echo $marks . " - ".($value."-".str_replace(" Qtr Grade", "", $str));exit;
                    $first_failed++;
                }
            }

            if (isset($setEligibilityData[$submission->second_choice][$value . "-" . str_replace(" Qtr Grade", "", $str)])) {
                if ($marks != "NA" && $marks > 0 && $setEligibilityData[$submission->second_choice][$value . "-" . str_replace(" Qtr Grade", "", $str)] > $marks) {
                    $second_failed++;
                }
            }


            $score[$value][$str] = $marks;
        }
        if ($first_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Pass";
        }

        if ($second_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Pass";
        }

        if ($submission->id == 317311) {
            echo "<pre>";
            print_r($score);
            print_r($setEligibilityData);
            exit;
        }
        return $score;
    }


    public function convertToArray($value)
    {
        $tmp = array();
        $tmp['id'] = $value->id;
        $tmp['student_id'] = $value->student_id;
        $tmp['first_name'] = $value->first_name;
        $tmp['last_name'] = $value->last_name;
        $tmp['next_grade'] = $value->next_grade;
        $tmp['current_grade'] = $value->current_grade;
        $tmp['zoned_school'] = $value->zoned_school;
        $tmp['current_school'] = $value->current_school;
        $tmp['lottery_number'] = $value->lottery_number;
        $tmp['race'] = $value->calculated_race;
        $tmp['submission_status'] = $value->submission_status;
        $tmp['first_sibling'] = $value->first_sibling;
        $tmp['second_sibling'] = $value->second_sibling;
        return $tmp;
    }


    public function exportSubmissions($firstdata, $seconddata, $subjects, $terms)
    {
        $config_subjects = Config::get('variables.subjects');
        $data_ary = [];
        $heading = array(
            "Submission ID",
            "Submission Status",
            "Race",
            "State ID",
            "Last Name",
            "First Name",
            "Next Grade",
            "Current School",
            "Zoned School",
            "First Choice",
            "Second Choice",
            "Sibling ID",
            "Lottery Number"
        );
        foreach ($subjects as $sbjct) {
            foreach ($terms as $term) {
                $heading[] = $config_subjects[$sbjct] . " " . $term;
            }
        }
        $heading[] = "B Info";
        $heading[] = "C Info";
        $heading[] = "D Info";
        $heading[] = "E Info";
        $heading[] = "Susp";
        $heading[] = "#Days of Suspension";
        $heading[] = "Priority";
        $data_ary[] = $heading;

        foreach ($firstdata as $key => $value) {
            $tmp = array();
            $tmp[] = $value['id'];
            $tmp[] = $value['submission_status'];
            $tmp[] = $value['race'];
            $tmp[] = $value['student_id'];
            $tmp[] = $value['last_name'];
            $tmp[] = $value['first_name'];
            $tmp[] = $value['next_grade'];
            $tmp[] = $value['current_school'];
            $tmp[] = '';
            $tmp[] = $value['first_program'];
            $tmp[] = $value['second_program'];
            $tmp[] = $value['first_sibling'];
            $tmp[] = $value['lottery_number'];

            foreach ($value['score'] as $skey => $sbjct) {
                foreach ($terms as $term) {
                    if (isset($sbjct[$term])) {
                        $tmp[] = $sbjct[$term];
                    } else
                        $tmp[] = "";
                }
            }
            foreach ($value['cdi'] as $vkey => $vcdi) {
                $tmp[] = ($value['cdi'][$vkey] == 0 ? "0" : $value['cdi'][$vkey]);
            }
            if ($value['first_sibling'] != "" && $value['lottery_number'] != "")
                $tmp[] = 1;
            else
                $tmp[] = 2;
            $data_ary[] = $tmp;
        }

        foreach ($seconddata as $key => $value) {
            $tmp = array();
            $tmp[] = $value['id'];
            $tmp[] = $value['submission_status'];
            $tmp[] = $value['race'];
            $tmp[] = $value['student_id'];
            $tmp[] = $value['last_name'];
            $tmp[] = $value['first_name'];
            $tmp[] = $value['next_grade'];
            $tmp[] = $value['current_school'];
            $tmp[] = '';
            $tmp[] = $value['first_program'];
            $tmp[] = $value['second_program'];
            $tmp[] = $value['first_sibling'];
            $tmp[] = $value['lottery_number'];
            foreach ($value['score'] as $skey => $sbjct) {
                foreach ($terms as $term) {
                    if (isset($sbjct[$term])) {
                        $tmp[] = $sbjct[$term];
                    } else
                        $tmp[] = "";
                }
            }
            foreach ($value['cdi'] as $vkey => $vcdi) {
                $tmp[] = ($value['cdi'][$vkey] == 0 ? "0" : $value['cdi'][$vkey]);
            }
            if ($value['second_sibling'] != "" && $value['lottery_number'] != "")
                $tmp[] = 1;
            else
                $tmp[] = 2;

            $data_ary[] = $tmp;
        }

        ob_end_clean();
        ob_start();

        return Excel::download(new SubmissionExport(collect($data_ary)), 'Submissions.xlsx');
    }

    public function exportMissingGrade($firstdata, $subjects, $terms)
    {
        $config_subjects = Config::get('variables.subjects');

        $data_ary['firstdata'] = $firstdata;
        $data_ary['subjects'] = $subjects;
        $data_ary['terms'] = $terms;

        ob_end_clean();
        ob_start();
        return Excel::download(new MissingGradesExport(collect($data_ary)), 'MissingGrades.xlsx');
    }

    public function exportMissingCDI($firstdata)
    {
        $data_ary = [];
        $heading = array(
            "Submission ID",
            "Submission Status",
            "State ID",
            "Last Name",
            "First Name",
            "Next Grade",
            "Current School",
            "First Choice",
            "Second Choice",
            "B Info",
            "C Info",
            "D Info",
            "E Info",
            "Susp",
            "#Days of Suspension",
            "Current Grade"
        );
        $data_ary[] = $heading;

        foreach ($firstdata as $key => $value) {
            $tmp = array();
            $tmp[] = $value['id'];
            $tmp[] = $value['submission_status'];
            $tmp[] = $value['student_id'];
            $tmp[] = $value['last_name'];
            $tmp[] = $value['first_name'];
            $tmp[] = $value['next_grade'];
            $tmp[] = $value['current_school'];
            $tmp[] = $value['first_program'];
            $tmp[] = $value['second_program'];
            foreach ($value['cdi'] as $skey => $sbjct) {
                if (isset($value['cdi'][$skey]) && is_numeric($value['cdi'][$skey])) {
                    $tmp[] = $value['cdi'][$skey];
                } else
                    $tmp[] = "";
            }
            $tmp[] = $value['current_grade'];
            $data_ary[] = $tmp;
        }
        ob_end_clean();
        ob_start();

        return Excel::download(new MissingCDIExport(collect($data_ary)), 'MissingCDI.xlsx');
    }

    public function downloadTemplate(Request $request)
    {
        $filename = 'Submissions.xlsx';
        return Excel::download(new DownloadTemplate(), $filename);
    }

    public function importGrade(Request $request)
    {
        $rules = [
            // 'upload_csv'=>'required',
            'upload_csv' => 'required|mimes:xlsx',
        ];
        $message = [
            // 'upload_csv.required'=>'File is required',
            'upload_csv.required' => 'File is required',
            'upload_csv.mimes' => 'Invalid file format | File format must be xlsx.',
        ];

        $import = new GradeImport;
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_csv');
            $headings = (new HeadingRowImport)->toArray($file);

            $headRow = array();
            $tmp = array();
            foreach ($headings[0][0] as $key => $value) {
                $value = str_replace("_", " ", $value);
                $value = str_replace(array_keys($import->termArr), array_values($import->termArr), $value);
                $tmp[] = ucwords($value);
            }
            $tmp[] = "Error";
            $headRow = $tmp;

            $import->import($file);

            $grade_array = array();
            $grade_array[] = $headRow;
            foreach ($import->invalidArr as $key => $data) {

                $tmp = $data;
                $tmp['Error'] = "Invalid Value";
                $grade_array[] = $tmp;
            }
            if (count($import->invalidArr) > 0) {
                return Excel::download(new MissingGradesExport(collect($grade_array)), 'GradeError.xlsx');
            }

            Session::flash('success', 'Grade Imported successfully');
        }
        return  redirect()->back();
    }

    public function importGradeGet($enrollment_id)
    {
        return view('Reports::import_missing_grade', compact("enrollment_id"));
    }

    public function importCDI(Request $request)
    {
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
            $import = new CDIImport;
            $import->import($file);

            $headings = (new HeadingRowImport)->toArray($file);

            $headRow = array();
            $tmp = array();
            foreach ($headings[0][0] as $key => $value) {
                if ($value != "") {
                    if ($value == "days_of_suspension")
                        $tmp[] = "#Days of Suspension";
                    else {
                        $value = str_replace("_", " ", $value);
                        $tmp[] = ucwords($value);
                    }
                }
            }
            $tmp[] = "Error";
            $headRow = $tmp;

            $cdi_array = array();
            $cdi_array[] = $headRow;
            foreach ($import->invalidArr as $key => $data) {
                $tmp = $data;
                $tmp['Error'] = "Invalid Value";
                $cdi_array[] = $tmp;
            }
            if (count($import->invalidArr) > 0) {
                return Excel::download(new MissingCDIExport(collect($cdi_array)), 'CDIError.xlsx');
            }
            Session::flash('success', 'CDI Imported successfully');
        }
        return  redirect()->back();
    }

    public function importCDIGet($enrollment_id)
    {
        return view('Reports::import_missing_cdi', compact("enrollment_id"));
    }

    public function saveGrade(Request $request, $id)
    {
        $arr = array("1 1" => "1.1", "1 2" => "1.2", "1 3" => "1.3", "1 4" => "1.4", "2 1" => "2.1", "2 2" => "2.2", "2 3" => "2.3", "2 4" => "2.4", "3 1" => "3.1", "3 2" => "3.2", "3 3" => "3.3", "3 4" => "3.4", "4 1" => "4.1", "4 2" => "4.2", "4 3" => "4.3", "4 4" => "4.4");
        $data = $request->all();
        $config_subjects = Config::get('variables.subjects');

        $courses = Config::get('variables.courseType');

        $submission_grade = SubmissionGrade::where("submission_id", $id)->join("submissions", "submissions.id", "submission_grade.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_grade.*", "submissions.application_id", "application.enrollment_id")->get();
        $current_grade = array();
        foreach ($submission_grade as $key => $value) {
            $tmp = array();
            $tmp['submission_id'] = $value->submission_id;
            $tmp['application_id'] = $value->application_id;
            $tmp['enrollment_id'] = $value->enrollment_id;
            $tmp['academicYear'] = $value->academicYear;
            $tmp['academicTerm'] = $value->academicTerm;
            $tmp['GradeName'] = $value->GradeName;
            $tmp['courseTypeID'] = $value->courseTypeID;
            $tmp['numericGrade'] = $value->numericGrade;
            $tmp['courseName'] = $value->courseName;
            $current_grade[] = $tmp;
        }


        $new_grade = array();
        $grades_data = [];
        $rs = Submissions::where("id", $id)->first();
        // dd($data);
        foreach ($data as $key => $value) {
            // dd($data);
            if ($key != "_token") {
                // $key = str_replace("id_".$id."_", "", $key);
                // dd($id);
                $insert = array();
                $insert['submission_id'] = $id;
                $tmp = explode(",", $key);
                // dd($tmp[1]);
                $insert['courseType'] = $config_subjects[$tmp[1]];
                $insert['courseTypeID'] = findArrayKey($courses, $config_subjects[$tmp[1]]);
                // $tmp1 = str_replace("_", " ", $tmp[1]);
                // $insert['GradeName'] = str_replace(array_keys($arr), array_values($arr), $tmp1);
                $insert['courseFullName'] = $insert['courseType'];
                $insert['academicYear'] = $tmp[3];
                //     if($rs->late_submission == "Y")
                //         $insert['academicYear'] = "2020-2021";
                //     else
                // $insert['academicYear'] = "2019-2020";
                $insert['academicTerm'] = str_replace("_", " ", $tmp[2]);
                $insert['stateID'] = $rs->student_id;
                $insert['numericGrade'] = $value;

                $exist = SubmissionGrade::where("submission_id", $id)->where("courseType", $insert['courseType'])->where('academicYear', $insert['academicYear'])->where('academicTerm', $insert['academicTerm'])->first();
                if (isset($exist)) {
                    // dd('exist');
                    $upd = $exist->update($insert);
                } else {
                    // dd('create');
                    $ins = SubmissionGrade::create($insert);
                }

                $initSubmission = Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", 'submissions.id as submission_id', "application.enrollment_id")->first();
                $insert['enrollment_id'] = $initSubmission->enrollment_id;
                $insert['application_id'] = $initSubmission->application_id;
                $new_grade[] = $insert;
            }
        }
        $this->modelGradeChanges($current_grade, $new_grade, "Submission Academic Grade Report");

        $failed = $this->checkMissingGrades($id);
        if ($failed == 0 && $rs->submission_status == "Pending") {
            $rs = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->first();
            if (!empty($rs)) {
                Submissions::where("id", $id)->update(array("submission_status" => "Active"));
            }
        }
        echo "Succ";
    }


    public function checkMissingGrades($id)
    {
        $submission = Submissions::where("id", $id)->first();

        $subjects = $terms = $eligibilityArr = array();
        $eligibilityData = getEligibilitiesByProgram($submission->first_choice_program_id, 'Academic Grade Calculation');

        if (count($eligibilityData) > 0) {
            if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                // echo $eligibilityData[0]->id;exit;
                $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                if (!empty($content)) {
                    if ($content->scoring->type == "DD") {
                        $tmp = array();

                        foreach ($content->subjects as $svalue) {
                            if (!in_array($svalue, $subjects)) {
                                $subjects[] = $svalue;
                            }
                        }

                        foreach ($content->terms_calc as $tvalue) {
                            if (!in_array($tvalue, $terms)) {
                                $terms[] = $tvalue;
                            }
                        }
                    }
                }
            }
        }

        if ($submission->second_choice_program_id > 0) {
            $eligibilityData = getEligibilitiesByProgram($submission->second_choice_program_id, 'Academic Grade Calculation');
            if (count($eligibilityData) > 0) {
                if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                    $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                    // echo $eligibilityData[0]->id;exit;
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                    if (!empty($content)) {
                        if ($content->scoring->type == "DD") {
                            $tmp = array();

                            foreach ($content->subjects as $value) {
                                if (!in_array($value, $subjects)) {
                                    $subjects[] = $value;
                                }
                            }

                            foreach ($content->terms_calc as $value) {
                                if (!in_array($value, $terms)) {
                                    $terms[] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;

        $gradeInfo = SubjectManagement::where("grade", $submission->next_grade)->first();
        $import_academic_year = Config::get('variables.import_academic_year');
        $first_failed = $second_failed = 0;
        $failed = 0;
        foreach ($subjects as $value) {
            foreach ($terms as $value1) {

                $marks = getSubmissionAcademicScoreMissing($submission->id, $config_subjects[$value], $value1, $import_academic_year, $import_academic_year);
                /* Here copy above function if condition  for NA */
                if ($marks == "NA") {
                    if (!empty($gradeInfo)) {
                        $field = strtolower(str_replace(" ", "_", $config_subjects[$value]));
                        echo $gradeInfo->{$field} . "-";
                        if ($gradeInfo->{$field} == "Y") {
                            echo " - " . $failed;
                            $failed++;
                        }
                    } else {
                        $failed++;
                    }
                }
            }
        }
        return $failed;
    }

    public function saveCDI(Request $request, $id)
    {
        $data = $request->all();

        $rs = Submissions::where("id", $id)->first();
        $insert = array();
        $insert['submission_id'] = $id;
        $insert['stateID'] = $rs->student_id;
        foreach ($data as $key => $value) {
            if ($key != "_token") {
                $key = str_replace("id_" . $id . "_", "", $key);
                $insert[$key] = $value;
            }
        }
        SubmissionConductDisciplinaryInfo::create($insert);

        $app_data = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_id", "b_info", "c_info", "d_info", "e_info", "susp", "susp_days",  "submissions.application_id", "application.enrollment_id", "submission.submissions_final_status")->first();

        //        print_r($app_data);exit;
        $this->modelCDICreate($app_data, "Submission - CDI");

        $failed = $this->checkMissingGrades($id);
        if ($failed == 0 && $app_data->submissions_final_status == "Pending") {
            Submissions::where("id", $id)->update(array("submission_status" => "Active"));
        }

        echo "Succ";
    }

    public function mcpssEmployeeVerification($submission_id, $status)
    {
        $data = Submissions::where('id', $submission_id)->first();
        if (isset($data)) {
            Submissions::where('id', $submission_id)->update(['mcpss_verification_status' => $status, 'mcpss_verification_status_by' => Auth::user()->id, 'mcpss_verification_status_at' => date("Y-m-d H:i:s")]);
        }
        Session::flash('success', 'Employee verification status changed successfully.');
        return redirect()->back();
    }

    public function mcpssEmployeeStatus($submission_id)
    {
        $data = Submissions::where('id', $submission_id)->first();
        if (isset($data)) {
            Submissions::where('id', $submission_id)->update(['magnet_program_employee' => 'Y', 'magnet_program_employee_by' => Auth::user()->id, 'magnet_program_employee_at' => date("Y-m-d H:i:s")]);
        }
        Session::flash('success', 'Employee status changed successfully.');
        return redirect()->back();
    }

    public function priorityCalculate($submission, $choice = "first")
    {
        $str = $choice . "_choice_program_id";
        $rank_counter = 0;
        if ($submission->{$str} != 0 && $submission->{$str} != '') {
            $priority_details = DB::table("priorities")->join("program", "program.priority", "priorities.id")->join("priority_details", "priority_details.priority_id", "priorities.id")->where("program.id", $submission->{$str})->select('priorities.*', 'priority_details.*', 'program.feeder_priorities', 'program.upload_program_check', 'program.feeder_field', 'program.magnet_priorities')->get();

            foreach ($priority_details as $count => $priority) {
                $flag = false;
                if ($priority->sibling == 'Y') {
                    if (isset($submission->{$choice . '_sibling'}) && $submission->{$choice . '_sibling'} != '') {
                        $flag = true;
                    }
                    if ($flag == false) {
                        continue;
                    }
                }

                // Magnet Employee
                $flag = false;
                if ($priority->majority_race_in_home_zone_school == 'Y') {

                    if (getMajorityRace($submission)) {
                        $flag = true;
                    }
                    if ($flag == false) {
                        continue;
                    }
                }

                // Feeder
                $flag = false;
                if ($priority->feeder == 'Y') {
                    if ($priority->feeder_field == "upload") {
                        if ($priority->upload_program_check != '') {
                            $tmp = explode(",", $priority->upload_program_check);
                            foreach ($tmp as $t => $v) {
                                $student_id = $submission->student_id;
                                $p_name = getProgramName($submission->{$str});
                                $current_grade = $submission->current_grade;
                                $rs = AgtToNch::where("student_id", $student_id)->where("program_name", $p_name)->where("grade_level", $current_grade)->first();
                                if (!empty($rs))
                                    $flag = true;
                            }
                            if ($flag == false) {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        if ($priority->feeder_priorities != '') {
                            $tmp = explode(",", $priority->feeder_priorities);
                            foreach ($tmp as $tk => $tv) {
                                $tmp[] = $tv;
                                $rsSchool = School::where("sis_name", $tv)->orWhere("name", $tv)->first();
                                if (!empty($rsSchool)) {
                                    $tmp[] = $rsSchool->sis_name;
                                    $tmp[] = $rsSchool->name;
                                }
                            }
                            if ($priority->feeder_field != '')
                                $field = $priority->feeder_field;
                            else
                                $field = "current_school";
                            if (in_array($submission->{$field}, $tmp)) {
                                $flag = true;
                            }
                            if ($flag == false) {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }
                }

                // Magnet School
                $flag = false;
                if ($priority->magnet_student == 'Y') {
                    if ($priority->magnet_priorities != '') {
                        $tmp = explode(",", $priority->magnet_priorities);
                        foreach ($tmp as $tk => $tv) {
                            $tmp[] = $tv;
                            $rsSchool = School::where("sis_name", $tv)->orWhere("name", $tv)->first();
                            if (!empty($rsSchool)) {
                                $tmp[] = $rsSchool->sis_name;
                                $tmp[] = $rsSchool->name;
                            }
                        }

                        if (in_array($submission->current_school, $tmp)) {
                            $flag = true;
                        }
                        if ($flag == false) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                return $count + 1;
            }

            return 404;
        }
        return 404;
    }

    public function settingUpdate($field, $val)
    {
        $rs = DB::table("reports_hide_option")->update([$field => $val]);
        echo "done";
    }

    public function checkCDIStatus($setCDIEligibilityData, $cdiData, $id)
    {
        if (isset($setCDIEligibilityData[$id]['b_info'])) {
            if ($cdiData['b_info'] == "NA") {
                return "Pass";
            } elseif ($cdiData['b_info'] > $setCDIEligibilityData[$id]['b_info'] || $cdiData['c_info'] > $setCDIEligibilityData[$id]['c_info'] || $cdiData['d_info'] > $setCDIEligibilityData[$id]['d_info'] || $cdiData['e_info'] > $setCDIEligibilityData[$id]['e_info'] || $cdiData['susp'] > $setCDIEligibilityData[$id]['susp'] || $cdiData['susp_days'] > $setCDIEligibilityData[$id]['susp_days']) {
                return "Fail";
            } else {
                return "Pass";
            }
        }
    }


    public function offerStatus($enrollment_id = 0, $type = "regular", $version = 0)
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $versions_lists = ProcessSelection::where("enrollment_id", $enrollment_id)->get();


        if ($type == "regular") {
            $tversion = 0;
            $from = new SubmissionsFinalStatus();
            $table = "submissions_final_status";
        } elseif ($type == "waitlist") {
            $tversion = $version;
            $from = new SubmissionsWaitlistFinalStatus();
            $table = "submissions_waitlist_final_status";
        } else {
            $tversion = $version;
            $from = new LateSubmissionFinalStatus();
            $table = "late_submissions_final_status";
        }
        // return $data_ary;

        $data_ary = $from::join('submissions', 'submissions.id', $table . '.submission_id')
            ->where(function ($query) {
                $query->where('first_choice_final_status', 'Offered');
                $query->orWhere('second_choice_final_status', 'Offered');
                $query->orWhere('third_choice_final_status', 'Offered');
            })->where($table . ".version", $version)
            ->get();

        $verson = $tversion;
        return view("Reports::offer_status", compact("enrollment_id", "enrollment", "data_ary", "versions_lists", "version", "type"));
    }

    public function seatStatus($enrollment_id = 0, $application_id)
    {
        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $district_id = Session::get("district_id");
        $submissions = Submissions::where('district_id', $district_id)->where("form_id", $application_id)->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        $prgCount = array();;
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if ($value->$choice != 0) {
                        if (!isset($programs[$value->$choice])) {
                            $programs[$value->$choice] = [];
                        }
                        if (!in_array($value->next_grade, $programs[$value->$choice])) {
                            array_push($programs[$value->$choice], $value->next_grade);
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
                $tmp['program_name'] = getProgramName($key) . " - Grade " . $ivalue;
                $rs = Availability::where("program_id", $key)->where("grade", $ivalue)->select("available_seats")->first();
                $tmp['total_seats'] = $rs->available_seats;
                $tmp['total_applicants'] = Submissions::where('district_id', $district_id)->where(function ($query) use ($key) {
                    $query->where('first_choice_program_id', $key);
                    $query->orWhere('second_choice_program_id', $key);
                })->where('next_grade', $ivalue)->get()->count();

                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['offered'] = $rs1 + $rs2;


                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Denied due to Ineligibility")
                    ->where("first_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Denied due to Ineligibility")
                    ->where("second_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['noteligible'] = $rs1 + $rs2;

                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Denied due To Incomplete Records")
                    ->where("first_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Denied due To Incomplete Records")
                    ->where("second_choice_program_id", $key)
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['Incomplete'] = $rs1 + $rs2;

                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->where("first_offer_status", 'Declined')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->where("second_offer_status", 'Declined')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['Decline'] = $rs1 + $rs2;

                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->where("first_offer_status", 'Declined & Waitlisted')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->where("second_offer_status", 'Declined & Waitlisted')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['Waitlisted'] = $rs1 + $rs2;

                $rs1 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->where("first_offer_status", 'Accepted')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $rs2 = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->where("second_offer_status", 'Accepted')
                    ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)
                    ->get()->count();
                $tmp['Accepted'] = $rs1 + $rs2;

                $tmp['remaining'] = $tmp['total_seats'] - $tmp['Accepted'];
                $final_data[] = $tmp;
            }
        }

        //print_r($final_data);exit;
        return view("Reports::seats_status", compact("enrollment_id", "enrollment", "final_data"));
    }


    public function newreport()
    {

        /*$rs = Submissions::get();
        foreach($rs as $key=>$value)
        {
            $first_choice = $value->first_choice;
            $second_choice = $value->second_choice;

            $program = ApplicationProgram::where("id", $first_choice)->first();
            echo $value->id."^".$value->first_choice."^".$value->first_choice_program_id."^".$program->program_id;
            if($second_choice != '')
            {
                $program = ApplicationProgram::where("id", $second_choice)->first();
                echo "^".$value->second_choice."^".$value->second_choice_program_id."^".$program->program_id;
            }
            else
                echo "^".$value->second_choice."^^";
            echo "<BR>"; 


        }
        exit;*/

        /* Get Next Grade Unique for Tabbing */
        $grade_data = Submissions::distinct()->where('next_grade', '<>', '')->orderBy('next_grade', 'DESC')->get(["next_grade"]);
        $gradeArr = array("K", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");
        $fgradeTab = [];
        foreach ($grade_data as $key => $value) {
            $fgradeTab[] = $value->next_grade;
        }
        $gradeTab = [];
        foreach ($gradeArr as $key => $value) {
            if (in_array($value, $fgradeTab))
                $gradeTab[] = $value;
        }
        $firstData = Submissions::distinct()->whereIn("submission_status", array("Waitlisted", "Declined / Waitlist for other"))->get(["first_choice"]);

        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = array();
        $eligibilityArr = array();
        foreach ($firstData as $value) {
            if ($value->first_choice != "") {
                $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            if ($content->scoring->type == "DD") {
                                $tmp = array();

                                foreach ($content->subjects as $value) {
                                    if (!in_array($value, $subjects)) {
                                        $subjects[] = $value;
                                    }
                                }

                                foreach ($content->terms_calc as $value) {
                                    if (!in_array($value, $terms)) {
                                        $terms[] = $value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        /* Get Set Eligibility Data Set for first choice program and second choice program
         */


        /* Get CDI Data */
        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->whereIn("submission_status", array("Waitlisted", "Declined / Waitlist for other"))
            //            ->where('submission_status', 'Denied due to Ineligibility')
            //            ->limit(5)
            ->get();
        //exit;//print_r($submissions);exit;

        $firstdata = $seconddata = array();
        $programGrades = array();
        foreach ($submissions as $key => $value) {


            $score = $this->collectionStudentGradeReport1($value, $subjects, $terms, $value->next_grade);
            if (count($score) > 0) {

                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
                if (!empty($cdi_data)) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = $cdi_data->b_info;
                    $cdiArr['c_info'] = $cdi_data->c_info;
                    $cdiArr['d_info'] = $cdi_data->d_info;
                    $cdiArr['e_info'] = $cdi_data->e_info;
                    $cdiArr['susp'] = $cdi_data->susp;
                    $cdiArr['susp_days'] = $cdi_data->susp_days;
                } else {
                    $cdiArr = array();
                    $cdiArr['b_info'] = "";
                    $cdiArr['c_info'] = "";
                    $cdiArr['d_info'] = "";
                    $cdiArr['e_info'] = "";
                    $cdiArr['susp'] = "";
                    $cdiArr['susp_days'] = "";
                }


                $tmp = $this->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['score'] = $score;
                $tmp['cdi'] = $cdiArr;
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                if ($this->eligibility_grade_pass[$value->id]['first'] == "Pass") {
                    $tmp['grade_status'] = "Pass";
                } else {
                    $tmp['grade_status'] = "Fail";
                }
                $firstdata[] = $tmp;
            }
        }
        $config_subjects = Config::get('variables.subjects');
        echo "Submission ID^Name^Submission Status^Next Grade^";

        foreach ($subjects as $sbjct) {
            foreach ($terms as $term) {
                echo $config_subjects[$sbjct] . " " . $term . "^";
            }
        }
        echo "B Info^C Info^D Info^E Info^Susp^# Days Susp^Status<br>";
        foreach ($firstdata as $key => $value) {
            echo $value['id'] . "^" . ($value['first_name'] . " " . $value['last_name']) . "^" . $value['submission_status'] . "^" . $value['next_grade'] . "^";

            foreach ($value['score'] as $skey => $sbjct) {
                foreach ($terms as $term) {

                    if (isset($sbjct[$term])) {
                        echo $sbjct[$term] . "^";
                    } else {
                        echo "^";
                    }
                }
            }

            foreach ($value['cdi'] as $vkey => $vcdi) {
                foreach ($terms as $term) {
                    echo $value['cdi'][$vkey] . "^";
                }
            }
            echo "<br>";
        }
    }


    public function getSubmissionAcademicScoreMissing($submission_id, $courseType, $GradeName, $term1, $term2)
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


    public function collectionStudentGradeReport1($submission, $subjects, $terms, $next_grade = 0)
    {

        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;
        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();

        $first_failed = $second_failed = 0;
        foreach ($subjects as $value) {
            foreach ($terms as $value1) {

                $marks = $this->getSubmissionAcademicScoreMissing($submission->id, $config_subjects[$value], $value1, (date("Y") - 1) . "-" . (date("Y")), (date("Y") - 1) . "-" . (date("y")));
                /* Here copy above function if condition  for NA */

                if ($marks == "NA") {
                    $score[$value][$value1] = "";
                } else {
                    $score[$value][$value1] = $marks;
                }
            }
        }

        if ($first_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['first'] = "Pass";
        }

        if ($second_failed > 0 && $submission->grade_override == "N") {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Fail";
        } else {
            $this->eligibility_grade_pass[$submission->id]['second'] = "Pass";
        }
        return $score;
    }

    public function populationChange($enrollment_id = 0, $application_id)
    {
        // Processing
        $form_id = 1;
        $pid = $form_id;
        $from = "form";

        $display_outcome = SubmissionsStatusUniqueLog::count();

        // Population Changes
        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        $submissions = Submissions::where('district_id', $district_id)->where("submissions.form_id", $application_id)->where(function ($q) {
            $q->where(function ($q1) {
                $q1->where("first_choice_final_status", "Offered")->where('second_choice_final_status', '<>', 'Offered');
            })
                ->orWhere(function ($q1) {
                    $q1->where("second_choice_final_status", "Offered")->where('first_choice_final_status', '<>', 'Offered');
                });
        })
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where('district_id', $district_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
            ->where("submissions.version", 0)
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);

        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if (!isset($programs[$value->$choice])) {
                        $programs[$value->$choice] = [];
                    }
                    if (!in_array($value->next_grade, $programs[$value->$choice])) {
                        array_push($programs[$value->$choice], $value->next_grade);
                    }
                }
            }
        }
        ksort($programs);
        $data_ary = [];
        $race_ary = [];


        foreach ($programs as $program_id => $grades) {
            foreach ($grades as $grade) {
                $availability = Availability::where('program_id', $program_id)
                    ->where('grade', $grade)->first(['total_seats', 'available_seats']);
                $race_count = [];
                if (!empty($availability)) {
                    foreach ($choices as $choice) {

                        if ($choice == "first_choice_program_id") {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('first_choice_final_status', "Offered")->where('second_choice_final_status', '<>', "Offered")->where("submissions.version", 0)
                                ->where('next_grade', $grade);
                        } else {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('second_choice_final_status', "Offered")->where('first_choice_final_status', '<>', "Offered")->where("submissions.version", 0)
                                ->where('next_grade', $grade);
                        }
                        $race = $submission_race_data->groupBy('calculated_race')->map->count();
                        if (count($race) > 0) {
                            $race_ary = array_merge($race_ary, $race->toArray());

                            if (count($race_count) > 0) {
                                foreach ($race as $key => $value) {

                                    if (isset($race_count[$key])) {
                                        $race_count[$key] = $race_count[$key] + $value;
                                    } else {
                                        $race_count[$key] = 1;
                                    }
                                }
                            } else {


                                $race_count = $race;
                            }
                        }
                    }

                    $data = [
                        'program_id' => $program_id,
                        'grade' => $grade,
                        'total_seats' => $availability->total_seats ?? 0,
                        'available_seats' => $availability->available_seats ?? 0,
                        'race_count' => $race_count,
                    ];
                    $data_ary[] = $data;
                    // sorting race in ascending
                    ksort($race_ary);
                }
            }
            // exit;
        }
        //        exit;
        // Submissions Result
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        return view("Reports::population_change", compact('data_ary', 'race_ary', 'pid', 'from', "display_outcome", "enrollment", "enrollment_id"));
    }

    public function submissionResults($enrollment_id = 0, $application_id)
    {

        $form_id = 1;
        $pid = $form_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $display_outcome = SubmissionsStatusUniqueLog::count();
        // $submissions = Submissions::where('district_id', $district_id)
        //     ->where('district_id', $district_id)
        //     ->where("submissions.form_id", $application_id)
        //     ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
        //     ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status']);

        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where('submissions.enrollment_id', SESSION::get('enrollment_id'))
            ->where("submissions.form_id", $application_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'zoned_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'third_choice_program_id',  'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status']);

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
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        return view("Reports::submissions_result", compact('final_data', 'pid', 'from', 'display_outcome', "enrollment", "enrollment_id"));
    }

    public function duplicate_student($enrollment_id = 0, $type = 0)
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        if ($type == 0) {
            $data = DB::table("submissions")->where("late_submission", "N")->select('first_name', 'last_name', 'parent_first_name', 'parent_last_name', DB::raw("count(last_name) as total_quantity"))
                ->groupBy('first_name', 'last_name', 'parent_first_name', 'parent_last_name')->havingRaw('count(last_name) > 1')
                ->get();
        } else {
            $data = DB::table("submissions")->where("late_submission", "Y")->select('first_name', 'last_name', 'parent_first_name', 'parent_last_name', DB::raw("count(last_name) as total_quantity"))
                ->groupBy('first_name', 'last_name', 'parent_first_name', 'parent_last_name')->havingRaw('count(last_name) > 1')
                ->get();
        }


        $dispData = [];
        foreach ($data as $key => $value) {
            $first_name = $value->first_name;
            $last_name = $value->last_name;
            $parent_first_name = $value->parent_first_name;
            $parent_last_name = $value->parent_last_name;
            if ($type == 0) {
                $submissions = Submissions::where("first_name", $first_name)->where("late_submission", "N")->where("last_name", $last_name)->where("parent_first_name", $parent_first_name)->where("parent_last_name", $parent_last_name)->get();
            } else {
                $submissions = Submissions::where("first_name", $first_name)->where("late_submission", "Y")->where("last_name", $last_name)->where("parent_first_name", $parent_first_name)->where("parent_last_name", $parent_last_name)->get();
            }

            $tmp = [];
            if (count($submissions) > 0) {
                foreach ($submissions as $sk => $sv) {
                    $tmp1 = array();
                    $tmp1['first_name'] = $sv->first_name;
                    $tmp1['last_name'] = $sv->last_name;
                    $tmp1['parent_first_name'] = $sv->parent_first_name;
                    $tmp1['parent_last_name'] = $sv->parent_last_name;
                    $tmp1['submission_id'] = $sv->id;
                    $tmp1['first_program'] = getProgramName($sv->first_choice_program_id);
                    $tmp1['second_program'] = getProgramName($sv->second_choice_program_id);
                    $tmp1['next_grade'] = $sv->next_grade;
                    $tmp1['current_school'] = $sv->current_school;
                    $tmp1['submission_status'] = $sv->submission_status;
                    $tmp1['created_at'] = getDateTimeFormat($sv->created_at);
                    $tmp1['student_id'] = $sv->student_id;
                    $tmp[] = $tmp1;
                }
            }
            if (count($tmp) > 0) {
                $dispData[] = $tmp;
            }
        }

        return view("Reports::duplicate_student", compact('enrollment_id', 'enrollment', 'dispData', 'type'));
    }

    public function gradeCdiUploadList($enrollment_id)
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $gradecdilist = Submissions::whereRaw('id in (SELECT submission_id FROM grade_cdi_files)')->where("enrollment_id", Session::get("enrollment_id"))->whereIn("submission_status", array('Active', 'Pending'))->get();

        return view("Reports::grade_cdi_upload_list", compact('enrollment', 'enrollment_id', 'gradecdilist'));
    }

    public function gradeCdiUploadConfirmed($submission_id, $type)
    {
        $data = Submissions::where('id', $submission_id)->first();
        if (isset($data)) {
            if ($type == 'grade') {
                Submissions::where('id', $submission_id)->update(['grade_upload_confirmed' => 'Y', 'grade_upload_confirmed_by' => Auth::user()->id, 'grade_upload_confirmed_at' => date("Y-m-d H:i:s")]);
                Session::flash('success', 'Grades Confirmed successfully.');
            } else if ($type == 'cdi') {
                Submissions::where('id', $submission_id)->update(['cdi_upload_confirmed' => 'Y', 'cdi_upload_confirmed_by' => Auth::user()->id, 'cdi_upload_confirmed_at' => date("Y-m-d H:i:s")]);
                Session::flash('success', 'CDI Confirmed successfully.');
            }
        }
        return redirect()->back();
    }

    public function missingSubmissionRecommendation($enrollment_id, Request $request)
    {
        $selection = "recommendation";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Recommendation Form")->join("program", "program.id", "program_eligibility.program_id")->where("program.enrollment_id", $enrollment_id)->pluck('program_eligibility.program_id');
        return view('Reports::missing_submission_recommendation', compact('enrollment_id', 'programs', 'enrollment', "selection"));
    }

    public function exportMissingSubmissionRecommendation($enrollment_id, $program_id)
    {
        if (isset($program_id) && $program_id == 'All') {
            $program_ids = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Recommendation Form")->pluck('program_eligibility.program_id');
        } else {
            $program_ids[] = $program_id;
        }
        $submissions = Submissions::where("submissions.district_id", Session::get("district_id"))
            ->join("application", "application.id", "submissions.application_id")
            ->whereIn('submission_status', array('Active', 'Pending'))
            ->select("submissions.id", "submissions.*")
            ->where("application.enrollment_id", $enrollment_id)
            ->where(function ($q) use ($program_ids) {
                $q->whereIn("first_choice_program_id", $program_ids);
                $q->orWhereIn("second_choice_program_id", $program_ids);
            })->get();

        $data_ary = [];

        $heading = array(
            "Submission ID",
            "Created At",
            "Submission Status",
            "State ID",
            "Last Name",
            "First Name",
            "Next Grade",
            "Current School",
            "First Choice",
            "Second Choice",
            "Missing Recommendations"
        );

        $data_ary[] = $heading;
        foreach ($submissions as $k => $value) {
            $tmp = [];
            $missingSub = [];
            $is_first_choice = false;
            $is_second_choice = false;
            $recommendation = SubmissionData::where('submission_id', $value->id)->where('config_name', 'LIKE', 'recommendation%')->pluck('config_value')->toArray();

            if (isset($recommendation) && !empty($recommendation)) {
                $submitted_recom = SubmissionRecommendation::where('submission_id', $value->id)->pluck('config_value')->toArray();
                $missing_recom = array_diff($recommendation, $submitted_recom);

                $tmp['submission_id'] = $value->id;
                $tmp['created_at'] = getDateTimeFormat($value->created_at);
                $tmp['submission_status'] = $value->submission_status;
                $tmp['state_id'] = $value->state_id;
                $tmp['last_name'] = $value->last_name;
                $tmp['first_name'] = $value->first_name;
                $tmp['next_grade'] = $value->next_grade;
                $tmp['current_school'] = $value->current_school;

                if (isset($missing_recom) && !empty($missing_recom)) {
                    foreach ($missing_recom as $mk => $mvalue) {
                        if ($mvalue != '') {
                            $config_value = explode('.', $mvalue);
                            $prog_id = end($config_value);

                            if ($value->first_choice_program_id == $prog_id) {
                                $is_first_choice = true;
                            }

                            if ($value->second_choice_program_id == $prog_id) {
                                $is_second_choice = true;
                            }
                            $missingSub[] = config('variables.recommendation_subject')[$config_value[0]];
                        }
                    }
                }

                $tmp['first_choice_program_id'] = ($is_first_choice) ? getProgramName($value->first_choice_program_id) : '';
                $tmp['second_choice_program_id'] = ($is_second_choice) ? getProgramName($value->second_choice_program_id) : '';
                $tmp['missing_recommendation'] = implode(',', $missingSub);
                if ($tmp['missing_recommendation'] != '')
                    $data_ary[] = $tmp;
            }
        }
        ob_end_clean();
        ob_start();
        return Excel::download(new MissingRecommendationExport(collect($data_ary)), 'MissingRecommendationsReport.xlsx');
    }

    public function giftedStudent($enrollment_id = 0)
    {
        $selection = "gifted_student";
        $display_outcome = SubmissionsStatusUniqueLog::count();
        // $display_outcome = 0;
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $firstdata = Submissions::join('application', 'application.id', 'submissions.application_id')
            ->join('enrollments', 'enrollments.id', 'application.enrollment_id')
            ->where("submissions.enrollment_id", $enrollment_id)
            ->where('submissions.district_id', Session::get('district_id'))
            ->whereIn('submission_status', array('Active', 'Pending'))
            ->where("gifted_student", "Parent Identified as Gifted")
            ->select('submissions.*', 'enrollments.school_year')
            ->orderBy('created_at', 'desc')
            ->get();
        return view("Reports::gifted_student_verification", compact("firstdata", "enrollment_id", "enrollment", "display_outcome", "selection"));
    }

    public function giftedStudentVerification($submission_id, $status)
    {
        $data = Submissions::where('id', $submission_id)->first();
        if (isset($data)) {
            Submissions::where('id', $submission_id)->update(['gifted_verification_status' => $status, 'gifted_verification_status_by' => Auth::user()->id, 'gifted_verification_status_at' => date("Y-m-d H:i:s")]);
        }
        Session::flash('success', 'Employee verification status changed successfully.');
        return redirect()->back();
    }

    public function missingWritingPrompt($enrollment_id = 0)
    {
        $selection = "writing_prompt";

        $req = request()->all();
        $choices = ['first', 'second'];

        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Writing Prompt")->pluck('program_id')->toArray();
        $programs = DB::table("program")->whereIn('id', $avail_program_ary)->where("enrollment_id", $enrollment_id)->get(['id', 'name']);

        if (isset($req['req_program'])) {
            // Export
            $req_program_ary = [];
            if ($req['req_program'] == 'all') {
                $req_program_ary = $avail_program_ary;
            } else {
                array_push($req_program_ary, $req['req_program']);
            }

            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->where(function ($q) use ($req_program_ary) {
                    $q->whereIn('first_choice_program_id', $req_program_ary);
                    $q->orWhereIn('second_choice_program_id', $req_program_ary);
                })
                ->get();

            $data_ary = [];
            foreach ($submissions as $key => $value) {
                $tmp = [];
                $tmp['id'] = $value->id;
                $tmp['created_at'] = getDateTimeFormat($value->created_at);
                $tmp['submission_status'] = $value->submission_status;
                $tmp['state_id'] = $value->student_id;
                $tmp['last_name'] = $value->last_name;
                $tmp['first_name'] = $value->first_name;
                $tmp['next_grade'] = $value->next_grade;
                $tmp['current_school'] = $value->current_school;
                $tmp['email'] = $value->parent_email;
                $store_data = false;
                foreach ($choices as $choice) {
                    $program_id = $value->{$choice . '_choice_program_id'} ?? 0;
                    $tmp[$choice . 'choice_missing_writing_sample'] = '';
                    if ($program_id != 0) {
                        $wp = DB::table('writing_prompt')->where('submission_id', $value->id)->where('program_id', $program_id)->first();
                        // Check eligibility & writing sample
                        if (in_array($program_id, $req_program_ary) && !isset($wp)) {
                            $store_data = true;
                            $tmp[$choice . 'choice_missing_writing_sample'] = getProgramName($program_id) . ' - Grade ' . $value->next_grade;
                        }
                    }
                }
                if ($store_data) {
                    $data_ary[] = $tmp;
                }
            }

            ob_end_clean();
            ob_start();
            $curr_date = date('Y-m-d');
            $file_name = 'MissingWritingPromptReport_' . $curr_date . '.xlsx';
            return Excel::download(new MissingWritingPromptExport(collect($data_ary)), $file_name);
        }
        return view("Reports::missing_writing_prompt", compact("enrollment_id", "enrollment", "programs", "selection"));
    }

    /*This was old function 
    public function missingCommitteeScore($enrollment_id=0)
    {
        $req = request()->all();
        $choices = ['first', 'second'];
        
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Committee Score")->pluck('program_id')->toArray();
        $programs = DB::table("program")->whereIn('id', $avail_program_ary)->get(['id', 'name']);

        if (isset($req['req_program'])) {
            // Export
            $req_program_ary = [];
            if ($req['req_program'] == 'all') {
                $req_program_ary = $avail_program_ary;
            } else {
                array_push($req_program_ary, $req['req_program']);
            }

            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->where(function($q) use ($req_program_ary) {
                    $q->whereIn('first_choice_program_id', $req_program_ary);
                    $q->orWhereIn('second_choice_program_id', $req_program_ary);
                })
                ->get();

            $data_ary = [];
            foreach ($submissions as $key => $value) {
                $tmp = [];
                $tmp['id'] = $value->id;
                $tmp['created_at'] = getDateTimeFormat($value->created_at);
                $tmp['submission_status'] = $value->submission_status;
                $tmp['state_id'] = $value->student_id;
                $tmp['last_name'] = $value->last_name;
                $tmp['first_name'] = $value->first_name;
                $tmp['next_grade'] = $value->next_grade;
                $tmp['current_school'] = $value->current_school;
                $store_data = false;
                foreach ($choices as $choice) {
                    $program_id = $value->{$choice.'_choice_program_id'} ?? 0;
                    $tmp[$choice.'choice_missing_committee_score'] = '';
                    if ($program_id != 0) {
                        $cs = SubmissionCommitteeScore::where('submission_id', $value->id)->where('program_id', $program_id)->first();
                        // Check eligibility & Committee Score
                        if (in_array($program_id, $req_program_ary) && !isset($cs)) {
                            $store_data = true;
                            $tmp[$choice.'choice_missing_committee_score'] = getProgramName($program_id).' - Grade '.$value->next_grade;
                        }
                    }
                }
                if ($store_data) {
                    $data_ary[] = $tmp;
                }
            }

            ob_end_clean();
            ob_start();
            $curr_date = date('Y-m-d');
            $file_name = 'MissingCommitteeScoreReport_'.$curr_date.'.xlsx';
            return Excel::download(new MissingCommitteeScoreExport(collect($data_ary)), $file_name);
        }
        return view("Reports::missing_committee_score",compact("enrollment_id","enrollment", "programs"));
    }
    */

    public function missingCommitteeScore($enrollment_id = 0)
    {
        $selection = "committee_score";

        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Committee Score")->where('program_eligibility.status', 'Y')->pluck('program_id')->toArray();
        if (str_contains(request()->url(), '/response')) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->whereIn("submission_status", array('Active', 'Pending'))
                ->where("submissions.enrollment_id", $enrollment_id)
                ->where(function ($q) use ($avail_program_ary) {
                    $q->whereIn('first_choice_program_id', $avail_program_ary);
                    $q->orWhereIn('second_choice_program_id', $avail_program_ary);
                })
                ->get();

            $data = [];
            foreach ($submissions as $key => $value) {
                // $skip = true;
                $tmp = $this->convertToArray($value);
                // $tmp['score'] = $score;
                $tmp['submission_id'] = $value->id;
                $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                // $tmp['score'] = $score;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['has_valid'] = [];
                $has_first = false;
                $tmp['first_choice_committee_score'] = 'NA';
                if (in_array($value->first_choice_program_id, $avail_program_ary)) {
                    array_push($tmp['has_valid'], 'first');
                    // $has_first = true;
                    $tmp['first_choice_committee_score'] = $this->getCommitteeScore($value->id, $value->first_choice_program_id);
                }
                $has_second = false;
                $tmp['second_choice_committee_score'] = 'NA';
                if (in_array($value->second_choice_program_id, $avail_program_ary)) {
                    if ($value->second_choice_program_id != 0) {
                        array_push($tmp['has_valid'], 'second');
                        // $has_second = true;
                        $tmp['second_choice_committee_score'] = $this->getCommitteeScore($value->id, $value->second_choice_program_id);
                    }
                }
                // return $tmp;
                if (
                    (!is_numeric($tmp['first_choice_committee_score']) && in_array('first', $tmp['has_valid'])) ||
                    (!is_numeric($tmp['second_choice_committee_score']) && in_array('second', $tmp['has_valid']))
                ) {
                    $data[] = $tmp;
                }
            }
            $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_committee_score_response", compact("data", "enrollment_id", "display_outcome"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        return view("Reports::missing_committee_score", compact("enrollment_id", "enrollment", "selection"));
    }
    public function getCommitteeScore($id, $program_id)
    {
        $committee_score = SubmissionCommitteeScore::where('submission_id', $id)->where('program_id', $program_id)->first();
        if (isset($committee_score)) {
            return $committee_score->data;
        }
        return '<i class="fas fa-exclamation-circle text-danger"></i>';
    }
    public function saveCommitteeScore(Request $request, $id)
    {
        $data = $request->data;

        foreach ($data as $key => $value) {
            $val_ary = explode(',', $key);
            $submission_id = $val_ary[0];
            $program_id = $val_ary[1];

            $oldObj = SubmissionCommitteeScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();
            // $old_value = getCommitteeScore($submission_id, $program_id);
            SubmissionCommitteeScore::updateOrCreate(
                [
                    'submission_id' => $submission_id,
                    'program_id' => $program_id
                ],
                ['data' => $value]
            );

            $newObj = SubmissionCommitteeScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj))
                $this->modelChanges($oldObj, $newObj, "Submission - Committee Score");
            else
                $this->modelCreate($newObj, "Submission - Committee Score");
        }


        echo "Succ";
    }

    public function export_submissions()
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $submission_status = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(submission_status)"))->get();

        $first_programs = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(first_choice_program_id)"))->get();

        $second_programs = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(second_choice_program_id)"))->get();
        $third_programs = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(third_choice_program_id)"))->get();

        // dd($third_programs);

        // Get available fields
        $match_ary = [
            'district_id' => Session('district_id'),
            'name' => 'submission_export_fields'
        ];
        $dgs_data = DistrictConfiguration::where($match_ary)->first();
        if (isset($dgs_data)) {
            $export_fields = json_decode($dgs_data->value);
        } else {
            $export_fields = [];
        }

        /*$prgArray1 = array();
        $prgArray2 = array();
        $prgArray3 = array();
        foreach($first_programs as $key=>$value)
        {
            if(!in_array($value->first_choice_program_id, $prgArray1))
            {
                $prgArray1[] = $value->first_choice_program_id;
            }
        }
        foreach($second_programs as $key=>$value)
        {
            if($value->second_choice_program_id > 0 && $value->second_choice_program_id != '' && !in_array($value->second_choice_program_id, $prgArray2))
            {
                $prgArray2[] = $value->second_choice_program_id;
            }
        }
        foreach($third_programs as $key=>$value)
        {
            if($value->third_choice_program_id > 0 && $value->third_choice_program_id != '' && !in_array($value->third_choice_program_id, $prgArray3))
            {
                $prgArray3[] = $value->third_choice_program_id;
            }
        }*/

        $prgArray = array();
        foreach ($first_programs as $key => $value) {
            if (!in_array($value->first_choice_program_id, $prgArray)) {
                $prgArray[] = $value->first_choice_program_id;
            }
        }
        foreach ($second_programs as $key => $value) {
            if ($value->second_choice_program_id > 0 && $value->second_choice_program_id != '' && !in_array($value->second_choice_program_id, $prgArray)) {
                $prgArray[] = $value->second_choice_program_id;
            }
        }
        foreach ($third_programs as $key => $value) {
            if ($value->third_choice_program_id > 0 && $value->third_choice_program_id != '' && !in_array($value->third_choice_program_id, $prgArray)) {
                $prgArray[] = $value->third_choice_program_id;
            }
        }
        return view("Reports::export_submissions", compact("enrollment", "prgArray", "submission_status", "export_fields"));
        // return view("Reports::export_submissions",compact("enrollment","prgArray1","prgArray2","prgArray3", "submission_status", "export_fields"));


    }


    public function exportSubmissionsReportData(Request $request)
    {
        // return $request;
        $status = $request->submission_status;


        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->where("submissions.enrollment_id", Session::get('enrollment_id'))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $request->enrollment_id);

        $program_ids = [];

        /*$data = $data->where(function($q) use ($request) {
            $q->where("first_choice_program_id", $request->programs1);
            $q->orWhereIn("second_choice_program_id", $request->programs2);
            $q->orWhereIn("third_choice_program_id", $request->programs3);
        });*/

        // $program_ids = array_merge($request->programs1, $request->programs2, $request->programs3);


        if (count($request->programs) > 0) {
            $program_ids = $request->programs;
            $data = $data->where(function ($q) use ($program_ids) {
                $q->whereIn("first_choice_program_id", $program_ids);
                $q->orWhereIn("second_choice_program_id", $program_ids);
                $q->orWhereIn("third_choice_program_id", $program_ids);
            });
        }

        if ($status != '0') {
            $data->where('submission_status', $status);
        }

        if ($request->late_submission != 'A') {
            $data->where('late_submission', $request->late_submission);
        }

        $submissions = $data->get();

        $req_fields = $request->fields;
        if (in_array('preliminary_score', $req_fields)) {
            $req_fields["AGC Rank (Score)"] = "agc_rank";
            $req_fields["Committee Score"] = "committee_score";
            $req_fields["Test Scores"] = "test_scores";
        }

        $field_array = array_keys($req_fields);
        $ts_field_ary = [];

        if (($key = array_search('Academic Grade Calculation', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('Test Scores', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('Committee Score', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('AGC Rank (Score)', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('Preliminary Score', $field_array)) !== false) {
            // $priliminary_score = true;
            unset($field_array[$key]);
        }
        if (($key = array_search('Interview Score', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('Composite Score', $field_array)) !== false) {
            unset($field_array[$key]);
        }
        if (($key = array_search('Created At', $field_array)) !== false) {
            unset($field_array[$key]);
        }

        array_unshift($field_array, "Submission ID", "First Choice Program", "Second Choice Program", "Third Choice Program");
        array_push($field_array, "Submission Status");

        if (in_array("agc", $req_fields)) {
            array_push($field_array, "Academic Grade Calculation");
        }
        if (in_array("committee_score", $req_fields)) {
            array_push($field_array, "First Choice Program", "First Choice Program Committee Score");
            array_push($field_array, "Second Choice Program", "Second Choice Program Committee Score");
            array_push($field_array, "Third Choice Program", "Third Choice Program Committee Score");
        }
        if (in_array("test_scores", $req_fields)) {
            array_push($field_array, "Test Scores");
        }
        if (in_array("agc_rank", $req_fields)) {
            array_push($field_array, "AGC Rank (Score)");
        }
        if (in_array("preliminary_score", $req_fields)) {
            array_push($field_array, "Preliminary Score");
        }
        if (in_array("interview_score", $req_fields)) {
            array_push($field_array, "Interview Score");
        }
        if (in_array("composite_score", $req_fields)) {
            array_push($field_array, "Composite Score");
        }
        if (in_array("created_at", $req_fields)) {
            array_push($field_array, "Created At");
        }

        $data_ary = [];

        if (isset($submissions) && !empty($data)) {
            foreach ($submissions as $key => $value) {
                $agcCnt = $cmsCnt = $tsCnt = $agcRankCnt = $pmnryScoreCnt = $cmpScoreCnt = $interviewScorecnt = 0;

                $tmp = [];
                $tmp['submission_id'] = $value['id'];
                $tmp['first_choice_program_id'] = getProgramName($value['first_choice_program_id']);
                $tmp['second_choice_program_id'] = getProgramName($value['second_choice_program_id']);
                $tmp['third_choice_program_id'] = getProgramName($value['third_choice_program_id']);

                $subjects = $terms = [];
                if (isset($req_fields) && !empty($req_fields)) {
                    foreach ($req_fields as $k => $field) {

                        if ($field == "agc") {
                            $agcCnt++;
                        } elseif ($field == "committee_score") {
                            $cmsCnt++;
                        } elseif ($field == "test_scores") {
                            $tsCnt++;
                        } elseif ($field == "agc_rank") {
                            $agcRankCnt++;
                        } elseif ($field == "preliminary_score") {
                            $pmnryScoreCnt++;
                        } elseif ($field == "composite_score") {
                            $cmpScoreCnt++;
                        } elseif ($field == "interview_score") {
                            $interviewScorecnt++;
                        } elseif ($field == 'created_at') {
                            continue;
                        } elseif ($field == 'application_id' && isset($value['application_id'])) {
                            $tmp['application_id'] = getApplicationName($value['application_id']);
                        } else {
                            $tmp[$field] = $value[$field];
                        }
                    }
                }

                $tmp['submission_status'] = $value['submission_status'];

                $empty_ids = empty($program_ids);
                // AGC
                if ($agcCnt > 0) {
                    $eligibilityData = getEligibilities($value->first_choice, 'Academic Grades');
                    if (count($eligibilityData) > 0) {
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                        if (!empty($content)) {
                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $svalue;
                                }
                            }
                            if (isset($content->terms_calc)) {
                                foreach ($content->terms_calc as $tkey => $tvalue) {
                                    if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                        $terms[$tkey] = $tvalue;
                                    }
                                }
                            }
                        }
                        $score = app('App\Modules\GenerateApplicationData\Controllers\GenerateApplicationDataController')->collectionStudentGrade($value->id, $subjects, $terms, $value->next_grade);
                        $avgSum = $avgCnt = 0;
                        foreach ($score as $sk => $sv) {
                            $avgSum += $sv['grade'];
                            $avgCnt++;
                        }

                        if ($avgCnt > 0) {
                            $tmp['agc'] = number_format($avgSum / $avgCnt, 2);
                        } else {
                            $tmp['agc'] = 0;
                        }
                    } else {
                        $tmp['agc'] = "-";
                    }
                }
                // Committee Score
                if ($cmsCnt > 0) {
                    $rs = SubmissionCommitteeScore::where("submission_id", $value->id)->where("program_id", $value->first_choice_program_id)->first();
                    $tmp['first_choice_program'] = getProgramName($value->first_choice_program_id);
                    if (!empty($rs)) {
                        $tmp['first_choice_committee_score'] = $rs->data;
                    } else {
                        $tmp['first_choice_committee_score'] = "";
                    }

                    $tmp['second_choice_program'] = getProgramName($value->second_choice_program_id);
                    $rs = SubmissionCommitteeScore::where("submission_id", $value->id)->where("program_id", $value->second_choice_program_id)->first();
                    if (!empty($rs)) {
                        $tmp['second_choice_committee_score'] = $rs->data;
                    } else {
                        $tmp['second_choice_committee_score'] = "";
                    }

                    $tmp['third_choice_program'] = getProgramName($value->third_choice_program_id);
                    $rs = SubmissionCommitteeScore::where("submission_id", $value->id)->where("program_id", $value->third_choice_program_id)->first();
                    if (!empty($rs)) {
                        $tmp['third_choice_committee_score'] = $rs->data;
                    } else {
                        $tmp['third_choice_committee_score'] = "";
                    }
                }
                // dd($value->id);
                // Test Scores
                if ($tsCnt > 0) {
                    $tmp['ts_data']['value'] = [];
                    $tmp['ts_data']['rank'] = [];
                    if ($empty_ids == true || in_array($value->first_choice_program_id, $program_ids)) {
                        $ts_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Test Score")->where("program_id", $value->first_choice_program_id)->first();
                    }
                    if (
                        !isset($ts_eligibility) &&
                        ($empty_ids == true || in_array($value->second_choice_program_id, $program_ids))
                    ) {
                        $ts_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Test Score")->where("program_id", $value->second_choice_program_id)->first();
                    }
                    if (
                        !isset($ts_eligibility) &&
                        ($empty_ids == true || in_array($value->third_choice_program_id, $program_ids))
                    ) {
                        $ts_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Test Score")->where("program_id", $value->third_choice_program_id)->first();
                    }

                    if (isset($ts_eligibility)) {
                        $ts_data = SubmissionTestScore::where('submission_id', $value->id)->where('program_id', $ts_eligibility->program_id)->get();

                        if (isset($ts_data) && count($ts_data) > 0) {

                            foreach ($ts_data as $ts_value) {
                                if (!in_array($ts_value->test_score_name, $ts_field_ary)) {
                                    array_push($ts_field_ary, $ts_value->test_score_name);
                                }
                                $tmp['ts_data']['value'][$ts_value->test_score_name] = $ts_value->test_score_value;
                                $tmp['ts_data']['rank'][$ts_value->test_score_name] = $ts_value->test_score_rank;
                            }
                        }
                    }
                }
                // Other
                if ($agcRankCnt > 0 || $pmnryScoreCnt > 0 || $interviewScorecnt > 0 || $cmpScoreCnt > 0) {
                    $grade_average_data = SubmissionAcademicGradeCalculation::where('submission_id', $value['id'])->first();
                    // AGC Rank
                    if ($agcRankCnt > 0) {
                        $tmp['agc_rank'] = $grade_average_data->given_score ?? '';
                    }
                    // Priliminary Score
                    if ($pmnryScoreCnt > 0 || $cmpScoreCnt > 0) {
                        $preliminary_score = 0;
                        $choice_ary = ['first', 'second', 'third'];

                        foreach ($choice_ary as $choice) {
                            $program_id = $value[$choice . '_choice_program_id'];
                            if ($empty_ids == true || in_array($program_id, $program_ids)) {
                                $score_data = [];
                                $use_calc = [];
                                $committee_score = getSubmissionCommitteeScore($value['id'], $program_id);
                                $eligibility = getEligibilities($value[$choice . '_choice'], 'Test Score');

                                if (isset($eligibility[0]) && !empty($eligibility[0])) {
                                    $score_data = getTestScoreDataIndividual($value['id'], $eligibility[0], $value['late_submission']);
                                    $tdata = getEligibilityConfig($program_id, $eligibility[0]->assigned_eigibility_name, 'use_calculation');
                                    $use_calc = array_merge($use_calc, explode(",", $tdata));

                                    // if ($value['id'] == 1042 && $program_id==12) {
                                    //     dd($use_calc, $score_data, $eligibility[0]->assigned_eigibility_name, $eligibility[0]);
                                    // }

                                    if (!empty($use_calc) && !empty($score_data)) {
                                        foreach ($score_data as $ts_name => $ts_data) {
                                            if (in_array($ts_name, $use_calc)) {
                                                $preliminary_score += (isset($ts_data['scorerank']) ? $ts_data['scorerank'] : 0);
                                            }
                                        }
                                    }
                                }
                                $preliminary_score += ($committee_score != '') ? $committee_score : 0;
                            }
                        }
                        $preliminary_score += (isset($grade_average_data->given_score) ? $grade_average_data->given_score : 0);

                        if ($pmnryScoreCnt > 0) {
                            $tmp['preliminary_score'] = $preliminary_score;
                        }
                    }
                    if ($interviewScorecnt > 0) {
                        $interview_score = SubmissionInterviewScore::where('submission_id', $value['id'])->first()->data ?? 0;
                        $tmp['interview_score'] = $interview_score;
                    }
                    // Composite Score
                    if ($cmpScoreCnt > 0) {
                        $interview_score = SubmissionInterviewScore::where('submission_id', $value['id'])->first()->data ?? 0;
                        $tmp['composite_score'] = $preliminary_score + $interview_score;
                    }
                }
                if (in_array("created_at", $req_fields)) {
                    $tmp['created_at'] = getDateTimeFormat($value['created_at']);
                }

                // return $tmp;
                $data_ary[] = $tmp;
            }
        }
        // dd(collect($data_ary), $field_array, $ts_field_ary);
        ob_end_clean();
        ob_start();

        return Excel::download(new SubmissionExport(collect($data_ary), $field_array, $ts_field_ary), 'ExportSubmissions.xlsx');
    }



    public function importCommitteeScoreGet($enrollment_id)
    {
        return view('Reports::import_missing_committee_score', compact("enrollment_id"));
    }

    public function importCommitteeScore(Request $request)
    {
        // return $request;
        $rules = [
            'imp_file' => 'required|mimes:xlsx',
        ];
        $message = [
            'imp_file.required' => 'File is required.',
            'imp_file.mimes' => 'Only .xlsx file allowed.',
        ];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select a proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('imp_file');
            $import = new CommitteeScoreImport();
            $import->import($file);

            if (count($import->invalidArr) > 0) {
                $data_ary['headings'] = (new HeadingRowImport)->toArray($file)[0][0] ?? [];
                $data_ary['data'] = collect($import->invalidArr);
                return Excel::download(new MissingCommitteeScoreErrorExport($data_ary), 'CommitteeScoreError.xlsx');
            }
            if (!Session('warning')) {
                Session::flash('success', 'Committee Score Imported successfully.');
            }
        }
        return  redirect()->back();
    }


    public function missingTestScore($enrollment_id = 0)
    {
        $selection = "test_score";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        if (str_contains(request()->url(), '/response')) {
            $choices = ['first', 'second'];
            $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Test Score")->pluck('program_id')->toArray();

            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->whereIn("submission_status", array('Active', 'Pending'))
                ->where("submissions.enrollment_id", $enrollment_id)
                ->where(function ($q) use ($avail_program_ary) {
                    $q->whereIn('first_choice_program_id', $avail_program_ary);
                    $q->orWhereIn('second_choice_program_id', $avail_program_ary);
                })
                ->get();

            $data = [];
            $ts_name_fields = [];
            foreach ($submissions as $key => $value) {
                $tmp = $this->convertToArray($value);
                $tmp['submission_id'] = $value->id;
                $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores']['program_id'] = [];

                foreach ($choices as $choice) {
                    if (in_array($value->{$choice . '_choice_program_id'}, $avail_program_ary)) {
                        $tmp['test_scores']['program_id'] = $value->{$choice . '_choice_program_id'};
                        $tmp['test_scores']['data'] = $this->getTestScore($value->id, $value->{$choice . '_choice_program_id'});
                        if (!empty($tmp['test_scores']['data'])) {
                            break;
                        }
                    }
                }

                if (!empty($tmp['test_scores']['data'])) {
                    // $ts_name_fields
                    foreach (array_keys($tmp['test_scores']['data']) as $ts_name) {
                        if (!in_array($ts_name, $ts_name_fields)) {
                            array_push($ts_name_fields, $ts_name);
                        }
                    }
                    $data[] = $tmp;
                }
            }
            // dd($ts_name_fields);
            // $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_test_score_response", compact("data", "enrollment_id", "ts_name_fields"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        return view("Reports::missing_test_score", compact("enrollment_id", "enrollment", "selection"));
    }
    public function getTestScore($submission_id, $program_id)
    {
        $eligibility_type = DB::table('eligibility_template')->where('name', 'Test Score')->first()->id ?? 0;
        if ($eligibility_type != 0) {
            $se_conf = SetEligibilityConfiguration::where('district_id', Session('district_id'))
                ->where('program_id', $program_id)
                ->where('eligibility_type', $eligibility_type)
                ->where('configuration_type', 'use_calculation')
                ->first()
                ->configuration_value ?? '';
            if ($se_conf != '') {
                $ts_ary = [];
                $ts_missing = false;
                $se_conf_ary = explode(',', $se_conf);
                $ts_data = SubmissionTestScore::where('submission_id', $submission_id)
                    ->where('program_id', $program_id)
                    ->whereIn('test_score_name', $se_conf_ary)
                    ->get();
                foreach ($se_conf_ary as $ts_name) {
                    $ts_rank = $ts_data->where('test_score_name', $ts_name)->first()->test_score_rank ?? '404';
                    $ts_ary[$ts_name] = $ts_rank;
                    if ($ts_rank == '404') {
                        $ts_missing = true;
                    }
                }
                if ($ts_missing) {
                    return $ts_ary;
                }
            }
        }
        return [];
    }


    public function saveTestScore(Request $request, $submission_id)
    {
        $data = $request->data;
        $program_id = $request->program_id;

        $submission = Submissions::where("submissions.id", $submission_id)
            ->join("application", "application.id", "submissions.application_id")
            ->select("submissions.application_id", "application.enrollment_id")
            ->first();

        // old data
        $ts = SubmissionTestScore::where("submission_id", $submission_id)
            ->where("program_id", $program_id)
            ->get(['test_score_name', 'test_score_rank']);
        if (count($ts) > 0) {
            $ts_data = [];
            foreach ($ts as $ts_val) {
                $ts_data[$ts_val->test_score_name] = $ts_val->test_score_rank;
            }
            $oldObj = [
                'submission_id' => $submission_id,
                'program_id' => $program_id,
                'application_id' => $submission->application_id,
                'enrollment_id' => $submission->enrollment_id,
                'ts_data' => $ts_data
            ];
        }

        foreach ($data as $ts_name => $ts_rank) {
            $data_ary = explode(',', $ts_name);
            //$program_id = $data_ary[0];
            $ts_name = $data_ary[1];
            // dd($program_id, $submission_id);

            SubmissionTestScore::updateOrCreate(
                [
                    'submission_id' => $submission_id,
                    'program_id' => $program_id,
                    'test_score_name' => $ts_name
                ],
                ['test_score_rank' => $ts_rank]
            );
        }

        // updated data
        $ts = SubmissionTestScore::where("submission_id", $submission_id)
            ->where("program_id", $program_id)
            ->get(['test_score_name', 'test_score_rank']);
        $ts_data = [];
        foreach ($ts as $ts_val) {
            $ts_data[$ts_val->test_score_name] = $ts_val->test_score_rank;
        }
        $newObj = [
            'submission_id' => $submission_id,
            'program_id' => $program_id,
            'application_id' => $submission->application_id,
            'enrollment_id' => $submission->enrollment_id,
            'ts_data' => $ts_data
        ];

        if (isset($oldObj)) {
            $this->modelTestScoreAudit($oldObj, $newObj, "Submission Test Score");
        } else {
            $this->modelTestScoreAudit($newObj, [], "Submission Test Score");
        }

        echo "Success";
    }


    public function missingInterviewScore($enrollment_id = 0)
    {
        $selection = "interview_score";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Interview Score")->where('program_eligibility.status', 'Y')->pluck('program_id')->toArray();

        if (str_contains(request()->url(), '/response')) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->whereIn("submission_status", array('Active', 'Pending'))
                ->where("submissions.enrollment_id", $enrollment_id)
                ->where(function ($q) use ($avail_program_ary) {
                    $q->whereIn('first_choice_program_id', $avail_program_ary);
                    $q->orWhereIn('second_choice_program_id', $avail_program_ary);
                })
                ->get();

            $data = [];
            foreach ($submissions as $key => $value) {
                $tmp = $this->convertToArray($value);
                $tmp['submission_id'] = $value->id;
                $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['interview_score'] = $this->getInterviewScore($value->id);

                if (!is_numeric($tmp['interview_score'])) {
                    $data[] = $tmp;
                }
            }
            $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_interview_score_response", compact("data", "enrollment_id", "display_outcome"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        return view("Reports::missing_interview_score", compact("enrollment_id", "enrollment", "selection"));
    }

    public function getInterviewScore($id)
    {
        $interview_score = SubmissionInterviewScore::where('submission_id', $id)->first();
        if (isset($interview_score)) {
            return $interview_score->data;
        }
        return '<i class="fas fa-exclamation-circle text-danger"></i>';
    }

    public function saveInterviewScore(Request $request, $id)
    {
        $data = $request->data;
        $submission_id = 0;
        foreach ($data as $key => $value) {
            $submission_id = $key;

            $oldObj = SubmissionInterviewScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj)) {
                SubmissionInterviewScore::where('submission_id', $submission_id)->update(['data' => $value]);
                $this->modelChanges($oldObj, $newObj, "Submission - Interview Score");
            } else {
                SubmissionInterviewScore::create(['data' => $value, 'submission_id' => $submission_id]);

                $newObj = SubmissionInterviewScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();

                $this->modelCreate($newObj, "Submission - Interview Score");
            }
        }
        /* Calculate Composite Score */
        generateCompositeScore($submission_id);

        echo "Succ";
    }


    public function missingAcademicScore($enrollment_id = 0)
    {
        $selection = "academic_score";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Test Score")->where('program_eligibility.status', 'Y')->pluck('program_id')->toArray();

        if (str_contains(request()->url(), '/response')) {
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->whereIn("submission_status", array('Active', 'Pending'))
                ->where("submissions.enrollment_id", $enrollment_id)
                ->where(function ($q) use ($avail_program_ary) {
                    $q->whereIn('first_choice_program_id', $avail_program_ary);
                    $q->orWhereIn('second_choice_program_id', $avail_program_ary);
                })
                ->get();

            $data = [];
            foreach ($submissions as $key => $value) {
                $tmp = $this->convertToArray($value);
                $tmp['submission_id'] = $value->id;
                $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;

                $grade_average_data = SubmissionAcademicGradeCalculation::where('submission_id', $value->id)->first();

                $tmp['academic_score'] = $grade_average_data->given_score ?? '<i class="fas fa-exclamation-circle text-danger"></i>';

                if (!is_numeric($tmp['academic_score'])) {
                    $data[] = $tmp;
                }
            }
            $display_outcome = SubmissionsStatusUniqueLog::count();
            $returnHTML =  view("Reports::missing_academic_score_response", compact("data", "enrollment_id", "display_outcome"))->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }

        return view("Reports::missing_academic_score", compact("enrollment_id", "enrollment", "selection"));
    }

    public function saveAcademicScore(Request $request, $id)
    {
        $data = $request->data;
        foreach ($data as $key => $value) {
            $submission_id = $key;

            $oldObj = SubmissionAcademicGradeCalculation::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_academic_grade_calculation.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_academic_grade_calculation.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj)) {
                SubmissionAcademicGradeCalculation::where('submission_id', $submission_id)->update(['given_score' => $value]);
                // $this->modelChanges($oldObj,$newObj,"Submission - Interview Score");
            } else {
                SubmissionAcademicGradeCalculation::create(['given_score' => $value, 'submission_id' => $submission_id]);

                $newObj = SubmissionAcademicGradeCalculation::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_academic_grade_calculation.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_academic_grade_calculation.*", "submissions.application_id", "application.enrollment_id")->first();

                // $this->modelCreate($newObj,"Submission - Interview Score");
            }
        }
        echo "Succ";
    }


    public function getAllProgramsByChoice($enrollment_id = 0)
    {
        $data = [];
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $district_id = Session::get('district_id');
        $submissions1 = Submissions::where('district_id', $district_id)
            ->distinct('first_choice')
            ->get(['first_choice', 'second_choice', 'next_grade']);

        $submissions2 = Submissions::where('district_id', $district_id)
            ->distinct('second_choice')
            ->get(['first_choice', 'second_choice', 'next_grade']);

        $submission_by_programs = [];
        $first_choice = [];
        if (!empty($submissions1)) {
            $first_choice = $submissions1->pluck('first_choice');
        }

        $second_choice = [];
        if (!empty($submissions2)) {
            $second_choice = $submissions2->pluck('second_choice');
        }
        $all_choices = $first_choice->merge($second_choice);
        $application_programs = ApplicationProgram::whereIn('application_programs.id', $all_choices)->join("program", "program.id", "application_programs.program_id")->orderBy("program.id")->orderBy("program.name")->get(['application_programs.id', 'program_id']);

        // Group choice_id by program
        if (!empty($application_programs)) {
            foreach ($application_programs as $key => $value) {
                if (!isset($submission_by_programs[$value->program_id])) {
                    $submission_by_programs[$value->program_id] = [];
                }
                array_push($submission_by_programs[$value->program_id], $value->id);
            }
        }

        $data['all_grades'] = DB::table('grade')->get();
        $data['first_choice_grade_count'] = $this->getGradeCount('first_choice', $submission_by_programs, $district_id);
        $data['second_choice_grade_count'] = $this->getGradeCount('second_choice', $submission_by_programs, $district_id);
        // dd($submission_by_programs, $data['grade_count']);

        return view("Reports::program_by_choice_list", compact("enrollment_id", "enrollment", "data"));
    }

    public function getGradeCount($choice, $submission_by_programs, $district_id)
    {
        // Calculating grade data based on program
        $grade_count = [];
        foreach ($submission_by_programs as $program_id => $value) {
            $choice_submissions = Submissions::where('district_id', $district_id)
                ->whereIn($choice, $value)
                ->get(['next_grade']);
            foreach ($choice_submissions as $key => $value2) {
                if (!isset($grade_count[$program_id][$value2->next_grade])) {
                    $grade_count[$program_id][$value2->next_grade] = 0;
                }
                $grade_count[$program_id][$value2->next_grade] = $grade_count[$program_id][$value2->next_grade] + 1;
            }
        }
        return $grade_count;
    }


    public function missingEligibility($enrollment_id = 0)
    {
        $req = request()->all();
        $choices = ['first', 'second'];
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $program_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")
            // ->where("eligibility_template.name", "Committee Score")
            ->get();
        $avail_program_ary = $program_eligibility->pluck('program_id')->unique()->toArray();
        $programs = DB::table("program")->whereIn('id', $avail_program_ary)->get(['id', 'name']);

        if (isset($req['req_program'])) {
            // Export
            $req_program_ary = [];
            if ($req['req_program'] == 'all') {
                $req_program_ary = $avail_program_ary;
            } else {
                array_push($req_program_ary, $req['req_program']);
            }
            $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
                ->whereIn("submission_status", array('Active', 'Pending'))
                ->where(function ($q) use ($req_program_ary) {
                    $q->whereIn('first_choice_program_id', $req_program_ary);
                    $q->orWhereIn('second_choice_program_id', $req_program_ary);
                })
                ->get();
            $data_ary = [];
            $data_ary['data'] = [];
            $data_ary['fields'] = [
                'Submission ID',
                'State ID',
                'Student Type',
                'Last Name',
                'First Name',
                'Next Grade',
                'Current School',
                'First Choice',
                'Second Choice',
                'Test Scores',
                'Recommendation Form',
                'First Choice Program Committee Score',
                'Second Choice Program Committee Score',
                'Missing First Choice Writing Sample',
                'Missing Second Choice Writing Sample',
                'AGC Rank (Score)',
                'Created At'
            ];
            $ts_name_fields = [];
            foreach ($submissions as $key => $value) {
                $tmp = $this->convertToArray($value);
                $tmp['submission_id'] = $value->id;
                $tmp['first_program'] = getProgramName($value->first_choice_program_id);
                $tmp['second_program'] = getProgramName($value->second_choice_program_id);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores']['program_id'] = [];
                $tmp['test_scores']['data'] = [];
                $tmp['missing_recommendation'] = 'NA';
                $tmp['first_choice_committee_score'] = 'NA';
                $tmp['second_choice_committee_score'] = 'NA';
                $tmp['first_choice_missing_writing_sample'] = 'NA';
                $tmp['second_choice_missing_writing_sample'] = 'NA';

                foreach ($choices as $choice) {
                    $program_id = $value->{$choice . '_choice_program_id'};

                    if (in_array($program_id, $avail_program_ary)) {
                        // Test Score
                        if (empty($tmp['test_scores']['data'])) {
                            $p_eligibility = $program_eligibility->where('program_id', $program_id)->where('name', 'Test Score')->first();
                            if (isset($p_eligibility)) {
                                $tmp['test_scores']['program_id'] = $value->{$choice . '_choice_program_id'};
                                $tmp['test_scores']['data'] = $this->getTestScore($value->id, $value->{$choice . '_choice_program_id'});
                            }
                        }

                        // Recommendation Form
                        if ($tmp['missing_recommendation'] == 'NA') {
                            $p_eligibility = $program_eligibility->where('program_id', $program_id)->where('name', 'Recommendation Form')->first();

                            if (isset($p_eligibility)) {
                                $req_recom = SubmissionData::where('submission_id', $value->id)->where('config_name', 'LIKE', 'recommendation%')->pluck('config_value')->toArray();

                                if (isset($req_recom) && !empty($req_recom)) {
                                    $submitted_recom = SubmissionRecommendation::where('submission_id', $value->id)->pluck('config_value')->toArray();
                                    $missing_recom = array_diff($req_recom, $submitted_recom);

                                    if (isset($missing_recom) && !empty($missing_recom)) {
                                        $missing_recom_state = true;
                                        $missingSub = [];
                                        foreach ($missing_recom as $mk => $mvalue) {
                                            if ($mvalue != '') {
                                                $config_value = explode('.', $mvalue);
                                                $prog_id = end($config_value);
                                                $missingSub[] = config('variables.recommendation_subject')[$config_value[0]];
                                            }
                                        }
                                        $tmp['missing_recommendation'] = implode(',', $missingSub);
                                    }
                                }
                            }
                        }
                        if ($tmp['missing_recommendation'] == "NA")
                            $tmp['missing_recommendation'] = '';

                        // Committee Score
                        $tmp[$choice . '_choice_committee_score'] = SubmissionCommitteeScore::where("submission_id", $value->id)->where("program_id", $program_id)->first()->data ?? 'No';
                        if ($tmp[$choice . '_choice_committee_score'] != 'No')
                            $tmp[$choice . '_choice_committee_score'] = '';

                        // Writing Prompt
                        $wp = WritingPrompt::where('submission_id', $value->id)->where('program_id', $program_id)->first();
                        $tmp[$choice . '_choice_missing_writing_sample'] = isset($wp) ? '' : 'No';
                        // return $tmp;
                    }
                }
                if (!empty($tmp['test_scores']['data'])) {
                    foreach (array_keys($tmp['test_scores']['data']) as $ts_name) {
                        if (!in_array($ts_name, $ts_name_fields)) {
                            array_push($ts_name_fields, $ts_name);
                        }
                    }
                }

                // Academic Grade
                if (getProgramName($value->first_choice_program_id) == "College Academy") {
                    $tmp['agc_rank'] = SubmissionAcademicGradeCalculation::where('submission_id', $value->id)->first()->given_score ?? 'NO';
                    if ($tmp['agc_rank'] != '' && $tmp['agc_rank'] != 'NO')
                        $tmp['agc_rank'] = '';
                    elseif ($tmp['agc_rank'] != 'NO')
                        $tmp['agc_rank'] = '';
                } else {
                    $tmp['agc_rank'] = 'NA';
                }
                $tmp['created_at'] = getDateTimeFormat($value['created_at']);

                $data_ary['data'][] = $tmp;
            }
            // return $data;
            $data_ary['ts_name_fields'] = $ts_name_fields;
            ob_end_clean();
            ob_start();
            $curr_date = date('Y-m-d');
            $file_name = 'MissingEligibilityReport_' . $curr_date . '.xlsx';
            return Excel::download(new MissingEligibilityExport(collect($data_ary)), $file_name);
        }
        $selection = "eligibility";
        return view("Reports::missing_eligibility", compact("enrollment_id", "enrollment", "programs", "selection"));
    }


    public function court_report()
    {
        $rs = Program::distinct()->select("report_name")->get();
        $race_ary = array("Black", "White", "Other");
        $Black = $White = $Other = 0;
        foreach ($race_ary as $rk => $rv) {
            ${$rv} += Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->get()->count();
        }

        $court_data = [];
        foreach ($rs as $k => $v) {

            $programs = Program::where("report_name", $v->report_name)->select("id", "name")->get()->toArray();
            $tmp_id = $tmp_name = [];
            foreach ($programs as $kp => $vp) {
                $tmp_id[] = $vp['id'];
                $tmp_name[] = $vp['name'];
            }

            $data = [];
            $data['name'] = $v->report_name;


            $total_application = 0;
            $count_data = $offer_data = $only_accepted_data = $withdrawn_data = $ineligible_data = $denied_space = [];
            foreach ($race_ary as $rk => $rv) {
                $count = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->where(function ($q1)  use ($tmp_id) {
                    $q1->whereIn('first_choice_program_id', $tmp_id)->orWhereIn('second_choice_program_id', $tmp_id);
                })->get()->count();

                $offered = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->whereIn("awarded_school", $tmp_name)->whereIn("submission_status", array("Offered and Declined", "Offered and Accepted"))->get()->count();


                $only_accepted = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->whereIn("awarded_school", $tmp_name)->whereIn("submission_status", array("Offered and Accepted"))->get()->count();

                $denied = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->whereIn("submission_status", array("Denied due to Space"))->where(function ($q1)  use ($tmp_id) {
                    $q1->whereIn('first_choice_program_id', $tmp_id)->orWhereIn('second_choice_program_id', $tmp_id);
                })->get()->count();

                $withdrawn = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("calculated_race", $rv)->whereIn("submission_status", array("Application Withdrawn"))->where(function ($q1)  use ($tmp_id) {
                    $q1->whereIn('first_choice_program_id', $tmp_id)->orWhereIn('second_choice_program_id', $tmp_id);
                })->get()->count();


                $ineligible = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where(function ($q1)  use ($tmp_id) {
                    $q1->whereIn('first_choice_program_id', $tmp_id)->orWhereIn('second_choice_program_id', $tmp_id);
                })->where("calculated_race", $rv)->whereIn("submission_status", array("Denied due to Ineligibility"))->get()->count();
                $count_data[$rv] = $count;
                $offer_data[$rv] = $offered;
                $withdrawn_data[$rv] = $withdrawn;
                $ineligible_data[$rv] = $ineligible;
                $denied_space[$rv] = $denied;
                $only_accepted_data[$rv] = $only_accepted;
            }
            $data['applications'] = $count_data;
            $data['offered'] = $offer_data;
            $data['withdrawn'] = $withdrawn_data;
            $data['ineligible'] = $ineligible_data;
            $data['denied_space'] = $denied_space;
            $data['enrolled_data'] = $only_accepted_data;
            $court_data[] = $data;
        }
        //return view("Reports::court_report", compact("court_data", "Black", "White", "Other", "race_ary"));
        ob_end_clean();
        ob_start();
        $field_array = $ts_field_ary = [];

        return Excel::download(new CourtReportExport(collect(array("court_data" => $court_data, "race_ary" => $race_ary, "Black" => $Black, "White" => $White, "Other" => $Other)), $field_array, $ts_field_ary), 'ExportCourtReport.xlsx');
    }

    public function missingAcademicSelection($enrollment_id)
    {
        $this->submission = new Submissions();
        $selection = "academy_selection";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $all_data = $this->submission->getSearhData();
        return view('Reports::academy_selection', compact('selection', 'enrollment', 'enrollment_id', 'all_data'));
    }

    public function missingAcademicSelectionResponse(Request $request, $enrollment_id = 0)
    {
        $req = $request->all();

        $next_grade = $request->next_grade ?? '0';
        $awarded_school = $request->awarded_school ?? '0';
        $program_choice = $request->program_choice ?? '0';
        $app_status = $request->app_status ?? '0';

        if ($awarded_school == '0') {
            return json_encode(['data' => []]);
        }

        $data = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where('submissions.enrollment_id', Session::get('enrollment_id'));

        if ($app_status != '0') {
            $data = $data->where('submissions.submission_status', $app_status);
        }


        if ($next_grade != '0') {
            $data = $data->where('next_grade', $next_grade);
        }

        if ($awarded_school != '0') {

            $data = $data->where(function ($query) use ($awarded_school, $program_choice) {

                if ($program_choice != '0') {
                    $query->where($program_choice, $awarded_school);
                } else {
                    $query->where('submissions.first_choice_program_id', $awarded_school)
                        ->orWhere('submissions.second_choice_program_id', $awarded_school)
                        ->orWhere('submissions.third_choice_program_id', $awarded_school);
                }
            });
        }

        $submission_data = $data->get();
        $final_arr = $data_arr = [];

        $total = $submission_data->count();

        if (isset($submission_data) && count($submission_data) > 0) {
            foreach ($submission_data as $key => $value) {
                $tmp = [];
                if ($value->submission_status == 'Active') {
                    $tmp['check_id'] = '<input type="checkbox" name="submission_id[]" value="' . $value->id . '" class="submission_check">';
                } else {
                    $tmp['check_id'] = '';
                }

                $choice_array = [
                    '1' => $value->first_choice_program_id,
                    '2' => $value->second_choice_program_id,
                    '3' => $value->third_choice_program_id,
                ];

                $tmp['status'] = $value->submission_status;
                $tmp['submission_id'] = $value->id;
                $tmp['ssid'] = $value->student_id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['program_name'] = getProgramName($awarded_school);
                $tmp['next_grade'] = $value->next_grade;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['current_school'] = $value->current_school;
                $tmp['race'] = $value->race;
                $tmp['gender'] = $value->gender;
                $tmp['awarded_school'] = $value->awarded_school;
                $tmp['choice_number'] = array_search($awarded_school, $choice_array);

                $final_arr[] = $tmp;
            }
        }

        $data_arr['recordsTotal'] = $total;
        $data_arr['recordsFiltered'] = $total;
        $data_arr['data'] = $final_arr;
        return json_encode($data_arr);
    }
    public function missingAcademicSelectionUpdateStatus(Request $request)
    {
        // return $request->all();
        $req = $request->all();
        $enrollment_id = $request->enrollment_id;
        if (isset($request->submission_ids) && $request->submission_ids != '') {
            $submission_ids = explode(',', $request->submission_ids);

            foreach ($submission_ids as $key => $value) {
                $submission = Submissions::where('id', $value)->first();

                $this->updateSubmissionFinalStatus($submission, $req);
                $this->updateSubmissionStatusLog($submission, $req);

                $update['submission_status'] = $request->submission_status;

                if (in_array($request->submission_status, ['Offered', 'Offered and Accepted'])) {
                    $update['awarded_school'] = getProgramName($request->awarded_school_id);
                }

                Submissions::where('id', $value)->update($update);

                Session::flash('success', 'Academy Selection Status updated successfully');
            }
        } else {
            Session::flash('error', 'Something went wrong');
        }


        return redirect('admin/Reports/missing/' . $enrollment_id . '/academy_selection');
    }

    public function updateSubmissionFinalStatus($submission, $extra = [])
    {
        $check_key = [
            'submission_id' => $submission->id,
            'enrollment_id' => $submission->enrollment_id
        ];

        $insert_arr = [
            'submission_id' => $submission->id,
            'application_id' => $submission->form_id,
            'enrollment_id' => $submission->enrollment_id
        ];

        $submission_status = $extra['submission_status'];

        $need_break = true;
        for ($i = 0; $i < config('variables.choice_count'); $i++) {
            $choice = config('variables.choice_ary')[$i];

            $tmp = 'Pending';

            if (in_array($extra['submission_status'], ['Offered', 'Offered and Accepted'])) {
                $tmp = 'Waitlisted';
                $submission_status = 'Offered';
            } else {
                $tmp = 'Pending';
                $need_break = false;
            }

            if ($extra['awarded_school_id'] == $submission->{$choice . '_choice_program_id'}) {
                $insert_arr[$choice . '_choice_final_status'] = $submission_status;

                if ($need_break) {
                    break;
                }
            } else {
                $insert_arr[$choice . '_choice_final_status'] = $tmp;
            }
        }
        SubmissionsFinalStatus::updateOrCreate($check_key, $insert_arr);
    }

    public function updateSubmissionStatusLog($submission, $extra = [])
    {
        $insert_arr = [
            'enrollment_id' => $submission->enrollment_id ?? '',
            'submission_id' => $submission->id ?? '',
            'old_status' => $submission->submission_status ?? '',
            'new_status' => $extra['submission_status'] ?? '',
            'updated_by' => Auth::user()->id ?? '',
            'comment' => 'Offered by Admin to ' . getProgramName($extra['awarded_school_id']),
        ];
        SubmissionsStatusLog::create($insert_arr);
    }



    public function missingReturningStudents($enrollment_id)
    {
        $this->submission = new Submissions();
        $selection = "returning_students";
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $all_data = $this->submission->getSearhData();
        return view('Reports::returning_students', compact('selection', 'enrollment', 'enrollment_id', 'all_data'));
    }
    public function missingReturningStudentsResponse(Request $request, $enrollment_id = 0)
    {
        $req = $request->all();
        $data = \App\Modules\ReturningStudent\Models\ReturningStudentSubmissions::orderBy("created_at", "DESC")->get();
        $final_arr = $data_arr = [];
        $total = $data->count();
        if (isset($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $tmp['index'] = ($key + 1);
                $tmp['ssid'] = $value['student_id'];
                $tmp['returning_customer'] = $value['returning_customer'];
                $tmp['reason'] = $value['reason'];
                $tmp['birthday'] = getDateFormat($value['birthday']);
                $tmp['student_name'] = $value['student_name'];
                $tmp['parent_first_name'] = $value['parent_first_name'];
                $tmp['parent_last_name'] = $value['parent_last_name'];
                $tmp['phone'] = $value['phone'];
                $tmp['alternate_phone'] = $value['alternate_phone'];
                $tmp['parent_email'] = $value['parent_email'];
                $tmp['next_grade'] = $value['next_grade'];
                $tmp['race'] = $value['race'];
                $tmp['current_school'] = $value['current_school'];
                $tmp['current_signature_academy'] = $value['current_signature_academy'];
                $tmp['created_at'] = getDateTimeFormat($value['created_at']);
                $final_arr[] = $tmp;
            }
        }
        $data_arr['data'] = $final_arr;
        return json_encode($data_arr);
    }


    public function storeSwingSettings(Request $request, $application_id = 0)
    {
        $req = $request->all();
        if (isset($req['swing_value'])) {
            $key_data['application_id'] = $application_id;
            $key_data['enrollment_id'] = Session::get('enrollment_id');
            $key_data['district_id'] = Session::get('district_id');
            //$key_data['user_id'] = Auth::user()->id;
            // $data['program_name'] = $value;
            $data['swing_percentage'] = implode(',', $req['swing_value']);
            $rs = \App\Modules\ProcessSelection\Models\ProgramSwingData::updateOrCreate($key_data, $data);
        }
        Session::flash("success", "Data Updated successfully.");
        return redirect("/admin/Process/Selection/settings/" . $application_id);
    }

    public function editSwingSettings($application_id = 0)
    {
        $swingData = \App\Modules\ProcessSelection\Models\ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))
            ->where("application_id", $application_id)
            ->where("district_id", Session::get("district_id"))
            //->where("user_id", Auth::user()->id)
            ->first();
        return view("ProcessSelection::settings_step_two", compact("application_id", "swingData"));
    }
}
