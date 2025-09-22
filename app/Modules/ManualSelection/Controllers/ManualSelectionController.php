<?php

namespace App\Modules\ManualSelection\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Program\Models\Program;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade};
use App\Modules\ManualSelection\Models\SelectionPreReq;
use Illuminate\Support\Facades\Session;

class ManualSelectionController extends Controller
{
    public $submission;
    public function  __construct()
    {
        $this->submission = new Submissions();
    }

    public function pre_req_index()
    {

        $enrollment_id = Session::get('enrollment_id');
        $district_id = Session::get('district_id');
        $data = SelectionPreReq::where('submissions_pre_req.enrollment_id', $enrollment_id)->where('submissions_pre_req.district_id', $district_id)
            ->join('program', 'submissions_pre_req.program_id', 'program.id')
            ->select('submissions_pre_req.*', 'program.name as program_name')
            ->get();
        // dd($data);
        return view("ManualSelection::Prerequisite.index", compact('data'));
    }

    public function create()
    {
        $programs = Program::where("district_id", Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->where('status', 'Y')->get();
        $grades = $this->submission->getSearhData()['next_grade'];
        $courses = SubmissionGrade::groupBy('courseName')->pluck('courseName');

        return view("ManualSelection::Prerequisite.create", compact('programs', 'grades', 'courses'));
    }

    public function store(Request $request)
    {

        $msg = [
            'program_id.required' => 'Program is required.',
            'program_id.unique' => 'This Prerequisite is already exists.',
            'grade.required' => 'Grade is required.',
            'course_name.required' => 'Course(s) is required.'
        ];

        $request->validate([
            'program_id' => 'required|unique:submissions_pre_req,program_id,NULL,id,grade,' . $request->input('grade'),
            'grade' => 'required',
            'course_name' => 'required',

        ], $msg);

        $create_arr = [
            'enrollment_id' => Session::get('enrollment_id'),
            'district_id' => Session::get('district_id'),
            'program_id' => $request->program_id,
            'grade' => $request->grade,
            'course_name' => implode('|', $request->course_name),
        ];

        $prereq = SelectionPreReq::create($create_arr);

        if (isset($prereq)) {
            Session::flash("success", "Selection Prerequisite added successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }

        if (isset($request->save_exit)) {
            return redirect('admin/ManualSelection/pre_req');
        }
        return redirect('admin/ManualSelection/pre_req/create/');
    }

    public function edit($id)
    {
        $data = SelectionPreReq::where('id', $id)->first();
        $programs = Program::where("district_id", Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->where('status', 'Y')->get();
        $grades = $this->submission->getSearhData()['next_grade'];
        $courses = SubmissionGrade::groupBy('courseName')->pluck('courseName');

        return view('ManualSelection::Prerequisite.edit', compact('data', 'programs', 'grades', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $msg = [
            // 'program_id.required'=>'Program is required.',
            // 'grade.required'=>'Grade is required.',
            'course_name.required' => 'Course(s) is required.'
        ];

        $request->validate([
            // 'program_id'=>'required',
            // 'grade'=>'required',
            'course_name' => 'required',

        ], $msg);

        $update_arr = [
            // 'program_id'=>$request->program_id,
            // 'grade'=>$request->grade,
            'course_name' => implode('|', $request->course_name),
        ];

        SelectionPreReq::where('id', $id)->update($update_arr);
        Session::flash("success", "Selection Prerequisite updated successfully.");

        if (isset($request->save_exit)) {
            return redirect('admin/ManualSelection/pre_req');
        }
        return redirect('admin/ManualSelection/pre_req/edit/' . $id);
    }

    public function destroy($id) {}
}
