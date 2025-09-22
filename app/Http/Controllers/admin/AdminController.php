<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Application\Models\ApplicationPragram;
use App\Modules\Application\Models\Application;
use App\Modules\Submissions\Models\{Submissions, SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus, LateSubmissionFinalStatus};
use App\Modules\ProcessSelection\Models\{ProgramSwingData, PreliminaryScore, ProcessSelection};
use App\Modules\Submissions\Models\SubmissionSteps;
use App\Modules\Program\Models\Program;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\SetAvailability\Models\Availability;
use App\User;
use App\Tenant;
use App\Modules\Waitlist\Models\{WaitlistController, WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

//use Session;

class AdminController extends Controller
{
    public $program;
    public $submission;
    public $user;
    public $enrollment;
    public $application;
    public $application_program;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->school = new School();
        // $this->form = new Form();
        $this->program = new Program();
        $this->submission = new Submissions();
        $this->user = new User();
        $this->enrollment = new Enrollment();
        $this->application = new Application();
        $this->application_program = new ApplicationProgram();
    }

    public function adminSubmission()
    {
        return view('layouts.admin.submission');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $district_id = Session('district_id');
        $applications_data = $this->application::where('district_id', $district_id)->where("starting_date", "<=", date("Y-m-d H:i:s"))->where("ending_date", ">=", date("Y-m-d H:i:s"))->where("enrollment_id", Session::get("enrollment_id"))->get();
        $application_arr = [];
        foreach ($applications_data as $key => $value) {
            $application_arr[] = $value->id;
        }

        $active_enrollments = $this->enrollment::where('district_id', $district_id)
            ->where('status', 'Y')
            ->where('id', Session::get("enrollment_id"))
            ->pluck('id');

        $data = [];
        if (!empty($active_enrollments)) {
            $applications = $this->application::whereIn('enrollment_id', $active_enrollments)
                ->get(['id', 'status']);

            if (!empty($applications)) {
                $submissions = $this->submission::whereIn('application_id', $application_arr)->get(['id', 'student_id', 'application_id', 'submission_status', 'calculated_race']);
                if (!empty($submissions)) {
                    // Total Number of Applicants Per Enrollment Period
                    $data['total_applicants_per_enrollment_period'] = $this->submission::whereIn('application_id', $applications->pluck('id'))->count();

                    $data['total_applicants_per_application_period'] = $submissions->count();


                    // Total Number of Current and Noncurrent Students Per Application Period
                    $data['total_noncurrent_students_per_application_period'] = $submissions->where('student_id', '')->whereIn("application_id", $application_arr)
                        ->count();
                    $data['total_current_students_per_application_period'] = $data['total_applicants_per_application_period'] - $data['total_noncurrent_students_per_application_period'];

                    // Submission Status
                    $data['active_submissions'] = $submissions->where('submission_status', 'Active')->count();
                    $data['pending_submissions'] = $submissions->where('submission_status', 'Pending')->count();

                    // Applicants by Race
                    $data['race'] = $submissions->groupBy('calculated_race')->map->count()->toArray();
                    $data['submission_status'] = $submissions->groupBy('submission_status')->map->count()->toArray();
                }
            }
        }
        $submissions = [];

        $all_choices = [];
        foreach (getChoiceAry() as $choicekey => $choicetxt) {
            ${$choicetxt . '_choice'} = $this->submission::where('district_id', $district_id)
                ->where('enrollment_id', Session::get("enrollment_id"))
                ->distinct($choicetxt . '_choice')
                ->where($choicetxt . '_choice', '!=', '')
                ->pluck($choicetxt . '_choice')
                ->toArray();
            $all_choices = array_merge($all_choices, ${$choicetxt . '_choice'});
        }
        $all_choices = array_unique($all_choices);

        $application_programs = $this->application_program::whereIn('application_programs.id', $all_choices)->join("program", "program.id", "application_programs.program_id")->where('program.enrollment_id', Session::get("enrollment_id"))->orderBy("program.id")->orderBy("program.name")->get(['application_programs.id', 'program_id']);

        // Group choice_id by program
        $submission_by_programs = [];
        if (!empty($application_programs)) {
            foreach ($application_programs as $key => $value) {
                if (!isset($submission_by_programs[$value->program_id])) {
                    $submission_by_programs[$value->program_id] = [];
                }
                array_push($submission_by_programs[$value->program_id], $value->id);
            }
        }
        $data['all_grades'] = DB::table('grade')->get();
        foreach (getChoiceAry() as $choicekey => $choicetxt) {
            $data[$choicetxt . '_choice_grade_count'] = $this->getGradeCount($choicetxt . '_choice', $submission_by_programs, $district_id);
        }

        // Number of Applicants Per Home Zone School
        $zoned_submissions = $this->submission::where('district_id', $district_id)->where('enrollment_id', Session::get("enrollment_id"))->get(['current_school', 'next_grade', 'student_id']);
        $data['zoned_school'] = [];
        foreach ($zoned_submissions as $key => $value) {
            // trim string after '(' bracket
            if ($value->student_id == '') {
                if (!isset($data['zoned_school']['Non HCS Schools'][$value->next_grade])) {
                    $data['zoned_school']['Non HCS Schools'][$value->next_grade] = 0;
                }
                $data['zoned_school']['Non HCS Schools'][$value->next_grade] = $data['zoned_school']['Non HCS Schools'][$value->next_grade] + 1;
            } elseif ($value->current_school != '') {
                $value->zoned_school = substr($value->current_school, 0, strpos($value->current_school, '('));
                if (isset($value->current_school) && isset($value->next_grade)) {
                    if (!isset($data['zoned_school'][$value->current_school][$value->next_grade])) {
                        $data['zoned_school'][$value->current_school][$value->next_grade] = 0;
                    }
                    $data['zoned_school'][$value->current_school][$value->next_grade] = $data['zoned_school'][$value->current_school][$value->next_grade] + 1;
                }
            }
        }

        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->distinct()->get(['form_id']);
        $group_racial_composition = [];
        foreach ($applications as $k => $v) {
            $group_racial_composition[] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updated_racial_composition($v->form_id, "desktop");
        }
        return view('layouts.admin.dashboard', compact('data', 'group_racial_composition'));
    }

    public function getShortCodeList()
    {
        $data = DB::table("short_code_list")->get();
        $arr = array();
        foreach ($data as $value) {
            $arr[] = $value->short_code_text;
        }
        return $arr;
    }

    public function getGradeCount($choice, $submission_by_programs, $district_id)
    {
        // Calculating grade data based on program
        $grade_count = [];
        foreach ($submission_by_programs as $program_id => $value) {
            $choice_submissions = $this->submission::where('district_id', $district_id)
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

    public function changedistrict($district_id)
    {
        $tenant = Tenant::where("id", $district_id)->first();
        if (!$tenant) {

            Session::put("district_id", "0");
            Session::put("theme_color", "#00346b");
        } else {
            Session::put("district_id", $tenant->id);
            Session::put("theme_color", $tenant->theme_color);
            date_default_timezone_set($tenant->district_timezone);
        }

        $enrollments = Enrollment::where('district_id', Session::get('district_id'))
            ->where('status', 'Y')
            ->where('begning_date', '<', date('Y-m-d'))
            ->where('ending_date', '>', date('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!isset($enrollments) && empty($enrollments)) {
            $enrollments = Enrollment::where('district_id', Session::get('district_id'))
                ->where('status', 'Y')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (isset($enrollments)) {
            Session::put('enrollment_id', $enrollments->id);
        }


        Session::save();
        echo "done";
    }


    public function changedEnrollment($enrollment_id)
    {
        $enrollments = Enrollment::where('id', $enrollment_id)->first();

        if (isset($enrollments)) {
            Session::put('enrollment_id', $enrollments->id);
        }

        echo "done";
    }
    /* Super Admin Offer Dashboard */



    /* Super Admin Offer Dashboard */
    public function superAdminOffer($version = 0)
    {
        $versions_lists = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("type", "waitlist")->orderBy("created_at", "desc")->get();

        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get('district_id'))->where("status", "Y")->orderBy("id")->get();
        $program_id = $programs->first()->id;
        $OfferedWaitlisted = $this->superOfferWaitlistedChart($version, $program_id, 1);
        $OfferedDeclined = $this->superOfferDeclinedChart($version, $program_id, 1);
        $OfferedAccepted = $this->superOfferAcceptedChart($version, $program_id, 1);
        return view('layouts.admin.dashboard_super_admin_offer', compact("programs", "OfferedWaitlisted", "OfferedDeclined", "OfferedAccepted", "program_id", "version", "versions_lists"));
    }

    public function superOfferAcceptedChart($version, $program_id, $return = 0)
    {
        /* Get Offered Count */

        if ($version  == 0) {
            $first_offer = SubmissionsFinalStatus::all()->where("enrollment_id", Session::get("enrollment_id"))->where('first_offer_status', 'Accepted')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortByDesc(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            // print_r($first_offer);exit;
            $second_offer = SubmissionsFinalStatus::all()->where("enrollment_id", Session::get("enrollment_id"))->where('second_offer_status', 'Accepted')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortByDesc(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        } else {
            $first_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('first_offer_status', 'Accepted')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortByDesc(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            // print_r($first_offer);exit;
            $second_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('second_offer_status', 'Accepted')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortByDesc(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        }
        $OfferedAccepted = array();
        foreach ($first_offer as $key => $value) {
            //echo $key. " - ".count($value)."<BR>";
            if ($version == 0) {

                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedAccepted[date("d", strtotime($key))])) {
                        $OfferedAccepted[date("d", strtotime($key))] = $OfferedAccepted[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedAccepted[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedAccepted[date("d", strtotime($key))])) {
                        $OfferedAccepted[date("d", strtotime($key))] = $OfferedAccepted[date("d", strtotime($key))] + cont($value);
                    } else {
                        $OfferedAccepted[date("d", strtotime($key))] = count($value);
                    }
                }
            }
        }

        foreach ($second_offer as $key => $value) {
            if ($version == 0) {
                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedAccepted[date("d", strtotime($key))])) {
                        $OfferedAccepted[date("d", strtotime($key))] = $OfferedAccepted[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedAccepted[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedAccepted[date("d", strtotime($key))])) {
                        $OfferedAccepted[date("d", strtotime($key))] = $OfferedAccepted[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedAccepted[date("d", strtotime($key))] = count($value);
                    }
                }
            }
        }


        if ($return == 0)
            return json_encode($OfferedAccepted);
        else
            return $OfferedAccepted;
    }
    public function superOfferDeclinedChart($version, $program_id, $return = 0)
    {

        /* Get Declined Offer Count */
        /* Get Offered Count */
        if ($version == 0) {
            $first_offer = SubmissionsFinalStatus::all()->where('first_offer_status', 'Declined')->where("enrollment_id", Session::get("enrollment_id"))->where('first_choice_final_status', 'Offered')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortByDesc(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            $second_offer = SubmissionsFinalStatus::all()->where('second_offer_status', 'Declined')->where("enrollment_id", Session::get("enrollment_id"))->where('second_choice_final_status', 'Offered')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortByDesc(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        } else {
            $first_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('first_offer_status', 'Declined')->where('first_choice_final_status', 'Offered')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortByDesc(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            $second_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('second_offer_status', 'Declined')->where('second_choice_final_status', 'Offered')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortByDesc(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        }
        $OfferedDeclined = array();
        foreach ($first_offer as $key => $value) {
            if ($version == 0) {

                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedDeclined[date("d", strtotime($key))])) {
                        $OfferedDeclined[date("d", strtotime($key))] = $OfferedDeclined[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedDeclined[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedDeclined[date("d", strtotime($key))])) {
                        $OfferedDeclined[date("d", strtotime($key))] = $OfferedDeclined[date("d", strtotime($key))] + 1;
                    } else {
                        $OfferedDeclined[date("d", strtotime($key))] = 1;
                    }
                }
            }
        }

        foreach ($second_offer as $key => $value) {
            if ($version == 0) {

                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedDeclined[date("d", strtotime($key))])) {
                        $OfferedDeclined[date("d", strtotime($key))] = $OfferedDeclined[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedDeclined[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedDeclined[date("d", strtotime($key))])) {
                        $OfferedDeclined[date("d", strtotime($key))] = $OfferedDeclined[date("d", strtotime($key))] + 1;
                    } else {
                        $OfferedDeclined[date("d", strtotime($key))] = 1;
                    }
                }
            }
        }
        if ($return == 0)
            return json_encode($OfferedDeclined);
        else
            return $OfferedDeclined;
    }
    public function superOfferWaitlistedChart($version, $program_id, $return = 0)
    {

        /* Get Waitlisted Offer Count */
        /* Get Offered Count */
        if ($version == 0) {
            $first_offer = SubmissionsFinalStatus::all()->where('first_offer_status', 'Declined & Waitlisted')->where("enrollment_id", Session::get("enrollment_id"))->where('first_choice_final_status', 'Offered')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortBy(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            $second_offer = SubmissionsFinalStatus::all()->where('second_offer_status', 'Declined & Waitlisted')->where("enrollment_id", Session::get("enrollment_id"))->where('second_choice_final_status', 'Offered')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortBy(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        } else {
            $first_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('first_offer_status', 'Declined & Waitlisted')->where('first_choice_final_status', 'Offered')->where('first_waitlist_for', $program_id)->where("first_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->first_offer_update_at));
            })
                ->sortBy(function ($item, $first_offer_update_at) {
                    return date('Y-m-d', strtotime($first_offer_update_at));
                });

            $second_offer = SubmissionsWaitlistFinalStatus::all()->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->where('second_offer_status', 'Declined & Waitlisted')->where('second_choice_final_status', 'Offered')->where('second_waitlist_for', $program_id)->where("second_offer_update_at", '<>', '')->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->second_offer_update_at));
            })
                ->sortBy(function ($item, $second_offer_update_at) {
                    return date('Y-m-d', strtotime($second_offer_update_at));
                });
        }

        $OfferedWaitlisted = array();
        foreach ($first_offer as $key => $value) {
            if ($version == 0) {

                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedWaitlisted[date("d", strtotime($key))])) {
                        $OfferedWaitlisted[date("d", strtotime($key))] = $OfferedWaitlisted[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedWaitlisted[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedWaitlisted[date("d", strtotime($key))])) {
                        $OfferedWaitlisted[date("d", strtotime($key))] = $OfferedWaitlisted[date("d", strtotime($key))] + 1;
                    } else {
                        $OfferedWaitlisted[date("d", strtotime($key))] = 1;
                    }
                }
            }
        }

        foreach ($second_offer as $key => $value) {
            if ($version == 0) {

                if (date("m", strtotime($key)) == 12) {
                    if (isset($OfferedWaitlisted[date("d", strtotime($key))])) {
                        $OfferedWaitlisted[date("d", strtotime($key))] = $OfferedWaitlisted[date("d", strtotime($key))] + count($value);
                    } else {
                        $OfferedWaitlisted[date("d", strtotime($key))] = count($value);
                    }
                }
            } else {
                if (date("M") == date("M", strtotime($key))) {
                    if (isset($OfferedWaitlisted[date("d", strtotime($key))])) {
                        $OfferedWaitlisted[date("d", strtotime($key))] = $OfferedWaitlisted[date("d", strtotime($key))] + 1;
                    } else {
                        $OfferedWaitlisted[date("d", strtotime($key))] = 1;
                    }
                }
            }
        }
        if ($return == 0)
            return json_encode($OfferedWaitlisted);
        else
            return $OfferedWaitlisted;
    }


    /* District Admin Offer Dashboard */
    public function districtAdminOffer()
    {
        /* Get Offered Count */
        $data = array();
        $rs = Submissions::where("enrollment_id", Session::get("enrollment_id"))->whereIn('submission_status', ['Offered', 'Auto Decline', 'Declined / Waitlist for Other', 'Offered and Declined'])->get()->count();

        $data['offer_count'] = $rs; // + $rs1 + $rs2;

        $rs = SubmissionsFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where(function ($q) {
            $q->where('first_choice_final_status', 'Waitlisted')
                ->where('second_choice_final_status', 'Waitlisted');
        })->orWhere(function ($q) {
            $q->where('first_choice_final_status', 'Waitlisted')
                ->where('second_choice_final_status', 'Pending');
        })->get()->count();
        $rs = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where(function ($q) {
            $q->where('submission_status', 'Waitlisted')
                ->orWhere('submission_status', 'Declined / Waitlist for other');
        })->get()->count();
        /*$rs1 = SubmissionsWaitlistFinalStatus::where(function($q) {
                $q->where('first_choice_final_status', 'Waitlisted')
                  ->where('second_choice_final_status', 'Waitlisted');
            })->orWhere(function($q) {
                $q->where('first_choice_final_status', 'Waitlisted')
                ->where('second_choice_final_status', 'Pending');
            })->get()->count();*/

        $data['waitlist_count'] = $rs;

        $rs = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("submission_status", "Offered")->get()->count();
        $data['outstanding_offer'] = $rs;

        $rs = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where('submission_status', 'Denied due to Ineligibility')->get()->count();
        $data['ineligibility_count'] = $rs;

        $rs = SubmissionsFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where('first_choice_final_status', 'Denied due to Incomplete Records')->get()->count();
        $data['incomplete_count'] = $rs;
        //dd($data);

        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get('district_id'))->where("status", "Y")->orderBy("id")->get();
        $program_id = $programs->first()->id;
        $arr = $this->getDistrictAdminOfferChart1($program_id);

        $arr1 = $this->getDistrictAdminOfferChart2($program_id);
        return view('layouts.admin.dashboard_district_admin_offer', compact("data", "arr", "programs", "program_id", "arr1"));
    }

    public function loadDynamicChart1($program_id)
    {
        echo json_encode($this->getDistrictAdminOfferChart1($program_id));
    }

    public function loadDynamicChart2($program_id)
    {
        echo json_encode($this->getDistrictAdminOfferChart2($program_id));
    }

    public function getDistrictAdminOfferChart1($program_id)
    {
        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $grade = Submissions::distinct()->where("enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
            $q->where('first_choice_program_id', $program_id)
                ->orWhere('second_choice_program_id', $program_id);
        })->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')->get(['next_grade'])->toArray();

        $data = array();
        $availableArr = $offerArr = $declineArr = $outstandingArr = array();
        $gradeArr = "";
        foreach ($grade as $key => $value) {
            $data[$key]['Grade'] = $value['next_grade'];
            $gradeArr .= '"' . $value['next_grade'] . '",';
        }

        foreach ($data as $key => $value) {
            $avail_grade = Availability::where("district_id", Session::get("district_id"))->where('grade', $value['Grade'])->where("program_id", $program_id)->first();
            if (!empty($avail_grade)) {
                $data[$key]['Available'] = $avail_grade->available_seats;
            } else {
                $data[$key]['Available'] = 0;
            }

            $avail_seats = WaitlistAvailabilityProcessLog::where("program_id", $program_id)->where("grade", $value['Grade'])->sum("withdrawn_seats");


            $availableArr[] = $data[$key]['Available'] + $avail_seats;
            $availableVal = $data[$key]['Available'];

            $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Pending')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();
            $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('second_offer_status', 'Pending')->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();


            $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('first_offer_status', 'Pending')->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();
            $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('second_offer_status', 'Pending')->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();

            $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('first_offer_status', 'Pending')->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();
            $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('second_offer_status', 'Pending')->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered')->where("next_grade", $value['Grade'])->get()->count();

            $OutstandingOffered = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;
            $outstandingArr[] = $OutstandingOffered;



            $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("first_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();
            $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("second_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();

            $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("first_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();
            $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("second_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();

            $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("first_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();
            $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->where("second_offer_status", "Accepted")->where('next_grade', $value['Grade'])->get()->count();

            $data[$key]['Accepted'] = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;

            $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
                ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for Other')->where('next_grade', $value['Grade'])->get()->count();
            $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
                ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for Other')->where('next_grade', $value['Grade'])->get()->count();
            $data[$key]['Declined'] = $rs1 + $rs2;
            $offeredAcceptedVal = $rs1 + $rs2;

            $offerArr[] = $data[$key]['Accepted'];

            // $outstandingArr[] = $availableVal - $data[$key]['Accepted'];
            //$declineArr[] = $data[$key]['Declined'];

        }
        $gradeArr = trim($gradeArr, ",");
        return array("grades" => $gradeArr, "availableArr" => $availableArr, "offerArr" => $offerArr, "outstandingArr" => $outstandingArr);
    }


    public function getDistrictAdminOfferChart2($program_id)
    {


        $avail_seats = Availability::where("district_id", Session::get("district_id"))->where("program_id", $program_id)->sum("available_seats");

        $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Pending')->where('submission_status', 'Offered')->get()->count();
        $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Pending')->where('submission_status', 'Offered')->get()->count();
        $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('first_offer_status', 'Pending')->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered')->get()->count();
        $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Pending')->where('submission_status', 'Offered')->get()->count();

        $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('first_offer_status', 'Pending')->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered')->get()->count();
        $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('second_offer_status', 'Pending')->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered')->get()->count();

        $OutstandingOffered = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;


        $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();
        $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();
        $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();
        $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();
        $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();
        $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Declined')->get()->count();

        $OfferedDeclined = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;


        $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();
        $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();
        $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();
        $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();
        $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();
        $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Declined / Waitlist for other')->get()->count();

        $OfferedWaitlisted = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;

        $rs1 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where("first_offer_status", "Accepted")->where('submission_status', 'Offered and Accepted')->get()->count();
        $rs2 = SubmissionsFinalStatus::join("submissions", "submissions.id", "submissions_final_status.submission_id")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where("second_offer_status", "Accepted")->where('submission_status', 'Offered and Accepted')->get()->count();
        $rs3 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where("first_offer_status", "Accepted")->where('submission_status', 'Offered and Accepted')->get()->count();
        $rs4 = SubmissionsWaitlistFinalStatus::join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->where("second_offer_status", "Accepted")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->get()->count();
        $rs5 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where("first_offer_status", "Accepted")->where('submissions.first_choice_program_id', $program_id)
            ->where('first_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->get()->count();
        $rs6 = LateSubmissionFinalStatus::join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->where("second_offer_status", "Accepted")->where('submissions.second_choice_program_id', $program_id)
            ->where('second_choice_final_status', 'Offered')->where('submission_status', 'Offered and Accepted')->get()->count();

        $OfferedAccepted = $rs1 + $rs2 + $rs3 + $rs4 + $rs5 + $rs6;

        //$OutstandingOffered = $avail_seats - $OfferedAccepted;

        return array("OutstandingOffered" => $OutstandingOffered, "OfferedDeclined" => $OfferedDeclined, "OfferedWaitlisted" => $OfferedWaitlisted, "OfferedAccepted" => $OfferedAccepted);
    }

    public function magnetDashboard()
    {
        if (Auth::user()->role_id != 1) {
            return redirect('admin/dashboard');
        }

        $enrollments = Enrollment::where('id', Session::get('enrollment_id'))->first();
        $ls_date = date('Y-m-d', strtotime($enrollments->begning_date));

        $late_submission = 'N';
        $ls_date_condition = '<';

        if (str_contains(request()->url(), '/late_submissions')) {
            $late_submission = 'Y';
            $ls_date_condition = '>=';
        }

        $submissions = Submissions::all()->where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))
            ->where('late_submission', $late_submission)
            ->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->created_at));
            })
            ->sortByDesc(function ($item, $created_at) {
                return date('Y-m-d', strtotime($created_at));
            });


        $student_applications1 = [];
        $no_zoned_school_found_addresses = [];
        $data_ary = [];

        if (!empty($submissions)) {
            foreach ($submissions as $created_at => $item) {
                $non_current_student_count = 0;
                $current_student_count = 0;
                foreach ($item as $submission) {
                    if ($submission->student_id != '') {
                        $current_student_count++;
                    } else {
                        $non_current_student_count++;
                    }
                }
                $student_applications1[] = [
                    'created_at' => $created_at,
                    'non_current_student_count' => $non_current_student_count,
                    'current_student_count' => $current_student_count,
                ];
            }
            $data_ary['student_applications1'] = $student_applications1;
        }

        $no_zoned_school_found = DB::table('no_zoned_school_found')->whereDate('created_at', ">", $ls_date)->get(['created_at']);
        // return $no_zoned_school_found;
        if (!empty($no_zoned_school_found)) {
            $no_zoned_school_found = collect($no_zoned_school_found)
                ->groupBy(function ($item) {
                    return date('Y-m-d', strtotime($item->created_at));
                })
                ->sortByDesc(function ($item, $created_at) {
                    return date('Y-m-d', strtotime($created_at));
                });

            $no_zoned_school_found_ary = [];
            foreach ($no_zoned_school_found as $key => $value) {
                // return count($value);
                $no_zoned_school_found_ary[] = [
                    'created_at' => $key,
                    'count' => count($value),
                ];
            }
            $data_ary['no_zoned_school_found_addresses'] = $no_zoned_school_found_ary;
            // $data_ary['no_zoned_school_found_addresses'] = array_keys($no_zoned_school_found->toArray()); 
        }
        $steps = SubmissionSteps::whereDate('created_at', ">", $ls_date)->get();

        $steps = $steps->groupBy(function ($item) {
            return date('Y-m-d', strtotime($item->created_at));
        })
            ->sortByDesc(function ($item, $created_at) {
                return date('Y-m-d', strtotime($created_at));
            });



        if (!empty($steps)) {
            $student_applications1 = [];
            foreach ($steps as $created_at => $item) {
                $step1 = $step2 = $step3 = $step4 = 0;
                foreach ($item as $submission) {
                    ${"step" . $submission->step_no}++;
                }
                $student_applications1[] = [
                    'created_at' => $created_at,
                    'step1' => $step1,
                    'step2' => $step2,
                    'step3' => $step3,
                    'step4' => $step4,
                ];
            }
            $data_ary['submission_steps'] = $student_applications1;
        }
        // Address Override
        $manual_address = DB::table('zone_address')->where('added_by', 'manual')
            ->whereDate('created_at', ">", $ls_date)
            ->get(['created_at']);
        // return $no_zoned_school_found;
        if (!empty($manual_address)) {
            $manual_address = collect($manual_address)
                ->groupBy(function ($item) {
                    return date('Y-m-d', strtotime($item->created_at));
                })
                ->sortByDesc(function ($item, $created_at) {
                    return date('Y-m-d', strtotime($created_at));
                });

            $manual_address_ary = [];
            foreach ($manual_address as $key => $value) {
                // return count($value);
                $manual_address_ary[] = [
                    'created_at' => $key,
                    'count' => count($value),
                ];
            }
            $data_ary['manual_address'] = $manual_address_ary;
            // $data_ary['no_zoned_school_found_addresses'] = array_keys($no_zoned_school_found->toArray()); 
        }


        return view('layouts.admin.dashboard_magnet', compact('data_ary', 'late_submission'));
    }

    public function loadAddresses(Request $request)
    {
        $req = $request::all();
        // return $req;
        $data_ary = [];
        if (isset($req['date'])) {
            $date = date('Y-m-d', strtotime($req['date']));
            $data = DB::table('no_zoned_school_found')->whereDate('created_at', $date)->get(['street_address', 'city', 'zip', 'created_at']);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $data_ary[] = [
                        ($value->street_address ?? '-'),
                        ($value->city ?? '-'),
                        ($value->zip ?? '-')
                    ];
                }
            }
        }
        return json_encode($data_ary);
    }


    public function loadOverrideAddresses(Request $request)
    {
        $req = $request::all();
        // return $req;
        $data_ary = [];
        if (isset($req['date'])) {
            $date = date('Y-m-d', strtotime($req['date']));
            $data = DB::table('zone_address')->where("added_by", "manual")->whereDate('created_at', $date)->get(['bldg_num', 'street_name', 'street_type', 'suffix_dir_full', 'city', 'zip', 'elementary_school', 'middle_school', 'intermediate_school', 'high_school', 'user_id']);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $data_ary[] = [
                        ($value->bldg_num ?? ''),
                        ($value->prefix_dir ?? ''),
                        ($value->street_name ?? ''),
                        ($value->street_type ?? ''),
                        ($value->unit_info ?? ''),
                        ($value->suffix_dir_full ?? ''),
                        ($value->city ?? '-'),
                        ($value->zip ?? '-'),
                        ($value->elementary_school ?? ''),
                        ($value->middle_school ?? ''),
                        ($value->intermediate_school ?? ''),
                        ($value->high_school ?? ''),
                        ($value->user_id != 0 ? getUserName($value->user_id) : '')

                    ];
                }
            }
        }
        return json_encode($data_ary);
    }
}
