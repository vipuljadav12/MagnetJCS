<?php

namespace App\Modules\Student\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Student\Models\StudentGrade;
use App\Modules\Student\Models\StudentCDI;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{

    public function index()
    {
        $students = Student::get();
        return view("Student::index", compact('students'));
    }

    public function loadData()
    {
        $data = Student::get();
        $row_array = [];
        foreach ($data as $key => $value) {
            $data_array = [];
            $data_array[] = $value->id ?? '';
            $data_array[] = $value->stateID ?? '';
            $data_array[] = $value->first_name ?? '';
            $data_array[] = $value->last_name ?? '';
            $data_array[] = $value->race ?? '';
            $data_array[] = $value->gender ?? '';
            $data_array[] = $value->birthday ?? '';
            $data_array[] = ($value->address ?? '') . ", " . ($value->city ?? '') . "," . ($value->zipcode ?? '');
            $data_array[] = $value->currentSchool ?? '';
            $data_array[] = $value->current_grade ?? '';
            $data_array[] = $value->state ?? '';
            $data_array[] = $value->email ?? '';
            $row_array[] = $data_array;
        }
        return json_encode($row_array);
    }


    public function grade($id)
    {
        $grades = StudentGrade::where('stateID', $id)->get();
        return view("Student::grades", compact('grades', 'id'));
    }

    public function cdi()
    {
        $cdi = StudentCDI::join('student', 'student.stateID', '=', 'student_conduct_disciplinary.stateID')
            ->select('student_conduct_disciplinary.*', 'student.first_name', 'student.last_name', 'student.currentSchool')
            // ->distinct('student.stateID')
            // ->take(10)
            ->get();
        foreach ($cdi as $key => $value) {
            $cdi_details = DB::table("student_cdi_details")->where("stateID", $value->stateID)->first();
            if (!empty($cdi_details)) {
                $value['cdi_details'] = 'Y';
            } else {
                $value['cdi_details'] = 'N';
            }
            $cdi[$key] = $value;
        }
        return view("Student::cdi", compact('cdi'));
    }

    public function cdiDetails($id)
    {
        $cdi_details = fetch_conduct_details($id);
        return view("Student::cdidetails", compact('cdi_details'));
    }
}
