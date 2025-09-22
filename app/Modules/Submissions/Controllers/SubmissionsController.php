<?php

namespace App\Modules\Submissions\Controllers;

use App\Modules\School\Models\School;
use App\Modules\District\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\Program\Models\{Program, ProgramEligibility};
use App\Modules\Submissions\Models\Submissions;
use App\Modules\LateSubmission\Models\LateSubmissionProcessLogs;
use App\Modules\Submissions\Models\{SubmissionGrade, SubmissionComment, SubmissionsStatusUniqueLog, SubmissionsFinalStatus, SubmissionsStatusLog, SubmissionGradeChange, SubmissionsWaitlistFinalStatus, SubmissionsWaitlistStatusUniqueLog, LateSubmissionFinalStatus, SubmissionRecommendation, SubmissionsLatestFinalStatus, SubmissionManualEmail, SubmissionsAttendance};
use App\Modules\Submissions\Models\{SubmissionAudition, SubmissionTestScore};
use App\Modules\Submissions\Models\SubmissionWritingPrompt;
use App\Modules\Submissions\Models\SubmissionInterviewScore;
use App\Modules\Submissions\Models\SubmissionCommitteeScore;
use App\Modules\Submissions\Models\SubmissionConductDisciplinaryInfo;
use App\Modules\Submissions\Models\SubmissionStandardizedTesting;
use App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation;
use App\Modules\Application\Models\ApplicationConfiguration;
use App\Modules\Eligibility\Models\SubjectManagement;
use App\Modules\ProcessSelection\Models\{Availability, ProcessSelection};
use App\Modules\EditCommunication\Models\{EditCommunication, EditCommunicationLog};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\LateSubmission\Models\LateSubmissionEditCommunication;
use App\Modules\Waitlist\Models\{WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog, WaitlistIndividualAvailability, WaitlistEditCommunication};
use App\Modules\WritingPrompt\Models\EmailActivityLog;
use App\Modules\Import\Models\AgtToNch;
use App\Modules\WritingPrompt\Models\WritingPrompt;
use App\Modules\WritingPrompt\Models\WritingPromptLog;
use App\Modules\WritingPrompt\Models\WritingPromptDetail;
use App\Modules\WritingPrompt\Models\WritingPromptDetailLog;
use App\Modules\Submissions\Models\SubmissionData;
use App\StudentGrade;
use App\StudentCDI;
use App\Traits\AuditTrail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class SubmissionsController extends Controller
{
    use AuditTrail;

    public $eligibility_grade_pass = array();
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->submission = new Submissions();
    }

    public function index()
    {
        // return Session::get('enrollment_id');

        /*$today="2021-01-16 01:05:09";
        $nextday=date("Y-m-d H:i:s", strtotime("$today +4 hour"));

        $submissions = Submissions::where("id", ">", 3018)->get();
        foreach($submissions as $key=>$value)
        {
            echo $nextday."<BR>";
            $rs = Submissions::where("id", $value->id)->update(array("grade_override"=>"N", "cdi_override"=>"N"));
            $nextday=date("Y-m-d H:i:s", strtotime("$nextday +1 hour"));

        }
        exit;*/


        $programs = Auth::user()->programs;
        if ($programs == "22") {
            $submissions = Submissions::join('application', 'application.id', 'submissions.application_id')
                ->join('enrollments', 'enrollments.id', 'application.enrollment_id')
                ->where('submissions.district_id', Session::get('district_id'))
                ->where(function ($query) use ($programs) {
                    $query->whereRaw('FIND_IN_SET(submissions.first_choice_program_id, "' . implode(",", $programs) . '")')
                        ->orWhereRaw('FIND_IN_SET(submissions.second_choice_program_id, "' . implode(",", $programs) . '")');
                })
                ->select('submissions.*', 'enrollments.school_year')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $submissions = Submissions::join('application', 'application.id', 'submissions.application_id')
                ->join('enrollments', 'enrollments.id', 'application.enrollment_id')
                ->where('submissions.district_id', Session::get('district_id'))
                ->select('submissions.*', 'enrollments.school_year')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $all_data = $this->submission->getSearhData();
        return view("Submissions::index", compact('all_data'));



        //return view("Submissions::index",compact('submissions'));
    }


    public function getSubmissions(Request $request)
    {
        $send_arr = $data_arr = array();

        /*  $offer_data = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->select("submissions_final_status.*", "submissions.*")->get();

        foreach($offer_data as $key=>$value)
        {

            if($value->first_choice_final_status == "Offered")
                $awarded_school = getProgramName($value->first_waitlist_for);
            elseif($value->second_choice_final_status == "Offered")
                $awarded_school = getProgramName($value->second_waitlist_for);
            else
                $awarded_school = "";
            $ts = Submissions::where("id", $value->submission_id)->update(array("awarded_school"=>$awarded_school));
        }*/

        $submissions = $this->submission->getSubmissionList($request->all(), 1);
        $total = $this->submission->getSubmissionList($request->all(), 0);

        foreach ($submissions as $value) {

            $sub_id_edit = $sub_status = $student_id = '';
            if ((checkPermission(Auth::user()->role_id, 'Submissions/edit') == 1)) {
                $sub_id_edit = "<a href=" . url('admin/Submissions/edit', $value->id) . " title='edit'>" . $value->id . "</a>";
                $sub_id_edit .= "<div class=''> <a href=" . url('admin/Submissions/edit', $value->id) . " class='font-18 ml-5 mr-5' title='Edit'><i class='far fa-edit'></i></a> </div>";
            } else {
                $sub_id_edit = $value->id;
            }

            if ($value->submission_status == "Active" || $value->submission_status == "Offered and Accepted") {
                $sub_status = "<div class='alert1 alert-success p-10 text-center d-block'>" . $value->submission_status . "</div>";
            } elseif ($value->submission_status == "Auto Decline") {
                $sub_status = "<div class='alert1 alert-secondary p-10 text-center d-block'>" . $value->submission_status . "</div>";
            } elseif ($value->submission_status == "Application Withdrawn" || $value->submission_status == "Offered and Declined" || $value->submission_status == "Denied due to Ineligibility") {
                $sub_status = "<div class='alert1 alert-danger p-10 text-center d-block'>" . $value->submission_status . "</div>";
            } elseif ($value->submission_status == "Denied due to Incomplete Records") {
                $sub_status = "<div class='alert1 alert-info p-10 text-center d-block'>" . $value->submission_status . "</div>";
            } else {
                $sub_status = "<div class='alert1 alert-warning p-10 text-center d-block'>" . $value->submission_status . "</div>";
            }
            if ($value->student_id != "") {
                $student_id = "<div class='alert1 alert-success p-10 text-center d-block'>Current</div>";
            } else {
                $student_id = "<div class='alert1 alert-warning p-10 text-center d-block'>New</div>";
            }

            if ($value->late_submission == "Y") {
                $late_submission = "<div class='alert1 alert-success text-center'>Yes</div>";
            } else {
                $late_submission = "<div class='alert1 alert-danger text-center'>No</div>";
            }

            $send_arr[] = [
                $sub_id_edit,
                $value->student_id,
                $value->school_year,
                $value->first_name . ' ' . $value->last_name,
                $value->parent_first_name . ' ' . $value->parent_last_name,
                $value->phone_number,
                $value->address . ", " . $value->city . ", " . $value->state . " - " . $value->zip,
                $value->parent_email,
                $value->race,
                getDateFormat($value->birthday),
                $value->current_school,
                $value->current_grade,
                $value->next_grade,
                getProgramName($value->first_choice_program_id),
                getProgramName($value->second_choice_program_id),
                getProgramName($value->third_choice_program_id),
                getDateTimeFormat($value->created_at),
                findSubmissionForm($value->application_id),
                $sub_status,
                $value->zoned_school,
                $student_id,
                $value->confirmation_no,
                $value->awarded_school,
                $late_submission,
                $value->gender

            ];
        }

        $data_arr['recordsTotal'] = $total;
        $data_arr['recordsFiltered'] = $total;
        $data_arr['data'] = $send_arr;
        return json_encode($data_arr);
    }

    public function testindex()
    {
        $submissions = Submissions::join('application', 'application.id', 'submissions.application_id')
            ->join('enrollments', 'enrollments.id', 'application.enrollment_id')
            ->where('submissions.district_id', Session::get('district_id'))
            ->select('submissions.*', 'enrollments.school_year')
            ->orderBy('created_at', 'desc')
            ->get();
        // return $submissions;
        return view("Submissions::testindex", compact('submissions'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $rsAcademicScore = SubmissionAcademicGradeCalculation::where("submission_id", $id)->first();
        if (!empty($rsAcademicScore)) {
            $academic_score = $rsAcademicScore->given_score;
        } else {
            $academic_score = "";
        }
        $display_outcome = SubmissionsStatusUniqueLog::count();
        $single_submission = Submissions::where("id", $id)->first();
        $last_process = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("commited", "Yes")->where("application_id", $single_submission->form_id)->orderBy("created_at", "DESC")->first();
        $offer_data = null;
        $obj = check_last_process($id);
        $from = "";
        $unique_id = 0;
        $version = 0;
        $fromObj = new SubmissionsFinalStatus();
        if ($obj['finalObj'] == "waitlist") {
            $fromObj = new SubmissionsWaitlistFinalStatus();
            $unique_id = $obj['id'];
            $version = $obj['version'];
            $from = "Waitlist";
        } elseif ($obj['finalObj'] == "late_submission") {
            $fromObj = new LateSubmissionFinalStatus();
            $unique_id = $obj['id'];
            $version = $obj['version'];
            $from = "LateSubmission";
        } elseif ($obj['finalObj'] == "regular") {
            $unique_id = $obj['id'];
            $from = "ProcessSelection";
        }

        $last_date_online_acceptance = "";
        $last_date_offline_acceptance = "";
        $offer_data = $fromObj::where("id", $unique_id)->first();
        if (!empty($offer_data)) {
            if ($offer_data->last_date_online_acceptance == "") {
                $last_process = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("commited", "Yes")->where("version", $offer_data->version)->where("type", $obj['finalObj'])->where("application_id", $single_submission->form_id)->orderBy("created_at", "DESC")->first();
                if (!empty($last_process)) {
                    $last_date_online_acceptance = $last_process->last_date_online_acceptance;
                    $last_date_offline_acceptance = $last_process->last_date_offline_acceptance;
                }
            } else {
                $last_date_online_acceptance = $offer_data->last_date_online_acceptance;
                $last_date_offline_acceptance = $offer_data->last_date_offline_acceptance;
            }
        }




        $grade_change_data = SubmissionGradeChange::where("submission_id", $id)->whereNotNull('old_contract_file_name')->orderBy('old_contract_date', 'desc')->get();
        $grade_change_count = SubmissionGradeChange::where("submission_id", $id)->count();

        $last_date_online_acceptance = $last_date_offline_acceptance = "";


        if (!empty($offer_data)) {
            if ($offer_data->last_date_online_acceptance != "")
                $last_date_online_acceptance = $offer_data->last_date_online_acceptance;
            if ($offer_data->last_date_offline_acceptance != "")
                $last_date_offline_acceptance = $offer_data->last_date_offline_acceptance;
        }



        $district = District::where("id", Session::get("district_id"))->first();

        $submission = Submissions::where('id', $id)->first();
        $gradeInfo = SubjectManagement::where("grade", $submission->next_grade)->where("application_id", $submission->application_id)->first();
        $submission->open_enrollment = Enrollment::join('application', 'application.enrollment_id', 'enrollments.id')->where('application.id', $submission->application_id)->select("enrollments.id")->first()->id;

        $data['grades'] = Grade::get();
        $data['enrollments'] = Enrollment::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();
        $data['schools'] = School::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();
        $applicationPrograms = Application::join('application_programs', 'application_programs.application_id', '=', 'application.id')
            ->where('application_id', $submission->application_id)
            ->select('application_programs.*')->get();
        //         return $data['schools'];
        //            print_r($applicationPrograms);exit;
        foreach ($applicationPrograms  as $key => $applicationProgram) {
            // echo $applicationProgram->program_id."<BR>";
            $applicationPrograms[$key]->grade_id = Grade::where('id', $applicationProgram->grade_id)->first()->name;
            $applicationPrograms[$key]->program_id = Program::where('id', $applicationProgram->program_id)->first()->name;
        }
        $data['applicationPrograms'] = $applicationPrograms;
        $data['comments'] = SubmissionComment::where('submission_id', $id)
            // ->where('user_id', \Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $data['status_logs'] = SubmissionsStatusLog::where('submission_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $data['email_communication'] = EmailActivityLog::where('submission_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();


        $rsNxt = Program::join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $submission->first_choice)->select('grade_lavel')->first();
        $nxt_grades = [];
        if (isset($rsNxt))
            $nxt_grades = explode(",", $rsNxt->grade_lavel);


        /* code for Discpline */
        $enrollment_data = Enrollment::where("id", $submission->enrollment_id)->first();
        $school_year = $enrollment_data->school_year;
        $tmp = explode("-", $school_year);
        $tmpYear = $tmp[0] - 1;
        $rs = SubmissionsAttendance::groupBy('attendance_code')->where("student_id", $submission->student_id)->whereDate("attendance_date", ">=", ($tmpYear) . "-08-01")->where('attendance_mode_code', 'ATT_ModeDaily')->whereDate('attendance_date', "<=", ($tmpYear + 1) . "-06-01")->where('attendance_code', 'AP')->select('attendance_code', DB::raw('count(*) as total'))->get();
        $discpline_data = $rs;

        /* CDI Data */
        $cdi_info = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->first();

        /* */
        $manual_processing = "N";
        if ($submission->submission_status == "Denied due to Incomplete Records" || $submission->submission_status == "Denied due to Ineligibility") {
            if ($submission->cdi_override == "Y" && $submission->grade_override == "Y")
                $manual_processing = "Y";
            else {
                $manual_processing = $this->checkSubmissionEligibility($submission);
            }
        }
        $waitlist_data = $late_submission_data = [];

        $manual_email = SubmissionManualEmail::where("submission_id", $id)->first();


        // return $submission;
        return view('Submissions::edit_singletab', compact('data', 'submission', 'district', 'gradeInfo', 'display_outcome', 'offer_data', 'waitlist_data', 'manual_processing', 'last_date_online_acceptance', 'last_date_offline_acceptance', "nxt_grades", "grade_change_data", "grade_change_count", "late_submission_data", "academic_score", "from", "manual_email", "cdi_info", "discpline_data", "tmpYear"));
    }

    public function checkSubmissionEligibility($submission)
    {
        $subjects = $terms = array();
        $eligibilityArr = array();

        $manual_processing = "N";

        $eligibilityData = getEligibilities($submission->first_choice, 'Academic Grade Calculation');
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

        if ($submission->second_choice != "") {
            $eligibilityData = getEligibilities($submission->second_choice, 'Academic Grade Calculation');
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

        $setEligibilityData = array();
        $data = getSetEligibilityData($submission->first_choice, 3);
        foreach ($subjects as $svalue) {
            foreach ($terms as $tvalue) {
                if (isset($data->{$svalue . "-" . $tvalue})) {
                    $setEligibilityData[$submission->first_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                }
                /*                        else
                    $setEligibilityData[$value->first_choice][$svalue."-".$tvalue] = 50;*/
            }
        }

        if ($submission->second_choice != '') {
            $data = getSetEligibilityData($submission->second_choice, 3);
            foreach ($subjects as $svalue) {
                foreach ($terms as $tvalue) {
                    if (isset($data->{$svalue . "-" . $tvalue})) {
                        $setEligibilityData[$submission->second_choice][$svalue . "-" . $tvalue] = $data->{$svalue . "-" . $tvalue}[0];
                    }
                    /*   else
                        $setEligibilityData[$value->second_choice][$svalue."-".$tvalue] = 50;*/
                }
            }
        }

        $setCDIEligibilityData = array();
        $data = getSetEligibilityData($submission->first_choice, 8);
        if (!empty($data)) {
            $setCDIEligibilityData[$submission->first_choice]['b_info'] = $data->B[0];
            $setCDIEligibilityData[$submission->first_choice]['c_info'] = $data->C[0];
            $setCDIEligibilityData[$submission->first_choice]['d_info'] = $data->D[0];
            $setCDIEligibilityData[$submission->first_choice]['e_info'] = $data->E[0];
            $setCDIEligibilityData[$submission->first_choice]['susp'] = $data->Susp[0];
            $setCDIEligibilityData[$submission->first_choice]['susp_days'] = $data->SuspDays[0];
        }

        if ($submission->second_choice != '') {
            $data = getSetEligibilityData($submission->second_choice, 8);
            if (!empty($data)) {
                $setCDIEligibilityData[$submission->second_choice]['b_info'] = $data->B[0];
                $setCDIEligibilityData[$submission->second_choice]['c_info'] = $data->C[0];
                $setCDIEligibilityData[$submission->second_choice]['d_info'] = $data->D[0];
                $setCDIEligibilityData[$submission->second_choice]['e_info'] = $data->E[0];
                $setCDIEligibilityData[$submission->second_choice]['susp'] = $data->Susp[0];
                $setCDIEligibilityData[$submission->second_choice]['susp_days'] = $data->SuspDays[0];
            }
        }
        $score =  $this->collectionStudentGradeReport($submission, $subjects, $terms, $submission->next_grade, $setEligibilityData);

        if (!empty($score)) {
            if ($submission->cdi_override == "Y") {
                $manual_processing = "Y";
            } else {
                $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $submission->id)->first();
                if (!empty($cdi_data)) {
                    $cdiArr = array();
                    $cdiArr['b_info'] = $cdi_data->b_info;
                    $cdiArr['c_info'] = $cdi_data->c_info;
                    $cdiArr['d_info'] = $cdi_data->d_info;
                    $cdiArr['e_info'] = $cdi_data->e_info;
                    $cdiArr['susp'] = $cdi_data->susp;
                    $cdiArr['susp_days'] = $cdi_data->susp_days;
                    if (isset($setCDIEligibilityData[$submission->first_choice]['b_info'])) {
                        if (!is_numeric($cdiArr['b_info'])) {
                            $manual_processing = "Y";
                        } elseif ($cdiArr['b_info'] > $setCDIEligibilityData[$submission->first_choice]['b_info'] || $cdiArr['c_info'] > $setCDIEligibilityData[$submission->first_choice]['c_info'] || $cdiArr['d_info'] > $setCDIEligibilityData[$submission->first_choice]['d_info'] || $cdiArr['e_info'] > $setCDIEligibilityData[$submission->first_choice]['e_info'] || $cdiArr['susp'] > $setCDIEligibilityData[$submission->first_choice]['susp'] || $cdiArr['susp_days'] > $setCDIEligibilityData[$submission->first_choice]['susp_days']) {
                        } else {
                            $manual_processing = "Y";
                        }
                    }
                } elseif ($submission->cdi_override == "Y") {
                    $manual_processing = "Y";
                }
            }


            if ($this->eligibility_grade_pass[$submission->id]['first'] != "Pass") {
                $manual_processing = "N";
            }
            if ($submission->second_choice != "") {
                if ($this->eligibility_grade_pass[$submission->id]['second'] != "Pass") {
                    $manual_processing = "N";
                }
            }
        }
        return $manual_processing;
    }

    public function collectionStudentGradeReport($submission, $subjects, $terms, $next_grade = 0, $setEligibilityData)
    {
        $config_subjects = Config::get('variables.subjects');
        $score = array();
        $missing = false;

        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();
        $import_academic_year = Config::get('variables.import_academic_year');
        $first_failed = $second_failed = 0;
        foreach ($subjects as $value) {
            foreach ($terms as $value1) {

                $marks = getSubmissionAcademicScoreMissing($submission->id, $config_subjects[$value], $value1, $import_academic_year, $import_academic_year);
                /* Here copy above function if condition  for NA */

                if ($marks == "NA") {
                    if ($submission->grade_override == "Y") {
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

    public function getProgramGrades($choice_id)
    {
        $rs = Program::join("application_programs", "application_programs.program_id", "program.id")->where("application_programs.id", $choice_id)->select('grade_lavel')->first();
        return explode($rs->grade_lavel);
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
        $req = $request->all();

        $first_choice_program_id = $second_choice_program_id = $third_choice_program_id = 0;
        if ($request->first_choice != "") {
            $rs = ApplicationProgram::where("id", $request->first_choice)->select("program_id")->first();
            if (!empty($rs))
                $first_choice_program_id = $rs->program_id;
        }


        if ($request->second_choice != "") {
            $rs = ApplicationProgram::where("id", $request->second_choice)->select("program_id")->first();
            if (!empty($rs))
                $second_choice_program_id = $rs->program_id;
        }


        if ($request->third_choice != "") {
            $rs = ApplicationProgram::where("id", $request->third_choice)->select("program_id")->first();
            if (!empty($rs))
                $third_choice_program_id = $rs->program_id;
        }



        //return $request;
        $data = [
            // 'student_id'=>$request->student_id,
            'student_id' => $request->student_id,
            // 'application_id'=>$request->application_id,
            'first_choice_program_id' => $first_choice_program_id,
            'second_choice_program_id' => $second_choice_program_id,
            'third_choice_program_id' => $third_choice_program_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'race' => $request->race,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'current_school' => $request->current_school,
            'current_grade' => $request->current_grade,
            'next_grade' => $request->next_grade,
            // 'non_hsv_student'=>$request->non_hsv_student,
            'special_accommodations' => $request->special_accommodations,
            'parent_first_name' => $request->parent_first_name,
            'parent_last_name' => $request->parent_last_name,
            'parent_email' => $request->parent_email,
            /*'emergency_contact'=>$request->emergency_contact,
            'emergency_contact_phone'=>$request->emergency_contact_phone,
            'emergency_contact_relationship'=>$request->emergency_contact_relationship,*/
            'phone_number' => $request->phone_number,
            'alternate_number' => $request->alternate_number,
            'zoned_school' => $request->zoned_school,
            // 'lottery_number'=>$request->lottery_number,
            'open_enrollment' => $request->open_enrollment,
            'submission_status' => $request->submission_status,
            'mcp_employee' => $request->mcp_employee,
            'employee_first_name' => $request->employee_first_name,
            'employee_last_name' => $request->employee_last_name,
            'work_location' => $request->work_location,
            'employee_id' => $request->employee_id,
            'sp_exception' => $request->sp_exception,
            'is_504_eligible' => $request->is_504_eligible,
            'manual_grade_change' => $request->manual_grade_change == "on" ? 'Y' : 'N',
            'override_student' => $request->override_student == 'on' ? 'Y' : 'N',
        ];

        // Dynamic Choices
        foreach (getChoiceAry() as $choicekey => $choicetxt) {
            $data[$choicetxt . '_choice'] = $request->{$choicetxt . '_choice'};
            $data[$choicetxt . '_sibling'] = $request->{$choicetxt . '_sibling'};
        }
        //return $data;
        if (!isset($request->current_grade))
            unset($data['current_grade']);
        // if($first_choice_program_id == 0)
        //     unset($data['first_choice_program_id']);
        if (!isset($request->next_grade))
            unset($data['next_grade']);


        /*  Code Audit Trail to Get Original Value */
        $initSubmission = Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        $result = Submissions::where('id', $id)->update($data);

        $initSubmission->gender = "";
        $initSubmission->letter_body = "";

        /*  Code Audit Trail to Get New Value */
        $newObj =  Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();

        if ($initSubmission->first_choice != $request->first_choice || $initSubmission->second_choice != $request->second_choice || $initSubmission->third_choice != $request->third_choice) {
            $submission_event = "";
            if ($initSubmission->first_choice != $request->first_choice) {
                $submission_event .= "First Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->first_choice_program_id) . "</span> TO <span class='text-success'>" . getProgramName($newObj->first_choice_program_id) . "<br></span>";
            }

            if ($initSubmission->second_choice != $request->second_choice && $request->second_choice != "" && $initSubmission->second_choice != '') {
                $submission_event .= "Second Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->second_choice_program_id) . "</span> TO <span class='text-success'>" . getProgramName($newObj->second_choice_program_id) . "<br></span>";
            } elseif ($initSubmission->second_choice != $request->second_choice && ($request->second_choice == "" && $request->second_choice == "0")) {
                $submission_event .= "<br>Second Choice Program : <span class='text-danger'>" . getProgramName($newObj->second_choice_program_id) . "</span> TO <span class='text-success'>None<br></span>";
            } elseif ($initSubmission->second_choice != $request->second_choice && ($initSubmission->second_choice == "")) {
                $submission_event .= "<br>Second Choice Program : <span class='text-danger'>None</span> TO <span class='text-success'>" . getProgramName($newObj->second_choice_program_id) . "<br></span>";
            }

            if ($initSubmission->third_choice != $request->third_choice && $request->third_choice != "" && $initSubmission->third_choice != '') {

                $submission_event .= "Third Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->third_choice_program_id) . "</span> TO <span class='text-success'>" . getProgramName($newObj->third_choice_program_id) . "<br></span>";
            } elseif ($initSubmission->third_choice != '' && !isset($request->third_choice)) {
                $submission_event .= "<br>Third Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->third_choice_program_id) . "</span> TO <span class='text-success'>None<br></span>";
            } elseif ($initSubmission->third_choice != $request->third_choice && $initSubmission->third_choice == "") {
                $submission_event .= "<br>Third Choice Program : <span class='text-danger'>None</span> TO <span class='text-success'>" . getProgramName($newObj->third_choice_program_id) . "<br></span>";
            }

            $comment_data = [
                'submission_id' => $id,
                'user_id' => Auth::user()->id,
                'comment' => $request->choice_comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->gender = $request->choice_comment;

            if ($initSubmission->second_choice != $request->second_choice &&  $request->second_choice == '') {
                $submission_event = "Second Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->second_choice_program_id) . "</span> TO <span class='text-success'>None<br></span>";
                $comment_data = [
                    'submission_id' => $id,
                    'user_id' => Auth::user()->id,
                    'comment' => $request->choice_comment,
                    'submission_event' => $submission_event
                ];
                SubmissionComment::create($comment_data);
                $newObj->gender = $request->choice_comment;
            }
        } elseif ($initSubmission->second_choice != $request->second_choice &&  $request->second_choice == '') {
            $submission_event = "Second Choice Program : <span class='text-danger'>" . getProgramName($initSubmission->second_choice_program_id) . "</span> TO <span class='text-success'>None<br></span>";
            $comment_data = [
                'submission_id' => $id,
                'user_id' => Auth::user()->id,
                'comment' => $request->choice_comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->gender = $request->choice_comment;
        }

        if ($initSubmission->manual_grade_change != $newObj->manual_grade_change) {
            $submission_event = "Manualy Grade Change  : <span class='text-danger'>" . $initSubmission->manual_grade_change . "</span> TO <span class='text-success'>" . $newObj->manual_grade_change . "<br></span>";
            $comment_data = [
                'submission_id' => $id,
                'user_id' => Auth::user()->id,
                'comment' => $request->grade_change_comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->gender = $request->choice_comment;
        }

        if ($initSubmission->submission_status != $request->submission_status) {
            $submission_event = "Submission Status : <span class='text-danger'>" . $initSubmission->submission_status . "</span> TO <span class='text-success'>" . $newObj->submission_status . "<br></span>";
            $comment_data = [
                'submission_id' => $id,
                'user_id' => Auth::user()->id,
                'comment' => $request->status_comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->letter_body = $request->status_comment;

            $commentObj = array();
            $commentObj['old_status'] = $initSubmission->submission_status;
            $commentObj['new_status'] = $newObj->submission_status;
            $commentObj['updated_by'] = Auth::user()->id;
            $commentObj['comment'] = $request->status_comment;
            $commentObj['submission_id'] = $id;
            SubmissionsStatusLog::create($commentObj);
        }

        /*if($initSubmission->submission_status != "Offered" && $newObj->submission_status == "Offered")
        {
            $rs = SubmissionsFinalStatus::where("submission_id", $id)->update(array("first_choice_final_status"=>"Offered", "first_waitlist_for"=>$newObj->first_choice_program_id));        
        }*/
        if ($initSubmission->submission_status != $request->submission_status) {
            $submission_event = "Submission Status : <span class='text-danger'>" . $initSubmission->submission_status . "</span> TO <span class='text-success'>" . $newObj->submission_status . "<br></span>";
            $comment_data = [
                'submission_id' => $id,
                'user_id' => Auth::user()->id,
                'comment' => $request->status_comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->letter_body = $request->status_comment;

            $commentObj = array();
            $commentObj['old_status'] = $initSubmission->submission_status;
            $commentObj['new_status'] = $newObj->submission_status;
            $commentObj['updated_by'] = Auth::user()->id;
            $commentObj['comment'] = $request->status_comment;
            $commentObj['submission_id'] = $id;
            SubmissionsStatusLog::create($commentObj);



            /* New code for Submission Status Update */
            $obj = check_last_process($id);
            $unique_id = 0;
            $version = 0;
            $from = new SubmissionsFinalStatus();
            if ($obj['finalObj'] == "waitlist") {
                $from = new SubmissionsWaitlistFinalStatus();
                $unique_id = $obj['id'];
                $version = $obj['version'];
            } elseif ($obj['finalObj'] == "late_submission") {
                $from = new LateSubmissionFinalStatus();
                $unique_id = $obj['id'];
                $version = $obj['version'];
            } elseif ($obj['finalObj'] == "regular") {
                $unique_id = $obj['id'];
            }


            /* When Offred or Offered and Accepted */
            $comment = "";
            if ($newObj->submission_status == "Offered" || $newObj->submission_status == "Offered and Accepted") { //8
                if ($newObj->submission_status == "Offered")
                    $awarded_school = getProgramName($request->newofferprogram);
                if ($initSubmission->submission_status == "Auto Decline") { // 7
                    $rsData = $from::where("id", $unique_id)->first();
                    $snid = $rsData->id;
                    if ($rsData->first_choice_final_status == "Offered") {
                        $tmp['first_offer_status'] = "Accepted";
                        $tmp['second_offer_status'] = "NoAction";
                        $tmp['third_offer_status'] = "NoAction";
                    } elseif ($rsData->second_choice_final_status == "Offered") {
                        $tmp['first_offer_status'] = "NoAction";
                        $tmp['second_offer_status'] = "Accepted";
                        $tmp['third_offer_status'] = "NoAction";
                    } elseif ($rsData->second_choice_final_status == "Offered") {
                        $tmp['first_offer_status'] = "NoAction";
                        $tmp['second_offer_status'] = "NoAction";
                        $tmp['third_offer_status'] = "Accepted";
                    }
                    $rs = SubmissionsLatestFinalStatus::where("submission_id", $id)->update($tmp);
                    $rsData = $from::where("id", $unique_id)->update($tmp);
                } //7
                else { // 6
                    if ($obj['finalObj'] == "" && $initSubmission->late_submission == "Y") {
                        $from = new LateSubmissionFinalStatus();
                    }

                    $data = [];
                    $data['submission_id'] = $id;
                    $data['enrollment_id'] = $newObj->enrollment_id;
                    $data['application_id'] = $newObj->form_id;
                    if (isset($request->newofferprogram)) { //5
                        if ($initSubmission->first_choice_program_id == $request->newofferprogram) {
                            $data['first_choice_final_status'] = "Offered";
                            $program_name = getProgramName($initSubmission->first_choice_program_id);

                            if ($newObj->submission_status == "Offered and Accepted") {
                                $data['first_offer_status'] = "Accepted";
                                $data['second_offer_status'] = "NoAction";
                                $data['third_offer_status'] = "NoAction";
                            } else {
                                $data['first_offer_status'] = "Pending";
                                $data['second_offer_status'] = "Pending";
                                $data['third_offer_status'] = "Pending";
                            }
                            $data['first_waitlist_for'] = $initSubmission->first_choice_program_id;
                            $data['second_choice_final_status'] = "Pending";
                            $data['third_choice_final_status'] = "Pending";
                        } elseif ($initSubmission->second_choice_program_id == $request->newofferprogram) {
                            $data['second_choice_final_status'] = "Offered";
                            $program_name = getProgramName($initSubmission->second_choice_program_id);

                            if ($newObj->submission_status == "Offered and Accepted") {
                                $data['first_offer_status'] = "NoAction";
                                $data['second_offer_status'] = "Offered";
                                $data['third_offer_status'] = "NoAction";
                            } else {
                                $data['first_offer_status'] = "Pending";
                                $data['second_offer_status'] = "Pending";
                                $data['third_offer_status'] = "Pending";
                            }
                            $data['second_waitlist_for'] = $initSubmission->second_choice_program_id;
                            $data['first_choice_final_status'] = "Waitlisted";
                            $data['third_choice_final_status'] = "Pending";
                        } else {
                            $data['first_choice_final_status'] = "Waitlisted";
                            $data['second_choice_final_status'] = "Waitlisted";
                            $data['third_choice_final_status'] = "Offered";
                            $program_name = getProgramName($initSubmission->second_choice_program_id);
                            if ($newObj->submission_status == "Offered and Accepted") {
                                $data['first_offer_status'] = "NoAction";
                                $data['second_offer_status'] = "NoAction";
                                $data['third_offer_status'] = "Accepted";
                            } else {
                                $data['first_offer_status'] = "Pending";
                                $data['second_offer_status'] = "Pending";
                                $data['third_offer_status'] = "Pending";
                            }
                            $data['third_waitlist_for'] = $initSubmission->third_choice_program_id;
                        }
                        do {
                            $code = mt_rand(100000, 999999);
                            $user_code1 = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                            $user_code2 = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                            $user_code3 = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                        } while (!empty($user_code1) && !empty($user_code2) && !empty($user_code3));
                        $data['offer_slug'] = $code;
                        $data['manually_updated'] = "Y";
                        $data['last_date_online_acceptance'] = $req['last_date_online_acceptance'];
                        $data['last_date_offline_acceptance'] = $req['last_date_offline_acceptance'];
                        $data['communication_sent'] = 'N';
                        $data['version'] = $version;
                    } //5
                    else { // 4
                        $newData = $from::where("id", $unique_id)->first();
                        if ($newData->first_choice_final_status == "Offered") {
                            $program_name = getProgramName($initSubmission->first_choice_program_id);

                            $data['first_offer_status'] = "Accepted";
                            $data['second_offer_status'] = "NoAction";
                            $data['third_offer_status'] = "NoAction";
                            $data['first_waitlist_for'] = $initSubmission->first_choice_program_id;
                            $data['second_choice_final_status'] = "Pending";
                            $data['third_choice_final_status'] = "Pending";
                        } elseif ($newData->second_choice_final_status == "Offered") {
                            $program_name = getProgramName($initSubmission->second_choice_program_id);

                            $data['first_offer_status'] = "NoAction";
                            $data['second_offer_status'] = "Accepted";
                            $data['third_offer_status'] = "NoAction";
                            $data['second_waitlist_for'] = $initSubmission->second_choice_program_id;
                            $data['first_choice_final_status'] = "Waitlisted";
                            $data['third_choice_final_status'] = "Pending";
                        } else {
                            $program_name = getProgramName($initSubmission->third_choice_program_id);

                            $data['first_offer_status'] = "NoAction";
                            $data['second_offer_status'] = "NoAction";
                            $data['third_offer_status'] = "Accepted";
                            $data['third_waitlist_for'] = $initSubmission->third_choice_program_id;
                            $data['first_choice_final_status'] = "Waitlisted";
                            $data['second_choice_final_status'] = "Waitlisted";
                        }
                    } //4
                    //dd($data);
                    SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $id], $data);

                    if ($unique_id == 0) {
                        $rs = $from::create($data);
                    } else {
                        $rs = $from::where("id", $unique_id)->update($data);
                    }
                    $comment = getUserName(Auth::user()->id) . " has Offered " . $program_name . " to Parent";
                } //6

                if ($newObj->submission_status == "Offered")
                    $rs = Submissions::where("id", $id)->update(["awarded_school" => $awarded_school]);
            } // 8

            /* When Offered and Declined */
            if ($newObj->submission_status == "Offered and Declined") { //3
                $data = [];
                $data['first_offer_status'] = "Declined";
                $data['second_offer_status'] = "Declined";
                $data['third_offer_status'] = "Declined";
                SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $id], $data);

                $rs = $from::where("id", $unique_id)->update($data);
            } //3

            /* When Waitlisted */
            if ($newObj->submission_status == "Waitlisted") { //2
                $data = [];
                $data['submission_id'] = $id;
                $data['enrollment_id'] = $newObj->enrollment_id;
                $data['application_id'] = $newObj->form_id;
                $data['first_choice_final_status'] = "Waitlisted";
                if ($initSubmission->second_choice != "")
                    $data['second_choice_final_status'] = "Waitlisted";
                if ($initSubmission->third_choice != "")
                    $data['third_choice_final_status'] = "Waitlisted";
                $data['first_offer_status'] = "Pending";
                $data['second_offer_status'] = "Pending";
                $data['third_offer_status'] = "Pending";
                $data['version'] = $version;
                SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $id], $data);

                if ($unique_id == 0) {
                    $rs = $from::create($data);
                } else {
                    $rs = $from::where("id", $unique_id)->update($data);
                }
            } //2

            /* When Waitlisted */
            if ($newObj->submission_status == "Denied due to Ineligibility" || $newObj->submission_status == "Denied due to Incomplete Records") { //1
                $data = [];
                $data['submission_id'] = $id;
                $data['enrollment_id'] = $newObj->enrollment_id;
                $data['application_id'] = $newObj->form_id;
                $data['first_choice_final_status'] = $newObj->submission_status;
                if ($initSubmission->second_choice != "")
                    $data['second_choice_final_status'] = $newObj->submission_status;
                if ($initSubmission->third_choice != "")
                    $data['third_choice_final_status'] = $newObj->submission_status;
                $data['first_offer_status'] = "NoAction";
                $data['second_offer_status'] = "NoAction";
                $data['third_offer_status'] = "NoAction";
                $data['version'] = $version;
                SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $id], $data);

                if ($unique_id == 0) {
                    $rs = $from::create($data);
                } else {
                    $rs = $from::where("id", $unique_id)->update($data);
                }
            } //1




            $rs = SubmissionsStatusLog::updateOrCreate(["submission_id" => $id], array("submission_id" => $id, "new_status" => $newObj->submission_status, "old_status" => $initSubmission->submission_status, "updated_by" => Auth::user()->id));
        }




        $this->modelChanges($initSubmission, $newObj, "Submission - General");

        $result =  $newObj;
        if (isset($result)) {
            Session::flash("success", "Submission Updated successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Submissions');
        }
        return redirect('admin/Submissions/edit/' . $id);
    }

    public function resendConfirmationEmail($id)
    {
        $submission_data = Submissions::where('id', $id)->first();
        $msg_data = ApplicationConfiguration::where("application_id", $submission_data['application_id'])->first();
        $application_data = Application::where("id", $submission_data['application_id'])->first();

        $emailArr = array();
        $emailArr['application_id'] = $submission_data['application_id'];
        $emailArr['first_name'] = $submission_data['first_name'];
        $emailArr['last_name'] = $submission_data['last_name'];
        $emailArr['parent_first_name'] = $submission_data['parent_first_name'];
        $emailArr['parent_last_name'] = $submission_data['parent_last_name'];
        $emailArr['email'] = $submission_data['parent_email'];
        $emailArr['confirm_number'] = $submission_data['confirmation_no'];
        $emailArr['transcript_due_date'] = getDateTimeFormat($application_data->transcript_due_date);

        if ($submission_data->submission_status == "Active") {
            $student_type = "active";
            $emailArr['type'] = "active_email";
            $emailArr['msg'] = $msg_data->active_email;
            $confirm_msg = $msg_data->active_screen;
            $msg_type = "exists_success_application_msg";
            $emailArr['email'] =    $submission_data['parent_email'];
            $subject = $msg_data->active_email_subject;
            $confirm_title = $msg_data->active_screen_title;
            $confirm_subject = $msg_data->active_screen_subject;
        } else {
            $emailArr['type'] = "pending_email";
            $student_type = "pending";
            $msg_type = "new_success_application_msg";
            $emailArr['email'] = $submission_data['parent_email'];
            $emailArr['msg'] = $msg_data->pending_email;
            $confirm_msg = $msg_data->pending_screen;
            $subject = $msg_data->pending_email_subject;
            $confirm_title = $msg_data->pending_screen_title;
            $confirm_subject = $msg_data->pending_screen_subject;
        }
        $subject = str_replace("{student_name}", $emailArr['first_name'] . " " . $emailArr['last_name'], $subject);
        $subject = str_replace("{parent_name}", $emailArr['parent_first_name'] . " " . $emailArr['parent_last_name'], $subject);
        $subject = str_replace("{confirm_number}", $emailArr['confirm_number'], $subject);
        $emailArr['subject'] = $subject;

        $mail = sendMail($emailArr);

        $msg = str_replace("{student_name}", (isset($emailArr['first_name']) ? $emailArr['first_name'] . " " . $emailArr['last_name'] : ""), $emailArr['msg']);
        $msg = str_replace("{parent_name}", (isset($emailArr['parent_first_name']) ? $emailArr['parent_first_name'] . " " . $emailArr['parent_last_name'] : ""), $msg);
        $msg = str_replace("{confirm_number}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
        $msg = str_replace("{confirmation_no}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
        $msg = str_replace("{transcript_due_date}", (isset($emailArr['transcript_due_date']) ? $emailArr['transcript_due_date'] : ""), $msg);

        $data = array();
        $data['submission_id'] = $id;
        $data['email_to'] = $submission_data['parent_email'];
        $data['email_subject'] = $emailArr['subject'];
        $data['email_body'] = $msg;
        $data['logo'] = getDistrictLogo();
        $data['module'] = "Submission - Resend Confirmation";


        if ($mail) {
            $data['status'] = "Success";
            Session::flash('success', 'Confirmation Mail Sent Successfully.');
        } else {
            $data['status'] = "Error";
        }
        createEmailActivityLog($data);

        return redirect('/admin/Submissions/edit/' . $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAudition(Request $req, $id)
    {
        $data = NULL;
        $checkExist = SubmissionAudition::where("submission_id", $id)->first();
        if (isset($checkExist->id)) {
            if (isset($checkExist->data)) {
                $old_data = json_decode($checkExist->data, true);
            }
        }

        if ($req->has('first_data')) {
            $f_choice = 'first';
            $s_choice = 'second';
        } else {
            $f_choice = 'second';
            $s_choice = 'first';
        }
        if (isset($req->{$f_choice . '_data'})) {
            $data[$f_choice . '_data'] = $req->{$f_choice . '_data'};
        }
        if (isset($old_data[$s_choice . '_data'])) {
            $data[$s_choice . '_data'] = $old_data[$s_choice . '_data'];
        }

        if (isset($data)) {
            $data = json_encode($data);
        }
        $result = SubmissionAudition::updateOrCreate(
            ['submission_id' => $id],
            ['data' => $data]
        );

        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }

    public function updateWritingPrompt(Request $req, $id)
    {
        $data  = array(
            'submission_id' => $id,
            'data' => isset($req['data']) ? $req['data'] : null
        );
        $checkExist = SubmissionWritingPrompt::where("submission_id", $id)->first();
        if (isset($checkExist->id)) {
            $checkExist->data = $req['data'];
            $result = $checkExist->save();
        } else {
            $result = SubmissionWritingPrompt::create($data);
        }
        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }
    public function updateInterviewScore(Request $req, $id)
    {
        // return $req;
        $data  = array(
            'submission_id' => $id,
            'data' => isset($req['data']) ? $req['data'] : null
        );

        $oldObj = SubmissionInterviewScore::where("submission_id", $id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();


        $checkExist = SubmissionInterviewScore::where("submission_id", $id)->first();
        if (isset($checkExist->id)) {
            $checkExist->data = $req['data'];
            $result = $checkExist->save();
        } else {
            $result = SubmissionInterviewScore::create($data);
        }
        $newObj = SubmissionInterviewScore::where("submission_id", $id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();

        if (!empty($oldObj)) {
            $this->modelChanges($oldObj, $newObj, "Submission - Interview Score");
        } else
            $this->modelCreate($newObj, "Submission - Interview Score");

        generateCompositeScore($id);
        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }

    public function updateCommitteeScore(Request $req, $id)
    {
        $data = NULL;
        $checkExist = SubmissionCommitteeScore::where("submission_id", $id)->first();
        if (isset($checkExist->id)) {
            if (isset($checkExist->data)) {
                $old_data = json_decode($checkExist->data, true);
            }
        }

        if ($req->has('first_data')) {
            $f_choice = 'first';
            $s_choice = 'second';
        } else {
            $f_choice = 'second';
            $s_choice = 'first';
        }
        if (isset($req->{$f_choice . '_data'})) {
            $data = $req->{$f_choice . '_data'};
        }
        /*if (isset($old_data[$s_choice.'_data'])) {
            $data = $old_data[$s_choice.'_data'];
        }*/
        $program_id = $req->program_id;

        if (isset($data)) {
            // $data = json_encode($data);
        }
        $oldObj = SubmissionCommitteeScore::where("submission_id", $id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();
        $result = SubmissionCommitteeScore::updateOrCreate(
            ['submission_id' => $id, "program_id" => $program_id],
            ['data' => $data]
        );
        $newObj = SubmissionCommitteeScore::where("submission_id", $id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();

        if (!empty($oldObj))
            $this->modelChanges($oldObj, $newObj, "Submission - Committee Score");
        else
            $this->modelCreate($newObj, "Submission - Committee Score");
        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }

    public function updateConductDisciplinaryInfo(Request $req, $id)
    {
        // return $req;
        $data  = [
            'b_info' => $req->b_info ?? 0,
            'c_info' => $req->c_info ?? 0,
            'd_info' => $req->d_info ?? 0,
            'e_info' => $req->e_info ?? 0,
            'susp' => $req->susp ?? 0,
            'susp_days' => $req->susp_days ?? 0,
        ];

        if (isset($req->class3_incident) && $req->class3_incident != "")
            $data['class3_incident'] = $req->class3_incident;

        $conduct_discplinary_info = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();
        if (isset($conduct_discplinary_info)) {
            $result = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->update($data);
            $newconduct_discplinary_info = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();
            $this->modelChanges($conduct_discplinary_info, $newconduct_discplinary_info, "Submission - CDI");
        } else {
            $submission = Submissions::findOrFail($id);
            if (isset($submission)) {
                $new_data = [
                    'submission_id' => $id,
                    'stateID' => $submission->student_id ?? null,
                    // 'student_id' => $submission->student_id ?? null,
                ];
                $data = array_merge($data, $new_data);
                $result = SubmissionConductDisciplinaryInfo::create($data);
                $app_data = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->join("submissions", "submissions.id", "submission_conduct_discplinary_info.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_conduct_discplinary_info.*", "submissions.application_id", "application.enrollment_id")->first();
                $this->modelCreate($app_data, "application");
            }
        }

        $display_outcome = SubmissionsStatusUniqueLog::count();
        $initSubmission = Submissions::where("id", $id)->first();
        if ($display_outcome == 0 && $initSubmission->submission_status == "Pending") {
            $rsGradeData = SubmissionGrade::where("submission_id", $id)->first();
            if (!empty($rsGradeData)) {
                $ins = array();
                $ins['submission_status'] = "Active";
                $rsD = Submissions::where("id", $id)->update($ins);
            }
        }
        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Submissions');
        }

        return redirect()->back();
    }
    public function updateStandardizedTesting(Request $req, $id)
    {
        return  $req;
        foreach ($req['data'] as $k => $v) {
            $data  = array(
                'submission_id' => $id,
                'data' => isset($req['data'][$k]) ? $req['data'][$k] : null,
                'subject' => isset($req['subject'][$k]) ? $req['subject'][$k] : null,
                'method' => isset($req['method'][$k]) ? $req['method'][$k] : null,
            );
            $checkExist = SubmissionStandardizedTesting::where("submission_id", $id)->where('subject', $data['subject'])->first();
            if (isset($checkExist->id)) {
                $checkExist->data = $data['data'];
                $checkExist->method = $data['method'];
                $result = $checkExist->save();
            } else {
                $result = SubmissionStandardizedTesting::create($data);
            }
        }
        // return $data;
        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }
    public function updateAcademicGradeCalculation(Request $req, $id)
    {
        //return $req;
        unset($req['_token']);
        if ($req['given_score'] != '') {
            $data = [];
            $data['gpa'] = $req['gpa'];
            $data['given_score'] = $req['given_score'];
            $data['submission_id'] = $id;

            $result = SubmissionAcademicGradeCalculation::updateOrCreate(["submission_id" => $id], $data);
            //}
            if (isset($result)) {
                Session::flash("success", "Data Updated successfully.");
            } else {
                Session::flash("warning", "Something went wrong , Please try again.");
            }
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        //
    }
    public function storeGrades($id, Request $request)
    {

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
            $tmp['advanced_course_bonus'] = $value->advanced_course_bonus;
            $tmp['actual_numeric_grade'] = $value->actual_numeric_grade;
            $tmp['courseName'] = $value->courseName;
            $current_grade[] = $tmp;
        }

        SubmissionGrade::where('submission_id', $id)->delete();
        $courseType = Config::get('variables.courseType');
        $new_grade = array();
        if (isset($request->academicYear) && count($request->academicYear) > 0) {
            $grades_data = [];
            foreach ($request->academicYear as $key => $value) {
                $grade_data = [
                    'submission_id' => $id,
                    'academicYear' => $request->academicYear[$key] ?? null,
                    'academicTerm' => $request->academicTerm[$key] ?? null,
                    'courseTypeID' => $request->courseTypeID[$key] ?? null,
                    'courseName' => $request->courseName[$key] ?? null,
                    'numericGrade' => $request->numericGrade[$key] ?? 0,
                    'actual_numeric_grade' => $request->actual_numeric_grade[$key] ?? 0,
                    'advanced_course_bonus' => $request->advanced_course_bonus[$key] ?? 0,
                    'sectionNumber' => $request->sectionNumber[$key] ?? null,
                    'courseType' => $request->courseType[$key] ?? $courseType[$request->courseTypeID[$key]],
                    'stateID' => $request->stateID[$key] ?? null,
                    'GradeName' => $request->academicTerm[$key] ?? null,
                    'sequence' => $request->sequence[$key] ?? null,
                    'courseFullName' => $request->courseFullName[$key] ?? null,
                    'fullsection_number' => $request->fullsection_number[$key] ?? null,
                ];

                $grades_data[] = $grade_data;
                $initSubmission = Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
                $grade_data['enrollment_id'] = $initSubmission->enrollment_id;
                $grade_data['application_id'] = $initSubmission->application_id;
                $new_grade[] = $grade_data;
            }
            // dd($new_grade);
            if (isset($grades_data)) {
                $result = SubmissionGrade::insert($grades_data);
            }

            $this->modelGradeChanges($current_grade, $new_grade, "Submission Academic Grade");
        } else {
            $result = 1;
        }

        $display_outcome = SubmissionsStatusUniqueLog::count();
        if ($display_outcome == 0 && $initSubmission->submission_status == "Pending") {
            $rsCDIData = SubmissionConductDisciplinaryInfo::where("submission_id", $id)->first();
            if (!empty($rsCDIData)) {
                $ins = array();
                $ins['submission_status'] = "Active";
                $rsD = Submissions::where("id", $id)->update($ins);
            }
        }

        if (isset($result)) {
            Session::flash("success", "Submission grades successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Submissions');
        }
        return redirect('admin/Submissions/edit/' . $id);
    }

    public function storeComments($id, Request $request)
    {
        // return $id;
        $rules = ['comment' => 'required'];
        $messages = ['comment.required' => 'Please write few words into comment box.'];
        $this->validate($request, $rules, $messages);
        $data = [
            'submission_id' => $id,
            'user_id' => Auth::user()->id,
            'comment' => $request->comment,
        ];
        $comment = SubmissionComment::create($data);
        if (!empty($comment)) {
            Session::flash('success', "Comment added successfully.");
        } else {
            Session::flash('warning', "Something went wrong , Please try again.");
        }



        return redirect('admin/Submissions/edit/' . $id);
    }

    public function transferGradeStudentToSubmission()
    {
        $submission_data = Submissions::whereNotNull('student_id')->where('data_in_submission', 'N')->where('grade_exists', 'Y')->get();

        if (isset($submission_data) && count($submission_data) > 0) {
            foreach ($submission_data as $key => $submission) {
                $submission_grade = SubmissionGrade::where('submission_id', $submission->id)->get();
                if (count($submission_grade) == 0) {
                    $student_grade = StudentGrade::where('stateID', $submission->student_id)->get();
                    if (isset($student_grade) && count($student_grade) > 0) {
                        $grades_data = [];
                        foreach ($student_grade as $key => $value) {
                            // return $value;
                            $array = [];
                            $grade_data = [
                                'submission_id' => $submission->id,
                                'stateID' => $submission->student_id,
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
                            $grades_data[] = $grade_data;
                        }

                        if (isset($grades_data)) {
                            SubmissionGrade::insert($grades_data);
                            Submissions::where('id', $submission->id)->update(['data_in_submission' => 'Y']);
                        }
                    }
                } else {
                    Submissions::where('id', $submission->id)->update(['data_in_submission' => 'Y']);
                }
            }
        }

        $submission_data = Submissions::whereNotNull('student_id')->where('conduct_disc_in_submission', 'N')->where('cdi_exists', 'Y')->get();

        if (isset($submission_data) && count($submission_data) > 0) {
            foreach ($submission_data as $key => $submission) {
                $submission_grade = SubmissionConductDisciplinaryInfo::where('submission_id', $submission->id)->get();
                if (count($submission_grade) == 0) {
                    $student_cdi = StudentCDI::where('stateID', $submission->student_id)->get();
                    if (isset($student_cdi) && count($student_cdi) > 0) {
                        $cdi_data = [];
                        foreach ($student_cdi as $key => $value) {
                            // return $value;
                            $array = [];
                            $data = [
                                'submission_id' => $submission->id,
                                'stateID' => $submission->student_id,
                                'b_info' => $value->b_info ?? 0,
                                'c_info' => $value->c_info ?? 0,
                                'd_info' => $value->d_info ?? 0,
                                'e_info' => $value->e_info ?? 0,
                                'susp' => $value->susp ?? 0,
                                'susp_days' => $value->susp_days ?? 0,
                            ];
                            $cdi_data[] = $data;
                        }

                        if (isset($cdi_data)) {
                            SubmissionConductDisciplinaryInfo::insert($cdi_data);
                            Submissions::where('id', $submission->id)->update(['conduct_disc_in_submission' => 'Y']);
                        }
                    }
                } else {
                    Submissions::where('id', $submission->id)->update(['conduct_disc_in_submission' => 'Y']);
                }
            }
        }
    }

    public function overrideCDI(Request $request)
    {
        $initSubmission = Submissions::where('submissions.id', $request->id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        $result = Submissions::where('id', $request->id)->update(['cdi_override' => $request->status]);
        $newObj =  Submissions::where('submissions.id', $request->id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        if ($request->status == "Y")
            $submission_event = "CDI Override - <span class='text-danger'>N</span> TO <span class='text-success'>Y</span>";
        else
            $submission_event = "CDI Override - <span class='text-danger'>Y</span> TO <span class='text-success'>N</span>";


        if (isset($request->comment) && $request->comment != '') {
            $initSubmission->gender = "";
            $comment_data = [
                'submission_id' => $request->id,
                'user_id' => Auth::user()->id,
                'comment' => $request->comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->gender = $request->comment;
        }
        $this->modelChanges($initSubmission, $newObj, "Submission - CDI Override");

        $display_outcome = SubmissionsStatusUniqueLog::count();
        if ($display_outcome == 0 && $initSubmission->submission_status == "Pending") {
            $rsGradeData = SubmissionGrade::where("submission_id", $request->id)->first();
            if (!empty($rsGradeData) || ($request->status == "Y" && $initSubmission->grade_override == "Y")) {
                $ins = array();
                $ins['submission_status'] = "Active";
                $rsD = Submissions::where("id", $request->id)->update($ins);
            }
        }

        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }

    public function overrideGrade(Request $request)
    {
        $initSubmission = Submissions::where('submissions.id', $request->id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        if ($request->status == "Y")
            $submission_event = "Academic Grade Override - <span class='text-danger'>N</span> TO <span class='text-success'>Y</span>";
        else
            $submission_event = "Academic Grade Override - <span class='text-danger'>Y</span> TO <span class='text-success'>N</span>";

        $result = Submissions::where('id', $request->id)->update(['grade_override' => $request->status]);
        $newObj =  Submissions::where('submissions.id', $request->id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();

        if (isset($request->comment) && $request->comment != '') {
            $initSubmission->gender = "";
            $comment_data = [
                'submission_id' => $request->id,
                'user_id' => Auth::user()->id,
                'comment' => $request->comment,
                'submission_event' => $submission_event
            ];
            SubmissionComment::create($comment_data);
            $newObj->gender = $request->comment;
        }
        $this->modelChanges($initSubmission, $newObj, "Submission - Grade Override");

        $display_outcome = SubmissionsStatusUniqueLog::count();
        if ($display_outcome == 0 && $initSubmission->submission_status == "Pending") {

            $rsCDIData = SubmissionConductDisciplinaryInfo::where("submission_id", $request->id)->first();
            if (!empty($rsCDIData) || ($request->status == "Y" && $initSubmission->cdi_override == "Y")) {
                $ins = array();
                $ins['submission_status'] = "Active";
                $rsD = Submissions::where("id", $request->id)->update($ins);
            }
        }

        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }


    public function fetchProgramGrade($first_program_id = 0, $second_program_id = 0)
    {
        if ($first_program_id == 0 && $second_program_id == 0) {
            $data = Submissions::select(DB::raw("DISTINCT(next_grade)"))->orderByDesc("next_grade")->where("district_id", Session::get("district_id"))->get();
        } else {
            $data = Submissions::where(function ($q) use ($first_program_id, $second_program_id) {
                if ($first_program_id == 0 && $second_program_id != 0) {
                    $q->where("second_choice_program_id", $second_program_id);
                } elseif ($second_program_id == 0 && $first_program_id != 0) {
                    $q->where("first_choice_program_id", $first_program_id);
                } else {
                    $q->where("second_choice_program_id", $second_program_id)->orWhere('first_choice_program_id', $first_program_id);
                }
            })->where("district_id", Session::get("district_id"))->select(DB::raw("DISTINCT(next_grade)"))->orderByDesc("next_grade")->get();
        }
        if (!empty($data)) {
            return json_encode($data);
        } else {
            $data = Submissions::select(DB::raw("DISTINCT(next_grade)"))->orderByDesc("next_grade")->where("district_id", Session::get("district_id"))->get();
            return json_encode($data);
        }
    }

    public function checkAvailability($choice_id, $grade)
    {
        //   $application_programs=ApplicationProgram::where('id',$choice_id)
        //      ->select('program_id')->first();
        $program_id = $choice_id; //$application_programs->program_id;

        $rs = Submissions::where("awarded_school", getProgramName($program_id))->whereIn('submission_status', ['Offered', 'Offered and Accepted'])->where("enrollment_id", Session::get('enrollment_id'))->where("next_grade", $grade)->count();

        $rs6_black = WaitlistProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("black_withdrawn");
        $rs6_white = WaitlistProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("white_withdrawn");
        $rs6_other = WaitlistProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("other_withdrawn");
        $rs6_additional_slots = WaitlistProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("additional_seats");

        $rs7_black = LateSubmissionProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("black_withdrawn");
        $rs7_white = LateSubmissionProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("white_withdrawn");
        $rs7_other = LateSubmissionProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("other_withdrawn");
        $rs7_additional_slots = LateSubmissionProcessLogs::where("program_id", $program_id)->where("grade", $grade)->sum("additional_seats");


        $totalOffered = $rs;



        $availability = Availability::where("program_id", $program_id)->first();
        $pending = $availability->available_seats + $rs6_black + $rs6_other + $rs6_white + $rs7_black + $rs7_white + $rs7_other + $rs6_additional_slots + $rs7_additional_slots - $totalOffered;
        echo "Available Seats are " . $pending . ". Do you wish to continue ?";
    }

    public function updateNextgrade(Request $request, $id)
    {
        $req = $request->all();
        $initSubmission = Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();

        $next_grade = $req['manual_next_grade'];
        if ($next_grade == "K")
            $current_grade = "PreK";
        elseif ($next_grade == "1")
            $current_grade = "K";
        else
            $current_grade = $next_grade - 1;

        if ($initSubmission->next_grade != $next_grade) {
            $first_choice_program_id = $initSubmission->first_choice_program_id;
            $second_choice_program_id = $initSubmission->second_choice_program_id;

            $grade = Grade::where("name", $next_grade)->first();
            $grade_id = $grade->id;
            $rs = ApplicationProgram::where("application_id", $initSubmission->application_id)->where("program_id", $first_choice_program_id)->where("grade_id", $grade_id)->first();

            $first_choice = $rs->id;
            $second_choice = "";
            if ($second_choice_program_id != 0) {
                $rs = ApplicationProgram::where("application_id", $initSubmission->application_id)->where("program_id", $second_choice_program_id)->where("grade_id", $grade_id)->first();
                $second_choice = $rs->id;
            }
            Submissions::where("id", $id)->update(array("next_grade" => $next_grade, "current_grade" => $current_grade));
            $newObj =  Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
            $this->modelChanges($initSubmission, $newObj, "Submission - General");

            $data = array();
            $data['first_choice'] = $first_choice;
            if ($second_choice != "") {
                $data['second_choice'] = $second_choice;
            }
            Submissions::where("id", $id)->update($data);

            $newdata = array();
            $newdata['old_grade'] = $initSubmission->next_grade;
            $newdata['updated_by'] = Auth::user()->id;
            $newdata['submission_id'] = $id;
            do {
                $code = Str::random(10);
                $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
            } while (!empty($user_code));


            $statusData = array();
            $statusData['offer_slug'] = $code;
            $offer_data = SubmissionsFinalStatus::where("submission_id", $id)->first();
            if (!empty($offer_data)) {

                if ($offer_data->first_offer_status == "Accepted" || $offer_data->second_offer_status == "Accepted") {

                    $newdata['offer_slug'] = $offer_data->offer_slug;

                    //$statusData['first_offer_status'] = "Pending";
                    //$statusData['second_offer_status'] = "Pending";

                    if ($offer_data->contract_status == "Signed") {
                        $statusData['contract_status'] = "UnSigned";
                        $statusData['contract_signed_on'] = null;
                        $statusData['contract_status_by'] = 0;
                        $statusData['contract_mode'] = "Pending";
                        $statusData['contract_name'] = null;

                        $file_path = "resources/assets/admin/online_contract/Contract-" . $initSubmission->confirmation_no . ".pdf";
                        $new_file_name = $initSubmission->confirmation_no . "_" . strtotime(date("Y-m-d H:i:s"));
                        $new_path = "resources/assets/admin/online_contract/" . $new_file_name . ".pdf";
                        $newdata['old_contract_file_name'] = $new_file_name;
                        $newdata['old_contract_date'] = $offer_data->contract_signed_on;
                        $success = File::copy($file_path, $new_path);
                    }
                    $statusData['first_offer_update_at'] = null;
                    $statusData['second_offer_update_at'] = null;



                    //Submissions::where("id", $id)->update(array("submission_status"=>"Offered and Accepted"));
                }
            }
            SubmissionsFinalStatus::where("submission_id", $id)->update($statusData);
            SubmissionGradeChange::create($newdata);
            Submissions::where("id", $id)->update(array("manual_grade_change" => "N"));
        }
        Session::flash("success", "Submission Updated successfully.");
        return redirect('admin/Submissions/edit/' . $id);
    }

    public function updateManualStatus(Request $request, $id)
    {
        $req = $request->all();
        $last_date_online_acceptance = $req['last_date_online_acceptance'];
        $last_date_offline_acceptance = $req['last_date_offline_acceptance'];

        $initSubmission = Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();

        $data = $data1 = array();

        $first_choice_final_status = $req['first_choice_final_status'];
        $data['submission_status'] = $first_choice_final_status;

        $data1['first_waitlist_for'] = $req['first_choice'];
        $data1['first_choice_final_status'] = $req['first_choice_final_status'];

        if ($first_choice_final_status == "Offered") {
            $data['awarded_school'] = getProgramName($req['first_choice']);
        }

        if (isset($req['second_choice'])) {
            $data1['second_waitlist_for'] = $req['second_choice'];
            $data1['second_choice_final_status'] = $req['second_choice_final_status'];
            if ($req['second_choice_final_status'] == "Offered") {
                $data['submission_status'] = "Offered";
                $data['awarded_school'] = getProgramName($req['second_choice']);
            }
        }






        Submissions::where("id", $id)->update($data);
        do {
            $code = Str::random(10);
            $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
        } while (!empty($user_code));
        $data1['offer_slug'] = $code;
        $data1['manually_updated'] = "Y";
        $data1['last_date_online_acceptance'] = $last_date_online_acceptance;
        $data1['last_date_offline_acceptance'] = $last_date_offline_acceptance;
        SubmissionsFinalStatus::where("submission_id", $id)->update($data1);


        $newObj =  Submissions::where('submissions.id', $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        $this->modelChanges($initSubmission, $newObj, "Submission - General");



        Session::flash("success", "Submission Updated successfully.");
        return redirect('admin/Submissions/edit/' . $id);
    }


    public function sendGeneralCommunicationEmailPost(Request $request, $type, $id)
    {
        $submission_data = Submissions::where("id", $id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission_data->application_id)->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

        $district_id = Session::get('district_id');

        $last_date_online_acceptance = $last_date_offline_acceptance = "";

        if (strtolower($type) == "waitlist") {
            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "waitlist")->where("commited", "Yes")->orderBy("created_at", "DESC")->first();

            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);

            $rs_data = SubmissionsWaitlistFinalStatus::where("submission_id", $id)->orderBy("created_at", "DESC")->first();
            if (!empty($rs_data)) {
                if ($rs_data->last_date_online_acceptance != '') {
                    $last_date_online_acceptance = $rs_data->last_date_online_acceptance;
                }
                if ($rs_data->last_date_offline_acceptance != '') {
                    $last_date_offline_acceptance = $rs_data->last_date_offline_acceptance;
                }
            }

            $submissions = Submissions::where('submissions.id', $id)
                ->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
                ->orderBy("submissions_waitlist_final_status.created_at", "desc")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = WaitlistEditCommunication::where("status", "Offered")->first();
        } elseif (strtolower($type) == "latesubmission" || strtolower($type) == "late_submission") {
            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "late_submission")->where("commited", "Yes")->orderBy("created_at", "DESC")->first();

            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);
            $rs_data = LateSubmissionFinalStatus::where("submission_id", $id)->orderBy("created_at", "DESC")->first();
            if (!empty($rs_data)) {
                if ($rs_data->last_date_online_acceptance != '') {
                    $last_date_online_acceptance = $rs_data->last_date_online_acceptance;
                }
                if ($rs_data->last_date_offline_acceptance != '') {
                    $last_date_offline_acceptance = $rs_data->last_date_offline_acceptance;
                }
            }

            $submissions = Submissions::where('submissions.id', $id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("late_submissions_final_status.created_at", "desc")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = LateSubmissionEditCommunication::where("status", "Offered")->first();
        } else {
            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "regular")->where("commited", "Yes")->orderBy("created_at", "DESC")->first();

            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);

            $rs_data = SubmissionsFinalStatus::where("submission_id", $id)->orderBy("created_at", "DESC")->first();
            if (!empty($rs_data)) {
                if ($rs_data->last_date_online_acceptance != '') {
                    $last_date_online_acceptance = $rs_data->last_date_online_acceptance;
                }
                if ($rs_data->last_date_offline_acceptance != '') {
                    $last_date_offline_acceptance = $rs_data->last_date_offline_acceptance;
                }
            }

            $submissions = Submissions::where('submissions.id', $id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = EditCommunication::where("status", "Offered")->first();
        }
        $value = $submissions;
        $application_data1 = $application_data;


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

        if ($value->first_choice_final_status == "Offered") {
            $program_id = $value->first_choice_program_id;
        } else {
            $program_id = $value->second_choice_program_id;
        }

        $tmp['program_name'] = getProgramName($program_id);
        $tmp['program_name_with_grade'] = getProgramName($program_id) . " - Grade " . $tmp['next_grade'];

        $tmp['offer_program'] = getProgramName($program_id);
        $tmp['offer_program_with_grade'] = getProgramName($program_id) . " - Grade " . $value->next_grade;



        $tmp['waitlist_program_1'] = "";
        $tmp['waitlist_program_1_with_grade'] = "";
        $tmp['waitlist_program_2'] = "";
        $tmp['waitlist_program_2_with_grade'] = "";


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
        if ($value->offer_slug != "") {
            $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
        } else {
            $tmp['offer_link'] = "";
        }

        if ($value->last_date_online_acceptance != '') {
            $tmp['online_offer_last_date'] = getDateTimeFormat($value->last_date_online_acceptance);
            $tmp['offline_offer_last_date'] = getDateTimeFormat($value->last_date_offline_acceptance);
        } else {
            $tmp['online_offer_last_date'] = $last_date_online_acceptance;
            $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
        }

        $msg = find_replace_string($request->mail_body, $tmp);
        $msg = str_replace("{", "", $msg);
        $msg = str_replace("}", "", $msg);
        $tmp['msg'] = $msg;

        $msg = find_replace_string($cdata->mail_subject, $tmp);
        $msg = str_replace("{", "", $msg);
        $msg = str_replace("}", "", $msg);
        $tmp['subject'] = $msg;

        $tmp['email'] = $value->parent_email;
        $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);

        $mail = sendMail($tmp);
        $data = array();
        $data['submission_id'] = $id;
        $data['email_to'] = $submission_data['parent_email'];
        $data['email_subject'] = $tmp['subject'];
        $data['email_body'] = $tmp['msg'];
        $data['logo'] = getDistrictLogo();
        $data['module'] = "Offer Confirmation Email";


        if ($mail) {
            $data['status'] = "Success";
            Session::flash('success', 'Confirmation Mail Sent Successfully.');
        } else {
            $data['status'] = "Error";
        }
        createEmailActivityLog($data);

        if ($type == "Waitlist") {
            SubmissionsWaitlistFinalStatus::where("submission_id", $value->id)->where("version", $process_selection->version)->update(array("communication_sent" => "Y", 'communication_text' => $msg));
        } elseif ($type == "LateSubmission") {
            LateSubmissionFinalStatus::where("submission_id", $value->id)->where("version", $process_selection->version)->update(array("communication_sent" => "Y", 'communication_text' => $msg));
        } else {
            SubmissionsFinalStatus::where("submission_id", $value->id)->update(array("communication_sent" => "Y", 'communication_text' => $msg));
        }
        $rs = SubmissionManualEmail::where("submission_id", $value->id)->delete();



        Session::flash("success", "Mail sent successfully.");
        return redirect("/admin/Submissions/edit/" . $id);
    }
    public function sendGeneralCommunicationEmail($type, $id, $preview = "")
    {
        $submission_data = Submissions::where("id", $id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission_data->application_id)->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();
        $district_id = Session::get('district_id');

        $last_date_online_acceptance = $last_date_offline_acceptance = "";

        if (strtolower($type) == "waitlist") {

            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "waitlist")->where("commited", "Yes")->orderBy("updated_at", "DESC")->orderBy("created_at", "DESC")->first();

            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);

            $submissions = Submissions::where('submissions.id', $id)
                ->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
                ->orderBy("submissions_waitlist_final_status.created_at", "desc")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = WaitlistEditCommunication::where("status", "Offered")->first();
        } elseif (strtolower($type) == "latesubmission" || strtolower($type) == "late_submission") {

            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "late_submission")->where("commited", "Yes")->orderBy("updated_at", "DESC")->orderBy("created_at", "DESC")->first();

            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);

            $submissions = Submissions::where('submissions.id', $id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("late_submissions_final_status.created_at", "desc")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = LateSubmissionEditCommunication::where("status", "Offered")->first();
        } else {
            $process_selection = ProcessSelection::where("application_id", $submission_data->form_id)->where("type", "regular")->where("commited", "Yes")->orderBy("updated_at", "DESC")->first();
            $last_date_online_acceptance = getDateTimeFormat($process_selection->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($process_selection->last_date_offline_acceptance);
            $submissions = Submissions::where('submissions.id', $id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->first(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
            $cdata = EditCommunication::where("status", "Offered")->first();
        }
        $value = $submissions;
        $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $submission_data->application_id)->select("application.*", "enrollments.school_year")->first();


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

        if ($value->first_choice_final_status == "Offered") {
            $program_id = $value->first_choice_program_id;
        } else {
            $program_id = $value->second_choice_program_id;
        }

        $tmp['program_name'] = getProgramName($program_id);
        $tmp['program_name_with_grade'] = getProgramName($program_id) . " - Grade " . $tmp['next_grade'];

        $tmp['offer_program'] = getProgramName($program_id);
        $tmp['offer_program_with_grade'] = getProgramName($program_id) . " - Grade " . $value->next_grade;



        $tmp['waitlist_program_1'] = "";
        $tmp['waitlist_program_1_with_grade'] = "";
        $tmp['waitlist_program_2'] = "";
        $tmp['waitlist_program_2_with_grade'] = "";


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
        if ($value->offer_slug != "") {
            $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
        } else {
            $tmp['offer_link'] = "";
        }

        if ($value->last_date_online_acceptance != '') {
            $tmp['online_offer_last_date'] = getDateTimeFormat($value->last_date_online_acceptance);
            $tmp['offline_offer_last_date'] = getDateTimeFormat($value->last_date_offline_acceptance);
        } else {
            $tmp['online_offer_last_date'] = $last_date_online_acceptance;
            $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
        }

        $msg = find_replace_string($cdata->mail_body, $tmp);
        $msg = str_replace("{", "", $msg);
        $msg = str_replace("}", "", $msg);
        $tmp['msg'] = $msg;

        $msg = find_replace_string($cdata->mail_subject, $tmp);
        $msg = str_replace("{", "", $msg);
        $msg = str_replace("}", "", $msg);
        $tmp['subject'] = $msg;

        $tmp['email'] = $value->parent_email;
        $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);

        if ($preview != "" && $preview != "Grade") {
            $msg = $tmp['msg'];
            return view("Submissions::preview_offer_email", compact('msg', "type", "id"));
        } else {
            sendMail($tmp);
            SubmissionsFinalStatus::where("submission_id", $value->id)->update(array("communication_sent" => "Y"));
            SubmissionsWaitlistFinalStatus::where("submission_id", $value->id)->update(array("communication_sent" => "Y"));
            Session::flash("success", "Mail sent successfully.");
            return redirect("/admin/Submissions/edit/" . $id);
        }
    }


    public function sendCommunicationEmail($id, $preview = "")
    {
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();
        $district_id = Session::get('district_id');

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);

        $submission = Submissions::where("id", $id)->first();
        if ($submission->first_choice != "" && $submission->second_choice != "")
            $status = "Offered and Waitlisted";
        else
            $status = "Offered";

        $cdata = EditCommunication::where("status", $status)->first();


        if ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('submissions.id', $id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'last_date_online_acceptance', 'last_date_offline_acceptance', 'offer_slug']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('submissions.id', $id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'last_date_online_acceptance', 'last_date_offline_acceptance']);
        }
        $student_data = array();
        foreach ($submissions as $key => $value) {
            $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

            $generated = false;
            if (($value->first_choice_final_status == $status && $status == "Offered") || ($value->first_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->first_choice_final_status == $status)) {
                $generated = true;
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
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];

                $tmp['offer_program'] = getProgramName($value->first_choice_program_id);
                $tmp['offer_program_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                if ($value->second_choice_program_id != 0) {
                    $tmp['waitlist_program'] = getProgramName($value->second_choice_program_id);
                    $tmp['waitlist_program_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                } else {
                    $tmp['waitlist_program'] = "";
                    $tmp['waitlist_program_with_grade'] = "";
                }

                if ($status == "Waitlisted") {
                    $tmp['waitlist_program_1'] = getProgramName($value->first_choice_program_id);
                    $tmp['waitlist_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                    if ($value->second_choice_program_id != 0) {
                        $tmp['waitlist_program_2'] = getProgramName($value->second_choice_program_id);
                        $tmp['waitlist_program_2_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
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
                if (($status == "Offered"  || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                    $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                } else {
                    $tmp['offer_link'] = "";
                }

                if ($value->last_date_online_acceptance != '') {
                    $tmp['online_offer_last_date'] = getDateTimeFormat($value->last_date_online_acceptance);
                    $tmp['offline_offer_last_date'] = getDateTimeFormat($value->last_date_offline_acceptance);
                } else {
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
                }

                $msg = find_replace_string($cdata->mail_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['msg'] = $msg;

                $msg = find_replace_string($cdata->mail_subject, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['subject'] = $msg;

                $tmp['email'] = $value->parent_email;
                $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);

                if ($preview != "" && $preview != "Grade") {
                    echo $tmp['msg'];
                    exit;
                } else {
                    sendMail($tmp);
                    SubmissionsFinalStatus::where("submission_id", $value->id)->update(array("communication_sent" => "Y"));
                    if ($preview == "Grade") {
                        Submissions::where("id", $value->id)->update(array("manual_grade_change" => "N"));
                    }
                }
            }

            if ((($value->second_choice_final_status == $status && $status == "Offered") || ($value->second_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->second_choice_final_status == $status)) && !$generated) {
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
                $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];

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
                $tmp['school_year'] = $application_data->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getProgramName($value->second_choice_program_id);
                $tmp['offer_program_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                if ($value->first_choice_program_id != 0) {
                    $tmp['waitlist_program'] = getProgramName($value->first_choice_program_id);
                    $tmp['waitlist_program_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                } else {
                    $tmp['waitlist_program'] = "";
                    $tmp['waitlist_program_with_grade'] = "";
                }

                if ($status == "Waitlisted") {
                    $tmp['waitlist_program_1'] = getProgramName($value->first_choice_program_id);
                    $tmp['waitlist_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                    if ($value->second_choice_program_id != 0) {
                        $tmp['waitlist_program_2'] = getProgramName($value->second_choice_program_id);
                        $tmp['waitlist_program_2_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
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

                if (($status == "Offered"  || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                    $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                } else {
                    $tmp['offer_link'] = "";
                }
                $tmp['program_name_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];


                if ($value->last_date_online_acceptance != '') {
                    $tmp['online_offer_last_date'] = getDateTimeFormat($value->last_date_online_acceptance);
                    $tmp['offline_offer_last_date'] = getDateTimeFormat($value->last_date_offline_acceptance);
                } else {
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
                }



                $msg = find_replace_string($cdata->mail_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['msg'] = $msg;

                $msg = find_replace_string($cdata->mail_subject, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['subject'] = $msg;

                $tmp['email'] = $value->parent_email;
                $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);

                if ($preview != "" && $preview != "Grade") {
                    echo $tmp['msg'];
                    exit;
                } else {
                    sendMail($tmp);
                    SubmissionsFinalStatus::where("submission_id", $value->id)->update(array("communication_sent" => "Y"));
                    if ($preview == "Grade") {
                        Submissions::where("id", $value->id)->update(array("manual_grade_change" => "N"));
                    }
                }
                $countMail++;
            }
        }
        ob_end_clean();
        ob_start();

        $fileName =  "EditCustomCommunication-" . strtotime(date("Y-m-d H:i:s")) . ".xlsx";
        $data = array();
        $data['district_id'] = Session::get("district_id");
        $data['communication_type'] = "Email";
        $data['mail_subject'] = $cdata->mail_subject;
        $data['mail_body'] = $cdata->mail_body;
        $data['status'] = $status;
        $data['file_name'] = $fileName;
        $data['total_count'] = count($student_data);
        $data['generated_by'] = Auth::user()->id;
        EditCommunicationLog::create($data);
        echo "Done";
    }

    public function recommendationPdfDownload($recommendation_id = 0)
    {
        $answer_data = SubmissionRecommendation::where('id', $recommendation_id)->first();
        $submission = Submissions::where('id', $answer_data->submission_id)->first();
        $content = json_decode($answer_data->answer);
        $value = $answer_data->config_value;
        $tmp = explode(".", $value);
        $program_id = $tmp[count($tmp) - 1];
        //return view('Submissions::recommendation_form_pdf',compact('answer_data','content', 'submission'));

        $pdf = Pdf::loadView('Submissions::recommendation_form_pdf', ['answer_data' => $answer_data, 'content' => $content, 'submission' => $submission, "program_id" => $program_id]);
        return $pdf->download($answer_data->config_value . '.pdf');
    }

    public function previewCommunicationEmail($id)
    {
        $data = EmailActivityLog::where("id", $id)->first();
        if (!empty($data)) {
            return view("Submissions::preview_email", compact('data'));
        }
    }

    public function updateTestScore(Request $request, $submission_id = 0, $program_id = 0)
    {

        $submission = Submissions::where("id", $submission_id)->first();
        $req = $request->all();
        $test_score_name = $req['test_score_name'];
        $test_score_value = $req['test_score_value'];
        $test_score_rank = $req['test_score_rank'];

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


        foreach ($test_score_name as $key => $value) {
            $data = [];
            $data['submission_id'] = $submission_id;
            $data['program_id'] = $program_id;
            $data['test_score_name'] = $test_score_name[$key];
            $data['test_score_value'] = $test_score_value[$key];
            $data['test_score_rank'] = $test_score_rank[$key];
            if ($data['test_score_rank'] != '') {
                $rs = SubmissionTestScore::updateOrCreate(["submission_id" => $submission_id, "program_id" => $program_id, "test_score_name" => $data['test_score_name']], $data);
                if (isset($rs)) {
                    Session::flash("success", "Data Updated successfully.");
                }
            }
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

        if (!session('success')) {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect()->back();
    }


    public function resendEmailCommunication($id)
    {
        $edata = EmailActivityLog::where('id', $id)->first();
        $submission_id = $edata->submission_id;
        $program_id = $edata->program_id;
        $submission = Submissions::where('id', $submission_id)->first();
        // Email data
        if (isset($edata)) {

            $application_data = \App\Modules\Application\Models\Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where("application.status", "Y")->where("application.id", $submission->application_id)->select("application.*", "enrollments.school_year")->first();
            $logo = getDistrictLogo($application_data->display_logo) ?? '';

            $data['submission_id'] = $submission_id;
            $data['program_id'] = $program_id;
            $data['email_text'] = $data['email_body'] = $edata->email_body;
            $data['logo'] = $logo;
            $data['email_to'] = $edata->email_to;
            $data['email_subject'] = $edata->email_subject ?? '';

            try {
                Mail::send('emails.index', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['email_to']);
                    $message->subject($data['email_subject']);
                });
                $data['status'] = "Success";
            } catch (\Exception $e) {
                $data['status'] = $e;
                $msg = 'Mail not sent.';
                $alrt_type = 'warning';
            }
            $data['module'] = "Resend From Log";
            $data['user_id'] = Auth::user()->id;
            createEmailActivityLog($data);
            $msg = "Email resend successfully.";
        }
        Session::flash(($alrt_type ?? 'success'), $msg);
        return redirect('admin/Submissions/edit/' . $submission_id);
    }

    public function writing_prompt_update($id, $program_id, $type = "first")
    {

        $rsOldData = SubmissionData::where("submission_id", $id)->where("config_name", "wp_" . $type . "_choice_link")->first();
        if (!empty($rsOldData)) {
            $config_value = $rsOldData->config_value;
            $tmpPrg = explode(".", $config_value);
            $tmpPrgId = $tmpPrg[1];
            $wp = WritingPrompt::where("program_id", $tmpPrgId)->where("submission_id", $tmpPrg[0])->first();
            if (!empty($wp)) {
                $wp_id = $wp->id;
                unset($wp['id']);
                unset($wp['updated_at']);
                unset($wp['created_at']);
                // Create wp log
                $wp_log = WritingPromptLog::create($wp->toArray());
                if (isset($wp_log)) {
                    $wp_detail = WritingPromptDetail::where('wp_id', $wp_id)->get();
                    // Create wp detail log
                    foreach ($wp_detail as $value) {
                        unset($value['id']);
                        unset($value['updated_at']);
                        unset($value['created_at']);
                        $value['wp_id'] = $wp_log->id;
                        WritingPromptDetailLog::create($value->toArray());
                    }
                    // Clear original data
                    WritingPromptDetail::where('wp_id', $wp_id)->delete();
                    $wp->delete();
                }
            }

            $rsDel = SubmissionData::where("id", $rsOldData->id)->delete();
        }

        if ($program_id != 0) {
            $writeEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("eligibility_template.name", "Writing Prompt")->where("program_id", $program_id)->first();
            if (!empty($writeEligibility)) {
                $sub_data = [];
                $sub_data['submission_id'] = $id;
                $sub_data['config_name'] = "wp_" . $type . "_choice_link";
                $sub_data['config_value'] = $id . "." . $program_id . "." . rand(101, 999);
                $in = SubmissionData::create($sub_data);
            }
        }
    }


    public function updatePriliminary(Request $request, $submission_id = 0, $program_id = 0)
    {
        // Committee Score
        if (isset($request->committee_score)) {
            $oldObj = SubmissionCommitteeScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();

            $cs = SubmissionCommitteeScore::updateOrCreate(
                [
                    'submission_id' => $submission_id,
                    'program_id' => $program_id
                ],
                ['data' => $request->committee_score]
            );

            $newObj = SubmissionCommitteeScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_committee_score.submission_id")->join("application", "application.id", "submissions.application_id")->where("submission_committee_score.program_id", $program_id)->select("submission_committee_score.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj))
                $this->modelChanges($oldObj, $newObj, "Submission - Committee Score");
            else
                $this->modelCreate($newObj, "Submission - Committee Score");
        }

        // Academic Grade Calculation
        if (isset($request->agc_score)) {
            $oldObj = SubmissionAcademicGradeCalculation::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_academic_grade_calculation.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_academic_grade_calculation.*", "submissions.application_id", "application.enrollment_id")->first();

            $agc = SubmissionAcademicGradeCalculation::updateOrCreate(
                ['submission_id' => $submission_id],
                ['given_score' => $request->agc_score]
            );

            $newObj = SubmissionAcademicGradeCalculation::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_academic_grade_calculation.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_academic_grade_calculation.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj))
                $this->modelChanges($oldObj, $newObj, "Submission - Academic Grade Calculation");
            else
                $this->modelCreate($newObj, "Submission - Academic Grade Calculation");
        }

        // Test Score
        if (isset($request->test_score) && !empty($request->test_score)) {
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
                $tsOldObj = [
                    'submission_id' => $submission_id,
                    'program_id' => $program_id,
                    'application_id' => $submission->application_id,
                    'enrollment_id' => $submission->enrollment_id,
                    'ts_data' => $ts_data
                ];
            }
            foreach ($request->test_score as $ts_name => $ts_rank) {
                if ($ts_rank != '') {
                    $ts = SubmissionTestScore::updateOrCreate(
                        [
                            'submission_id' => $submission_id,
                            'program_id' => $program_id,
                            'test_score_name' => $ts_name
                        ],
                        ['test_score_rank' => $ts_rank]
                    );
                }
            }
            // updated data
            $ts = SubmissionTestScore::where("submission_id", $submission_id)
                ->where("program_id", $program_id)
                ->get(['test_score_name', 'test_score_rank']);
            $ts_data = [];
            foreach ($ts as $ts_val) {
                $ts_data[$ts_val->test_score_name] = $ts_val->test_score_rank;
            }
            $tsNewObj = [
                'submission_id' => $submission_id,
                'program_id' => $program_id,
                'application_id' => $submission->application_id,
                'enrollment_id' => $submission->enrollment_id,
                'ts_data' => $ts_data
            ];

            if (isset($tsOldObj)) {
                $this->modelTestScoreAudit($tsOldObj, $tsNewObj, "Submission Test Score");
            } else {
                $this->modelTestScoreAudit($tsNewObj, [], "Submission Test Score");
            }
        }

        Session::flash('success', 'Data Updated Successfully.');
        return redirect()->back();
    }

    public function updateComposite(Request $request, $submission_id = 0)
    {
        // Interview Score

        if (isset($request->interview_score)) {
            $oldObj = SubmissionInterviewScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();

            $cs = SubmissionInterviewScore::updateOrCreate(
                ['submission_id' => $submission_id],
                ['data' => $request->interview_score]
            );

            $newObj = SubmissionInterviewScore::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submission_interview_score.submission_id")->join("application", "application.id", "submissions.application_id")->select("submission_interview_score.*", "submissions.application_id", "application.enrollment_id")->first();

            if (!empty($oldObj))
                $this->modelChanges($oldObj, $newObj, "Submission - Interview Score");
            else
                $this->modelCreate($newObj, "Submission - Interview Score");
            generateCompositeScore($submission_id);
        }
        Session::flash('success', 'Data Updated Successfully.');
        return redirect()->back();
    }

    public function priorityCalculate($submission, $choice = "first")
    {
        $str = $choice . "_choice_program_id";
        $rank_counter = 0;
        $priority_data = [];
        if ($submission->{$str} != 0 && $submission->{$str} != '') {
            $priority_details = DB::table("priorities")->join("program", "program.priority", "priorities.id")->join("priority_details", "priority_details.priority_id", "priorities.id")->where("program.id", $submission->{$str})->select('priorities.*', 'priority_details.*', 'program.feeder_priorities', 'program.upload_program_check', 'program.feeder_field', 'program.magnet_priorities')->get();

            foreach ($priority_details as $count => $priority) {
                $flag = false;
                if ($priority->sibling == 'Y') {
                    if (isset($submission->{$choice . '_sibling'}) && $submission->{$choice . '_sibling'} != '') {
                        $priority_data['Sibling'] = 'Yes';
                    } else {
                        $priority_data['Sibling'] = 'No';
                    }
                }


                // Magnet Employee
                if ($priority->majority_race_in_home_zone_school == 'Y') {

                    if (getMajorityRace($submission)) {
                        $priority_data['Majority Race'] = "Yes";
                    } else {
                        $priority_data['Majority Race'] = "No";
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
                                if (!empty($rs)) {
                                    $priority_data['Feeder'] = "Yes";
                                } else {
                                    $priority_data['Feeder'] = "No";
                                }
                            }
                        } else {
                            $priority_data['Feeder'] = "No";
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
                                $priority_data['Feeder'] = "Yes";
                            } else {
                                $priority_data['Feeder'] = "No";
                            }
                        } else {
                            $priority_data['Feeder'] = "No";
                        }
                    }
                }

                // Magnet School
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
                            $priority_data['Magnet Student'] = "Yes";
                        } else {
                            $priority_data['Magnet Student'] = "No";
                        }
                    } else {
                        $priority_data['Magnet Student'] = "No";
                    }
                }
            }
        }
        return $priority_data;
    }

    public function manual_recommendation_send(Request $request)
    {
        $submission_id = $request->submission_id;
        $config_id = $request->config_id;
        $email = $request->email;

        $data = Submissions::where("id", $submission_id)->first();
        $application_data = Application::where("id", $data->application_id)->first();

        $rs = SubmissionData::where("id", $config_id)->first();
        $str = "";
        if (!empty($rs)) {
            $config_name = $rs->config_name;
            $config_value = $rs->config_value;
            $tmp = explode(".", $config_value);
            if (isset($tmp[2])) {
                $program_id = $tmp[2];
                $recoEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("program_eligibility.application_id", $data->application_id)->where("eligibility_template.name", "Recommendation Form")->where("program_id", $tmp[2])->first();
                if (!empty($recoEligibility)) {
                    $eligibility_id = $recoEligibility->assigned_eigibility_name;

                    $teacher_link_text = getEligibilityConfigDynamic($program_id, $eligibility_id, "manual_email_text", $data->application_id);
                    $subjects = config('variables.recommendation_subject');

                    $link = url('/recommendation/') . "/" . $config_value;
                    $tmp_strs = [];
                    $tmp_strs['recommendation_teacher_title'] = $subjects[$tmp[0]];
                    $tmp_strs['recommendation_teacher_link'] = "<a href='" . $link . "'>" . $link . "</a>";
                    $tmp_strs['student_name'] = $data->first_name . " " . $data->last_name;
                    $tmp_strs['zoned_school'] = $data->zoned_school;
                    $tmp_strs['recommendation_due_date'] = getDateTimeFormat($application_data->recommendation_due_date);
                    $tmp_strs['confirmation_no'] = $data->confirmation_no;


                    if ($teacher_link_text != "") {
                        $str = $teacher_link_text;
                    } else {

                        $str = '<p>{student_name} has applied to the {zoned_school}. This application requires a Recommendation be completed by their teacher.</p><p></p>
                                <p>Please visit the link below and complete the Teacher Recommendation for this student.<br />
                                    <br />{recommendation_teacher_link}.</p>
                                <p>This must be completed before {recommendation_due_date}. Please contact the Attendance Office should you have any questions or concerns.</p><p></p><p>This is an automated email message from a mailbox that does not accept replies.</p>';
                    }
                    $instructions = str_replace("{recommendation_links_section}", $str, $teacher_link_text);
                    $instructions = str_replace("{recommendation_due_date}", getDateTimeFormat($application_data->recommendation_due_date), $instructions);

                    $subject = getEligibilityConfigDynamic($program_id, $eligibility_id, "manual_email_subject", $data->application_id);



                    $instructions = find_replace_string($instructions, $tmp_strs);
                    $subject = find_replace_string($subject, $tmp_strs);

                    $emailArr = [];
                    $emailArr['email'] = $email;
                    $emailArr['subject'] = $subject;
                    $emailArr['email_text'] = $instructions;
                    $emailArr['logo'] = getDistrictLogo();

                    $email_data = array();
                    $email_data['submission_id'] = $submission_id;
                    $email_data['email_to'] = $email;
                    $email_data['email_subject'] = $subject;
                    $email_data['email_body'] = $instructions;
                    $email_data['logo'] = getDistrictLogo();
                    $email_data['module'] = "Manual Recommendation Form Link to " . $subjects[$tmp[0]] . " teacher";

                    try {
                        Mail::send('emails.index', ['data' => $emailArr], function ($message) use ($emailArr, $data) {
                            $message->to($emailArr['email']);
                            $message->subject($emailArr['subject']);
                        });
                        $email_data['status'] = "Success";

                        createEmailActivityLog($email_data);
                    } catch (\Exception $e) {
                        // Get error here
                        echo 'Message: ' . $e->getMessage();
                        exit;
                        $email_data['status'] = $e->getMessage();
                        createEmailActivityLog($email_data);
                    }
                }
            }
        }
        Session::flash("success", "Submission Updated successfully.");
        return redirect('/admin/Submissions/edit/' . $submission_id);
    }


    public function manual_resend_confirmation_email()
    {
        //$ids = array(1350,1349,1348,1347,1346,1345,1344,1343,1342,1341,1340,1339,1338,1337,1336,1335,1334,1333,1332,1331,1330,1329,1328,1327,1326,1325,1324,1323,1322,1321,1320,1319,1318,1317,1316,1315,1314,1313,1312,1311,1310,1309,1308,1307,1306,1305,1304,1303,1302,1301,1300,1299,1298,1297,1296,1295,1294,1293,1292,1291,1290,1289,1288,1287,1286,1285,1284,1283,1282,1281,1280,1279,1278,1277,1276,1275,1274,1273,1272,1271,1270,1269,1268,1267,1266,1265,1264,1263,1262,1261,1260,1259,1258,1257,1256,1255,1254,1253,1252,1251,1250,1249,1248,1247,1246,1245,1244,1243,1242,1241,1240,1239,1238,1237,1236,1235,1234,1233,1232,1231,1230,1229,1228,1227,1226,1225,1224,1223,1222,1221,1220,1219,1218,1217,1216,1215,1214,1213,1212,1211,1210,1209,1208,1207,1206,1205,1204,1203,1202,1201,1200,1199,1198,1197,1196,1195,1194,1193,1192,1191,1190,1189,1188,1187,1186,1185,1184,1183,1182,1181,1180,1179,1178,1177,1176,1175,1174,1173,1172,1171,1170,1169,1168,1167,1166,1165,1164,1163,1162,1161,1160,1159,1158,1157,1156,1155,1154,1153,1152,1151,1150,1149,1148,1147,1146,1145,1144,1143,1142,1141,1140,1139,1138,1137,1136,1135,1134,1133,1132,1131,1130,1129,1128,1127,1126,1125,1124,1123,1122,1121,1120,1119,1118,1117,1116,1115,1114,1113,1112,1111,1110,1109,1108,1107,1106,1105,1104,1103,1102,1101,1100,1099,1098,1097,1096,1095,1094,1093,1092,1091,1090,1089,1088,1087,1086,1085,1084,1083,1082,1081,1080,1079,1078,1077,1076,1075,1074,1073,1072,1071,1070,1069,1068,1067,1066,1065,1038,1036); 

        $ids = array(1, 2, 3, 4, 5, 6, 7);

        foreach ($ids as $id) {
            $submission_data = Submissions::where('id', $id)->first();
            $msg_data = ApplicationConfiguration::where("application_id", $submission_data['application_id'])->first();
            $application_data = Application::where("id", $submission_data['application_id'])->first();

            $emailArr = array();
            $emailArr['application_id'] = $submission_data['application_id'];
            $emailArr['first_name'] = $submission_data['first_name'];
            $emailArr['last_name'] = $submission_data['last_name'];
            $emailArr['parent_first_name'] = $submission_data['parent_first_name'];
            $emailArr['parent_last_name'] = $submission_data['parent_last_name'];
            $emailArr['email'] = $submission_data['parent_email'];
            $emailArr['confirm_number'] = $submission_data['confirmation_no'];
            $emailArr['transcript_due_date'] = getDateTimeFormat($application_data->transcript_due_date);

            if ($submission_data->submission_status == "Active") {
                $student_type = "active";
                $emailArr['type'] = "active_email";
                $emailArr['msg'] = $msg_data->active_email;
                $confirm_msg = $msg_data->active_screen;
                $msg_type = "exists_success_application_msg";
                $emailArr['email'] =    $submission_data['parent_email'];
                $subject = $msg_data->active_email_subject;
                $confirm_title = $msg_data->active_screen_title;
                $confirm_subject = $msg_data->active_screen_subject;
            } else {
                $emailArr['type'] = "pending_email";
                $student_type = "pending";
                $msg_type = "new_success_application_msg";
                $emailArr['email'] = $submission_data['parent_email'];
                $emailArr['msg'] = $msg_data->pending_email;
                $confirm_msg = $msg_data->pending_screen;
                $subject = $msg_data->pending_email_subject;
                $confirm_title = $msg_data->pending_screen_title;
                $confirm_subject = $msg_data->pending_screen_subject;
            }
            $subject = str_replace("{student_name}", $emailArr['first_name'] . " " . $emailArr['last_name'], $subject);
            $subject = str_replace("{parent_name}", $emailArr['parent_first_name'] . " " . $emailArr['parent_last_name'], $subject);
            $subject = str_replace("{confirm_number}", $emailArr['confirm_number'], $subject);
            $emailArr['subject'] = $subject;


            $mail = sendMail($emailArr);

            $msg = str_replace("{student_name}", (isset($emailArr['first_name']) ? $emailArr['first_name'] . " " . $emailArr['last_name'] : ""), $emailArr['msg']);
            $msg = str_replace("{parent_name}", (isset($emailArr['parent_first_name']) ? $emailArr['parent_first_name'] . " " . $emailArr['parent_last_name'] : ""), $msg);
            $msg = str_replace("{confirm_number}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
            $msg = str_replace("{confirmation_no}", (isset($emailArr['confirm_number']) ? $emailArr['confirm_number'] : ""), $msg);
            $msg = str_replace("{transcript_due_date}", (isset($emailArr['transcript_due_date']) ? $emailArr['transcript_due_date'] : ""), $msg);

            $data = array();
            $data['submission_id'] = $id;
            $data['email_to'] = $submission_data['parent_email'];
            $data['email_subject'] = $emailArr['subject'];
            $data['email_body'] = $msg;
            $data['logo'] = getDistrictLogo();
            $data['module'] = "Submission - Resend Confirmation";


            if ($mail) {
                $data['status'] = "Success";
                Session::flash('success', 'Confirmation Mail Sent Successfully.');
            } else {
                $data['status'] = "Error";
            }
            createEmailActivityLog($data);
        }
    }
}
