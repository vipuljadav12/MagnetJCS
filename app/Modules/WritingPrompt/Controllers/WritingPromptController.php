<?php

namespace App\Modules\WritingPrompt\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\WritingPrompt\Models\WritingPrompt;
use App\Modules\WritingPrompt\Models\WritingPromptLog;
use App\Modules\WritingPrompt\Models\WritingPromptDetail;
use App\Modules\WritingPrompt\Models\WritingPromptDetailLog;
use App\Modules\WritingPrompt\Models\WritingPromptConfig;
use App\Modules\Submissions\Models\Submissions;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WritingPromptController extends Controller
{
    public function index()
    {
        $data = WritingPromptConfig::where('district_id', Session::get('district_id'))->first();
        return view('WritingPrompt::index', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'duration' => 'required|numeric|digits_between:0,3',
            'intro_txt' => 'required',
            'mail_subject' => 'required|max:255',
            'mail_body' => 'required'
        ];
        $messages = [
            'duration.required' => 'Duration Time is required.',
            'duration.numeric' => 'Enter only digits.',
            'duration.digits_between' => 'No more than 3 digits.',
            'intro_txt.required' => 'Introductory Text is required.',
            'mail_subject.required' => 'Mail Subject is required.',
            'mail_subject.max' => 'Mail Subject may not be greater than 255 characters.',
            'mail_body.required' => 'Mail Body is required.'
        ];
        $validate = Validator::make($request->all(), $rules, $messages);

        if ($validate->fails()) {
            return redirect('admin/WritingPrompt')->withErrors($validate)->withInput();
        }

        $data = [
            'duration' => $request->duration,
            'intro_txt' => $request->intro_txt,
            'mail_subject' => $request->mail_subject,
            'mail_body' => $request->mail_body,
        ];
        $result = WritingPromptConfig::updateOrCreate(
            ['district_id' => Session::get('district_id')],
            $data
        );

        if (isset($result)) {
            Session::flash("success", "Data Updated successfully.");
        } else {
            Session::flash("warning", "Something went wrong , Please try again.");
        }
        return redirect('admin/WritingPrompt');
    }

    public function sendLinkMail(Request $request)
    {

        $eligibility_template = 'Writing Prompt';
        // $wp_config = WritingPromptConfig::where('district_id', Session::get('district_id'))->first();
        $choice = $request->choice;
        $submission_id = $request->submission_id;
        $submission = Submissions::where('id', $submission_id)->first();
        $program_id = $submission->{$choice . '_choice_program_id'};
        // Email data
        $e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
        $email_config = [];

        if (isset($e_temp->id)) {
            $email_config = DB::table('set_eligibility_configuration')
                ->where('district_id', session('district_id'))
                ->where('program_id', $program_id)
                ->where('eligibility_type', $e_temp->id)
                ->get();
        }

        if (!empty($email_config)) {
            $submission_data = DB::table('submission_data')
                ->where('submission_id', $submission_id)
                ->where('config_name', 'wp_' . $choice . '_choice_link')
                ->first();
            $link = $submission_data->config_value ?? '';

            if (($request->parent_email != '') && ($link != '')) {
                // $submission = Submissions::where('id', $submission_id)->first();
                $sortcode_values = getSortCodeValues($submission, $choice);

                $application_data = \App\Modules\Application\Models\Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where("application.status", "Y")->where("application.id", $submission->application_id)->select("application.*", "enrollments.school_year")->first();
                $logo = getDistrictLogo($application_data->display_logo) ?? '';

                $email_body = $email_config->where('configuration_type', 'email')->first();
                $msg_body = $email_body->configuration_value ?? '';
                $email_subject = $email_config->where('configuration_type', 'email_subject')->first();
                $sub = find_replace_string($email_subject->configuration_value ?? '', $sortcode_values);
                $sub = str_replace("{", "", $sub);
                $sub = str_replace("}", "", $sub);
                //$email_subject = $sub;

                $msg = find_replace_string($msg_body, $sortcode_values);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);

                $msg .= '<br> Writing Prompt Link: <a href="' . $link . '" target="_blank">' . $link . '</a><br>';

                // $data['user_id'] = \Auth::user()->id;
                $data['submission_id'] = $submission_id;
                $data['program_id'] = $program_id;
                // $data['district_id'] = session('district_id');
                $data['email_text'] = $data['email_body'] = $msg;
                $data['logo'] = $logo;
                // $data['link'] = $link;
                $data['email_to'] = $request->parent_email;
                $data['email_subject'] = $sub;
                $data['module'] = "Manual Resend Email";
                try {
                    Mail::send('emails.index', ['data' => $data], function ($message) use ($data) {
                        $message->to($data['email_to']);
                        $message->subject($data['email_subject']);
                    });
                    createEmailActivityLog($data);
                } catch (\Exception $e) {
                    return 'Mail not sent.';
                }
                return 'true';
            }
        }
        return 'false';
    }

    public function clear(Request $request)
    {
        if (isset($request->submission_id) && isset($request->program_id)) {
            $wp = WritingPrompt::where('submission_id', $request->submission_id)
                ->where('program_id', $request->program_id)
                ->first();
            if (isset($wp)) {
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
                    return 'true';
                }
            }
        }
        return 'false';
    }

    public function print($submission_id = 0, $program_id = 0)
    {
        if ($submission_id != 0 && $program_id != 0) {
            $wp = WritingPrompt::where('submission_id', $submission_id)
                ->where('program_id', $program_id)
                ->first();
            if (isset($wp)) {
                $submission = Submissions::where('id', $submission_id)->first();
                $program_name = getProgramName($program_id);
                $submission['program_name'] = $program_name;
                $wp_detail = WritingPromptDetail::where('wp_id', $wp->id)->get();
                if (!empty($wp_detail)) {
                    // view()->share('submission',$submission);
                    // view()->share('wp_detail',$wp_detail);
                    // ob_end_clean();
                    // ob_start();

                    // return view('WritingPrompt::writing_sample_print',['wp_detail'=>$wp_detail, 'submission'=>$submission]);

                    try {
                        $pdf = Pdf::loadView('WritingPrompt::writing_sample_print', ['wp_detail' => $wp_detail, 'submission' => $submission]);
                        return $pdf->download('WritingSample_' . $submission_id . '_' . $program_id . '.pdf');
                    } catch (\Exception $e) {
                        Session::flash("warning", "Something went wrong , Please try again.");
                        return redirect()->back();
                    }
                }
            }
        }
        // return 'false';
    }
}
