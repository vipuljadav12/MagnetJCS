<?php

namespace App\Modules\SetEligibility\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Application\Models\Application;
use App\Modules\District\Models\District;
use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Program\Models\Program;
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\Program\Models\ProgramEligibilityLateSubmission;
use App\Modules\Priority\Models\Priority;
use App\Modules\SetEligibility\Models\{SetEligibility, SetEligibilityConfiguration, SetEligibilityLateSubmission};
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SetEligibilityController extends Controller
{
    use AuditTrail;
    protected $url;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->url = url("admin/SetEligibility");
        View::share(["module_url" => $this->url]);
    }
    public function index()
    {
        if (Session::get("district_id") != '0')
            $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        else
            $programs = Program::where('status', '!=', 'T')->where('enrollment_id', Session::get('enrollment_id'))->get();
        // return $programs;
        // return view("Program::index",compact('programs'));
        return view("SetEligibility::index", compact('programs'));
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
    public function edit($id, $application_id = 0)
    {
        $district = District::where('id', session('district_id'))->first();
        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $programeligibilities = ProgramEligibility::where('program_id', $id)->first();
        if (!empty($programeligibilities) && $application_id == 0) {
            $application_id = $programeligibilities->application_id;
        }

        if ($application_id == 0) {
            if (count($applications) > 0)
                $application_id = $applications[0]->id;
        }

        $priorities = Priority::where('district_id', session('district_id'))->where('status', '!=', 'T')->where('enrollment_id', Session::get('enrollment_id'))->get();
        $program = Program::where('id', $id)->first();
        $programeligibilities = ProgramEligibility::where('program_id', $id)->get();
        // return $programeligibilities;
        $eligibility_templates = EligibilityTemplate::all()->toArray();
        // $eligibility_templates[] = array("id"=>0,"name"=>"Template 2");
        // return $eligibility_templates;
        $eligibility_types = Eligibility::where('status', 'Y')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        $eligibilities = [];
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
        foreach ($eligibilities as $key => $eligibility) {
            foreach ($programeligibilities as $k => $programeligibility) {
                if ($programeligibility->eligibility_type == $eligibility['id']) {
                    $eligibilities[$key]['program_eligibility'] = $programeligibility;
                }
            }
        }
        // return $eligibilities;
        $setEligibility = SetEligibility::where('program_id', $id)->where("application_id", $application_id)->get()->keyBy('eligibility_type');
        // Zoned Schools
        // $zoned_schools = $this->getDistinctZonedSchools();
        $zoned_schools = app('\App\Modules\Program\Controllers\ProgramController')->getDistinctZonedSchools();

        return view('SetEligibility::edit', compact('program', 'eligibilities', 'priorities', 'district', 'setEligibility', 'applications', 'application_id', 'zoned_schools'));
    }

    public function extra_values(Request $req)
    {
        $table = 'seteligibility_extravalue';
        $application_id = $req['application_id'];
        $setEligibilitySingle  = SetEligibility::where("program_id", $req['program_id'])->where('eligibility_type', $req['eligibility_id'])->where("application_id", $application_id)->first();


        $eligibility = Eligibility::join('eligibility_content', 'eligibility_content.eligibility_id', '=', 'eligibiility.id')
            ->where('eligibiility.id', $req['eligibility_id'])
            ->select('eligibiility.*', 'eligibility_content.content')
            ->first();

        $eligibilityTemplate = EligibilityTemplate::where('id', $eligibility->template_id)->first();

        $extraValue = DB::table($table)->where('program_id', $req['program_id'])->where('application_id', $req['application_id'])->where('eligibility_type', $req['eligibility_type'])->first();

        // dd($extraValue);
        if (isset($extraValue->id)) {
            $extraValue = json_decode($extraValue->extra_values, 1);
        } else {
            $extraValue = null;
        }

        return view('SetEligibility::editExtra', compact('eligibility', 'eligibilityTemplate', 'setEligibilitySingle', 'req', 'extraValue', 'application_id'));
        // return view("SetEligibility::editExtra");
    }

    public function extra_value_save(Request $req)
    {
        $table = 'seteligibility_extravalue';
        $application_id = $req['application_id'];

        if (isset($req['value'])) {
            $insert = array(
                "program_id" => $req['program_id'],
                "application_id" => $req['application_id'],
                "eligibility_type" => $req["eligibility_type"],
                "extra_values" => json_encode($req['value'])
            );

            $checkExist = DB::table($table)->where('program_id', $req['program_id'])->where("application_id", $application_id)->where('eligibility_type', $req['eligibility_type'])->first();
            if (isset($checkExist->id)) {
                $result = DB::table($table)->where('program_id', $req['program_id'])->where('eligibility_type', $req['eligibility_type'])->where('application_id', $application_id)->update(["extra_values" => $insert["extra_values"]]);
            } else {
                $result = DB::table($table)->insert($insert);
            }

            if (isset($result)) {
                return "true";
            }
        } else {
            return "false";
        }
        return "false";
        return $req;
    }

    /* Configuration */
    public function configurations(Request $req)
    {
        $application_id = $req['application_id'];
        $table = 'seteligibility_extravalue';
        $setEligibilitySingle  = SetEligibility::where("program_id", $req['program_id'])->where("application_id", $req['application_id'])->where('eligibility_type', $req['eligibility_id'])->first();


        $eligibility = Eligibility::join('eligibility_content', 'eligibility_content.eligibility_id', '=', 'eligibiility.id')
            ->where('eligibiility.id', $req['eligibility_id'])
            ->select('eligibiility.*', 'eligibility_content.content')
            ->first();

        $eligibilityTemplate = EligibilityTemplate::where('id', $eligibility->template_id)->first();

        $extraValue = DB::table($table)->where("application_id", $req['application_id'])->where('program_id', $req['program_id'])->where('eligibility_type', $req['eligibility_type'])->first();


        $setEligibilitySingle = array();


        $eligibility = Eligibility::join('eligibility_content', 'eligibility_content.eligibility_id', '=', 'eligibiility.id')
            ->where('eligibiility.id', $req['eligibility_id'])
            ->select('eligibiility.*', 'eligibility_content.content')
            ->first();

        $eligibilityTemplate = EligibilityTemplate::where('id', $eligibility->template_id)->first();

        if (isset($extraValue->id)) {
            $extraValue = json_decode($extraValue->extra_values, 1);
        } else {
            $extraValue = null;
        }

        return view('SetEligibility::editConfiguration', compact('eligibility', 'eligibilityTemplate', 'setEligibilitySingle', 'req', 'extraValue', 'application_id'));
        // return view("SetEligibility::editExtra");
    }

    public function configurations_save(Request $req)
    {
        //return $req;
        $data = $req->all();
        $program_id = $data['program_id'];
        $eligibility_id = $data['eligibility_id'];
        $district_id = Session::get("district_id");
        $application_id = $data['application_id'];
        $eligibility_type = $data['eligibility_type'];

        foreach ($data as $key => $value) {
            if (!in_array($key, array("_token", "program_id", "eligibility_id", "eligibility_type", "late_submission", "application_id"))) {
                $insert = array();
                $insert['program_id'] = $program_id;
                $insert['eligibility_id'] = $eligibility_id;
                $insert['application_id'] = $application_id;
                $insert['district_id'] = $district_id;
                $insert['eligibility_type'] = $eligibility_type;
                $insert['configuration_type'] = $key;
                if (is_array($value))
                    $insert['configuration_value'] = implode(",", $value);
                else
                    $insert['configuration_value'] = $value;


                $rs = SetEligibilityConfiguration::updateOrCreate(["program_id" => $program_id, "eligibility_id" => $eligibility_id, "configuration_type" => $key, "eligibility_type" => $eligibility_type, "application_id" => $application_id], $insert);
            }
        }
        Session::flash("success", "Data saved successfully.");
        return redirect('admin/SetEligibility/edit/' . $program_id . "/" . $application_id);
        return "true";
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
        // return $request;
        $data = $request->except("_token");
        $application_id = $data['application_id'];
        $district_id = Session::get("district_id");

        if (isset($data['eligibility_type'])) {
            $newData = array();
            foreach ($data['eligibility_type'] as $k => $v) {
                $single['program_id'] = $id;
                $single['district_id'] = $district_id;
                $single['application_id'] = $application_id;
                $single['eligibility_type'] = $v;
                $single['eligibility_id'] = isset($data['eligibility_id'][$v]) ? $data['eligibility_id'][$v][0] : '';
                $single['required'] = isset($data['required'][$v]) ? $data['required'][$v][0] : '';
                $single['eligibility_value'] = isset($data['eligibility_value'][$v]) ? $data['eligibility_value'][$v][0] : '';
                $single['status'] = isset($data['status'][$v]) ? 'Y' : 'N';
                $newData[] = $single;
                $checkExist = SetEligibility::where('program_id', $id)->where('application_id', $application_id)->where("eligibility_id", $single['eligibility_id'])->first();
                if (isset($checkExist->id)) {
                    $initObj = SetEligibility::where('program_id', $id)->where('application_id', $application_id)->where("eligibility_id", $single['eligibility_id'])->first();
                    $result[] = SetEligibility::where('program_id', $id)->where('application_id', $application_id)->where("eligibility_id", $single['eligibility_id'])->update($single);
                    $newObj = SetEligibility::where('program_id', $id)->where('application_id', $application_id)->where("eligibility_id", $single['eligibility_id'])->first();

                    //$this->modelChanges($initObj,$newObj,"SetEligibility");
                } else {
                    $result[] = SetEligibility::create($single);
                }
            }
        }

        //print_r($data);exit;
        // For late submission


        if (isset($result)) {
            Session::flash("success", "Data saved successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/SetEligibility');
        } else {
            return redirect('admin/SetEligibility/edit/' . $id . "/" . $application_id);
        }
        if (isset($request->save_edit)) {
            return redirect('admin/SetEligibility/edit/' . $id . "/" . $application_id);
        }
        return redirect('admin/SetEligibility');
        // return $result;
        // return $newData;
        // return $data;
        // return $id;
        // return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
