<?php

namespace App\Modules\WritingPrompt\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\WritingPrompt\Models\WritingPrompt;
use App\Modules\WritingPrompt\Models\WritingPromptDetail;
use App\Modules\WritingPrompt\Models\WritingPromptConfig;
use App\Modules\Application\Models\Application;
use App\Modules\Submissions\Models\{Submissions, SubmissionData};
use App\Modules\SetEligibility\Models\SetEligibilityConfiguration;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use Illuminate\Support\Facades\DB;

class WritingPromptFrontController extends Controller
{
    public function create()
    {
        $eligibility_template = 'Writing Prompt';
        $req_url = request()->url();
        $data = $this->verifyLink($req_url);

        if (!empty($data)) {
            $submission_data = Submissions::where("id", $data['submission_id'])->first();
            if (!empty($submission_data)) {
                $application = Application::where("id", $submission_data->application_id)->first();
                $writing_prompt_due_date = $application->writing_prompt_due_date;
                if ($writing_prompt_due_date != '') {
                    $writing_prompt_due_date = date("Y-m-d H:i:s", strtotime($writing_prompt_due_date));
                    if (date("Y-m-d H:i:s") > $writing_prompt_due_date) {
                        return redirect("/WritingPrompt/expired/msg");
                    }
                }
            }
            $rs = WritingPrompt::where("submission_id", $data['submission_id'])->where("program_id", $data['program_id'])->first();
            if (!empty($rs)) {
                session()->forget('wp_user_data');
                return redirect("/msgs/writingprompt_success/" . $data['submission_id']);
            }
            // $wp_config = WritingPromptConfig::where('district_id', $data['submission']->district_id)->first(['duration']);
            $e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
            $duration = 0;
            $intro_txt = "";
            if (isset($e_temp->id)) {
                $email_config = SetEligibilityConfiguration::where('district_id', $data['submission']->district_id)
                    ->where('program_id', $data['program_id'])
                    ->where('eligibility_type', $e_temp->id)
                    //->where('configuration_type', 'prompt_timer')
                    ->get();
                foreach ($email_config as $key => $value) {
                    if ($value->configuration_type == "welcome_screen_text") {
                        $intro_txt = $value->configuration_value;
                    } elseif ($value->configuration_type == "prompt_timer") {
                        $duration = $value->configuration_value;
                    }
                }
                //$duration = $email_config->configuration_value ?? 0;
            }
            return view("WritingPrompt::front.index", compact('data', 'duration', 'intro_txt'));
        }
        abort(404);
    }

    public function exam(Request $request)
    {
        //echo $_SERVER['HTTP_REFERER']."<BR>";
        $eligibility_template = 'Writing Prompt';

        if (session('wp_user_data')) {
            $data = session('wp_user_data');

            // Store exam start time
            $find_wp = WritingPrompt::where('submission_id', $data['submission_id'])->where('program_id', $data['program_id'])->first();
            if (!isset($find_wp)) {
                // if(!isset($find_wp) || true){
                $data['start_time'] = date('Y-m-d H:i:s');
                $wp_create_data = $data;

                $submission = Submissions::where('id', $data['submission_id'])->first();
                // Prepare for exam
                $e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
                if (isset($e_temp->id)) {
                    $table = 'seteligibility_extravalue';
                    $extra = DB::table($table)->where('program_id', $data['program_id'])->where('eligibility_type', $e_temp->id)->first();
                    $wp_data = $extra->extra_values ?? '';
                    if ($wp_data != '') {
                        $wp_data = json_decode($wp_data, true);
                        if (!empty($wp_data['wp_question'])) {
                            $e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
                            $duration = 0;
                            $intro_txt = '';
                            if (isset($e_temp->id)) {
                                // Get email config value
                                $email_config = DB::table('set_eligibility_configuration')
                                    ->where('district_id', $submission->district_id)
                                    ->where('program_id', $data['program_id'])
                                    ->where('eligibility_type', $e_temp->id)
                                    ->get();
                                $tmp_txt = $email_config->where('configuration_type', 'welcome_screen_text')->first();
                                $intro_txt = $tmp_txt->configuration_value ?? '';
                                $tmp_duration = $email_config->where('configuration_type', 'prompt_timer')->first();
                                $duration = $tmp_duration->configuration_value ?? 0;
                            }
                            $data['intro_txt'] = $intro_txt;
                            $data['duration'] = $duration;

                            // $wp_config = WritingPromptConfig::where('district_id', $submission->district_id)->first(['duration', 'intro_txt']);
                            $data['submission'] = $submission;
                            // $data['wp_config'] = $wp_config;
                            $data['wp_question'] = $wp_data['wp_question'];

                            $create = WritingPrompt::create($wp_create_data);
                            if (isset($create)) {
                                return view("WritingPrompt::front.exam", compact('data'));
                            }
                        }
                    }
                }
            }
        }
        return redirect("/formsubmitted/error");
    }

