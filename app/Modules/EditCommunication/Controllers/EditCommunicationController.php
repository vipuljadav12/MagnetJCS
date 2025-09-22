<?php

namespace App\Modules\EditCommunication\Controllers;

use App\Modules\EditCommunication\Models\EditCommunicationLog;
use App\Modules\EditCommunication\Models\EditCommunication;
use App\Modules\Submissions\Models\{Submissions, SubmissionsStatusUniqueLog};
use App\Modules\Application\Models\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Form\Models\Form;
use App\Modules\ProcessSelection\Models\ProcessSelection;
use App\Modules\CustomCommunication\Export\{CustomCommunicationEmails};
use App\Modules\CustomCommunication\Models\CustomCommunication;
use App\Modules\CustomCommunication\Models\CustomCommunicationData;
use App\Modules\Submissions\Models\SubmissionsFinalStatus;
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\LateSubmission\Models\LateSubmissionEditCommunication;
use App\Modules\Waitlist\Models\WaitlistEditCommunication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class EditCommunicationController extends Controller
{
    public $statusArr = ["Offered" => "Offer Communication", "Waitlisted" => "Waitlist", "DeniedEligibility" => "Denied due to Ineligibility", "OfferedWaitlisted" => "Offered and Waitlist Option", "DeniedIncomplete" => "Denied due to Incomplete Records"]; //,"ContractLetterText" => "Contract Revisit Text"];

    public $dbStatusArr = ["Offered" => "Offered", "Waitlisted" => "Waitlisted", "DeniedEligibility" => "Denied due to Ineligibility", "OfferedWaitlisted" => "Offered and Waitlisted", "DeniedIncomplete" => "Denied due to Incomplete Records"]; //,"ContractLetterText" => "Contract Revisit Text"];

    public function application_index()
    {
        $applications = Form::where("status", "y")->get();
        return view("EditCommunication::application_index", compact("applications"));
    }

    public function index($application_id, $status = "Offered")
    {
        $display_outcome = SubmissionsStatusUniqueLog::count();
        $statusArr = $this->statusArr;
        $dbStatusArr = $this->dbStatusArr;

        $data = EditCommunicationLog::where("status", $dbStatusArr[$status])->where("district_id", Session::get("district_id"))->where("application_id", $application_id)->orderByDesc("created_at")->get();

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
        $data = EditCommunication::where("application_id", $application_id)->where('district_id', $district_id)->where('status', $dbStatusArr[$status])->first();
        return view('EditCommunication::index', compact('data', 'statusArr', 'status', 'dbStatusArr', 'letter_download_data', 'mail_download_data', "display_outcome", "application_id"));
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
        $rs = EditCommunication::updateOrCreate(["status" => $data['status'], "application_id" => $data['application_id']], $data);
        if (isset($request->generate_letter_now)) {
            return $this->generate_letter_now($data['status'], $req['application_id']);
        }

        Session::flash('success', 'Communication data saved successfully.');
        return redirect('admin/EditCommunication/application/' . $data['application_id'] . '/' . $req['redirect_status']);
    }


    public function generate_letter_now($status, $application_id, $preview = false)
    {

        set_time_limit(-1);
        $district_id = Session::get("district_id");
        $cdata = EditCommunication::where("status", $status)->where("application_id", $application_id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->where("application.form_id", $application_id)->where('application.enrollment_id', Session::get("enrollment_id"))->first();

        if ($status == "Waitlisted") {
            /*
            ->where(function ($q) use ($status) {
                                    $q->where(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Waitlisted");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "<>", "Offered");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "<>", "Offered")->Where("second_choice_final_status", "Waitlisted");
                                    });
                                })
                                */
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            // ->where(function ($q) use ($status) {
            //                         $q->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
            //                     })
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("first_choice_final_status", ["Pending", "Waitlisted"])->whereIn("second_choice_final_status", ["Pending", "Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
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
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        }

        $last_date_online_acceptance = $last_date_offline_acceptance = "";

        $rs = ProcessSelection::where("application_id", $application_id)->where('enrollment_id', Session::get("enrollment_id"))->first();
        if (!empty($rs)) {
            $last_date_online_acceptance = getDateTimeFormat($rs->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($rs->last_date_offline_acceptance);
        } else {
            $last_date_online_acceptance = $last_date_offline_acceptance = getDateTimeFormat('');
        }

        $student_data = array();
        foreach ($submissions as $key => $value) {
            // $application = Application::where('id', $value->application_id)->first();
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
                $tmp['program_name'] = getPrograMname($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");
                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getPrograMname($value->first_choice_program_id);
                $tmp['offer_program_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['accepted_program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
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
                $tmp['program_name'] = getPrograMname($value->second_choice_program_id);
                $tmp['program_name_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                $tmp['accepted_program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;



                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $tmp['offer_program'] = getPrograMname($value->second_choice_program_id);
                $tmp['offer_program_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;

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
            $tmp['parent_address'] = "123, Street Address<br>City, State Zip";
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
            EditCommunicationLog::create($data);
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

        $rs = EditCommunication::updateOrCreate(["status" => $data['status'], "application_id" => $data['application_id']], $data);
        if (isset($request->send_email_now)) {
            $this->send_email_now($req['status'], $application_id);
            Session::flash("success", "Custom Communication emails sent successfully.");
        }

        Session::flash('success', 'Communication data saved successfully.');
        return redirect('admin/EditCommunication/application/' . $data['application_id'] . "/" . $req['redirect_status']);
    }


    public function send_email_now($status, $application_id, $preview = false)
    {

        $cdata = EditCommunication::where("status", $status)->where("application_id", $application_id)->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.form_id", $application_id)->select("application.*", "enrollments.school_year")->first();
        $district_id = Session::get('district_id');

        $last_date_online_acceptance = $last_date_offline_acceptance = "";

        $rs = ProcessSelection::where("application_id", $application_id)->where('enrollment_id', Session::get("enrollment_id"))->first();
        if (!empty($rs)) {
            $last_date_online_acceptance = getDateTimeFormat($rs->last_date_online_acceptance);
            $last_date_offline_acceptance = getDateTimeFormat($rs->last_date_offline_acceptance);
        } else {
            $last_date_online_acceptance = getDateTimeFormat('');
            $last_date_offline_acceptance = getDateTimeFormat('');
        }

        if ($status == "Waitlisted") {
            /*
            ->where(function ($q) use ($status) {
                                    $q->where(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Waitlisted");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "<>", "Offered");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "<>", "Offered")->Where("second_choice_final_status", "Waitlisted");
                                    });
                                })
                                */
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            // ->where(function ($q) use ($status) {
            //                         $q->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
            //                     })
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("first_choice_final_status", ["Pending", "Waitlisted"])->whereIn("second_choice_final_status", ["Pending", "Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
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
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'third_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
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
            $type = "regular";
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
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                    $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                    $tmp['program_name'] = getPrograMname($value->first_choice_program_id);
                    $tmp['program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];

                    $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");


                    $tmp['offer_program'] = getPrograMname($value->first_choice_program_id);
                    $tmp['offer_program_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                    $tmp['accepted_program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
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
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                    $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                    $tmp['program_name'] = getPrograMname($value->second_choice_program_id);
                    $tmp['program_name_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];

                    $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                    $tmp['accepted_program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


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

                    $tmp['offer_program'] = getPrograMname($value->second_choice_program_id);
                    $tmp['offer_program_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;

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

                    if (($status == "Offered"  || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                        $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                    } else {
                        $tmp['offer_link'] = "";
                    }
                    $tmp['program_name_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];
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
                    $tmp['first_name'] = $value->first_name;
                    $tmp['last_name'] = $value->last_name;
                    $tmp['current_grade'] = $value->current_grade;
                    $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                    $tmp['current_school'] = $value->current_school;
                    $tmp['zoned_school'] = $value->zoned_school;
                    $tmp['created_at'] = getDateFormat($value->created_at);
                    $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                    $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                    $tmp['program_name'] = getPrograMname($value->third_choice_program_id);
                    $tmp['program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $tmp['next_grade'];

                    $tmp['choice_program_1_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                    $tmp['choice_program_2_with_grade'] = ($value->second_choice_program_id > 0 ? getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'] : "");

                    $tmp['accepted_program_name_with_grade'] = getPrograMname($value->third_choice_program_id) . " - Grade " . $value->next_grade;
                    $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


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


                    if (($status == "Offered"  || $status == "Offered and Waitlisted") && $value->offer_slug != "") {
                        $tmp['offer_link'] = url('/Offers/' . $value->offer_slug);
                    } else {
                        $tmp['offer_link'] = "";
                    }
                    $tmp['program_name_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $tmp['next_grade'];
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
            EditCommunicationLog::create($data);

            Excel::store(new CustomCommunicationEmails(collect($student_data)), $fileName, 'edit_communication');
        }


        //Excel::download(new SubmissionExport(collect($data_ary)), 'Submissions.xlsx');


    }


    public function fetchEmails(Request $request)
    {
        $status = $request->status;
        $district_id = Session::get("district_id");
        $application_id = $request->application_id;
        if ($status == "Waitlisted") {
            /*
            ->where(function ($q) use ($status) {
                                    $q->where(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Waitlisted")->where("third_choice_final_status", "Waitlisted");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "<>", "Offered");
                                    })->orWhere(function ($q1) {
                                        $q1->where("first_choice_final_status", "<>", "Offered")->Where("second_choice_final_status", "Waitlisted");
                                    });
                                })
                                */
            $submissions = Submissions::where('district_id', $district_id)
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->where('submissions.submission_status', "Waitlisted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            // ->where(function ($q) use ($status) {
            //                         $q->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
            //                     })
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->whereIn("first_choice_final_status", ["Pending", "Waitlisted"])->whereIn("second_choice_final_status", ["Pending", "Waitlisted"])->where("third_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
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
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.form_id", $application_id)
                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
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
        $data = EditCommunicationLog::where("id", $id)->first();
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


    public function previewPDF($status, $application_id = 1)
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
            $sdata =  WaitlistEditCommunication::where('district_id', $district_id)
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
        $rs = ProcessSelection::where("application_id", $application_id)->where("enrollment_id", Session::get("enrollment_id"))->first();
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
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered and Waitlisted") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where("first_choice_final_status", "Waitlisted")->where("second_choice_final_status", "Offered");
            })
                ->whereIn("second_offer_status", array("Pending"))
                ->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Offered") {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($status) {
                $q->where(function ($q1) {
                    $q1->where("first_choice_final_status", "Offered");
                })->orWhere(function ($q1) {
                    $q1->where("first_choice_final_status", "<>", "Waitlisted")->where("second_choice_final_status", "Offered");
                });
            })
                /*->whereNotIn("submissions.id", array(1519,1093,1967,1066,1974,1088,1394,1438,1166,1667,1139,1320,1794,1745,1177,1179,2123,1228,1677,1176,1786,1202,1773,1799,1999,1205,1435,1493,1077,1528,1446,1274,1512,1169,1192,1185,1663,1389,1540,2152,1772,1979,1337,1104,1199,1617,1079,1277,1041,1479,1747,1237,1351,2067,1485,1426,1024,1575,2144,1851,1509,2143,1218,1385,1359,1761,1550,1766,1805,1935,2049,1719,1375,1854,1271,1097,1486,1020,2068,1135,1222,1454,1207,1250,1376,1223,1473,1245,1287,1334,1961,1236,1395,1063,1291,1767,1246,1496,1357,1103,1887,2020,1399,1857,1043,1030,1046,1072,1085,1089,1095,1108,1111))*/
                ->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } elseif ($status == "Denied due to Ineligibility" || $status == "Denied due to Incomplete Records") {
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", $status)->where("submissions.form_id", $application_id)
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)->where(function ($q)  use ($status) {
                $q->where("first_choice_final_status", $status)
                    ->orWhere("second_choice_final_status", $status);
            })
                ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug', 'third_choice_final_status']);
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
                $tmp['first_choice'] = getPrograMname($value->first_choice_program_id);
                $tmp['second_choice'] = getPrograMname($value->second_choice_program_id);
                $tmp['program_name'] = getPrograMname($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];

                $tmp['offer_program'] = getPrograMname($value->first_choice_program_id);
                $tmp['offer_program_with_grade'] = getPrograMname($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                if ($value->second_choice_program_id != 0) {
                    $tmp['waitlist_program'] = getPrograMname($value->second_choice_program_id);
                    $tmp['waitlist_program_with_grade'] = getPrograMname($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                } else {
                    $tmp['waitlist_program'] = "";
                    $tmp['waitlist_program_with_grade'] = "";
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
                    } elseif ($value->second_choice_final_status == "Waitlisted") {
                        $tmp['waitlist_program_1'] = getPrograMname($value->second_choice_final_status);
                        $tmp['waitlist_program_1_with_grade'] = getPrograMname($value->second_choice_final_status) . " - Grade " . $value->next_grade;
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
                // echo $email;exit;
                if ($countMail == 0) {
                    sendMail($tmp, true);
                }
                $countMail++;
            }
        }
        echo "done";
    }
}
