<?php

namespace App\Modules\Import\Controllers;


use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Import\Rule\ExcelRule;
use App\Modules\Import\ImportFiles\GiftedStudentsImport;
use App\Modules\Import\ImportFiles\AgtNewCenturyImport;
use App\Modules\Import\ImportFiles\ExceptionalityImport;
use App\Modules\Import\ImportFiles\SubmissionsImport;
use Maatwebsite\Excel\HeadingRowImport;
use App\StudentData;
use App\Modules\Program\Models\Program;
use App\Modules\Import\Models\AgtToNch;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Import\ExportFiles\NonJCSStudentExport;
use App\Modules\Import\ImportFiles\NonJCSStudentImport;
use App\Modules\Import\ExportFiles\DataExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    use AuditTrail;
    protected $module_url = 'admin/import';

    public function importGiftedStudents()
    {
        return view('Import::import_gifted_students');
    }

    public function saveGiftedStudents(Request $request)
    {
        $rules = [
            'upload_csv' => ['required', new ExcelRule($request->file('upload_csv'))],
        ];
        $message = [
            'upload_csv.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $rs = StudentData::where("field_name", "like", "%gifted%")->delete();

            $file = $request->file('upload_csv');
            $headings = (new HeadingRowImport)->toArray($file);
            $excelHeader = $headings[0][0];
            $fixheader = ['currentenrollmentstatus', 'stateidnumber', 'lastname', 'firstname', 'gr', 'school', 'primaryexceptionality', 'casemanager', 'specialeducationstatus', 'enrichmentstudent', ''];
            $fixheader1 = ['currentenrollmentstatus', 'stateidnumber', 'lastname', 'firstname', 'gr', 'school', 'primaryexceptionality', 'casemanager', 'specialeducationstatus', 'enrichmentstudent'];

            if (!(CheckExcelHeader($excelHeader, $fixheader)) && !(CheckExcelHeader($excelHeader, $fixheader1))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new GiftedStudentsImport;
            $import->import($file);
            Session::flash('success', 'Gifted Students Imported successfully');
        }
        return  redirect()->back();
    }

    public function importAGTNewCentury()
    {
        $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        return view('Import::import_agt_nch', compact("programs"));
    }

    public function storeImportAGTNewCentury(Request $request)
    {
        $rules = [
            'program_name' => ['required'],
            'upload_agt_nch' => ['required', new ExcelRule($request->file('upload_agt_nch'))],
        ];
        $message = [
            'program_name.required' => 'Program is required',
            'upload_agt_nch.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            Session::flash('error', 'Something wrong. Please check all fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_agt_nch');
            $headings = (new HeadingRowImport)->toArray($file);
            $excelHeader = array_filter($headings[0][0]);

            $fixheader = ['student_id', 'grade_level', 'name'];

            if (!(CheckExcelHeader($excelHeader, $fixheader))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new AgtNewCenturyImport;
            $import->program_name = $request->program_name;
            $import->import($file);
            Session::flash('success', 'AGT priority to New Century Imported successfully');
        }
        return  redirect()->back();
    }

    public function importNonJCSStudents()
    {
        return view('Import::import_non_jcs_students')->with('module_url', $this->module_url);
    }

    public function storeNonJCSStudent(Request $request)
    {
        Validator::extend('validate_file', function ($attribute, $value, $parameters, $validator) use ($request) {
            return in_array($request->file($attribute)->getClientOriginalExtension(), $parameters);
        });
        $max_mb = 10; // 23712
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
        $import = new NonJCSStudentImport();
        $import->import($request->file('file'));
        // dd($import->errors());
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            $data['headings'] = [
                'SSID',
                'First Name',
                'Last Name',
                'Date of Birth',
                'Current School',
                'Current Grade',
                'Type of Transfer',
                'Approved Zoned School',
            ];
            return Excel::download(new NonJCSStudentExport($data), 'NonJCSStudent_ImportError.xlsx');
        }
        Session::flash('success', 'Records imported successfully.');
        return redirect($this->module_url . '/zoned_school_override');
    }

    public function downloadSampleNonJCSStudent()
    {
        $data['data'] = collect([]);
        $data['headings'] = [
            'SSID',
            'FirstName',
            'LastName',
            'Date of Birth',
            'Current School',
            'Current Grade',
            'Type of Transfer',
            'Approved Zoned School',
        ];
        return Excel::download(new NonJCSStudentExport($data), 'NonJcsStudentSample.xlsx');
    }

    public function importExceptionality()
    {
        return view('Import::exceptionality');
    }

    public function saveExceptionality(Request $request)
    {
        $rules = [
            'upload_csv' => ['required', new ExcelRule($request->file('upload_csv'))],
        ];
        $message = [
            'upload_csv.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_csv');
            $headings = (new HeadingRowImport)->toArray($file);
            $excelHeader = $headings[0][0];
            $fixheader = ['ssid', 'confirmation_number', 'student_name', 'sp_exception', '504_eligible'];

            if (!(CheckExcelHeader($excelHeader, $fixheader))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new ExceptionalityImport;
            $import->import($file);
            Session::flash('success', 'Exceptionality Imported successfully');
        }
        return  redirect()->back();
    }
    public function importSubmissions()
    {
        return view('Import::import_submissions');
    }
    public function importSubmissionsSample()
    {
        $data['headings'] = $this->getSubmissionsImportFields('title');
        return Excel::download(new DataExport($data), 'SubmissionImportSample.xlsx');
    }
    public function storeImportSubmissions(Request $request)
    {
        $rules = [
            'import_file' => ['required', new ExcelRule($request->file('import_file'))],
        ];
        $message = [
            'import_file.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            Session::flash('error', 'Something wrong. Please try again.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('import_file');
            /*$headings = (new HeadingRowImport)->toArray($file);
            $excelHeader=array_filter($headings[0][0]);*/
            $fields_ary = $this->getSubmissionsImportFields();
            $import = new SubmissionsImport($fields_ary);
            $import->program_name = $request->program_name;
            $import->import($file);
            $invalidArr = $import->invalidArr;
            if (!empty($invalidArr)) {
                $data['data'] = collect($invalidArr);
                $data['headings'] = $this->getSubmissionsImportFields('title');
                $data['headings'][] = 'error';
                return Excel::download(new DataExport($data), 'SubmissionImportSample.xlsx');
            }
            Session::flash('success', 'Data imported successfully');
        }
        return  redirect()->back();
    }
    public function getSubmissionsImportFields($column = '')
    {
        $fields = [
            'student_state_id' => ['title' => 'Student State ID', 'db_field' => 'student_id'],
            'first_name' => ['title' => 'First Name'],
            'last_name' => ['title' => 'Last Name'],
            'race' => ['title' => 'Race'],
            'gender' => ['title' => 'Gender'],
            'birthday' => ['title' => 'Birthday'],
            'address' => ['title' => 'Address'],
            'city' => ['title' => 'City'],
            'state' => ['title' => 'State'],
            'zip' => ['title' => 'Zip'],
            'race' => ['title' => 'Race'],
            'zoned_school' => ['title' => 'Zoned School'],
            'awarded_school' => ['title' => 'Awarded School'],
            'current_school' => ['title' => 'Current School'],
            'current_grade' => ['title' => 'Current Grade'],
            'next_grade' => ['title' => 'Next Grade'],
            'parent_first_name' => ['title' => 'Parent First Name'],
            'parent_last_name' => ['title' => 'Parent Last Name'],
            'parent_email' => ['title' => 'Parent Email'],
            'phone_number' => ['title' => 'Phone Number'],
            'first_choice_program' => ['title' => 'First Choice Program', 'db_field' => 'first_choice_program_id'],
            'second_choice_program' => ['title' => 'Second Choice Program', 'db_field' => 'second_choice_program_id'],
            'third_choice_program' => ['title' => 'Third Choice Program', 'db_field' => 'third_choice_program_id'],
            'first_sibling' => ['title' => 'First Sibling'],
            'second_sibling' => ['title' => 'Second Sibling'],
            'third_sibling' => ['title' => 'Third Sibling'],
            'sp_exception' => ['title' => 'SP Exception'],
            'is_504_eligible' => ['title' => 'Is 504 Eligible'],

        ];
        if ($column != '') {
            $field_values = array_values($this->getSubmissionsImportFields());
            return array_column($field_values, $column);
        }
        return $fields;
    }
}
