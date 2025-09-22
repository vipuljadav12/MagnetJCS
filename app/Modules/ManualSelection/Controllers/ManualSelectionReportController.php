<?php

namespace App\Modules\ManualSelection\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Program\Models\Program;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade};
use App\Modules\ManualSelection\Models\SelectionPreReq;
use Illuminate\Support\Facades\Session;

class ManualSelectionReportController extends Controller
{
	public $submission;
	public function  __construct()
	{
		$this->submission = new Submissions();
	}
	public function missingProgramPrerequisite($enrollment_id)
	{
		$selection = "program_pre_req";
		$enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
		$all_data['awarded_school'] = $this->submission->getSearhData()['awarded_school'];
		$all_data['next_grade'] = $this->submission->getSearhData()['next_grade'];

		return view('ManualSelection::Reports.program_pre_req_index', compact('selection', 'enrollment', 'all_data', 'enrollment_id'));
	}

	public function missingProgramPrerequisiteResponse(Request $request, $enrollment_id)
	{

		$req = $request->all();
		$program_course_arr = [];
		$enrollment_id = Session::get('enrollment_id');
		$district_id = Session::get('district_id');

		$next_grade = $request->next_grade ?? 0;
		$program_id = $request->awarded_school ?? '0';

		if ($program_id == '0') {
			return json_encode(['data' => []]);
		}

		$program_pre_req = SelectionPreReq::where('enrollment_id', $enrollment_id)
			->where('district_id', $district_id)
			->where('program_id', $program_id)
			->Where('grade', $next_grade)
			->first(['course_name']);

		if (!isset($program_pre_req) && empty($program_pre_req) && $next_grade == '0') {
			$program_pre_req = SelectionPreReq::where('enrollment_id', $enrollment_id)
				->where('district_id', $district_id)
				->where('program_id', $program_id)
				->first(['course_name']);
		}

		if (isset($program_pre_req) && !empty($program_pre_req)) {
			$program_course_arr = explode('|', $program_pre_req->course_name);
		}

		$data = $this->submission::join('submission_grade', 'submissions.id', 'submission_grade.submission_id')
			->where(function ($query) use ($program_id) {
				$query->where('first_choice_program_id', $program_id)
					->orWhere('second_choice_program_id', $program_id)
					->orWhere('third_choice_program_id', $program_id);
			})
			->whereIn('submission_grade.courseName', $program_course_arr)
			->select('submission_grade.courseName', 'submission_grade.academicYear', 'submission_grade.teacher_name', 'submission_grade.school_name', 'submission_grade.courseType', 'submission_grade.grade_level', 'submissions.*')
			->distinct('submission_grade.courseName')
			->get()->toArray();

		$final_arr = $data_arr = [];
		$total = count($data);
		if (isset($data) && count($data) > 0) {
			foreach ($data as $key => $submission) {
				$tmp = [];
				$tmp['submissin_id'] = $submission['id'];
				$tmp['state_id'] = $submission['student_id'];
				$tmp['student_name'] = implode(' ', [$submission['first_name'], $submission['last_name']]);
				$tmp['program_name'] = getProgramName($program_id);
				$tmp['current_grade'] = $submission['current_grade'];
				$tmp['academic_year'] = $submission['academicYear'];
				// $tmp['academic_term'] = $submission['academicTerm'];
				$tmp['course_type'] = $submission['courseType'];
				$tmp['course_name'] = $submission['courseName'];
				$tmp['grade_level'] = $submission['grade_level'];
				$tmp['school_name'] = $submission['school_name'];
				//$tmp['teacher_name'] = $submission['teacher_name'];

				$final_arr[] = $tmp;
			}
		}

		$data_arr['recordsTotal'] = $total;
		$data_arr['recordsFiltered'] = $total;
		$data_arr['data'] = $final_arr;
		return json_encode($data_arr);
	}

	public function missingProgramNoPrerequisiteResponse(Request $request, $enrollment_id)
	{
		$req = $request->all();
		$program_course_arr = [];
		$enrollment_id = Session::get('enrollment_id');
		$district_id = Session::get('district_id');

		$next_grade = $request->next_grade ?? 0;
		$program_id = $request->awarded_school ?? '0';

		if ($program_id == '0') {
			return json_encode(['data' => []]);
		}

		$program_pre_req = SelectionPreReq::where('enrollment_id', $enrollment_id)
			->where('district_id', $district_id)
			->where('program_id', $program_id)
			->Where('grade', $next_grade)
			->first(['course_name']);

		if (!isset($program_pre_req) && empty($program_pre_req) && $next_grade == '0') {
			$program_pre_req = SelectionPreReq::where('enrollment_id', $enrollment_id)
				->where('district_id', $district_id)
				->where('program_id', $program_id)
				->first(['course_name']);
		}

		if (isset($program_pre_req) && !empty($program_pre_req)) {
			$program_course_arr = explode('|', $program_pre_req->course_name);
		}

		$data = $this->submission::with(['getSubmissionGrade' => function ($query) use ($program_course_arr) {
				return $query->whereIn('courseName', $program_course_arr);
			}])
			->where(function ($query) use ($program_id) {
				$query->where('first_choice_program_id', $program_id)
					->orWhere('second_choice_program_id', $program_id)
					->orWhere('third_choice_program_id', $program_id);
			})
			->get()
			->toArray();

		$final_arr = $data_arr = [];
		$total = count($data);
		if (isset($data) && count($data) > 0) {
			foreach ($data as $key => $submission) {
				if (count($submission['get_submission_grade']) == 0) {
					$tmp = [];
					$tmp['submissin_id'] = $submission['id'];
					$tmp['stateID'] = $submission['student_id'];
					$tmp['student_name'] = implode(' ', [$submission['first_name'], $submission['last_name']]);
					$tmp['program_name'] = getProgramName($program_id);
					$tmp['current_grade'] = $submission['current_grade'];

					$final_arr[] = $tmp;
				}
			}
		}

		$data_arr['recordsTotal'] = $total;
		$data_arr['recordsFiltered'] = $total;
		$data_arr['data'] = $final_arr;
		return json_encode($data_arr);
	}
}
