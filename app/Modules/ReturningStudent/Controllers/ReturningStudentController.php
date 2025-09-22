<?php

namespace App\Modules\ReturningStudent\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\ReturningStudents\Models\ReturningStudents;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\ReturningStudent\ImportExport\DataExport;
use App\Modules\ReturningStudent\ImportExport\DataImport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReturningStudentController extends Controller
{
    protected $module_url = 'admin/ReturningStudent';

    public function index()
    {
        return view('ReturningStudent::index')->with('module_url', $this->module_url);
    }

    public function store(Request $request)
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
        $import = new DataImport();
        $import->import($request->file('file'));
        // dd($import->errors());
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            $data['headings'] = array_merge($this->getHeadings(), ['Errors']);
            return Excel::download(new DataExport($data), 'ImportError.xlsx');
        }
        Session::flash('success', 'Records imported successfully.');
        return redirect($this->module_url);
    }

    public function getHeadings()
    {
        return [
            'SSID',
            'Date of Birth',
            'Student Name',
            'Next Grade',
            'Race',
            'Current School',
            'Current Signature Academy'
        ];
    }

    public function downloadSample()
    {
        $data['data'] = collect([]);
        $data['headings'] = $this->getHeadings();
        return Excel::download(new DataExport($data), 'Returning Students Sample.xlsx');
    }
}
