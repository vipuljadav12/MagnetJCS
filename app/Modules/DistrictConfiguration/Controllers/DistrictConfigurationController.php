<?php

namespace App\Modules\DistrictConfiguration\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Application\Models\Application;
use App\Modules\Submissions\Models\{Submissions, SubmissionsStatusUniqueLog};
use Illuminate\Support\Facades\Session;

class DistrictConfigurationController extends Controller
{
    public function index()
    {
        // return date_default_timezone_get();
        $district_id = Session('district_id');
        $old_letter_signature = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'letter_signature')
            ->first();

        $old_email_signature = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'email_signature')
            ->first();

        $contract_accept_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'contract_accept_screen')
            ->first();

        /*$old_timezone = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'timezone')
                ->first(['value']);
        $old_timezone = ($old_timezone->value ?? '');
        */
        if (!empty($old_letter_signature)) {
            $old_letter_signature_value = $old_letter_signature->value;
        } else {
            $old_letter_signature_value = '';
        }

        if (!empty($old_email_signature)) {
            $old_email_signature_value = $old_email_signature->value;
        } else {
            $old_email_signature_value = '';
        }

        if (!empty($contract_accept_screen)) {
            $contract_accept_screen = $contract_accept_screen->value;
        } else {
            $contract_accept_screen = '';
        }

        return view("DistrictConfiguration::index", compact('old_email_signature_value', 'old_letter_signature_value', 'contract_accept_screen')); //, 'old_timezone'));
    }

    public function store(Request $request)
    {
        /*        $rules = [
            'letter_signature' => 'mimetypes:image/png,image/jpeg,image/gif|dimensions:max_width=500,max_height=500',
            'email_signature' => 'mimetypes:image/png,image/jpeg,image/gif|dimensions:max_width=500,max_height=500'
        ];
        $messages = [
            'letter_signature.required' => 'Letter Signature Image File is required.',
            'letter_signature.mimetypes' => 'Letter Signature Image File is the file of type .png/.jpg/.gif',
            'email_signature.required' => 'Email Signature Image File is required.',
            'email_signature.mimetypes' => 'Email Signature Image File is the file of type .png/.jpg/.gif',

        ];
        $this->validate($request, $rules, $messages);*/
        $district_id = \Session('district_id');

        // Signature
        if ($request->letter_signature != '') {

            $old_letter_signature = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'letter_signature')
                ->first();
            if (!empty($old_letter_signature)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'letter_signature',
                    'value' => $request->letter_signature,
                ];
                $result = $old_letter_signature->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'letter_signature',
                    'value' => $request->letter_signature,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if ($request->email_signature != '') {

            $old_email_signature = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'email_signature')
                ->first();
            if (!empty($old_email_signature)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'email_signature',
                    'value' => $request->email_signature,
                ];
                $result = $old_email_signature->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'email_signature',
                    'value' => $request->email_signature,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }


        if ($request->contract_accept_screen != '') {

            $contract_accept_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'contract_accept_screen')
                ->first();
            if (!empty($contract_accept_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'contract_accept_screen',
                    'value' => $request->contract_accept_screen,
                ];
                $result = $contract_accept_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'contract_accept_screen',
                    'value' => $request->contract_accept_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }
        // Timezone
        /*$old_timezone = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'timezone')
            ->first();
        if (isset($request->timezone)) {
            $data = [
                'district_id' => $district_id,
                'name' => 'timezone',
                'value' => $request->timezone,
            ];
            if (!empty($old_timezone)) {
                $result = $old_timezone->update($data);
            }else{
                $result = DistrictConfiguration::create($data);
            }
        }elseif(!empty($old_timezone)) {
            $old_timezone->delete();
        }
        changeTimezone();*/

        Session::flash("success", "Configuration updated successfully.");
        return redirect('admin/DistrictConfiguration');
    }

    public function searchIndex()
    {
        return view("DistrictConfiguration::student_search");
    }

    /* Late Submission Email Part */


    public function edit_late_submission_email()
    {
        $district_id = Session('district_id');

        $offer_accepted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_accepted_mail_subject')
            ->first();
        $offer_accepted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_accepted_mail_body')
            ->first();


        $offer_declined_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_declined_mail_subject')
            ->first();
        $offer_declined_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_declined_mail_body')
            ->first();

        $offer_waitlisted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_waitlisted_mail_subject')
            ->first();
        $offer_waitlisted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_waitlisted_mail_body')
            ->first();


        $contract_signed_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_contract_signed_mail_subject')
            ->first();
        $contract_signed_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_contract_signed_mail_body')
            ->first();
        return view("DistrictConfiguration::edit_late_submission_thanks_emails", compact('offer_accepted_mail_subject', 'offer_accepted_mail_body', 'offer_declined_mail_subject', 'offer_declined_mail_body', 'offer_waitlisted_mail_subject', 'offer_waitlisted_mail_body', 'contract_signed_mail_subject', 'contract_signed_mail_body')); //, 'old_timezone')); 

    }

    public function saveLateSubmissionEditEmail(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        $arr = array("late_submission_offer_accepted", "late_submission_offer_declined", "late_submission_offer_waitlisted", "late_submission_contract_signed");

        foreach ($arr as $key => $value) {
            $str = $value . '_mail_subject';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }

            $str = $value . '_mail_body';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }
        }
        Session::flash("success", "Email Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_late_submission_email');
    }

    public function previewLateSubmissionThanksEmail($slug)
    {
        $district_id = Session::get('district_id');
        if ($slug == "late_submission_offer_accepted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_accepted_mail_body')
                ->first();
        } elseif ($slug == "late_submission_offer_declined") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_declined_mail_body')
                ->first();
        } elseif ($slug == "late_submission_offer_waitlisted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_waitlisted_mail_body')
                ->first();
        } elseif ($slug == "late_submission_contract_signed") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_contract_signed_mail_body')
                ->first();
        } else {
            $data = array();
        }

        if (!empty($data)) {
            $tmp = array();
            $tmp['email_text'] = $data->value;
            $tmp['logo'] = getDistrictLogo();
            $data = $tmp;

            return view("emails.preview_thanks_index", compact('data', 'slug'));
        }
    }


    /* Waitlist Email Part */


    public function edit_waitlist_email()
    {
        $district_id = Session('district_id');

        $offer_accepted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_accepted_mail_subject')
            ->first();
        $offer_accepted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_accepted_mail_body')
            ->first();


        $offer_declined_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_declined_mail_subject')
            ->first();
        $offer_declined_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_declined_mail_body')
            ->first();

        $offer_waitlisted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_waitlisted_mail_subject')
            ->first();
        $offer_waitlisted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_waitlisted_mail_body')
            ->first();


        $contract_signed_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_contract_signed_mail_subject')
            ->first();
        $contract_signed_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_contract_signed_mail_body')
            ->first();
        return view("DistrictConfiguration::edit_waitlist_thanks_emails", compact('offer_accepted_mail_subject', 'offer_accepted_mail_body', 'offer_declined_mail_subject', 'offer_declined_mail_body', 'offer_waitlisted_mail_subject', 'offer_waitlisted_mail_body', 'contract_signed_mail_subject', 'contract_signed_mail_body')); //, 'old_timezone')); 

    }

    public function saveWaitlistEditEmail(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        $arr = array("waitlist_offer_accepted", "waitlist_offer_declined", "waitlist_offer_waitlisted", "waitlist_contract_signed");

        foreach ($arr as $key => $value) {
            $str = $value . '_mail_subject';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }

            $str = $value . '_mail_body';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }
        }
        Session::flash("success", "Email Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_waitlist_email');
    }

    public function previewWaitlistThanksEmail($slug)
    {
        $district_id = Session::get('district_id');
        if ($slug == "offer_accepted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_accepted_mail_body')
                ->first();
        } elseif ($slug == "offer_declined") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_declinedmail_body')
                ->first();
        } elseif ($slug == "offer_waitlisted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_waitlisted_mail_body')
                ->first();
        } else {
            $data = array();
        }

        if (!empty($data)) {
            $tmp = array();
            $tmp['email_text'] = $data->value;
            $tmp['logo'] = getDistrictLogo();
            $data = $tmp;

            return view("emails.preview_thanks_index", compact('data'));
        }
    }

    /* Normal Email Part */

    public function edit_email()
    {
        $district_id = Session('district_id');

        $offer_accepted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_accepted_mail_subject')
            ->first();
        $offer_accepted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_accepted_mail_body')
            ->first();


        $offer_declined_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_declined_mail_subject')
            ->first();
        $offer_declined_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_declined_mail_body')
            ->first();

        $offer_waitlisted_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_waitlisted_mail_subject')
            ->first();
        $offer_waitlisted_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_waitlisted_mail_body')
            ->first();


        $contract_signed_mail_subject = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'contract_signed_mail_subject')
            ->first();
        $contract_signed_mail_body = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'contract_signed_mail_body')
            ->first();
        return view("DistrictConfiguration::edit_thanks_emails", compact('offer_accepted_mail_subject', 'offer_accepted_mail_body', 'offer_declined_mail_subject', 'offer_declined_mail_body', 'offer_waitlisted_mail_subject', 'offer_waitlisted_mail_body', 'contract_signed_mail_subject', 'contract_signed_mail_body')); //, 'old_timezone')); 

    }

    public function saveEditEmail(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        $arr = array("offer_accepted", "offer_declined", "offer_waitlisted", "contract_signed");

        foreach ($arr as $key => $value) {
            $str = $value . '_mail_subject';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }

            $str = $value . '_mail_body';
            if (isset($req[$str])) {
                $rs = DistrictConfiguration::where('district_id', $district_id)
                    ->where('name', $str)
                    ->first();
                if (!empty($rs)) {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = $rs->update($data);
                } else {
                    $data = [
                        'district_id' => $district_id,
                        'name' => $str,
                        'value' => $request->{$str},
                    ];
                    $result = DistrictConfiguration::create($data);
                }
            }
        }
        Session::flash("success", "Email Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_email');
    }

    public function previewThanksEmail($slug)
    {
        $district_id = Session::get('district_id');
        if ($slug == "offer_accepted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_accepted_mail_body')
                ->first();
        } elseif ($slug == "offer_declined") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_declined_mail_body')
                ->first();
        } elseif ($slug == "offer_waitlisted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_waitlisted_mail_body')
                ->first();
        } elseif ($slug == "waitlist_offer_accepted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_accepted_mail_body')
                ->first();
        } elseif ($slug == "waitlist_offer_declined") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_declined_mail_body')
                ->first();
        } elseif ($slug == "waitlist_offer_waitlisted") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_waitlisted_mail_body')
                ->first();
        } elseif ($slug == "waitlist_contract_signed") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_contract_signed_mail_body')
                ->first();
        } elseif ($slug == "contract_signed") {
            $data =  DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'contract_signed_mail_body')
                ->first();
        } else {
            $data = array();
        }

        if (!empty($data)) {
            $tmp = array();
            $tmp['email_text'] = $data->value;
            $tmp['logo'] = getDistrictLogo();
            $data = $tmp;

            return view("emails.preview_thanks_index", compact('data', 'slug', 'type'));
        }
    }

    /* Waitlist Screen Text */
    public function edit_waitlist_text()
    {
        $district_id = Session('district_id');

        $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_accept_screen')
            ->first();

        $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_contract_option_screen')
            ->first();

        $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_confirmation_screen')
            ->first();

        $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_waitlist_screen')
            ->first();

        $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'waitlist_offer_declined_screen')
            ->first();

        return view("DistrictConfiguration::edit_waitlist_text", compact('offer_accept_screen', 'contract_option_screen', 'offer_confirmation_screen', 'offer_waitlist_screen', 'offer_declined_screen')); //, 'old_timezone')); 
    }

    public function saveWaitlistEditText(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        if (isset($req['offer_accept_screen']) && $req['offer_accept_screen'] != '') {
            $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_accept_screen')
                ->first();
            if (!empty($offer_accept_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = $offer_accept_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['contract_option_screen']) && $req['contract_option_screen'] != '') {
            $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_contract_option_screen')
                ->first();
            if (!empty($contract_option_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = $contract_option_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_confirmation_screen']) && $req['offer_confirmation_screen'] != '') {
            $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_confirmation_screen')
                ->first();
            if (!empty($offer_confirmation_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = $offer_confirmation_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_waitlist_screen']) && $req['offer_waitlist_screen'] != '') {
            $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_waitlist_screen')
                ->first();
            if (!empty($offer_waitlist_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = $offer_waitlist_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_declined_screen']) && $req['offer_declined_screen'] != '') {
            $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'waitlist_offer_declined_screen')
                ->first();
            if (!empty($offer_declined_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = $offer_declined_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'waitlist_offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }
        Session::flash("success", "Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_waitlist_text');
    }

    /* Late Submission Screen Text */
    public function edit_late_submission_text()
    {
        $district_id = Session('district_id');

        $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_accept_screen')
            ->first();

        $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_contract_option_screen')
            ->first();

        $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_confirmation_screen')
            ->first();

        $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_waitlist_screen')
            ->first();

        $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'late_submission_offer_declined_screen')
            ->first();

        return view("DistrictConfiguration::edit_late_submission_text", compact('offer_accept_screen', 'contract_option_screen', 'offer_confirmation_screen', 'offer_waitlist_screen', 'offer_declined_screen')); //, 'old_timezone')); 
    }

    public function saveSubmissionEditText(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        if (isset($req['offer_accept_screen']) && $req['offer_accept_screen'] != '') {
            $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_accept_screen')
                ->first();
            if (!empty($offer_accept_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = $offer_accept_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['contract_option_screen']) && $req['contract_option_screen'] != '') {
            $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_contract_option_screen')
                ->first();
            if (!empty($contract_option_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = $contract_option_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_confirmation_screen']) && $req['offer_confirmation_screen'] != '') {
            $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_confirmation_screen')
                ->first();
            if (!empty($offer_confirmation_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = $offer_confirmation_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_waitlist_screen']) && $req['offer_waitlist_screen'] != '') {
            $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_waitlist_screen')
                ->first();
            if (!empty($offer_waitlist_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = $offer_waitlist_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_declined_screen']) && $req['offer_declined_screen'] != '') {
            $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'late_submission_offer_declined_screen')
                ->first();
            if (!empty($offer_declined_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = $offer_declined_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'late_submission_offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }
        Session::flash("success", "Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_late_submission_text');
    }

    /* Regular Screen Text */
    public function edit_text()
    {
        $district_id = Session('district_id');

        $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_accept_screen')
            ->first();

        $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'contract_option_screen')
            ->first();

        $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_confirmation_screen')
            ->first();

        $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_waitlist_screen')
            ->first();

        $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
            ->where('name', 'offer_declined_screen')
            ->first();

        return view("DistrictConfiguration::edit_text", compact('offer_accept_screen', 'contract_option_screen', 'offer_confirmation_screen', 'offer_waitlist_screen', 'offer_declined_screen')); //, 'old_timezone')); 
    }

    public function saveEditText(Request $request)
    {
        $district_id = Session::get("district_id");
        $req = $request->all();

        if (isset($req['offer_accept_screen']) && $req['offer_accept_screen'] != '') {
            $offer_accept_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_accept_screen')
                ->first();
            if (!empty($offer_accept_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = $offer_accept_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_accept_screen',
                    'value' => $request->offer_accept_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['contract_option_screen']) && $req['contract_option_screen'] != '') {
            $contract_option_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'contract_option_screen')
                ->first();
            if (!empty($contract_option_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = $contract_option_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'contract_option_screen',
                    'value' => $request->contract_option_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_confirmation_screen']) && $req['offer_confirmation_screen'] != '') {
            $offer_confirmation_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_confirmation_screen')
                ->first();
            if (!empty($offer_confirmation_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = $offer_confirmation_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_confirmation_screen',
                    'value' => $request->offer_confirmation_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_waitlist_screen']) && $req['offer_waitlist_screen'] != '') {
            $offer_waitlist_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_waitlist_screen')
                ->first();
            if (!empty($offer_waitlist_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = $offer_waitlist_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_waitlist_screen',
                    'value' => $request->offer_waitlist_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }

        if (isset($req['offer_declined_screen']) && $req['offer_declined_screen'] != '') {
            $offer_declined_screen = DistrictConfiguration::where('district_id', $district_id)
                ->where('name', 'offer_declined_screen')
                ->first();
            if (!empty($offer_declined_screen)) {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = $offer_declined_screen->update($data);
            } else {
                $data = [
                    'district_id' => $district_id,
                    'name' => 'offer_declined_screen',
                    'value' => $request->offer_declined_screen,
                ];
                $result = DistrictConfiguration::create($data);
            }
        }
        Session::flash("success", "Text updated successfully.");
        return redirect('admin/DistrictConfiguration/edit_text');
    }

    public function sendTestMail(Request $request)
    {
        $req = $request->all();
        $email = $req['email'];
        $slug = $status = $req['status'];
        $district_id = 3;

        $sdata =  DistrictConfiguration::where('district_id', $district_id)
            ->where('name', $slug . "_mail_subject")
            ->first();

        $cdata =  DistrictConfiguration::where('district_id', $district_id)
            ->where('name', $slug . "_mail_body")
            ->first();

        if ($slug == "contract_signed" || $slug == "waitlist_contract_signed" || $slug == "late_submission_contract_signed") {
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

            $msg = find_replace_string($cdata->value, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['msg'] = $msg;

            $msg = find_replace_string($sdata->value, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['subject'] = $msg;

            $tmp['email'] = $email;
            sendMail($tmp);
            echo "Done";
            exit;
        }

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_waitlist_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_waitlist_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);

        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();



        if ($slug == "offer_accepted" || $slug == "waitlist_offer_accepted" || $slug == "late_submission_offer_accepted") {
            $status = "Offered and Accepted";
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", "Offered and Accepted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status']);
        } elseif ($slug == "waitlist_offer_declined" || $slug == "offer_declined" || $slug == "late_submission_offer_accepted") {
            $status = "Offered and Declined";
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", "Offered and Declined")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->orderBy("next_grade")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } elseif ($slug == "offer_waitlisted" || $slug == "waitlist_offer_waitlisted" || $slug == "late_submission_offer_accepted") {
            $status = "Declined / Waitlist for other";
            $submissions = Submissions::where('district_id', $district_id)->where("submission_status", "Waitlisted")
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        } else {
            $submissions = Submissions::where('district_id', $district_id)
                ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                ->get(['submissions.*', 'first_offered_rank', 'second_offered_rank', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status', 'offer_slug']);
        }
        $countMail = 0;

        foreach ($submissions as $key => $value) {
            if ($countMail == 0) {
                $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

                $generated = false;
                if ($value->submission_status == $status) {
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
                    $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;


                    $msg = find_replace_string($cdata->value, $tmp);
                    $msg = str_replace("{", "", $msg);
                    $msg = str_replace("}", "", $msg);
                    $tmp['msg'] = $msg;

                    $msg = find_replace_string($sdata->value, $tmp);
                    $msg = str_replace("{", "", $msg);
                    $msg = str_replace("}", "", $msg);
                    $tmp['subject'] = $msg;

                    $tmp['email'] = $email;
                    $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);
                    if ($countMail == 0) {
                        sendMail($tmp);
                    }
                }
                $countMail++;
            }
        }
        echo "done";
    }
}
