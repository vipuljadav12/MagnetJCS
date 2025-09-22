<?php

namespace App\Modules\LateSubmission\Controllers;

use App\Modules\LateSubmission\Models\LateSubmissionEditCommunicationLog;
use App\Modules\LateSubmission\Models\LateSubmissionEditCommunication;
use App\Modules\Submissions\Models\{Submissions, SubmissionsStatusUniqueLog};
use App\Modules\Application\Models\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Modules\Form\Models\Form;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\ProcessSelection\Models\ProcessSelection;
use App\Modules\CustomCommunication\Export\{CustomCommunicationEmails};
use App\Modules\CustomCommunication\Models\CustomCommunication;
use App\Modules\CustomCommunication\Models\CustomCommunicationData;
use App\Modules\Submissions\Models\{LateSubmissionFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionsFinalStatus};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\EditCommunication\Models\EditCommunication;
use App\Modules\LateSubmission\Models\LateSubmissionProcessLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class LateSubmissionEditCommunicationController extends Controller
{
    /* public $statusArr = ["Offered" => "Offer Communication", "Waitlisted" => "Waitlist", "DeniedEligibility" => "Denied due to Ineligibility", "OfferedWaitlisted" => "Offered and Waitlist Option", "DeniedIncomplete" => "Denied due to Incomplete Records"];//,"ContractLetterText" => "Contract Revisit Text"];

  public $dbStatusArr = ["Offered" => "Offered", "Waitlisted" => "Waitlisted", "DeniedEligibility" => "Denied due to Ineligibility", "OfferedWaitlisted" => "Offered and Waitlisted", "DeniedIncomplete" => "Denied due to Incomplete Records"];//,"ContractLetterText" => "Contract Revisit Text"];*/

    public $statusArr = ["Offered" => "Offer Communication", "Waitlisted" => "Waitlist", "OfferedWaitlisted" => "Offered and Waitlist Option", "DeniedEligibility" => "Denied due to Ineligibility"]; //,"ContractLetterText" => "Contract Revisit Text"];

    public $dbStatusArr = ["Offered" => "Offered", "Waitlisted" => "Waitlisted", "OfferedWaitlisted" => "Offered and Waitlisted", "DeniedEligibility" => "Denied due to Ineligibility"]; //,"ContractLetterText" => "Contract Revisit Text"];

    public function application_index()
    {
        $applications = Form::where("status", "y")->get();
        return view("LateSubmission::waitlist_application_index", compact("applications"));
    }

    public function index($application_id, $status = "Offered")
    {
        $display_outcome = SubmissionsStatusUniqueLog::count();
        $statusArr = $this->statusArr;
        $dbStatusArr = $this->dbStatusArr;

        $data = LateSubmissionEditCommunicationLog::where("status", $dbStatusArr[$status])->where("district_id", Session::get("district_id"))->where("application_id", $application_id)->orderByDesc("created_at")->get();

        $mail_download_data = $letter_download_data = array();
        foreach ($data as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['created_by'] = getUserName($value->generated_by);
            $tmp['file_name'] = $value->file_name;
            $tmp['total_count'] = $value->total_count;
            $tmp['created_at'] = getDateTimeFormat($value->created_at);
            if ($value->communication_type == "Letter")
                $letter_download_data[] = $tmp;
            else
                $mail_download_data[] = $tmp;
        }


        $data = [];
        $district_id = Session::get('district_id');
        $data = LateSubmissionEditCommunication::where("application_id", $application_id)->where('district_id', $district_id)->where('status', $dbStatusArr[$status])->first();
        return view('LateSubmission::edit_communication_index', compact('data', 'statusArr', 'status', 'dbStatusArr', 'letter_download_data', 'mail_download_data', "display_outcome", "application_id"));
    }

    public function storeLetter(Request $request)
    {

        $req = $request->all();
        $data = array();
        $data['district_id'] = Session::get('district_id');
        $data['letter_body'] = $req['letter_body'];
        $data['application_id'] = $req['application_id'];
        $data['status'] = $req['status'];
        $data['created_by'] = Auth::user()->id;
        $rs = LateSubmissionEditCommunication::updateOrCreate(["status" => $data['status'], "application_id" => $data['application_id']], $data);
        if (isset($request->generate_letter_now)) {
            return $this->generate_letter_now($data['status'], $req['application_id']);
        }

        Session::flash('success', 'Communication data saved successfully.');
        return redirect('admin/LateSubmission/EditCommunication/application/' . $data['application_id'] . '/' . $req['redirect_status']);
    }


    public function generate_letter_now($status, $application_id, $preview = false)
    {
        set_time_limit(0);

        $rs = ProcessSelection::where("application_id", $application_id)->where("type", "late_submission")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
        $version = $rs->version;

        $district_id = Session::get("district_id");
        $cdata = LateSubmissionEditCommunication::where("status", $status)->where("application_id", $application_id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->where("application.form_id", $application_id)->first();

        if ($status == "Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("second_choice_final_status", ["Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Pending")->where("third_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        }

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = ProcessSelection::where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->last_date_online_acceptance);
        $last_date_offline_acceptance = getDateTimeFormat($rs->last_date_offline_acceptance);

        $student_data = array();
        foreach ($submissions as $key => $value) {
            // $application = Application::where('id', $value->application_id)->first();
            $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.form_id", $value->form_id)->select("application.*", "enrollments.school_year")->first();
            $generated = false;
            if (($value->first_choice_final_status == $status && $status == "Offered") || ($value->first_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->first_choice_final_status == $status)) {
                $generated = true;
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['first_name'] = $value->first_name;
                $tmp['last_name'] = $value->last_name;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');
                $tmp['signature'] = get_signature('letter_signature');
                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");
                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getProgramName($value->first_choice_program_id);
                $tmp['offer_program_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['accepted_program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                $tmp['waitlist_program'] = "";
                $tmp['waitlist_program_with_grade'] = "";


                $tmp['waitlist_program_1'] = "";
                $tmp['waitlist_program_1_with_grade'] = "";
                $tmp['waitlist_program_2'] = "";
                $tmp['waitlist_program_2_with_grade'] = "";
                $tmp['waitlist_program_3'] = "";
                $tmp['waitlist_program_3_with_grade'] = "";




                if ($status == "Waitlisted") {
                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getProgramName($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getProgramName($value->second_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_3'] = getProgramName($value->third_choice_program_id);
                            $tmp['waitlist_program_3_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    } elseif ($value->second_choice_final_status == "Waitlisted") {
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getProgramName($value->second_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getProgramName($value->third_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                    } elseif ($value->third_choice_final_status == "Waitlisted") {
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getProgramName($value->third_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        $tmp['waitlist_program_2'] = "";
                        $tmp['waitlist_program_2_with_grade'] = "";
                        $tmp['waitlist_program_3'] = "";
                        $tmp['waitlist_program_3_with_grade'] = "";
                    }
                }

                /* Offer link */
                if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                    $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                } else {
                    $tmp['offer_link'] = "";
                }
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
                $msg = find_replace_string($cdata->letter_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['letter_body'] = $msg;
                $student_data[] = $tmp;
            }


            if ((($value->second_choice_final_status == $status && $status == "Offered") || ($value->second_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->second_choice_final_status == $status)) && !$generated) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['first_name'] = $value->first_name;
                $tmp['last_name'] = $value->last_name;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');
                $tmp['signature'] = get_signature('letter_signature');
                $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                $tmp['accepted_program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getProgramName($value->second_choice_program_id);
                $tmp['offer_program_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                $tmp['waitlist_program_1'] = "";
                $tmp['waitlist_program_1_with_grade'] = "";
                $tmp['waitlist_program_2'] = "";
                $tmp['waitlist_program_2_with_grade'] = "";
                $tmp['waitlist_program_3'] = "";
                $tmp['waitlist_program_3_with_grade'] = "";

                if ($value->first_choice_final_status == "Waitlisted") {
                    $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                    $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                }


                if ($status == "Waitlisted") {
                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_3'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_3_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    } elseif ($value->second_choice_final_status == "Waitlisted") {
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->second_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                    } elseif ($value->third_choice_final_status == "Waitlisted") {
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        $tmp['waitlist_program_2'] = "";
                        $tmp['waitlist_program_2_with_grade'] = "";
                        $tmp['waitlist_program_3'] = "";
                        $tmp['waitlist_program_3_with_grade'] = "";
                    }
                }



                if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                    $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                } else {
                    $tmp['offer_link'] = "";
                }
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
                $msg = find_replace_string($cdata->letter_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['letter_body'] = $msg;
                $student_data[] = $tmp;
            }

            if ((($value->third_choice_final_status == $status && $status == "Offered") || ($value->third_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->third_choice_final_status == $status)) && !$generated) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['first_name'] = $value->first_name;
                $tmp['last_name'] = $value->last_name;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');
                $tmp['signature'] = get_signature('letter_signature');
                $tmp['program_name'] = getPrograMname($value->third_choice_program_id);
                $tmp['program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                $tmp['accepted_program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;



                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getPrograMname($value->third_choice_program_id);
                $tmp['offer_program_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;

                $tmp['waitlist_program_1'] = "";
                $tmp['waitlist_program_1_with_grade'] = "";
                $tmp['waitlist_program_2'] = "";
                $tmp['waitlist_program_2_with_grade'] = "";
                $tmp['waitlist_program_3'] = "";
                $tmp['waitlist_program_3_with_grade'] = "";

                if ($value->first_choice_final_status == "Waitlisted") {
                    $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                    $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                }
                if ($value->second_choice_final_status == "Waitlisted") {
                    $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                    $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                }

                if ($status == "Waitlisted") {
                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_3'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_3_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    } elseif ($value->second_choice_final_status == "Waitlisted") {
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->second_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                    } elseif ($value->third_choice_final_status == "Waitlisted") {
                        if ($value->third_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->third_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_1'] = "";
                            $tmp['waitlist_program_1_with_grade'] = "";
                        }
                        $tmp['waitlist_program_2'] = "";
                        $tmp['waitlist_program_2_with_grade'] = "";
                        $tmp['waitlist_program_3'] = "";
                        $tmp['waitlist_program_3_with_grade'] = "";
                    }
                }





                if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                    $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                } else {
                    $tmp['offer_link'] = "";
                }
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;
                $msg = find_replace_string($cdata->letter_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['letter_body'] = $msg;
                $student_data[] = $tmp;
            }
        }
        if ($preview == true) {
            $student_data = array();
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['signature'] = get_signature('letter_signature');
            $tmp['application_url'] = url('/');

            //$msg = strtr($cdata->letter_body,$tmp);
            $msg = $cdata->letter_body;
            // $msg = str_replace("{","",$msg);
            //$msg = str_replace("}","",$msg);
            $tmp['letter_body'] = $msg;
            $student_data[] = $tmp;
        }
        view()->share('student_data', $student_data);
        view()->share("application_data", $application_data);

        $fileName =  "EditCustomCommunication-" . strtotime(date("Y-m-d H:i:s")) . '.pdf';
        $path = "resources/assets/admin/edit_communication";
        if ($preview) {
            $pdf = Pdf::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $fileName = "preview.pdf";
            $pdf->save($path . '/' . $fileName);
            return response()->file($path . "/" . $fileName);
        } else {
            $pdf = Pdf::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $pdf->save($path . '/' . $fileName);

            $data = array();
            $data['district_id'] = Session::get("district_id");
            $data['communication_type'] = "Letter";
            $data['status'] = $status;
            $data['file_name'] = $fileName;
            $data['total_count'] = count($student_data);
            $data['generated_by'] = Auth::user()->id;
            $data['application_id'] = $application_id;
            LateSubmissionEditCommunicationLog::create($data);
            return $pdf->download($fileName);
        }
    }


    public function storeEmail(Request $request)
    {

        $req = $request->all();
        $data = array();
        $data['district_id'] = Session::get('district_id');
        $data['mail_subject'] = $req['mail_subject'];
        $data['mail_body'] = $req['mail_body'];
        $data['status'] = $req['status'];
        $data['created_by'] = Auth::user()->id;
        $data['application_id'] = $application_id = $req['application_id'];

        $rs = LateSubmissionEditCommunication::updateOrCreate(["status" => $data['status'], "application_id" => $data['application_id']], $data);
        if (isset($request->send_email_now)) {
            $this->send_email_now($req['status'], $application_id);
            Session::flash("success", "Custom Communication emails sent successfully.");
        }

        Session::flash('success', 'Communication data saved successfully.');
        return redirect('admin/LateSubmission/EditCommunication/application/' . $data['application_id'] . "/" . $req['redirect_status']);
    }


    public function send_email_now($status, $application_id, $preview = false)
    {
        $cdata = LateSubmissionEditCommunication::where("status", $status)->where("application_id", $application_id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.form_id", $application_id)->select("application.*", "enrollments.school_year")->first();
        $district_id = Session::get('district_id');

        $last_date_online_acceptance = $last_date_offline_acceptance = "";

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
        $version = $rs->version;

        if (!empty($rs)) {
            $last_date_online_acceptance = getDateTimeFormat($rs->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($rs->last_date_offline_acceptance);
        } else {
            $last_date_online_acceptance = getDateTimeFormat('');
            $last_date_offline_acceptance = getDateTimeFormat('');
        }

        if ($status == "Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("second_choice_final_status", ["Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Pending")->where("third_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        }

        $student_data = array();

        if ($preview == true) {
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['application_url'] = url('/');
            $tmp['signature'] = get_signature('email_signature');


            $msg = find_replace_string($cdata->mail_body, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $msg = $cdata->mail_body;
            $tmp['email_text'] = $msg;
            $tmp['logo'] = getDistrictLogo();


            $msg = find_replace_string($cdata->mail_subject, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['subject'] = $msg;
            $data = $tmp;
            $type = "late_submission";
            return view("emails.preview_communication_index", compact('data', 'status', 'type', 'application_id'));
        } else {
            $countMail = 0;
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
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                    $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                    $tmp['birth_date'] = getDateFormat($value->birthday);
                    $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                    $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                    $tmp['parent_email'] = $value->parent_email;
                    $tmp['student_id'] = $value->student_id;
                    $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                    $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                    $tmp['application_url'] = url('/');
                    $tmp['signature'] = get_signature('letter_signature');
                    $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                    $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");
                    $tmp['school_year'] = $application_data1->school_year;
                    $tmp['enrollment_period'] = $tmp['school_year'];
                    $t1 = explode("-", $tmp['school_year']);
                    $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                    $tmp['next_year'] = date("Y") + 1;

                    $tmp['offer_program'] = getProgramName($value->first_choice_program_id);
                    $tmp['offer_program_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    $tmp['accepted_program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                    $tmp['waitlist_program'] = "";
                    $tmp['waitlist_program_with_grade'] = "";


                    $tmp['waitlist_program_1'] = "";
                    $tmp['waitlist_program_1_with_grade'] = "";
                    $tmp['waitlist_program_2'] = "";
                    $tmp['waitlist_program_2_with_grade'] = "";
                    $tmp['waitlist_program_3'] = "";
                    $tmp['waitlist_program_3_with_grade'] = "";




                    if ($status == "Waitlisted") {
                        if ($value->first_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getProgramName($value->first_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getProgramName($value->second_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_3'] = getProgramName($value->third_choice_program_id);
                                $tmp['waitlist_program_3_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_3'] = "";
                                $tmp['waitlist_program_3_with_grade'] = "";
                            }
                        } elseif ($value->second_choice_final_status == "Waitlisted") {
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getProgramName($value->second_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getProgramName($value->third_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                        } elseif ($value->third_choice_final_status == "Waitlisted") {
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getProgramName($value->third_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getProgramName($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    }

                    /* Offer link */
                    if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                        $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                    } else {
                        $tmp['offer_link'] = "";
                    }
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;


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
                    if (config('variables.environment') == 'production')
                        $countMail = 0;
                    if ($countMail == 0) {
                        sendMail($tmp, true);
                    }
                    $countMail++;
                }


                if ((($value->second_choice_final_status == $status && $status == "Offered") || ($value->second_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->second_choice_final_status == $status)) && !$generated) {
                    $tmp = array();
                    $tmp['id'] = $value->id;
                    $tmp['student_id'] = $value->student_id;
                    $tmp['confirmation_no'] = $value->confirmation_no;
                    $tmp['name'] = $value->first_name . " " . $value->last_name;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                    $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                    $tmp['birth_date'] = getDateFormat($value->birthday);
                    $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                    $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                    $tmp['parent_email'] = $value->parent_email;
                    $tmp['student_id'] = $value->student_id;
                    $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                    $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                    $tmp['application_url'] = url('/');
                    $tmp['signature'] = get_signature('letter_signature');
                    $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                    $tmp['program_name_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getProgramName($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                    $tmp['accepted_program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                    $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                    $tmp['school_year'] = $application_data1->school_year;
                    $tmp['enrollment_period'] = $tmp['school_year'];
                    $t1 = explode("-", $tmp['school_year']);
                    $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                    $tmp['next_year'] = date("Y") + 1;

                    $tmp['offer_program'] = getProgramName($value->second_choice_program_id);
                    $tmp['offer_program_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                    $tmp['waitlist_program_1'] = "";
                    $tmp['waitlist_program_1_with_grade'] = "";
                    $tmp['waitlist_program_2'] = "";
                    $tmp['waitlist_program_2_with_grade'] = "";
                    $tmp['waitlist_program_3'] = "";
                    $tmp['waitlist_program_3_with_grade'] = "";

                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    }


                    if ($status == "Waitlisted") {
                        if ($value->first_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_3'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_3_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_3'] = "";
                                $tmp['waitlist_program_3_with_grade'] = "";
                            }
                        } elseif ($value->second_choice_final_status == "Waitlisted") {
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getPrograMname($value->second_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                        } elseif ($value->third_choice_final_status == "Waitlisted") {
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    }



                    if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                        $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                    } else {
                        $tmp['offer_link'] = "";
                    }
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;


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
                    if (config('variables.environment') == 'production')
                        $countMail = 0;
                    if ($countMail == 0) {
                        sendMail($tmp, true);
                    }
                    $countMail++;
                }

                if ((($value->third_choice_final_status == $status && $status == "Offered") || ($value->third_choice_final_status == "Offered" && $status == "Offered and Waitlisted") || ($value->third_choice_final_status == $status)) && !$generated) {
                    $tmp = array();
                    $tmp['id'] = $value->id;
                    $tmp['student_id'] = $value->student_id;
                    $tmp['confirmation_no'] = $value->confirmation_no;
                    $tmp['name'] = $value->first_name . " " . $value->last_name;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                    $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                    $tmp['birth_date'] = getDateFormat($value->birthday);
                    $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                    $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                    $tmp['parent_email'] = $value->parent_email;
                    $tmp['student_id'] = $value->student_id;
                    $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                    $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                    $tmp['application_url'] = url('/');
                    $tmp['signature'] = get_signature('letter_signature');
                    $tmp['program_name'] = getPrograMname($value->third_choice_program_id);
                    $tmp['program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                    $tmp['accepted_program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                    $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;



                    $tmp['school_year'] = $application_data1->school_year;
                    $tmp['enrollment_period'] = $tmp['school_year'];
                    $t1 = explode("-", $tmp['school_year']);
                    $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                    $tmp['next_year'] = date("Y") + 1;

                    $tmp['offer_program'] = getPrograMname($value->third_choice_program_id);
                    $tmp['offer_program_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;

                    $tmp['waitlist_program_1'] = "";
                    $tmp['waitlist_program_1_with_grade'] = "";
                    $tmp['waitlist_program_2'] = "";
                    $tmp['waitlist_program_2_with_grade'] = "";
                    $tmp['waitlist_program_3'] = "";
                    $tmp['waitlist_program_3_with_grade'] = "";

                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    }
                    if ($value->second_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                        $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                    }

                    if ($status == "Waitlisted") {
                        if ($value->first_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_1'] = getPrograMname($value->first_choice_program_id);
                            $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getPrograMname($value->second_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_3'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_3_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_3'] = "";
                                $tmp['waitlist_program_3_with_grade'] = "";
                            }
                        } elseif ($value->second_choice_final_status == "Waitlisted") {
                            if ($value->second_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getPrograMname($value->second_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_2'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_2_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_2'] = "";
                                $tmp['waitlist_program_2_with_grade'] = "";
                            }
                        } elseif ($value->third_choice_final_status == "Waitlisted") {
                            if ($value->third_choice_final_status == "Waitlisted") {
                                $tmp['waitlist_program_1'] = getPrograMname($value->third_choice_program_id);
                                $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                            } else {
                                $tmp['waitlist_program_1'] = "";
                                $tmp['waitlist_program_1_with_grade'] = "";
                            }
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                            $tmp['waitlist_program_3'] = "";
                            $tmp['waitlist_program_3_with_grade'] = "";
                        }
                    }





                    if (($status == "Offered" || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                        $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                    } else {
                        $tmp['offer_link'] = "";
                    }
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;



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
                    if (config('variables.environment') == 'production')
                        $countMail = 0;
                    if ($countMail == 0) {
                        sendMail($tmp, true);
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
            $data['application_id'] = $application_id;
            $data['total_count'] = count($student_data);
            $data['generated_by'] = Auth::user()->id;
            LateSubmissionEditCommunicationLog::create($data);

            Excel::store(new CustomCommunicationEmails(collect($student_data)), $fileName, 'edit_communication');
        }


        //Excel::download(new SubmissionExport(collect($data_ary)), 'Submissions.xlsx');

    }


    public function fetchEmails(Request $request)
    {
        $status = $request->status;
        $district_id = Session::get("district_id");
        $application_id = $request->application_id;

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
        $version = $rs->version;


        if ($status == "Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("second_choice_final_status", ["Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                //->whereIn("second_offer_status", array("Pending"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Pending")->where("second_choice_final_status", "Pending")->where("third_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('late_submissions_final_status.version', $version)
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        }
        $student_data = array();
        foreach ($submissions as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['student_name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
            $tmp['parent_email'] = $value->parent_email;
            $student_data[] = $tmp;
        }
        return json_encode($student_data);
    }



    public function downloadFile($id)
    {
        $data = LateSubmissionEditCommunicationLog::where("id", $id)->first();
        if (!empty($data)) {
            $file_path = 'resources/assets/admin/edit_communication/' . $data->file_name;

            if ($data->type == "Email") {
                $headers = array(
                    'Content-Type: application/vnd.openxmlformats-officedocument',
                );
            } else {
                $headers = array(
                    'Content-Type: application/pdf',
                );
            }
            return Response::download($file_path, $data->file_name, $headers);
        }
    }
    public function previewPDF($status, $application_id)
    {
        return $this->generate_letter_now($this->dbStatusArr[$status], $application_id, true);
    }

    public function previewEmail($status, $application_id = 59)
    {
        return $this->send_email_now($this->dbStatusArr[$status], $application_id, true);
    }


    public function sendTestMail(Request $request)
    {
        $req = $request->all();
        $email = $req['email'];
        $slug = $status = $req['status'];
        $type = $req['type'];
        $application_id = $req['application_id'];
        $district_id = 3;

        //$slug = "ContractLetterText";
        if ($type == "late_submission") {
            $sdata =  LateSubmissionEditCommunication::where('district_id', $district_id)
                ->where('status', $status)
                ->where("application_id", $application_id)
                ->first();
        } elseif ($type == "waitlist") {
            $sdata =  LateSubmissionEditCommunication::where('district_id', $district_id)
                ->where('status', $status)
                ->where("application_id", $application_id)
                ->first();
        } else {
            $sdata =  EditCommunication::where('district_id', $district_id)
                ->where('status', $status)
                ->where("application_id", $application_id)
                ->first();
        }

        if ($slug == "contract_signed" || $slug == "waitlist_contract_signed") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", "Offered and Accepted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->first();
            $parent_name = $submissions->parent_first_name . " " . $submissions->parent_last_name;
            $student_name = $submissions->first_name . " " . $submissions->last_name;
            $signature = get_signature('email_signature');

            $tmp = array();
            $tmp['parent_name'] = $parent_name;
            $tmp['student_name'] = $student_name;
            $tmp['signature'] = $signature;
            $tmp['confirmation_no'] = $submissions->confirmation_no;

            $msg = find_replace_string($sdata->value, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['msg'] = $msg;

            $msg = find_replace_string($sdata->value, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['subject'] = $msg;

            $tmp['email'] = $email;
            sendMail($tmp, true);
            echo "Done";
            exit;
        }

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = ProcessSelection::where("application_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();
        if (!empty($rs)) {
            $last_date_online_acceptance = getDateTimeFormat($rs->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($rs->last_date_offline_acceptance);
        } else {
            $last_date_online_acceptance = getDateTimeFormat('');
            $last_date_offline_acceptance = getDateTimeFormat('');
        }

        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();



        if ($status == "Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Waitlisted");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "<>", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "<>", "Offered")->Where("second_choice_final_status", "Waitlisted");
                });
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } elseif ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
            })
                ->whereIn("second_offer_status", array("Pending"))
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "<>", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        }
        $countMail = 0;

        foreach ($submissions as $key => $value) {
            if ($countMail == 0) {
                $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

                $generated = false;

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
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                if ($value->second_choice_program_id != 0) {
                    $tmp['waitlist_program'] = getProgramName($value->second_choice_program_id);
                    $tmp['waitlist_program_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                } else {
                    $tmp['waitlist_program'] = "";
                    $tmp['waitlist_program_with_grade'] = "";
                }

                if ($status == "Waitlisted") {
                    if ($value->first_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getProgramName($value->first_choice_program_id);
                        $tmp['waitlist_program_1_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        if ($value->second_choice_final_status == "Waitlisted") {
                            $tmp['waitlist_program_2'] = getProgramName($value->second_choice_program_id);
                            $tmp['waitlist_program_2_with_grade'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        } else {
                            $tmp['waitlist_program_2'] = "";
                            $tmp['waitlist_program_2_with_grade'] = "";
                        }
                    } elseif ($value->second_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getProgramName($value->second_choice_final_status);
                        $tmp['waitlist_program_1_with_grade'] = getProgramName($value->second_choice_final_status) . " - Grade " . $value->next_grade;
                        $tmp['waitlist_program_2'] = "";
                        $tmp['waitlist_program_2_with_grade'] = "";
                    } else {
                        $tmp['waitlist_program_1'] = "";
                        $tmp['waitlist_program_1_with_grade'] = "";
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

                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;


                $msg = find_replace_string($sdata->mail_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['msg'] = $msg;

                $msg = find_replace_string($sdata->mail_subject, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['subject'] = $msg;

                $tmp['email'] = $email;
                $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);
                if ($countMail == 0) {
                    sendMail($tmp, true);
                }
                $countMail++;
            }
        }
        echo "done";
    }
}
