<?php

namespace App\Modules\GiftedStudents\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GiftedStudents\Models\GiftedStudents;
use App\Modules\GiftedStudents\ImportFiles\GiftedStudentsImport;
use App\Modules\GiftedStudents\Rule\ExcelRule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;

class GiftedStudentsController extends Controller
{
    public function index()
    {
        $district_id = Session::get('district_id');
        $enrollment_id = Session::get('enrollment_id');

        $data = GiftedStudents::where('district_id', $district_id)->where('enrollment_id', $enrollment_id)->get();

        return view("GiftedStudents::index", compact('data'));
    }

    public function importGiftedStudent()
    {
        return view('GiftedStudents::import_gifted_students');
    }

    public function saveGiftedStudent(Request $request)
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
            $fixheader = ['ssid', 'firstname', 'lastname', 'admin'];
            if (!(CheckExcelHeader($excelHeader, $fixheader))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new GiftedStudentsImport;
            $import->import($file);
            Session::flash('success', 'Gifted Students Imported successfully');
        }
        return  redirect()->back();
    }

    public function create()
    {
        return view('GiftedStudents::create');
    }

    public function store(Request $request)
    {
        $district_id = Session::get('district_id');
        $enrollment_id = Session::get('enrollment_id');

        $rules = [
            'stateID' => 'required',
        ];
        $message = [
            'stateID.required' => 'Student ID is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $insert = $request->except(['_token', 'submit']);

            $insert['district_id'] = $district_id;
            $insert['enrollment_id'] = $enrollment_id;

            $gifted_student = GiftedStudents::where('district_id', $district_id)->where('enrollment_id', $enrollment_id)->first();

            if (isset($gifted_student) && !empty($gifted_student)) {
                $gifted_student->update($insert);
                Session::flash('success', 'Gifted Student updated successfully');
            } else {
                GiftedStudents::create($insert);
                Session::flash('success', 'Gifted Student added successfully');
            }


            if (isset($request->submit)) {
                return redirect('admin/GiftedStudents/create');
            }
            return redirect('admin/GiftedStudents');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $delete = GiftedStudents::where('id', $id)->delete();
        if ($delete) {
            Session::flash('success', 'Gifted Student Deleted successfully');
        }
        return redirect('admin/GiftedStudents');
    }
}
