<?php

namespace App\Modules\ProcessSelection\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\ProcessSelection\Models\{Availability, ProgramSwingData, PreliminaryScore};
use App\Modules\Program\Models\Program;
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Enrollment\Models\{Enrollment, EnrollmentRaceComposition};
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\Submissions\Models\{Submissions, SubmissionAcademicGradeCalculation, SubmissionCommitteeScore, SubmissionsFinalStatus, SubmissionsStatusLog, SubmissionsStatusUniqueLog, SubmissionTestScore};
use App\Modules\SetEligibility\Models\{SetEligibility, SetEligibilityConfiguration, SetEligibilityLateSubmission};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PreliminaryController extends Controller
{

    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prelim_count = PreliminaryScore::join("submissions", "submissions.id", "submission_preliminary_score.submission_id")->where("enrollment_id", Session::get("enrollment_id"))->count();
        $rs = Application::where('preliminary_processing', 'Y')->select("id")->get()->toArray();
        $app_arr = array();
        foreach ($rs as $key => $value) {
            $app_arr[] = $value['id'];
        }
        $exist_count = 0;
        if (count($app_arr) == 0) {
            $submissions = 0;
        } else {
            $submissions = Submissions::whereIn("application_id", $app_arr)->whereIn("submission_status", array('Active', 'Pending'))->where("enrollment_id", Session::get("enrollment_id"))->count();
            foreach ($app_arr as $k => $v) {
                $exist_count += getPreliminaryScoreSubmissionCount($v);
            }
        }
        return view("ProcessSelection::Preliminary.index", compact("submissions", "exist_count", "prelim_count"));
    }

    public function calculate(Request $request)
    {
        $rs = Application::where('preliminary_processing', 'Y')->select("id")->get()->toArray();
        $app_arr = array();
        foreach ($rs as $key => $value) {
            $app_arr[] = $value['id'];
        }

        $submissions = Submissions::whereIn("application_id", $app_arr)->whereIn("submission_status", array('Active', 'Pending'))->get();

        $pass = $fail = $pending = 0;

        $html = "<table class='table table-striped mb-0 dataTable no-footer' id='datatable'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th class='align-middle text-center'>Submission ID</th>";
        $html .= "<th class='align-middle text-center'>Zoned School</th>";
        $html .= "<th class='align-middle text-center'>Next Grade</th>";
        $html .= "<th class='align-middle text-center'>Committee Score</th>";
        //$html .= "<th class='align-middle text-center'>Test Score 1</th>";
        //$html .= "<th class='align-middle text-center'>Test Score 2</th>";
        $html2 = "<th class='align-middle text-center'>Academic Grade Score</th>";

        $html2 .= "<th class='align-middle text-center'>Preliminary Score</th>";
        $html2 .= "<th class='align-middle text-center'>Status</th>";
        $html2 .= "</tr>";
        $html2 .= "</thead>";

        $tmpArr = [];
        foreach ($submissions as $key => $value) {
            $rs = SetEligibilityConfiguration::where("program_id", $value->first_choice_program_id)->where("application_id", $value->application_id)->where("eligibility_type", 12)->where("configuration_type", 'use_calculation')->first();
            if (!empty($rs)) {
                $tmp = explode(",", $rs->configuration_value);
                foreach ($tmp as $k => $v) {
                    if (!in_array($v, $tmpArr)) {
                        $tmpArr[] = $v;
                    }
                }
            }
            $rs = SetEligibilityConfiguration::where("program_id", $value->second_choice_program_id)->where("eligibility_type", 12)->where("application_id", $value->application_id)->where("configuration_type", 'use_calculation')->first();
            if (!empty($rs)) {
                $tmp = explode(",", $rs->configuration_value);
                foreach ($tmp as $k => $v) {
                    if (!in_array($v, $tmpArr)) {
                        $tmpArr[] = $v;
                    }
                }
            }
        }

        if (count($tmpArr) > 0) {
            foreach ($tmpArr as $v) {
                $html .= "<th class='align-middle text-center'>" . $v . "</th>";
            }


            $html .= $html2;

            foreach ($submissions as $key => $value) {
                $missing = false;

                $total_score = 0;
                $rs_committee_score = SubmissionCommitteeScore::where("submission_id", $value->id)->get();


                $commitee_status = "Pass";
                if (count($rs_committee_score) > 0) {
                    $committee_score = $rs_committee_score->sum('data');
                    $total_score += $committee_score;

                    $rs_set_eligibility = DB::table("seteligibility_extravalue")->where('program_id', $value->first_choice_program_id)->where('application_id', $value->application_id)->where('eligibility_type', 7)->first();
                    if (!empty($rs_set_eligibility)) {
                        $extraValue = json_decode($rs_set_eligibility->extra_values, 1);
                        $minimum_score = $extraValue['minimum_score'];
                    } else {
                        $minimum_score = 2;
                    }

                    if ($committee_score < $minimum_score)
                        $commitee_status = "Fail";
                } else {
                    $missing = true;
                    $committee_score = '<div class="alert alert-warning"><i class="fas fa-exclamation"></i></div>';
                }

                $tstHTML = "";
                foreach ($tmpArr as $k => $v) {
                    $rs = SubmissionTestScore::where("submission_id", $value->id)->where("test_score_name", $v)->first();
                    if (!empty($rs)) {
                        $tst_score = $rs->test_score_rank;
                        $total_score += $tst_score;
                    } else {
                        $missing = true;
                        $tst_score = '<div class="alert alert-warning"><i class="fas fa-exclamation"></i></div>';
                    }
                    $tstHTML .= "<td class='align-middle text-center'>" . $tst_score . "</td>";
                }
                $rs_academic_score = SubmissionAcademicGradeCalculation::where("submission_id", $value->id)->first();
                if (!empty($rs_academic_score)) {
                    $academic_score = $rs_academic_score->given_score;
                    $total_score += $academic_score;
                } else {
                    $missing = true;
                    $academic_score = '<div class="alert alert-warning"><i class="fas fa-exclamation"></i></div>';
                }

                if (!$missing) {
                    if ($total_score >= $request->thresold_val && $commitee_status == "Pass")
                        $pass++;
                    else
                        $fail++;
                } else {
                    $pending++;
                }
                $html .= "<tr>";
                $html .= "<td class='align-middle text-center'>" . $value->id . "</td>";
                $html .= "<td class='align-middle text-center'>" . $value->zoned_school . "</td>";
                $html .= "<td class='align-middle text-center'>" . $value->next_grade . "</td>";
                $html .= "<td class='align-middle text-center'>" . $committee_score . "</td>";
                $html .= $tstHTML;
                $html .= "<td class='align-middle text-center'>" . $academic_score . "</td>";
                if (!$missing) {
                    if ($total_score >= $request->thresold_val && $commitee_status == "Pass") {
                        $status = "Pass";
                        $class = "alert1 alert-success";
                    } else {
                        $status = "Denied";
                        $class = "alert1 alert-danger";
                    }
                } else {
                    $status = "Pending";
                    $class = "alert1 alert-warning";
                }
                $html .= "<td class='align-middle text-center'><div class='" . $class . "'>" . $total_score . "</div></td>";
                $html .= "<td class='align-middle text-center'><div class='" . $class . "'>" . $status . "</div></td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        }
        if (count($tmpArr) > 0)
            return json_encode(array("pass" => $pass, "fail" => $fail, "pending" => $pending, "html" => $html));
        else
            return json_encode(array("pass" => 0, "fail" => 0, "pending" => 0, "html" => "<p class='text-center'><strong>Configuration of Test Score considertion for calculation is pending. You can set it from Set Eligibility.</strong></p>"));
    }


    public function commit_score(Request $request)
    {
        $rs = Application::where('preliminary_processing', 'Y')->select("id")->get()->toArray();
        $app_arr = array();
        foreach ($rs as $key => $value) {
            $app_arr[] = $value['id'];
        }

        $submissions = Submissions::whereIn("application_id", $app_arr)->whereIn("submission_status", array('Active', 'Pending'))->get();

        $tmpArr = [];
        foreach ($submissions as $key => $value) {
            $rs = SetEligibilityConfiguration::where("program_id", $value->first_choice_program_id)->where("eligibility_type", 12)->where("application_id", $value->application_id)->where("configuration_type", 'use_calculation')->first();
            if (!empty($rs)) {
                $tmp = explode(",", $rs->configuration_value);
                foreach ($tmp as $k => $v) {
                    if (!in_array($v, $tmpArr)) {
                        $tmpArr[] = $v;
                    }
                }
            }
            $rs = SetEligibilityConfiguration::where("program_id", $value->second_choice_program_id)->where("eligibility_type", 12)->where("application_id", $value->application_id)->where("configuration_type", 'use_calculation')->first();
            if (!empty($rs)) {
                $tmp = explode(",", $rs->configuration_value);
                foreach ($tmp as $k => $v) {
                    if (!in_array($v, $tmpArr)) {
                        $tmpArr[] = $v;
                    }
                }
            }
        }

        $pass = $fail = 0;

        foreach ($submissions as $key => $value) {
            $total_score = 0;
            $commitee_status = "Pass";
            $committee_score = SubmissionCommitteeScore::where("submission_id", $value->id)->sum('data');

            $rs_set_eligibility = DB::table("seteligibility_extravalue")->where('program_id', $value->first_choice_program_id)->where('application_id', $value->application_id)->where('eligibility_type', 7)->first();
            if (!empty($rs_set_eligibility)) {
                $extraValue = json_decode($rs_set_eligibility->extra_values, 1);
                $minimum_score = $extraValue['minimum_score'];
            } else {
                $minimum_score = 2;
            }

            if ($committee_score < $minimum_score)
                $commitee_status = "Fail";

            foreach ($tmpArr as $k => $v) {
                $rs = SubmissionTestScore::where("submission_id", $value->id)->where("test_score_name", $v)->first();
                if (!empty($rs)) {
                    $total_score += $rs->test_score_rank;
                }
            }

            $academic_score = SubmissionAcademicGradeCalculation::where("submission_id", $value->id)->sum('given_score');
            $total_score += $committee_score + $academic_score;



            $rs = PreliminaryScore::updateOrCreate(["submission_id" => $value->id], array("submission_id" => $value->id, "score_value" => $total_score, "thresold_val" => $request->thresold_val));

            if ($total_score < $request->thresold_val || $commitee_status == "Fail") {
                $insert = [];
                $insert['submission_id'] = $value->id;
                $insert['first_choice_final_status'] = "Denied due to Ineligibility";
                $insert['first_waitlist_for'] = $value->first_choice_program_id;
                $insert['first_choice_eligibility_reason'] = "Denied due to Preliminary Score";
                if ($value->second_choice_program_id != 0 && $value->second_choice_program_id != "") {
                    $insert['second_waitlist_for'] = $value->second_choice_program_id;
                    $insert['second_choice_final_status'] = "Denied due to Ineligibility";
                    $insert['second_choice_eligibility_reason'] = "Denied due to Preliminary Score";
                }
                $insert['application_id'] = $value->application_id;
                $insert['enrollment_id'] = Session::get("enrollment_id");
                $comment = "System has denied the application because of Ineligible Preliminary Score";
                $old_status = $value->submission_status;
                $status = "Denied due to Ineligibility";
                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value->id], $insert);

                $rs = Submissions::where("id", $value->id)->update(array("submission_status" => "Denied due to Ineligibility"));
                $rs = SubmissionsStatusLog::create(array("submission_id" => $value->id, "new_status" => $status, "old_status" => $old_status, "enrollment_id" => Session::get("enrollment_id"), "updated_by" => Auth::user()->id, "comment" => $comment));

                $rs = SubmissionsStatusUniqueLog::updateOrCreate(["submission_id" => $value->id], array("submission_id" => $value->id, "enrollment_id" => Session::get("enrollment_id"), "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id));
            }
        }

        return "Done";
    }

    public function roll_back_submissions()
    {
        $rs = SubmissionsStatusLog::where("comment", "System has denied the application because of Ineligible Preliminary Score")->get();
        foreach ($rs as $key => $value) {
            $status = $value->old_status;
            $submission_id = $value->submission_id;
            $rs1 = Submissions::where("id", $submission_id)->update(array("submission_status" => $status));
            $rs1 = SubmissionsFinalStatus::where("submission_id", $submission_id)->delete();

            $rs = PreliminaryScore::where("submission_id", $value->submission_id)->delete();
        }
        $rs = SubmissionsStatusLog::where("comment", "System has denied the application because of Ineligible Preliminary Score")->delete();

        echo "Done";
        # code...
    }
}