    public function storeExam(Request $request)
    {
        if (session('wp_user_data')) {
            $data = session('wp_user_data');

            $wp = WritingPrompt::where('submission_id', $data['submission_id'])->where('program_id', $data['program_id'])->first();
            if (isset($wp) && $wp->end_time == '') {
                // if(isset($wp) && $wp->end_time == '' || true){
                $cur_timestamp = date('Y-m-d H:i:s');
                $wp->update(['end_time' => $cur_timestamp]);
                $insert_data = [];
                if (!empty($request->writing_prompt)) {
                    // $ts_val = date('Y-m-d H:i:s');
                    foreach ($request->writing_prompt as $key => $value) {
                        $insert_data[] = [
                            'wp_id' => $wp->id,
                            'writing_prompt' => $request->writing_prompt[$key],
                            'writing_sample' => $request->writing_sample[$key] ?? '',
                            'created_at' => $cur_timestamp,
                            'updated_at' => $cur_timestamp,
                        ];
                    }
                }
                WritingPromptDetail::insert($insert_data);
                $submission_id = $data['submission_id'];
                session()->forget('wp_user_data');
                // Session::flash('success', 'Your essay is submitted successfully.');
                return redirect("/msgs/writingprompt_success/" . $submission_id);

                //return redirect(url('/msgs/writingprompt_success'));
            }
        }
        return redirect("/formsubmitted/error");
    }

    public function submitted($submission_id)
    {
        $rs = Submissions::where("id", $submission_id)->select("confirmation_no")->first();
    }

    public function verifyLink($req_url = '')
    {
        // return [];
        // $req_url = request()->url();
        $url_segments = explode('/', $req_url);
        $key = end($url_segments);
        $key_segments = explode('.', $key);

        if (isset($key_segments[0]) && is_numeric($key_segments[0])) {
            $submission_id = $key_segments[0];

            $submission = Submissions::where('id', $submission_id)->first();
            // Verify submission
            if (isset($submission)) {
                if (isset($key_segments[1]) && is_numeric($key_segments[1])) {

                    $program_id = $key_segments[1];

                    if (isset($submission->first_choice_program_id) && $submission->first_choice_program_id == $program_id) {
                        $choice = 'first';
                    } else if (isset($submission->second_choice_program_id) && $submission->second_choice_program_id == $program_id) {
                        $choice = 'second';
                    }

                    if (isset($choice)) {
                        $submission_data = SubmissionData::where('submission_id', $submission_id)
                            ->where('config_name', 'wp_' . $choice . '_choice_link')
                            ->first();
                        $db_link = $submission_data->config_value ?? '';

                        // $submission_data = DB::table('submission_data')->where('submission_id', $submission_id)->first();       
                        // $db_link = $submission_data->{'wp_'.$choice.'_choice_link'} ?? '';

                        // Verify link
                        //echo $db_link . "==". str_replace(url('/')."/", "", $req_url);exit;
                        if (url('/WritingPrompt/') . "/" . $db_link == $req_url) {
                            $tmp_data = [
                                'submission_id' => $submission_id,
                                'program_id' => $program_id
                            ];
                            session()->put('wp_user_data', $tmp_data);
                            $tmp_data['submission'] = $submission;
                            return $tmp_data;
                        }
                    }
                }
            }
        }
        return [];
    }

    public function expired()
    {
        return view("WritingPrompt::front.expired");
    }

    public function previewFront($eligibility_id, $program_id, $application_id)
    {
        $eligibility_template = 'Writing Prompt';
        $table = 'seteligibility_extravalue';
        $data = [];

        $e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
        $submission = Submissions::where('first_choice_program_id', $program_id)->orWhere('second_choice_program_id', $program_id)->first();
        $wp_data = DB::table($table)->where('program_id', $program_id)->where('application_id', $application_id)->where('eligibility_type', $e_temp->id)->first()->extra_values;

        if ($wp_data != '') {
            $wp_data = json_decode($wp_data, true);
            if (!empty($wp_data['wp_question'])) {
                $intro_txt = '';
                if (isset($e_temp->id)) {
                    $email_config = DB::table('set_eligibility_configuration')
                        // ->where('district_id', $submission->district_id)
                        ->where('application_id', $application_id)
                        ->where('program_id', $program_id)
                        ->where('eligibility_type', $e_temp->id)
                        ->where('configuration_type', 'welcome_screen_text')->first();

                    $intro_txt = $email_config->configuration_value ?? '';
                }
                $data['intro_txt'] = $intro_txt;
                $data['program_id'] = $program_id;
                $data['submission'] = $submission;
                $data['wp_question'] = $wp_data['wp_question'];

                return view("WritingPrompt::front.writing_prompt_preview", compact('data'));
            }
        }
    }
}
