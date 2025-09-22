<?php

namespace App\Modules\Enrollment\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\ADMData;
use App\Modules\School\Models\School;
use App\Traits\AuditTrail;
use App\Modules\Priority\Models\Priority;
use App\Modules\Priority\Models\PriorityDetail;
use App\Modules\Program\Models\{Program, ProgramEligibility};
use App\Modules\Application\Models\{Application, ApplicationProgram, ApplicationConfiguration};
use App\Modules\SetEligibility\Models\{SetEligibility, SetEligibilityConfiguration, SetEligibilityExtraValue};
use App\Modules\Eligibility\Models\{Eligibility, EligibilityContent};
use App\Modules\Enrollment\Excel\ADMDataImport;
use App\Modules\Enrollment\Excel\ImportErrorExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class EnrollmentController extends Controller
{
    use AuditTrail;
    public function index()
    {
        // dd(Session::get('enrollment_id'));
        if (Session('district_id') == 0) {
            $enrollments = Enrollment::where('status', '!=', 'T')
                ->get();
        } else {
            $enrollments = Enrollment::where('district_id', Session('district_id'))
                ->where('status', '!=', 'T')
                ->get();
        }
        return view("Enrollment::index", compact('enrollments'));
    }

    public function create()
    {
        if (Session('district_id') != 0) {
            return view("Enrollment::create");
        }
        return redirect('admin/Enrollment');
    }

    public function validationRules()
    {
        $validation = new Enrollment();
        $validation->rules = [
            'school_year' => 'required|max:10|string|regex:/(^[\d]+[\-][\d]+$)/',
            // 'school_year' => 'required|max:10|string|regex:/^[0-9-]*$/',
            'confirmation_style' => 'required|max:30|string|regex:/[0-9a-z\s]+$/i',
            //'import_grades_by' => 'required',
            'begning_date' => 'required|date',
            'ending_date' => 'required|date|after_or_equal:begning_date',
            'perk_birthday_cut_off' => 'required|date',
            'kindergarten_birthday_cut_off' => 'required|date',
            'first_grade_birthday_cut_off' => 'required|date',
        ];
        $validation->messages = [
            '*.required' => ':attribute is required.',
            '*.date' => 'Enter valid date.',
            'school_year.regex' => 'Enter valid school year.',
            'confirmation_style.regex' => 'Do not enter special characters.',
        ];
        return $validation;
    }

    public function store(Request $request)
    {
        // return $request;
        $validation = $this->validationRules();
        $this->validate($request, $validation->rules, $validation->messages);

        // switch($request->import_grades_by){
        //     case 'submission_date':
        //         $request['import_grades_by'] = 'SD';
        //         break;
        //     default:
        //         $request['import_grades_by'] = '';
        // }
        $req = $request->all();
        $req['begning_date'] = date("Y-m-d", strtotime($req['begning_date']));
        $req['ending_date'] = date("Y-m-d", strtotime($req['ending_date']));
        $req['first_grade_birthday_cut_off'] = date("Y-m-d", strtotime($req['first_grade_birthday_cut_off']));
        $req['kindergarten_birthday_cut_off'] = date("Y-m-d", strtotime($req['kindergarten_birthday_cut_off']));
        $req['perk_birthday_cut_off'] = date("Y-m-d", strtotime($req['perk_birthday_cut_off']));

        unset($req['exit']);
        unset($req['import_grades_by']);
        unset($req['save_exit']);
        unset($req['_token']);
        unset($req['submit']);
        $req['district_id'] = Session('district_id');
        // dd($req);exit;

        $last_enroll_id = Enrollment::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->latest('updated_at')->first(['id']);

        $store = Enrollment::create($req);
        $enroll_id = $store->id;

        $priority_ids_by_key_value = $this->createPriority($enroll_id, $last_enroll_id->id);
        $program_ids_by_key_value = $this->createProgram($enroll_id, $last_enroll_id->id, $priority_ids_by_key_value);

        // always call this function after creation of new programs.
        $application_ids_by_key_value = $this->createApplication($enroll_id, $last_enroll_id->id, $program_ids_by_key_value);

        $this->createEligibility($enroll_id, $last_enroll_id->id, $application_ids_by_key_value, $program_ids_by_key_value);

        if (isset($store)) {
            $newObj = Enrollment::where('id', $store->id)->first();
            $this->modelCreate($newObj, "enrollment");
            Session::flash('success', "Enrollment Period added successfully.");
        } else {
            Session::flash('success', "Enrollment Period not added.");
            return redirect('admin/Enrollment/create');
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Enrollment');
        } else {
            // return redirect()->back();
            return redirect("admin/Enrollment/edit/" . $store->id);
        }
        return redirect('admin/Enrollment');
    }

    public function createEligibility($enroll_id, $last_enroll_id, $application_ids_by_key_value, $program_ids_by_key_value)
    {
        $eligibility_create_array = $old_eligibility_id = $new_eligibility_id = $old_eligibility_id_array = $eligibility_content_create_array = $eligibility_content_array = $set_eligibility_array = $set_eligibility_create_array = $set_program_eligibility_array = $set_program_eligibility_create_array = $set_eligibility_config_array = $set_eligibility_config_create_array = $set_eligibility_extra_value = $set_eligibility_extravalue_array = $set_eligibility_extravalue_create_array = [];

        $old_eligibility = Eligibility::where('district_id', session('district_id'))->where('enrollment_id', $last_enroll_id)->get();

        if (isset($old_eligibility) && !empty($old_eligibility)) {

            // Eligibility Clone start
            foreach ($old_eligibility as $key => $eligibility) {
                $eligibility_array = $eligibility->replicate()->toArray();
                $eligibility_array['enrollment_id'] = $enroll_id;

                $old_eligibility_id_array[] = $eligibility->id;
                $eligibility_create_array[] = $eligibility_array;
            }
            Eligibility::insert($eligibility_create_array);
            // Eligibility Clone end
        }

        $new_eligibility = Eligibility::where('district_id', session('district_id'))->where('enrollment_id', $enroll_id)->get();

        // Get Old and New eligibility by key(old) and value(new) pair start.
        foreach ($old_eligibility as $o_key => $value) {
            $old_eligibility_id[] = $value->id;
        }
        foreach ($new_eligibility as $n_key => $value1) {
            $new_eligibility_id[] = $value1->id;
        }
        $eligibility_ids = array_combine($old_eligibility_id, $new_eligibility_id);
        // Get Old and New eligibility by key(old) and value(new) pair end.

        // Eligibility Content clone start.
        $eligibility_content = EligibilityContent::whereIn('eligibility_id', $old_eligibility_id_array)->get();
        if (isset($eligibility_content) && !empty($eligibility_content)) {
            foreach ($eligibility_content as $c_key => $c_value) {
                $eligibility_content_array = $c_value->replicate()->toArray();
                $eligibility_content_array['eligibility_id'] = $eligibility_ids[$c_value->eligibility_id] ?? null;

                $eligibility_content_create_array[] = $eligibility_content_array;
            }
            EligibilityContent::insert($eligibility_content_create_array);
        }
        // Eligibility Content clone end.

        $old_application_ids_array = Application::where('district_id', session('district_id'))->where('enrollment_id', $last_enroll_id)->pluck('id');

        // SetEligibility clone start.
        $set_eligibility = SetEligibility::where('district_id', session('district_id'))->whereIn('application_id', $old_application_ids_array)->get();
        if (isset($set_eligibility) && !empty($set_eligibility)) {
            foreach ($set_eligibility as $key => $set_value) {
                $set_eligibility_array = $set_value->replicate()->toArray();
                $set_eligibility_array['application_id'] = $application_ids_by_key_value[$set_value->application_id] ?? null;
                $set_eligibility_array['program_id'] = $program_ids_by_key_value[$set_value->program_id] ?? null;
                $set_eligibility_array['eligibility_id'] = $eligibility_ids[$set_value->eligibility_id] ?? null;

                $set_eligibility_create_array[] = $set_eligibility_array;
            }
            SetEligibility::insert($set_eligibility_create_array);
        }
        // SetEligibility clone end.

        // SetEligibilityConfiguration clone start.
        $set_eligibility_configuration = SetEligibilityConfiguration::where('district_id', session('district_id'))->whereIn('application_id', $old_application_ids_array)->get();
        if (isset($set_eligibility_configuration) && !empty($set_eligibility_configuration)) {
            foreach ($set_eligibility_configuration as $key => $set_config_value) {
                $set_eligibility_config_array = $set_config_value->replicate()->toArray();
                $set_eligibility_config_array['application_id'] = $application_ids_by_key_value[$set_config_value->application_id] ?? null;
                $set_eligibility_config_array['program_id'] = $program_ids_by_key_value[$set_config_value->program_id] ?? null;
                $set_eligibility_config_array['eligibility_id'] = $eligibility_ids[$set_config_value->eligibility_id] ?? null;

                $set_eligibility_config_create_array[] = $set_eligibility_config_array;
            }
            SetEligibilityConfiguration::insert($set_eligibility_config_create_array);
        }
        // SetEligibilityConfiguration clone end.

        // SetEligibilityConfiguration clone start.
        $set_eligibility_extra_value = SetEligibilityExtraValue::whereIn('application_id', $old_application_ids_array)->get();
        if (isset($set_eligibility_extra_value) && !empty($set_eligibility_extra_value)) {
            foreach ($set_eligibility_extra_value as $key => $set_extra_value) {
                $set_eligibility_extravalue_array = $set_extra_value->replicate()->toArray();
                $set_eligibility_extravalue_array['application_id'] = $application_ids_by_key_value[$set_extra_value->application_id] ?? null;
                $set_eligibility_extravalue_array['program_id'] = $program_ids_by_key_value[$set_extra_value->program_id] ?? null;

                $set_eligibility_extravalue_create_array[] = $set_eligibility_extravalue_array;
            }
            SetEligibilityExtraValue::insert($set_eligibility_extravalue_create_array);
        }
        // SetEligibilityConfiguration clone end.

        // Set ProgramEligibility start
        $set_program_eligibility = ProgramEligibility::whereIn('application_id', $old_application_ids_array)->get();
        if (isset($set_program_eligibility) && !empty($set_program_eligibility)) {
            foreach ($set_program_eligibility as $key => $set_program_value) {
                $set_program_eligibility_array = $set_program_value->replicate()->toArray();
                $set_program_eligibility_array['application_id'] = $application_ids_by_key_value[$set_program_value->application_id] ?? null;
                $set_program_eligibility_array['program_id'] = $program_ids_by_key_value[$set_program_value->program_id] ?? null;
                $set_program_eligibility_array['assigned_eigibility_name'] = $eligibility_ids[$set_program_value->assigned_eigibility_name] ?? null;

                $set_program_eligibility_create_array[] = $set_program_eligibility_array;
            }
            ProgramEligibility::insert($set_program_eligibility_create_array);
        }
        // Set ProgramEligibility end

        return 'success';
    }

    public function createApplication($enroll_id, $last_enroll_id, $program_ids_by_key_value)
    {
        $old_application_id = $new_application_id = $application_ids = [];

        $old_applications = Application::where('district_id', session('district_id'))->where('enrollment_id', $last_enroll_id)->get();

        if (isset($program_ids_by_key_value) && !empty($program_ids_by_key_value) && isset($old_applications) && !empty($old_applications)) {
            foreach ($old_applications as $a_key => $application) {

                // Application Clone start
                $application_array = $application->replicate()->toArray();
                $application_array['enrollment_id'] = $enroll_id;
                $application_id = Application::create($application_array)->id;
                // Application Clone end

                // Application Program Clone start
                $application_program_array = [];
                $application_program = ApplicationProgram::where('application_id', $application->id)->get();
                foreach ($application_program as $app_prog_key => $app_prog_value) {
                    $app_prog_array = $app_prog_value->replicate()->toArray();

                    $app_prog_array['application_id'] = $application_id;
                    $app_prog_array['program_id'] = $program_ids_by_key_value[$app_prog_value->program_id];

                    $application_program_array[] = $app_prog_array;
                }
                ApplicationProgram::insert($application_program_array);
                // Application Program Clone end

                // Application Configuration Clone start
                $applciation_config = ApplicationConfiguration::where('application_id', $application->id)->first();
                $app_config_array = $applciation_config->replicate()->toArray();
                $app_config_array['application_id'] = $application_id;

                ApplicationConfiguration::create($app_config_array);
                // Application Configuration Clone end
            }
        }

        $new_applications = Application::where('district_id', session('district_id'))->where('enrollment_id', $enroll_id)->get();
        foreach ($old_applications as $a_key => $a_value) {
            $old_application_id[] = $a_value->id;
        }

        foreach ($new_applications as $n_key => $n_value1) {
            $new_application_id[] = $n_value1->id;
        }

        $application_ids = array_combine($old_application_id, $new_application_id);

        return $application_ids;
    }

    public function createProgram($enroll_id, $last_enroll_id, $priority_ids_by_key_value)
    {

        $old_program_id = $new_program_id = $program_ids = [];
        $old_programs = Program::where("enrollment_id", $last_enroll_id)->where("district_id", session("district_id"))->get();

        foreach ($old_programs as $key => $program) {

            $program_array = $program->replicate()->toArray();
            $program_array['enrollment_id'] = $enroll_id;
            $program_array['priority'] = $priority_ids_by_key_value[$program->priority] ?? null;
            $result = Program::create($program_array);
        }

        $new_programs = Program::where("enrollment_id", $enroll_id)->where("district_id", session("district_id"))->get();
        foreach ($old_programs as $p_key => $p_value) {
            $old_program_id[] = $p_value->id;
        }

        foreach ($new_programs as $n_key => $n_value1) {
            $new_program_id[] = $n_value1->id;
        }

        $program_ids = array_combine($old_program_id, $new_program_id);

        return $program_ids;
    }

    public function createPriority($enroll_id, $last_enroll_id)
    {
        $old_priority_id = $new_priority_id = $priority_ids = [];
        $old_priorities = Priority::where('district_id', session('district_id'))->where('status', '!=', 'T')->where('enrollment_id', $last_enroll_id)->get();

        foreach ($old_priorities as $key => $priority) {

            $priority_array = $priority->replicate()->toArray();
            $priority_array['enrollment_id'] = $enroll_id;
            $priority_id = Priority::create($priority_array)->id;

            $priorityDetails = PriorityDetail::where('priority_id', $priority->id)->get();
            $priority_detail_create_array = [];

            foreach ($priorityDetails as $key => $priority_detail) {
                $priority_detail_array = $priority_detail->replicate()->toArray();
                $priority_detail_array['priority_id'] = $priority_id;
                $priority_detail_create_array[] = $priority_detail_array;
            }

            PriorityDetail::insert($priority_detail_create_array);
        }

        $new_priorities = Priority::where('district_id', session('district_id'))->where('status', '!=', 'T')->where('enrollment_id', $enroll_id)->get();
        foreach ($old_priorities as $p_key => $p_value) {
            $old_priority_id[] = $p_value->id;
        }

        foreach ($new_priorities as $n_key => $n_value) {
            $new_priority_id[] = $n_value->id;
        }

        $priority_ids = array_combine($old_priority_id, $new_priority_id);
        return $priority_ids;
    }

    public function edit($id)
    {
        $schools = School::where('status', 'Y')->get(['id', 'name']);
        $adm_data = ADMData::where('enrollment_id', $id)->get();
        $enrollment = Enrollment::where('id', $id)->first();
        if (isset($enrollment)) {
            return view('Enrollment::edit', compact('enrollment', 'schools', 'adm_data'));
        }
        return redirect('admin/Enrollment');
    }

    public function update(Request $request, $id)
    {
        // return $request;

        $validation = $this->validationRules();
        $this->validate($request, $validation->rules, $validation->messages);

        // Selecting value for import_grades_by
        // switch($request->import_grades_by){
        //     case 'submission_date':
        //         $request['import_grades_by'] = 'SD';
        //         break;
        //     default:
        //         $request['import_grades_by'] = '';
        // }

        /** Udpate ADM Data start **/
        if (isset($request->adm_data) && !empty($request->adm_data)) {
            $adm_data = $request->adm_data;

            //ADMData::where('enrollment_id', $id)->delete();
            $curr_timestamp = date('Y-m-d H:i:s');
            //dd($adm_data);
            foreach ($adm_data as $key => $value) {
                if (isset($value['school_id'])) {
                    $data = [];
                    $data['enrollment_id'] = $id;
                    $data['created_at'] = $curr_timestamp;
                    $data['updated_at'] = $curr_timestamp;
                    $data['black'] = ($value['black'] ?? NULL);
                    $data['non_black'] = ($value['non_black'] ?? NULL);
                    $isValidBlack = !isset($data['black']) || is_numeric($data['black']);
                    $isValidNonBlack = !isset($data['non_black']) || is_numeric($data['non_black']);
                    if ($isValidBlack && $isValidNonBlack) {
                        $data['majority_race'] = ($data['black'] > 50) ? 'black' : (($data['non_black'] > 50) ? 'non_black' : '');
                        $rs = ADMData::updateOrCreate(array("enrollment_id" => $id, "school_id" => $value['school_id']), $data);
                    }
                }
            }
        }
        unset($request['adm_data']);
        /** Udpate ADM Data end **/

        $req = $request->all();
        $req['begning_date'] = date("Y-m-d", strtotime($req['begning_date']));
        $req['ending_date'] = date("Y-m-d", strtotime($req['ending_date']));
        $req['first_grade_birthday_cut_off'] = date("Y-m-d", strtotime($req['first_grade_birthday_cut_off']));
        $req['kindergarten_birthday_cut_off'] = date("Y-m-d", strtotime($req['kindergarten_birthday_cut_off']));
        $req['perk_birthday_cut_off'] = date("Y-m-d", strtotime($req['perk_birthday_cut_off']));

        unset($req['exit']);
        unset($req['save_exit']);
        unset($req['import_grades_by']);
        unset($req['_token']);
        unset($req['submit']);

        $initObj = Enrollment::where('id', $id)->first();
        $update = Enrollment::where('id', $id)
            ->update($req);
        $newObj = Enrollment::where('id', $id)->first();

        if (isset($update)) {
            $this->modelChanges($initObj, $newObj, "Enrollment");
            Session::flash('success', "Enrollment Period updated successfully.");
            if ($request->has('exit')) {
                return redirect('admin/Enrollment');
            }
        } else {
            Session::flash('success', "Enrollment Period not updated.");
            // return redirect('admin/Enrollment/edit/'.$id);
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Enrollment');
        } else {
            // return redirect()->back();
            return redirect("admin/Enrollment/edit/" . $id);
        }
        return redirect('admin/Enrollment/edit/' . $id);
        // return redirect('admin/Enrollment');
    }

    public function updateStatus(Request $request)
    {
        $update = Enrollment::where('id', $request->id)
            ->update(['status' => $request->status]);
        if (isset($update)) {
            return 'true';
        }
        return  'false';
    }

    public function trash()
    {
        if (Session('district_id') == 0) {
            $enrollments = Enrollment::where('status', 'T')
                ->get();
        } else {
            $enrollments = Enrollment::where('district_id', Session('district_id'))
                ->where('status', 'T')
                ->get();
        }
        return view('Enrollment::trash', compact('enrollments'));
    }

    public function moveToTrash($id)
    {
        $delete = Enrollment::where('id', $id)
            ->update(['status' => 'T']);
        if (isset($delete)) {
            Session()->flash('success', "Enrollment Period moved to trash successfully");
        } else {
            Session()->flash('error', "Enrollment Period not moved to trash.");
        }
        return redirect('admin/Enrollment');
    }

    public function delete($id)
    {
        $delete = Enrollment::where('id', $id)
            ->delete();
        if (isset($delete)) {
            Session()->flash('success', "Enrollment Period deleted successfully");
        } else {
            Session()->flash('error', "Enrollment Period not deleted.");
        }
        return redirect('admin/Enrollment/trash');
    }

    public function restore($id)
    {
        $restore = Enrollment::where('id', $id)
            ->update(['status' => 'Y']);
        if (isset($restore)) {
            Session()->flash('success', "Enrollment Period restored successfully.");
        } else {
            Session()->flash('error', "Enrollment Period not restored.");
        }
        return redirect('admin/Enrollment/trash');
    }

    public function import($id)
    {
        return view('Enrollment::import', compact('id'));
    }

    public function importTemplate()
    {
        $district_id = Session::get('district_id');
        $school_data = School::select('school.id', 'school.name')->where('school.name', 'like', '%High%')->where('status', '!=', 'T')->where('district_id', $district_id)->get();
        return Excel::download(new ImportErrorExport(collect(['data' => collect($school_data)])), 'Enrollment_RacialData_Import_Template.xlsx');
    }

    public function storeImport(Request $request, $id)
    {
        Session::put('upADM_Enroll_id', $id);
        Validator::extend('validate_file', function ($attribute, $value, $parameters, $validator) use ($request) {
            return in_array($request->file($attribute)->getClientOriginalExtension(), $parameters);
        });
        $max_mb = 10; // max file limit
        $max_limit = ($max_mb * 1024); // in Bytes
        $rules = [
            'file' =>  [
                'required',
                'validate_file:xlsx,xls',
                'max:' . $max_limit
            ]
        ];
        $messages = [
            'file.required' => 'File is required.',
            'file.max' => 'File may not be greater than 10 MB.',
            'file.validate_file' => 'The file must be a file of type: xls, xlsx.'
        ];
        $this->validate($request, $rules, $messages);
        $import = new ADMDataImport();
        $import->import(request()->file('file'));
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            return Excel::download(new ImportErrorExport($data), 'Enrollment_RacialData_Import_Error.xlsx');
        }
        Session::flash('success', 'Data imported successfully.');
        return redirect('admin/Enrollment/adm_data/import/' . Session::get('upADM_Enroll_id'));
    }
}
