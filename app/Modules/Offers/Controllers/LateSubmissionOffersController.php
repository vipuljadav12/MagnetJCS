<?php

namespace App\Modules\Offers\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Modules\Submissions\Models\{Submissions, SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionContractsLog, SubmissionsStatusLog, SubmissionsLatestFinalStatus, LateSubmissionFinalStatus};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Application\Models\Application;
use App\Modules\EditCommunication\Models\EditCommunication;
use App\Modules\ProcessSelection\Models\ProcessSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class LateSubmissionOffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {}

    public function index()
    {
        echo $encryptedValue = Crypt::encryptString('Hello world.');
        //print_r($eligibilities);exit;
        //return view("Eligibility::index1",compact('eligibilities','eligibilityTemplates'));
    }

    public function adminOfferChoice($slug)
    {
        $date = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $msg = "";
        $last_offline_date = date("Y-m-d H:i:s", strtotime($date->value));



        $submission = LateSubmissionFinalStatus::where("offer_slug", $slug)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "late_submissions_final_status.version as waitversion", "submissions.*")->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission->application_id)->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

        $process_selection = ProcessSelection::where("enrollment_id", $submission->enrollment_id)->where("form_id", $submission->form_id)->where("type", "late_submission")->where("version", $submission->waitversion)->first();

        if (!empty($process_selection)) {
            if ($submission->last_date_online_acceptance != '')
                $last_online_date = date("Y-m-d H:i:s", strtotime($submission->last_date_online_acceptance));
            else
                $last_online_date = date("Y-m-d H:i:s", strtotime($process_selection->last_date_online_acceptance));


            if ($submission->last_date_offline_acceptance != '')
                $last_offline_date = date("Y-m-d H:i:s", strtotime($submission->last_date_offline_acceptance));
            else
                $last_offline_date = date("Y-m-d H:i:s", strtotime($process_selection->last_date_offline_acceptance));


            if (Auth::check()) {
                Session::put("contract_from_admin", "Y");
            }


            if ($submission->submission_status == "Offered and Accepted" || $submission->contract_status == "Auto Decline") {
                if (Auth::check()) {
                    Session::put("contract_from_admin", "Y");
                }
                return view("Offers::LateSubmission.admin_index", compact("slug"));
            } else
                return view("Offers::LateSubmission.admin_index", compact("slug"));

            //return view("Offers::LateSubmission.timed_out", compact("submission", "application_data", "msg"));

        } else
            return view("Offers::LateSubmission.timed_out", compact("submission", "application_data", "msg"));
    }

    public function offerChoice($slug)
    {
        if (!Session::has("district_id"))
            Session::put("district_id", 3);
        elseif (Session::get("district_id") == 0)
            Session::put("district_id", 3);



        $submission1 = LateSubmissionFinalStatus::where("offer_slug", $slug)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "late_submissions_final_status.version as waitversion", "submissions.*")->first();

        $version = $submission1->waitversion;
        $submission = SubmissionsLatestFinalStatus::where("submission_id", $submission1->submission_id)->join("submissions", "submissions.id", "submissions_latest_final_status.submission_id")->select("submissions_latest_final_status.*", "submissions_latest_final_status.version as waitversion", "submissions.*")->first();


        if (empty($submission)) {
            $submission = LateSubmissionFinalStatus::where("submission_id", $submission1->submission_id)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where("late_submissions_final_status.version", $version)->select("late_submissions_final_status.*", "late_submissions_final_status.version as waitversion", "submissions.*")->first();
        }
        if ($submission->submission_status == "Offered and Accepted" || $submission->submission_status == 'Auto Decline') {
            return view("Offers::LateSubmission.timed_out");
        }

        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission->application_id)->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();


        $process_selection = ProcessSelection::where("enrollment_id", $submission->enrollment_id)->where("form_id", $submission->form_id)->where("type", "late_submission")->where("version", $version)->first();
        $msg = "";
        if (!empty($process_selection)) {
            $str = DistrictConfiguration::where("name", "late_submission_offer_accept_screen")->select("value")->first();
            $msg = $str->value;

            if ($submission->last_date_online_acceptance != '')
                $last_online_date = date("Y-m-d H:i:s", strtotime($submission->last_date_online_acceptance));
            else
                $last_online_date = date("Y-m-d H:i:s", strtotime($process_selection->last_date_online_acceptance));


            if ($submission->last_date_offline_acceptance != '')
                $last_offline_date = date("Y-m-d H:i:s", strtotime($submission->last_date_offline_acceptance));
            else
                $last_offline_date = date("Y-m-d H:i:s", strtotime($process_selection->last_date_offline_acceptance));
        } else {
            return view("Offers::LateSubmission.timed_out", compact("submission", "application_data", "msg"));
        }



        if (!Session::has("contract_from_admin") && date("Y-m-d H:i:s") > $last_online_date && $submission->submission_status != "Offered and Accepted") {
            return view("Offers::LateSubmission.timed_out", compact("submission", "application_data", "msg"));
        } else {
            $tmp = generateShortCode($submission);
            $tmp['offer_link'] = url('/Offers/' . $slug);
            $tmp['online_offer_last_date'] = getDateTimeFormat($last_online_date);
            $tmp['offline_offer_last_date'] = getDateTimeFormat($last_offline_date);



            $second_program = "";
            $approve_program_id = 0;
            if (!empty($submission)) {
                if ($submission->first_choice_final_status == "Offered") {
                    $first_program = getProgramName($submission->first_waitlist_for);
                    $tmp['offer_program'] = $first_program;
                    $approve_program_id = $submission->first_waitlist_for;
                    // if($submission->second_choice_final_status != "Pending" && $submission->second_choice_final_status != "Denied due to Ineligibility")
                    // {
                    //     $second_program = getProgramName($submission->second_waitlist_for);
                    // }
                } elseif ($submission->second_choice_final_status == "Offered") {
                    $approve_program_id = $submission->second_waitlist_for;
                    $first_program = getProgramName($submission->second_waitlist_for);
                    $tmp['offer_program'] = $first_program;
                    if ($submission->first_choice_final_status != "Pending" && $submission->first_choice_final_status != "Denied due to Ineligibility") {
                        $second_program = getProgramName($submission->first_waitlist_for);
                    }
                } elseif ($submission->third_choice_final_status == "Offered") {
                    $approve_program_id = $submission->third_waitlist_for;
                    $first_program = getProgramName($submission->third_waitlist_for);

                    if ($submission->first_choice_final_status != "Pending" && $submission->first_choice_final_status != "Denied due to Ineligibility" && $submission->first_choice_final_status == "Waitlisted") {
                        $second_program = getProgramName($submission->first_waitlist_for);
                    } else if ($submission->second_choice_final_status != "Pending" && $submission->second_choice_final_status != "Denied due to Ineligibility" && $submission->second_choice_final_status == "Waitlisted") {
                        $second_program = getProgramName($submission->second_waitlist_for);
                    }
                }
                $tmp['program_name'] = $first_program;
                $tmp['offer_program'] = $first_program;
                $tmp['program_name_with_grade'] = $first_program . " - Grade " . $tmp['next_grade'];
                $tmp['offer_program_with_grade'] = $tmp['accepted_program_name_with_grade'] = getProgramName($approve_program_id) . " - Grade " . $tmp['next_grade'];

                if ($submission->first_offer_status == "Declined" && $submission->second_offer_status == "Declined" && $submission->third_offer_status == "Declined") {
                    $str = DistrictConfiguration::where("name", "waitlist_offer_declined_screen")->select("value")->first();
                    $msg = $str->value;
                    $msg = find_replace_string($msg, $tmp);

                    return view("Offers::Waitlist.decline_screen", compact("submission", "application_data", "msg"));
                }

                if ($submission->first_offer_status == "Declined & Waitlisted" || $submission->second_offer_status == "Declined & Waitlisted" || $submission->third_offer_status == "Declined & Waitlisted") {
                    $str = DistrictConfiguration::where("name", "waitlist_offer_waitlist_screen")->select("value")->first();
                    $msg = $str->value;
                    $msg = find_replace_string($msg, $tmp);
                    return view("Offers::Waitlist.wailist_screen", compact("submission", "application_data", "msg"));
                }

                if ($submission->submission_status == "Offered and Accepted") {
                    $str = DistrictConfiguration::where("name", "waitlist_offer_confirmation_screen")->select("value")->first();
                    $msg = $str->value;
                    $msg = find_replace_string($msg, $tmp);
                    return view("Offers::Waitlist.confirm_screen", compact("submission", "application_data", "msg"));
                }





                //$version = $submission->waitversion;
                $msg = find_replace_string($msg, $tmp);

                return view("Offers::LateSubmission.index", compact('submission', 'first_program', 'second_program', "last_online_date", "last_offline_date", "application_data", "approve_program_id", "msg", "version"));
            } else {
                echo "T";
                exit;
            }
        }
    }

    public function offerSave(Request $request)
    {
        $last_online_date = date("Y-m-d H:i:s");

        $last_offline_date = date("Y-m-d H:i:s");


        $req = $request->all();
        $version = $req['version'];
        if (Session::has("contract_from_admin")) {
            $rs = LateSubmissionFinalStatus::where("submission_id", $req['submission_id'])->update(["offer_status_by" => Auth::user()->id]);
        }

        if (isset($request->accept_btn)) {
            $program_id = $req['accept_btn'];
            $submission_id = $req['submission_id'];

            $submission = SubmissionsLatestFinalStatus::where("submission_id", $req['submission_id'])->join("submissions", "submissions.id", "submissions_latest_final_status.submission_id")->select("submissions_latest_final_status.*", "submissions.*")->first();
            if (empty($submission)) {
                $submission = LateSubmissionFinalStatus::where("submission_id", $req['submission_id'])->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "submissions.*")->first();
            }
            $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission->application_id)->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

            $rs = Submissions::where("id", $submission_id)->update(array("submission_status" => "Offered and Accepted"));




            $data = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->first();
            if (empty($data)) {
                $data = LateSubmissionFinalStatus::where("submission_id", $submission_id)->first();
            }
            if (!empty($data)) {
                $redirectN = true;
                if ($data->first_choice_final_status == "Offered" && $data->first_waitlist_for == $program_id) {
                    $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["first_offer_status" => "Accepted", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "NoAction"]);

                    $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["first_offer_status" => "Accepted", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "NoAction", "third_offer_status" => "NoAction"]);



                    $program_name = getProgramName($submission->first_waitlist_for);
                    $program_id = $submission->first_waitlist_for;
                    $redirectN = false;
                } elseif ($data->second_choice_final_status == "Offered" && $data->second_waitlist_for == $program_id) {
                    $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["second_offer_status" => "Accepted", "second_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "NoAction"]);
                    $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["second_offer_status" => "Accepted", "second_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "NoAction", "third_offer_status" => "NoAction"]);
                    $program_name = getProgramName($submission->second_waitlist_for);
                    $program_id = $submission->second_waitlist_for;
                    $redirectN = false;
                } elseif ($data->third_choice_final_status == "Offered" && $data->third_waitlist_for == $program_id) {
                    $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["third_offer_status" => "Accepted", "third_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "NoAction"]);
                    $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["third_offer_status" => "Accepted", "third_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "NoAction", "first_offer_status" => "NoAction"]);
                    $program_name = getProgramName($submission->third_waitlist_for);
                    $program_id = $submission->third_waitlist_for;
                    $redirectN = false;
                }

                if ($redirectN) {
                    return redirect()->back();
                } else {
                    $commentObj = array();
                    $commentObj['old_status'] = $submission->submission_status;
                    $commentObj['new_status'] = "Offered and Accepted";
                    if (Session::has("contract_from_admin")) {
                        $commentObj['comment'] = "Jefferson Admin has Accepted the Offer for " . $program_name . " - Grade " . $submission->next_grade;
                        $commentObj['updated_by'] = Auth::user()->id;
                    } else {
                        $commentObj['updated_by'] = 0;
                        $commentObj['comment'] = "Parent has Accepted the Offer for " . $program_name . " - Grade " . $submission->next_grade;
                    }
                    $commentObj['submission_id'] = $submission->id;
                    SubmissionsStatusLog::create($commentObj);

                    $submission = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $req['submission_id'])->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "submissions.*")->orderBy("late_submissions_final_status.created_at", "DESC")->first();

                    $this->sendOfferEmails($submission, $req, "offer_accepted");

                    return redirect()->back();
                }
            }
        } elseif (isset($request->decline_btn)) {
            $submission_id = $req['submission_id'];
            $rs = Submissions::where("id", $submission_id)->update(array("submission_status" => "Offered and Declined"));

            $submission = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "submissions.*")->first();
            $tmp = generateShortCode($submission);
            $str = DistrictConfiguration::where("name", "late_submission_offer_declined_screen")->select("value")->first();
            $msg = $str->value;
            $msg = find_replace_string($msg, $tmp);

            $commentObj = array();
            $commentObj['old_status'] = "Offered";
            $commentObj['new_status'] = "Offered and Declined";


            if ($submission->first_choice_final_status == "Offered") {
                $program_name = getProgramName($submission->first_choice_program_id);
            } elseif ($submission->second_choice_final_status == "Offered") {
                $program_name = getProgramName($submission->second_choice_program_id);
            } else {
                $program_name = getProgramName($submission->third_choice_program_id);
            }
            if (Session::has("contract_from_admin")) {
                $commentObj['comment'] = "Jefferson Admin has Declined the Offer for " . $program_name . " - Grade " . $submission->next_grade;
                $commentObj['updated_by'] = Auth::user()->id;
            } else {
                $commentObj['updated_by'] = 0;
                $commentObj['comment'] = "Parent has Declined the Offer for " . $program_name . " - Grade " . $submission->next_grade;
            }
            $commentObj['submission_id'] = $submission_id;
            SubmissionsStatusLog::create($commentObj);

            $this->sendOfferEmails($submission, $req, "offer_declined");

            $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["first_offer_status" => "Declined", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "Declined", "second_offer_update_at" => date("Y-m-d H:i:s"), "third_offer_status" => "Declined", "third_offer_update_at" => date("Y-m-d H:i:s")]);
            $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["first_offer_status" => "Declined", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "Declined", "second_offer_update_at" => date("Y-m-d H:i:s"), "third_offer_status" => "Declined", "third_offer_update_at" => date("Y-m-d H:i:s")]);
            $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission->application_id)->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();
            Session::forget("contract_from_admin");
            return view("Offers::LateSubmission.decline_screen", compact("submission", "application_data", "msg"));
        } elseif (isset($request->decline_waitlist)) {
            $submission_id = $req['submission_id'];
            $program_id = $req['decline_waitlist'];


            $rs = Submissions::where("id", $submission_id)->update(["submission_status" => "Declined / Waitlist for other"]);

            $submission = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->select("late_submissions_final_status.*", "submissions.*")->first();
            $tmp = generateShortCode($submission);
            $tmp['offer_link'] = url('/Offers/' . $submission->offer_slug);
            $tmp['online_offer_last_date'] = getDateTimeFormat($last_online_date);
            $tmp['offline_offer_last_date'] = getDateTimeFormat($last_offline_date);
            $tmp['program_name'] = getProgramName($program_id);
            $tmp['program_name_with_grade'] = getProgramName($program_id) . " - Grade " . $submission->next_grade;

            $str = DistrictConfiguration::where("name", "late_submission_offer_waitlist_screen")->select("value")->first();
            $msg = $str->value;


            if ($submission->first_choice_final_status == "Offered" && $submission->first_waitlist_for == $program_id) {
                $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["first_offer_status" => "Declined & Waitlisted", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "Waitlisted"]);

                $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["first_offer_status" => "Declined & Waitlisted", "first_offer_update_at" => date("Y-m-d H:i:s"), "second_offer_status" => "Waitlisted"]);
                $program_name = getProgramName($submission->second_waitlist_for);
            } elseif ($submission->second_choice_final_status == "Offered" && $submission->second_waitlist_for == $program_id) {
                $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["second_offer_status" => "Declined & Waitlisted", "second_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "Waitlisted"]);
                $rs = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->update(["second_offer_status" => "Declined & Waitlisted", "second_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "Waitlisted"]);
                $program_name = getProgramName($submission->first_waitlist_for);
            } elseif ($submission->third_choice_final_status == "Offered" && $submission->third_waitlist_for == $program_id) {
                $rs = LateSubmissionFinalStatus::where("late_submissions_final_status.version", $version)->where("submission_id", $submission_id)->update(["third_offer_status" => "Declined & Waitlisted", "third_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "Waitlisted"]);
                $rs = DB::table("submissions_latest_final_status")->where("submission_id", $submission_id)->update(["third_offer_status" => "Declined & Waitlisted", "third_offer_update_at" => date("Y-m-d H:i:s"), "first_offer_status" => "Waitlisted"]);
                $program_name = getProgramName($submission->third_waitlist_for);
            }

            if ($submission->first_choice_final_status == "Offered")
                $second_program = getProgramName($submission->second_choice_program_id);
            else if ($submission->second_choice_final_status == "Offered")
                $second_program = getProgramName($submission->first_choice_program_id);
            else
                $second_program = getProgramName($submission->first_choice_program_id);

            $tmp['program_name'] = $second_program;
            $tmp['program_name_with_grade'] =  $tmp['waitlist_program_with_grade'] = $second_program . " - Grade " . $submission->next_grade;
            $msg = find_replace_string($msg, $tmp);



            $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.id', $submission->application_id)->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

            $commentObj = array();
            $commentObj['old_status'] = "Offered";
            $commentObj['new_status'] = "Declined / Waitlist for other";


            if (Session::has("contract_from_admin")) {
                $commentObj['comment'] = "Jefferson Admin has selected to be Waitlisted for " . $second_program . " - Grade " . $submission->next_grade;
                $commentObj['updated_by'] = Auth::user()->id;
            } else {
                $commentObj['updated_by'] = 0;
                $commentObj['comment'] = "Parent has selected to be Waitlisted for " . $second_program . " - Grade " . $submission->next_grade;
            }
            $commentObj['submission_id'] = $submission_id;
            SubmissionsStatusLog::create($commentObj);

            $submission = SubmissionsLatestFinalStatus::where("submission_id", $submission_id)->join("submissions", "submissions.id", "submissions_latest_final_status.submission_id")->select("submissions_latest_final_status.*", "submissions.*")->first();

            $this->sendOfferEmails($submission, $req, "offer_waitlisted");


            Session::forget("contract_from_admin");
            return view("Offers::wailist_screen", compact("submission", "application_data", "program_name", "msg"));
        }
    }

    public function contractOption($slug) {}

    public function onlineContract($slug) {}

    public function contractOptionStore(Request $request) {}

    public function finalizeContract(Request $request) {}

    public function sendOfferEmails($submission, $req, $mailType)
    {
        Session::put("district_id", "3");
        $district_id = 3; //Session::get("district_id");
        $subject_str = $mailType . "_mail_subject";
        $body_str = $mailType . "_mail_body";

        $subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', $subject_str)
            ->first();
        $body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', $body_str)
            ->first();

        if (!empty($body) && !empty($subject)) {
            $district_id = Session::get("district_id");
            $date = DistrictConfiguration::where("name", "last_date_waitlist_online_acceptance")->select("value")->first();
            $last_online_date = date("Y-m-d H:i:s", strtotime($date->value));

            $date = DistrictConfiguration::where("name", "last_date_waitlist_offline_acceptance")->select("value")->first();
            $last_offline_date = date("Y-m-d H:i:s", strtotime($date->value));


            $msg = $body->value;
            $subject = $subject->value;



            $tmp = generateShortCode($submission);
            $tmp['offer_link'] = url('/Offers/' . $submission->offer_slug);
            $tmp['online_offer_last_date'] = getDateTimeFormat($last_online_date);
            $tmp['offline_offer_last_date'] = getDateTimeFormat($last_offline_date);

            if ($submission->first_choice_final_status == "Offered") {
                $program_id = $submission->first_waitlist_for;
                $program_name = getProgramName($submission->first_waitlist_for);
            } elseif ($submission->second_choice_final_status == "Offered") {
                $program_id = $submission->second_waitlist_for;
                $program_name = getProgramName($submission->second_waitlist_for);
            } elseif ($submission->third_choice_final_status == "Offered") {
                $program_id = $submission->third_waitlist_for;
                $program_name = getProgramName($submission->third_waitlist_for);
            }

            $tmp['program_name_with_grade'] = $program_name . " - Grade " . $tmp['next_grade'];
            $tmp['offer_program_with_grade'] = $program_name . " - Grade " . $tmp['next_grade'];

            if ($submission->first_choice_final_status == "Offered" && $submission->second_choice_final_status == "Waitlisted" && $submission->first_offer_status == "Declined & Waitlisted" && $mailType == "offer_waitlisted") {

                $program_id = $submission->second_waitlist_for;
                $program_name = getProgramName($submission->second_waitlist_for);
            } else if ($submission->second_choice_final_status == "Offered" && $submission->first_choice_final_status == "Waitlisted" && $submission->second_offer_status == "Declined & Waitlisted"  && $mailType == "offer_waitlisted") {
                $program_id = $submission->first_waitlist_for;
                $program_name = getProgramName($submission->first_waitlist_for);
            } else if ($submission->third_choice_final_status == "Offered" && $submission->first_choice_final_status == "Waitlisted" && $submission->third_offer_status == "Declined & Waitlisted"  && $mailType == "offer_waitlisted") {
                $program_id = $submission->first_waitlist_for;
                $program_name = getProgramName($submission->first_waitlist_for);
            }

            $tmp['program_name'] = $program_name;
            $tmp['waitlist_program_with_grade'] = $program_name . " - Grade " . $tmp['next_grade'];


            $msg = find_replace_string($msg, $tmp);

            $subject = find_replace_string($subject, $tmp);

            $rs = LateSubmissionFinalStatus::where("submission_id", $req['submission_id'])->update(array("contract_mode" => "Offline"));

            $emailArr = array();
            $emailArr['email_text'] = $msg;
            $emailArr['subject'] = $subject;
            $emailArr['logo'] = getDistrictLogo();
            $emailArr['email'] = $submission->parent_email; //"mcpssparent@gmail.com";

            try {
                Mail::send('emails.index', ['data' => $emailArr], function ($message) use ($emailArr) {
                    $message->to($emailArr['email']);
                    $message->subject($emailArr['subject']);
                });
            } catch (\Exception $e) {
                // Get error here
                //echo 'Message: ' .$e->getMessage();exit;
            }
        }
    }

    public function autoDecline()
    {
        $rs = ProcessSelection::where("last_date_online_acceptance", "<", date("Y-m-d H:i:s"))->where('commited', 'Yes')->where("type", "late_submission")->where('auto_decline_cron', 'N')->get();

        foreach ($rs as $key => $value) {
            $submissions = Submissions::where('district_id', 3)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")->where("late_submissions_final_status.version", $value->version)->where('submission_status', 'Offered')->select('submissions.id', 'late_submissions_final_status.version', 'late_submissions_final_status.submission_id', 'submission_status')->get();
            foreach ($submissions as $sk => $sv) {
                $rs = Submissions::where("submission_status", "Offered")->where("id", $sv->submission_id)->update(array("submission_status" => "Offered and Declined"));
                $rs = LateSubmissionFinalStatus::where("submission_id", $sv->submission_id)->where("version", $sv->version)->update(array('first_offer_status' => 'Auto Decline', 'second_offer_status' => 'Auto Decline'));
                $rs = SubmissionsStatusLog::create(array("submission_id" => $sv->submission_id, "new_status" => "Offered and Declined", "old_status" => $sv->submission_status, "updated_by" => 0, "comment" => 'No response from user hence system has updated status to "Offered and Declined"'));
            }
        }
        echo "Done";
        exit;
    }
}
