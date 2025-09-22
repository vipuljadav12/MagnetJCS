<?php

namespace App\Modules\SetAvailability\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Program\Models\Program;
use App\Modules\SetAvailability\Models\Availability;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Submissions\Models\SubmissionsStatusUniqueLog;
use App\Modules\SetAvailability\Excel\AvailabilityImport;
use App\Modules\SetAvailability\Excel\ImportErrorExport;
use App\Modules\SetAvailability\Excel\RisingCompositionExport;
use App\Modules\SetAvailability\Excel\RisingCompositionImport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SetAvailabilityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        if (Session::get("district_id") != '0') {
            $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        } else
            $programs = Program::where('status', '!=', 'T')->where('enrollment_id', Session::get('enrollment_id'))->get();

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                if ($program->applicant_filter1 == '' && $program->applicant_filter1 == '' && $program->applicant_filter3 == '') {
                    array_push($af_programs, $program->name);
                } else {
                    foreach ($af as $key => $af_field) {
                        if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                            array_push($af_programs, $program->$af_field);
                        }
                    }
                }
            }
        }
        // return $af_programs;

        // return $programs;
        return view("SetAvailability::index", compact("af_programs"));
        // return view("SetAvailability::index",compact("programs", "af_programs"));
    }

    public function getOptionsByProgram(Program $program)
    {
        // $display_outcome = SubmissionsStatusUniqueLog::count();
        // $display_outcome = SubmissionsStatusUniqueLog::join("submissions", "submissions.id", "submissions_status_unique_log.submission_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->count();
        $availabilities =  Availability::where("program_id", $program->id)->where('district_id', $program->district_id)->where('enrollment_id', $program->enrollment_id)->first();
        $enrollment = Enrollment::where('status', 'Y')->where("district_id", $program->district_id)
            ->get()->last();
        return view("SetAvailability::options", compact("program", "availabilities", "enrollment"));
    }

    public function store(Request $request)
    {
        $key_data['program_id'] = $request['program_id'];
        $key_data['district_id'] = Session::get("district_id");
        $key_data['enrollment_id'] = Session::get("enrollment_id");
        $data['rising_composition'] = isset($request["rising_composition"]) ? json_encode($request["rising_composition"]) : null;
        $data['available_seats'] = $request['available_seats'] ?? 0;
        $data['year'] = $request["year"] ?? null;
        Availability::updateOrCreate($key_data, $data);
        Session::flash("success", "Availability saved successfully");
        return redirect('admin/Availability');
    }


    public function getPrograms(Request $request)
    {
        $af = [
            'application_filter_1' => 'applicant_filter1',
            'application_filter_2' => 'applicant_filter2',
            'application_filter_3' => 'applicant_filter3'
        ];
        $seat_type = [
            'available_seats' => 'Total Available Seats',
            // 'white_seats' => 'White',
            // 'other_seats' => 'Other'
        ];

        $req_filter = $request->application_filter;
        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where('status', '!=', 'T');
        if (Session::get("district_id") != '0') {
            $programs = $programs->where('district_id', Session::get('district_id'));
        }
        if (Session::get("enrollment_id") != '0') {
            $programs = $programs->where('enrollment_id', Session::get('enrollment_id'));
        }

        if ($req_filter == '') {
            // return all programs
            $data = ['data' => $programs->get()];
        } else {
            $programs = $programs->where(function ($q) use ($req_filter) {
                $q->where('applicant_filter1', $req_filter);
                $q->orWhere('applicant_filter2', $req_filter);
                $q->orWhere('applicant_filter3', $req_filter);
                $q->orWhere('name', $req_filter);
            })->get();

            // Filter by application filter
            $filtered_programs = [];
            $avg_data = [];
            if (!empty($programs)) {
                $programs_avg = [];
                foreach ($programs as $key => $program) {
                    if ($program->selection_by == "Program Name")
                        $selection_by = "name";
                    else
                        $selection_by = strtolower(str_replace(' ', '_', $program->selection_by));
                    $selection_by = "application_filter_1";
                    if ($selection_by == "name" && $program->{$selection_by} == $req_filter) {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                    } elseif (
                        isset($af[$selection_by]) &&
                        ($program->{$af[$selection_by]} != '') &&
                        $program->{$af[$selection_by]} == $req_filter
                    ) {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                    }
                }

                // avg availability
                // if (!empty($programs_avg)) {
                //     $total = 0;
                //     $availabilities =  Availability::whereIn("program_id",$programs_avg)->where('district_id',Session('district_id'))->get();

                //     foreach ($seat_type as $stype => $svalue) {

                //         $sum = $availabilities->sum($stype);
                //         $total += $sum;
                //         $avg_data['data'][$svalue] = $sum;
                //     }
                //     $avg_data['total'] = $total;
                // }
            }
            $data = [
                'data' => $filtered_programs,
                'avg_data' => $avg_data
            ];
        }
        return json_encode($data);
    }

    public function import()
    {
        return view('SetAvailability::import');
    }

    public function importTemplate()
    {
        $district_id = Session::get('district_id');
        // $school_data = School::select('school.id','school.name')->where('status','!=','T')->where('district_id',$district_id)->get();
        $program_data = [];
        $programs = Program::where('status', '!=', 'T')->where('district_id', $district_id)->where('enrollment_id', Session::get('enrollment_id'))->get();

        foreach ($programs as $key => $value) {
            $tmp = [];
            $tmp['program_id'] = $value->id;
            $tmp['program_name'] = $value->name;
            //$tmp['grade'] = $g_value;

            $program_data[] = $tmp;
        }
        // return $program_data;
        return Excel::download(new ImportErrorExport(collect(['data' => collect($program_data)])), 'Availability_Import_Template.xlsx');
    }

    public function storeImport(Request $request)
    {
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
        $import = new AvailabilityImport();
        $import->import(request()->file('file'));
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            return Excel::download(new ImportErrorExport($data), 'Availability_Import_Error.xlsx');
        }
        Session::flash('success', 'Data imported successfully.');
        return redirect('admin/Availability/import');
    }

    //

    public function importRisingComposition()
    {
        return view('SetAvailability::import_rising_composition');
    }

    public function importRisingCompositionTemplate()
    {
        $district_id = Session::get('district_id');
        // $school_data = School::select('school.id','school.name')->where('status','!=','T')->where('district_id',$district_id)->get();
        $program_data = [];
        $programs = Program::where('status', '!=', 'T')->where('district_id', $district_id)->where('enrollment_id', Session::get('enrollment_id'))->get();

        foreach ($programs as $key => $value) {
            $tmp = [];
            $tmp['program_id'] = $value->id;
            $tmp['program_name'] = $value->name;
            //$tmp['grade'] = $g_value;

            $program_data[] = $tmp;
        }
        // return $program_data;
        return Excel::download(new RisingCompositionExport(collect(['data' => collect($program_data)])), 'RisingComposition_Template.xlsx');
    }

    public function storeImportRisingComposition(Request $request)
    {
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
        $import = new RisingCompositionImport();
        $import->import(request()->file('file'));
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            $data['error'] = true;
            return Excel::download(new RisingCompositionExport($data), 'RisingComposition_Import_Error.xlsx');
        }
        Session::flash('success', 'Data imported successfully.');
        return redirect('admin/Availability/import_rising_composition');
    }
}
