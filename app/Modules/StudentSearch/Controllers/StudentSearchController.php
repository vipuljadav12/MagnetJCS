<?php

namespace App\Modules\StudentSearch\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\Validator;

class StudentSearchController extends Controller
{
    protected $module_url = 'admin/StudentSearch';
    public function index()
    {
        return view('StudentSearch::index')->with('module_url', $this->module_url);
    }

    public function data(Request $request)
    {
        $id = $request->id;
        $data['student'] = Student::where('stateID', $id)->first();
        return view('StudentSearch::data', compact('data'))->with('module_url', $this->module_url);
    }

    public function updateData(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'current_grade' => 'required',
            'birthday' => 'required|date',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'current_school' => 'required',
            'next_school' => 'required',
            'race' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return 'false';
        }
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'current_grade' => $request->current_grade,
            'birthday' => date('Y-m-d', strtotime($request->birthday)),
            'address' => $request->address,
            'city' => $request->city,
            'current_school' => $request->current_school,
            'next_school' => $request->next_school,
            'zip' => $request->zip,
            'race' => $request->race,
        ];
        $id = $request->id;
        $update = Student::where('stateID', $id)->update($data);
        return $update ? 'true' : 'false';
    }
}
