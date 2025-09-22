<?php

namespace App\Modules\CronManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Submissions\Models\Submissions;
use App\Modules\Submissions\Models\SubmissionData;
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuditionEmailCron extends Controller
{
	public function sendMail()
	{
		$err_msg = '';
		$choices = ['first', 'second'];
		$eligibility_template = 'Audition';

		$submissions = Submissions::where('audition_email', "N")->whereIn("submission_status", array("Active", "Pending"))->get();
		foreach ($submissions as $key => $submission) {
			foreach ($choices as $choice) {
				// return $submission;
				if (
					isset($submission->{$choice . '_choice_program_id'}) &&
					($submission->{$choice . '_choice_program_id'} != 0)
				) {
					$data['email_to'] = $submission->parent_email ?? '';
					// $data['user_id'] = \Auth::user()->id;
					// $data['district_id'] = session('district_id');
					$data['submission_id'] = $submission->id;
					$data['program_id'] = $submission->{$choice . '_choice_program_id'};

					$writeEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", $eligibility_template)->where("program_id", $data['program_id'])->where("application_id", $submission->application_id)->first();

					// Check eligibility
					if (!empty($writeEligibility)) {
						//						echo $data['program_id']."<Br>"
						// Get wp email config
						$e_temp = EligibilityTemplate::where('name', $eligibility_template)->first();
						$duration = 0;
						if (isset($e_temp->id)) {
							$wp_email_config = DB::table('set_eligibility_configuration')
								->where('district_id', session('district_id'))
								->where("application_id", $submission->application_id)
								->where('program_id', $data['program_id'])
								->where('eligibility_type', $e_temp->id)
								->get();

							if (!empty($wp_email_config)) {
								// get short code value
								$sortcode_values = getSortCodeValues($submission, $choice);
								// get logo
								$application_data = \App\Modules\Application\Models\Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where("application.status", "Y")->where("application.id", $submission->application_id)->select("application.*", "enrollments.school_year")->first();
								$data['logo'] = getDistrictLogo($application_data->display_logo) ?? '';
								// Process email body
								$email_body = $wp_email_config->where('configuration_type', 'email')->first()->configuration_value ?? '';

								$email_subject = $wp_email_config->where('configuration_type', 'email_subject')->first()->configuration_value ?? '';


								$msg = find_replace_string($email_body, $sortcode_values);

								$audition_date = $wp_email_config->where('configuration_type', 'audition_due_date')->first()->configuration_value ?? date("Y-m-d h:i A");
								$audition_date = date("m/d/Y h:i A", strtotime($audition_date));

								$msg = str_replace("{audition_due_date}", $audition_date, $msg);
								$msg = str_replace("{", "", $msg);
								$msg = str_replace("}", "", $msg);

								$sub = find_replace_string($email_subject, $sortcode_values);
								$sub = str_replace("{", "", $sub);
								$sub = str_replace("}", "", $sub);


								// Set data values
								$data['email_text'] = $data['email_body'] = $msg;
								$data['email_subject'] = $sub;

								try {
									Mail::send('emails.index', ['data' => $data], function ($message) use ($data) {
										$message->to($data['email_to']);
										$message->subject($data['email_subject']);
									});
									$data['status'] = 'success';
								} catch (\Exception $e) {
									$data['status'] = $e;
								}
							} else {
								$data['status'] = 'Email config not present.';
							}
							$data['module'] = "System";
							createEmailActivityLog($data);
						}
					} else {
						$data['status'] = 'Eligibility not available.';
					}
					// Create email activity log

				}
				Submissions::where("id", $submission->id)->update(array("audition_email" => "Y"));
			}
		}
	}
}
