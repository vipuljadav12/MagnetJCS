<?php

namespace App\Modules\Program\Controllers;

use App\Modules\District\Models\District;
use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Program\Models\Program;
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\Program\Models\ProgramEligibilityLateSubmission;
use App\Modules\Priority\Models\Priority;
use App\Modules\Priority\Models\PriorityDetail;
use App\Modules\Form\Models\Form;
use App\Modules\Application\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\School;
use App\Traits\AuditTrail;
use App\Modules\Submissions\Models\RecommendationException;
use App\Modules\Submissions\Models\ProgramChoiceException;
use App\Modules\ZonedSchool\Models\ZonedSchool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProgramController extends Controller
{
    use AuditTrail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Session::get("district_id") != '0')
            $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        else
            $programs = Program::where('status', '!=', 'T')->where('enrollment_id', Session::get('enrollment_id'))->get();
        return view("Program::index", compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $forms = Form::where("district_id", Session::get("district_id"))->get();

        $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("status", "Y")->get();

        $priorities = Priority::where('district_id', session('district_id'))->where("enrollment_id", Session::get("enrollment_id"))->where('status', '!=', 'T')->get();
        $schools = School::where("district_id", Session::get("district_id"))->orderBy('name')->select(DB::raw("DISTINCT(name)"))->get();


        $eligibility_templates = EligibilityTemplate::all();
        $eligibility_types = Eligibility::where('status', 'Y')->get();

        $magnet_priority_count = PriorityDetail::where("magnet_student", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        $sibling_priority_count = PriorityDetail::where("sibling", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        $feeder_priority_count = PriorityDetail::where("feeder", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        $eligibilities = null;
        foreach ($eligibility_templates as $k => $eligibility_template) {
            $eligibility = null;
            foreach ($eligibility_types as $key => $eligibility_type) {
                if ($eligibility_template->id == $eligibility_type->template_id) {
                    $eligibility[] = $eligibility_type;
                }
            }
            if ($eligibility != null) {
                $eligibilities[] = array_merge($eligibility_template->toArray(), array('eligibility_types' => $eligibility));
            }
        }
        // Zoned Schools
        $zoned_schools = $this->getDistinctZonedSchools();

        return view('Program::create', compact('eligibilities', 'priorities', 'schools', 'forms', 'magnet_priority_count', 'sibling_priority_count', 'feeder_priority_count', "programs", "zoned_schools"));
    }

    public function getDistinctZonedSchools()
    {
        $zoned_schools = \App\Modules\School\Models\School::select('name')->get()->toArray();
        //dd($zoned_schools);
        /*$zoned_schools_model = ZonedSchool::where(function($qry) {
            $qry->where('elementary_school', '!=', '');
            $qry->where('middle_school', '!=', '');
            $qry->where('high_school', '!=', '');
        })->get();
        
        $elementary_zonedschools = $zoned_schools_model->pluck('elementary_school')->unique()->toArray();
        $middle_zonedschools = $zoned_schools_model->pluck('middle_school')->unique()->toArray();
        $high_zonedschools = $zoned_schools_model->pluck('high_school')->unique()->toArray();
        $zoned_schools = array_merge($elementary_zonedschools, $middle_zonedschools, $high_zonedschools);*/
        return $zoned_schools;
    }

    public function store(Request $request)
    {
        //        return $request;
        $msg = ['priority.required' => 'The priority field is required. ', 'grade_lavel.required' => 'The grade lavel field is required.', 'applicant_filter1.required' => 'The Applicant Group Filter 1 is required. '];
        $request->validate([
            'name' => 'required|max:255',
            // 'applicant_filter1'=>'required|max:255',
            'applicant_filter1' => 'max:255',
            'applicant_filter2' => 'max:255',
            'applicant_filter3' => 'max:255',
            'grade_lavel' => 'required',
            'parent_submission_form' => 'required',

        ], $msg);
        $currentdate = date("Y-m-d h:m:s", time());
        $priority = '';
        $grade_lavel = '';
        if (isset($request->priority)) {
            foreach ($request->priority as $key => $value) {
                if ($key == 0) {
                    $priority = $value;
                    continue;
                }
                $priority = $priority . ',' . $value;
            }
        }
        if (isset($request->grade_lavel)) {
            foreach ($request->grade_lavel as $key => $value) {
                if ($key == 0) {
                    $grade_lavel = $value;
                    continue;
                }
                $grade_lavel = $grade_lavel . ',' . $value;
            }
        }

        if (isset($request->feeder_priorities) && !empty($request->feeder_priorities)) {
            $feeder_priorities = implode(',', $request->feeder_priorities);
        } else {
            $feeder_priorities = "";
        }

        if (isset($request->magnet_priorities) && !empty($request->magnet_priorities)) {
            $magnet_priorities = implode(',', $request->magnet_priorities);
        } else {
            $magnet_priorities = "";
        }

        if (isset($request->sibling_schools) && !empty($request->sibling_schools)) {
            $sibling_schools = implode(',', $request->sibling_schools);
        } else {
            $sibling_schools = "";
        }

        /* Here code to save feeder field */
        $feeder_field = $request->feeder_field;
        if ($feeder_field == "upload") {
            $feeder_priorities = "";
            if (isset($request->upload_program_check) && !empty($request->upload_program_check)) {
                $upload_program_check = implode(",", $request->upload_program_check);
            } else {
                $upload_program_check = "";
            }
        } else {
            $upload_program_check = "";
        }

        if (isset($request->zoned_schools) && !empty($request->zoned_schools)) {
            $zoned_schools = implode(',', $request->zoned_schools);
        } else {
            $zoned_schools = "";
        }

        $programdata = [
            'district_id' => Session::get("district_id"),
            'name' => $request->name,
            'enrollment_id' => Session::get("enrollment_id"),
            'applicant_filter1' => $request->applicant_filter1,
            'applicant_filter2' => $request->applicant_filter2,
            'applicant_filter3' => $request->applicant_filter3,
            'grade_lavel' => $grade_lavel,
            'feeder_field' => $feeder_field,
            'upload_program_check' => $upload_program_check,
            'parent_submission_form' => $request->parent_submission_form,
            'magnet_school' => $request->magnet_school,
            'sibling_enabled' => $request->sibling_enabled == 'on' ? 'Y' : 'N',
            'silbling_check' => $request->silbling_check == 'on' ? 'Y' : 'N',
            'existing_magnet_program_alert' => $request->existing_magnet_program_alert == 'on' ? 'Y' : 'N',
            'priority' => $priority,
            'sibling_schools' => $sibling_schools,
            'feeder_priorities' => $feeder_priorities,
            'magnet_priorities' => $magnet_priorities,
            'zoned_schools' => $zoned_schools,
            'submission_status' => $request->submission_status,
            'created_at' => $currentdate,
            'updated_at' => $currentdate
        ];

        $programresult = Program::create($programdata);
        if (isset($programresult)) {
            $prog_data = Program::where('id', $programresult->id)->first();
            $this->modelCreate($prog_data, "program");
            Session::flash("success", "Program data added successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            //            return 'edit';
            return redirect('admin/Program/');
        }
        return redirect('admin/Program/edit/' . $programresult->id);
        //        return redirect('admin/Program');

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
    public function edit($id, $application_id = 0)
    {
        $req = request()->all();
        $exception_choice = '';
        if (isset($req['exception_choice'])) {
            $exception_choice = $req['exception_choice'];
        }

        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $programeligibilities = ProgramEligibility::where('program_id', $id)->first();
        if (!empty($programeligibilities)  && $application_id == 0) {
            $application_id = $programeligibilities->application_id;
        }
        if ($application_id == 0) {
            if (count($applications) > 0)
                $application_id = $applications[0]->id;
        }

        $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("status", "Y")->get();

        $forms = Form::where("district_id", Session::get("district_id"))->get();
        $district = District::where('id', session('district_id'))->first();

        $priorities = Priority::where('district_id', Session::get('district_id'))->where('status', '!=', 'T')->where("enrollment_id", Session::get("enrollment_id"))->get();
        $schools = School::where("district_id", Session::get("district_id"))->orderBy('name')->select(DB::raw("DISTINCT(name)"))->get();

        $magnet_priority_count = PriorityDetail::where("magnet_student", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        $sibling_priority_count = PriorityDetail::where("sibling", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        $feeder_priority_count = PriorityDetail::where("feeder", "Y")->join("priorities", "priorities.id", "priority_details.priority_id")->where("priorities.enrollment_id", Session::get("enrollment_id"))->where("priorities.status", "Y")->count();

        //$schools = DB::table("student")->select(DB::raw("DISTINCT(current_school)"))->get();
        //print_r($schools);exit;

        //return $priorities;
        $program = Program::where('id', $id)->first();



        $programeligibilities = ProgramEligibility::where('program_id', $id)->where("application_id", $application_id)->get();


        $eligibility_templates = EligibilityTemplate::all()->toArray();
        // $eligibility_templates[] = array("id"=>0,"name"=>"Template 2");
        // return $eligibility_templates;
        $eligibility_types = Eligibility::where('status', 'Y')->where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get("enrollment_id"))->get();
        $eligibilities = null;
        foreach ($eligibility_templates as $k => $eligibility_template) {
            $eligibility = null;
            foreach ($eligibility_types as $key => $eligibility_type) {
                if ($eligibility_template['id'] == $eligibility_type->template_id) {
                    $eligibility[] = $eligibility_type;
                }
                /*if($eligibility_type->template_id == 0){
                    $eligibility[]=$eligibility_type;
                }*/
            }
            if ($eligibility != null) {
                $eligibilities[] = array_merge($eligibility_template, array('eligibility_types' => $eligibility));
            }
        }

        $et = EligibilityTemplate::where('name', 'Recommendation Form')->where('status', 'Y')->first(['id']);
        $rec_form_data['status'] = false;

        if (!empty($eligibilities)) {
            foreach ($eligibilities as $key => $eligibility) {
                foreach ($programeligibilities as $k => $programeligibility) {
                    if ($programeligibility->eligibility_type == $eligibility['id']) {
                        // Check for recommendation form eligibility
                        if (isset($et->id)) {
                            if (($et->id == $programeligibility->eligibility_type) &&
                                ($programeligibility->assigned_eigibility_name != '')
                            ) {
                                $rec_form_data['eligibility_id'] = $programeligibility->assigned_eigibility_name;
                                $rec_form_data['status'] = true;
                            }
                        }
                        $eligibilities[$key]['program_eligibility'] = $programeligibility;
                    }
                }
            }
        } else
            $eligibilities = array();
        // return $eligibilities;

        // Exception
        $rec_form_data['data'] = [];
        if ($exception_choice == 'recommendation_form') {
            // Recommendation Form
            if ($rec_form_data['status'] == true) {
                $tmp_cnd = [
                    'program_id' => $id,
                    'eligibility_id' => $rec_form_data['eligibility_id']
                ];
                $rf_data = RecommendationException::where($tmp_cnd)->get();
                if (!empty($rf_data)) {
                    foreach ($rf_data as $value) {
                        $tmp_data = '';
                        if ($value->subject_teacher != '') {
                            $tmp_data = explode(',', $value->subject_teacher);
                        }
                        $rec_form_data['data'][$value->grade] = $tmp_data;
                    }
                }
            }
        } else if ($exception_choice == 'program_choice') {
            // Program Choice
            $rec_form_data['data'] = ProgramChoiceException::where('program_id', $id)->get(['grade', 'display_name']);
        }
        // Zoned Schools
        $zoned_schools = $this->getDistinctZonedSchools();

        return view('Program::edit', compact('program', 'eligibilities', 'priorities', 'district', 'schools', 'forms', 'rec_form_data', 'exception_choice', 'magnet_priority_count', 'sibling_priority_count', 'feeder_priority_count', 'programs', "applications", "application_id", "zoned_schools"));
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
        //        echo "T";exit;
        $msg = ['priority.required' => 'The priority field is required. ', 'grade_lavel.required' => 'The grade lavel field is required.', 'applicant_filter1.required' => 'The Applicant Group Filter 1 is required. '];
        $request->validate([
            'name' => 'required|max:255',
            // 'applicant_filter1'=>'required|max:255',
            'applicant_filter1' => 'max:255',
            'applicant_filter2' => 'max:255',
            'applicant_filter3' => 'max:255',
            'grade_lavel' => 'required',
            'parent_submission_form' => 'required',
        ], $msg);
        $currentdate = date("Y-m-d h:m:s", time());
        $priority = '';
        $grade_lavel = $exclude_grade_lavel = '';
        if (isset($request->priority)) {
            foreach ($request->priority as $key => $value) {
                if ($key == 0) {
                    $priority = $value;
                    continue;
                }
                $priority = $priority . ',' . $value;
            }
        }
        if (isset($request->grade_lavel)) {
            foreach ($request->grade_lavel as $key => $value) {
                if ($key == 0) {
                    $grade_lavel = $value;
                    continue;
                }
                $grade_lavel = $grade_lavel . ',' . $value;
            }
        }
        if (isset($request->exclude_grade_lavel) && $request->existing_magnet_program_alert == "on") {
            foreach ($request->exclude_grade_lavel as $key => $value) {
                if ($key == 0) {
                    $exclude_grade_lavel = $value;
                    continue;
                }
                $exclude_grade_lavel = $exclude_grade_lavel . ',' . $value;
            }
        }

        if (isset($request->feeder_priorities) && !empty($request->feeder_priorities)) {
            $feeder_priorities = implode(',', $request->feeder_priorities);
        } else {
            $feeder_priorities = "";
        }

        if (isset($request->sibling_schools) && !empty($request->sibling_schools)) {
            $sibling_schools = implode(',', $request->sibling_schools);
        } else {
            $sibling_schools = "";
        }

        if (isset($request->magnet_priorities) && !empty($request->magnet_priorities)) {
            $magnet_priorities = implode(',', $request->magnet_priorities);
        } else {
            $magnet_priorities = "";
        }

        $feeder_field = $request->feeder_field;
        if ($feeder_field == "upload") {
            $feeder_priorities = "";
            if (isset($request->upload_program_check) && !empty($request->upload_program_check)) {
                $upload_program_check = implode(",", $request->upload_program_check);
            } else {
                $upload_program_check = "";
            }
        } else {
            $upload_program_check = "";
        }

        if (isset($request->zoned_schools) && !empty($request->zoned_schools)) {
            $zoned_schools = implode(',', $request->zoned_schools);
        } else {
            $zoned_schools = "";
        }

        $data = [
            'name' => $request->name,
            'applicant_filter1' => $request->applicant_filter1,
            'applicant_filter2' => $request->applicant_filter2,
            'applicant_filter3' => $request->applicant_filter3,
            'grade_lavel' => $grade_lavel,
            'exclude_grade_lavel' => $exclude_grade_lavel,
            'parent_submission_form' => $request->parent_submission_form,
            'priority' => $priority,
            'feeder_field' => $feeder_field,
            'upload_program_check' => $upload_program_check,
            'current_over_new' => $request->current_over_new,
            'committee_score' => $request->committee_score,
            'audition_score' => $request->audition_score,
            'rating_priority' => $request->rating_priority,
            'combine_score' => $request->combine_score,
            'final_score' => $request->final_score,
            'lottery_number' => $request->lottery_number,
            'selection_method' => $request->selection_method,
            'selection_by' => $request->selection_by,
            'seat_availability_enter_by' => $request->seat_availability_enter_by,
            'sibling_enabled' => $request->sibling_enabled == 'on' ? 'Y' : 'N',
            'basic_method_only' => $request->basic_method_only == 'on' ? 'Y' : 'N',
            'basic_method_only_ls' => $request->basic_method_only_ls == 'on' ? 'Y' : 'N',
            'combined_scoring' => $request->combined_scoring == 'on' ? 'Y' : 'N',
            'combined_scoring_ls' => $request->combined_scoring_ls == 'on' ? 'Y' : 'N',
            'combined_eligibility' => $request->combined_eligibility,
            'combined_eligibility_ls' => $request->combined_eligibility_ls,
            'magnet_school' => $request->magnet_school,
            'created_at' => $currentdate,
            'silbling_check' => $request->silbling_check == 'on' ? 'Y' : 'N',
            'existing_magnet_program_alert' => $request->existing_magnet_program_alert == 'on' ? 'Y' : 'N',
            'sibling_schools' => $sibling_schools,
            'feeder_priorities' => $feeder_priorities,
            'magnet_priorities' => $magnet_priorities,
            'updated_at' => $currentdate,
            'zoned_schools' => $zoned_schools,
            'submission_status' => $request->submission_status,
        ];

        // return $data;
        // return $request->eligibility_type;
        $initObj = Program::where('id', $id)->first();
        $result = Program::where('id', $id)->update($data);
        $newObj = Program::where('id', $id)->first();

        $this->modelChanges($initObj, $newObj, "program");

        $application_id = $request->application_id;


        if (isset($request->eligibility_type)) {
            foreach ($request->eligibility_type as $key => $value) {
                if ($request->assigned_eigibility_name[$key] != '') {
                    $grade = null;
                    $eligibilitydata = [
                        'program_id' => $id,
                        'application_id' => $application_id,
                        'eligibility_type' => $value,
                        'determination_method' => $request->determination_method[$key],
                        'eligibility_define' => $request->eligibility_define[$key],
                        'assigned_eigibility_name' => $request->assigned_eigibility_name[$key],
                        'weight' => (isset($request->{"weight" . $value}) ? $request->{"weight" . $value} : ""),
                        'grade_lavel_or_recommendation_by' => '',
                        'status' => isset($request->status[$value][0]) && $request->status[$value][0] == 'on' ? 'Y' : 'N',
                    ];
                    $grade_lavel_or_recommendation_by = str_replace("-", ",", $request->grade_lavel_or_recommendation_by[$key]);
                    $grade_lavel_or_recommendation_by = trim($grade_lavel_or_recommendation_by, ",");
                    $eligibilitydata['grade_lavel_or_recommendation_by'] = $grade_lavel_or_recommendation_by;

                    $avilableeligibility = ProgramEligibility::where("application_id", $application_id)->where('program_id', $id)->where('eligibility_type', $value)->where('application_id', $application_id)->first();
                    if (isset($avilableeligibility)) {
                        $eligibilityresult = ProgramEligibility::where("application_id", $application_id)->where('program_id', $id)->where('eligibility_type', $value)->update($eligibilitydata);
                        $neweligibility = ProgramEligibility::where("application_id", $application_id)->where('program_id', $id)->where('eligibility_type', $value)->first();

                        $this->modelChanges($avilableeligibility, $neweligibility, "program-eligibility");
                    } else {
                        $eligibilityresult = ProgramEligibility::create($eligibilitydata);
                        $elig_data = ProgramEligibility::where('id', $eligibilityresult->id)->first();
                        $this->modelCreate($elig_data, "program-eligibility");
                    }
                } else {
                    $rs = ProgramEligibility::where("application_id", $application_id)->where("eligibility_type", $value)->where("program_id", $id)->get();

                    foreach ($rs as $rk => $rv) {
                        $rs1 = ProgramEligibility::where("id", $rv->id)->update(["status" => "N"]);
                    }
                }
            }
        }

        // return $request;
        // For late submission



        if (isset($result)) {
            Session::flash("success", "Program Updated successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            return redirect('admin/Program');
        }
        return redirect('admin/Program/edit/' . $id . '/' . $application_id);
    }

    public function delete($id)
    {
        $currentdate = date("Y-m-d h:m:s", time());
        $result = Program::where('id', $id)->update(['status' => 'T', 'updated_at' => $currentdate]);
        if (isset($result)) {
            Session::flash("success", "Program Data Move into Trash successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        return redirect('admin/Program');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        //
        $programs = Program::where('status', 'T')->get();
        return view('Program::trash', compact('programs'));
    }
    public function restore($id)
    {
        $currentdate = date("Y-m-d h:m:s", time());
        $result = Program::where('id', $id)->update(['status' => 'Y', 'updated_at' => $currentdate]);
        if (isset($result)) {
            Session::flash("success", "Program Data restore successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        return redirect('admin/Program');
    }
    public function status(Request $request)
    {
        $currentdate = date("Y-m-d h:m:s", time());
        //        return $request;
        $result = Program::where('id', $request->id)->update(['status' => $request->status, 'updated_at' => $currentdate]);
        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }


    public function storeRecommendationFormException(Request $request, $program_id)
    {
        $user_id = Auth::user()->id;
        // remove old data
        RecommendationException::where('user_id', $user_id)
            ->where('program_id', $program_id)
            ->delete();
        if (isset($request->extra['grade'])) {
            foreach ($request->extra['grade'] as $key => $grade) {
                $subject_teacher = NULL;
                if (isset($request->extra['rec_subj'][$grade])) {
                    if (!empty($request->extra['rec_subj'][$grade])) {
                        $subject_teacher = implode(',', $request->extra['rec_subj'][$grade]);
                    }
                }
                $data = [
                    'user_id' => $user_id,
                    'program_id' => $program_id,
                    'eligibility_id' => $request->assigned_eligibility_id,
                    'grade' => $grade,
                    'subject_teacher' => $subject_teacher
                ];
                RecommendationException::create($data);
            }
        }
        Session::flash("success", "Data Updated successfully.");
        return redirect()->back();
    }

    public function storeProgramChoiceException(Request $request, $program_id)
    {
        // return $request;
        // remove old data
        ProgramChoiceException::where('program_id', $program_id)->delete();
        if (isset($request->extra['grade']) && !empty($request->extra['grade'])) {
            foreach ($request->extra['grade'] as $grade) {
                $display_name = '';
                if (isset($request->extra['name'][$grade])) {
                    $display_name = $request->extra['name'][$grade];
                }
                $data = [
                    'program_id' => $program_id,
                    'grade' => $grade,
                    'display_name' => $display_name
                ];
                ProgramChoiceException::create($data);
            }
        }
        Session::flash("success", "Data Updated successfully.");
        return redirect()->back();
    }
}
