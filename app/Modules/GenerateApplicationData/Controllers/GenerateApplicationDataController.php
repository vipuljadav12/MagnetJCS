<?php

namespace App\Modules\GenerateApplicationData\Controllers;

use App\Modules\School\Models\School;
use App\Modules\District\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Application\Models\Application;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Program\Models\Program;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionsWaitlistFinalStatus, SubmissionsFinalStatus};
use App\Modules\GenerateApplicationData\Models\GenerateApplicationDataGenerated;
use App\Modules\GenerateApplicationData\Models\GenerateContractDataGenerated;
use App\Modules\Eligibility\Models\SubjectManagement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class GenerateApplicationDataController extends Controller
{

    public function index()
    {
        /* set_time_limit(0);
        $rsData = DB::select("select * from student_session_sunday LIMIT 25000 OFFSET 175000");

        foreach($rsData as $key=>$value)
        {
            $rsData1 = DB::table("student_session")->where("stateID", $value->stateID)->first();
            if(empty($rsData1))
            {
                $data = array();
                $data['stateID'] = $value->stateID;
                $data['student_id'] = $value->student_id;
                $data['first_name'] = $value->first_name;
                $data['middle_name'] = $value->middle_name;
                $data['last_name'] = $value->last_name;
                $data['parent_first_name'] = $value->parent_first_name;
                $data['parent_last_name'] = $value->parent_last_name;
                $data['race'] = $value->race;
                $data['gender'] = $value->gender;
                $data['birthday'] = $value->birthday;
                $data['address'] = $value->address;
                $data['city'] = $value->city;
                $data['zip'] = $value->zip;
                $data['current_school'] = $value->current_school;
                $data['current_grade'] = $value->current_grade;
                $data['IsHispanic'] = $value->IsHispanic;
                $data['nonHSVStudent'] = $value->nonHSVStudent;
                $data['state'] = $value->state;
                $data['email'] = $value->email;
                $data['phone'] = $value->phone;
                $data['work_phone'] = $value->work_phone;
                $data['studentFileNumber'] = $value->studentFileNumber;
                $data['grade_fetched'] = $value->grade_fetched;
                $data['cdi_fetched'] = $value->cdi_fetched;
                $data['mcps_email'] = $value->mcps_email;
                $rs3 = DB::table("student_session")->insert($data);                

            }
        }
        echo "done";exit;*/
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $programs = Program::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->where('status', 'Y')->orderBy('name')->get();
        $grades = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('next_grade')->select(DB::raw("DISTINCT(next_grade)"))->get();
        $submission_status = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(submission_status)"))->get();


        return view("GenerateApplicationData::index", compact('enrollment', 'programs', 'grades', 'submission_status'));
    }

    public function pdfview()
    {
        $items = DB::table("submissions")->get();
        view()->share('items', $items);

        $pdf = Pdf::loadView('GenerateApplicationData::pdfview', ['items']);
        return $pdf->download('pdfview.pdf');
    }

    public function generateData(Request $request)
    {
        set_time_limit(300);
        $first_program_id = $request['first_program'];
        $second_program_id = $request['second_program'];
        $enrollment_id = $request['enrollment'];
        $grade = $request['grade'];
        $status = $request['status'];
        $application_data = Application::where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get("enrollment_id"))->where("status", "Y")->first();


        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $enrollment_id);

        if ($request['awarded_program'] != '' && $request['awarded_program'] != "All" && $request['awarded_program'] != "0") {
            $data->where('awarded_school', $request['awarded_program']);
        } else {
            if ($first_program_id != 'All' && $first_program_id != '0' && $first_program_id != '') {
                $data->where(function ($q) use ($first_program_id) {
                    $q->where("first_choice_program_id", $first_program_id);
                });
            }
            //echo $second_program_id;exit;
            if ($second_program_id != 'All' && $second_program_id != '' && $second_program_id != '0') {
                $data->where(function ($q) use ($second_program_id) {
                    $q->where("second_choice_program_id", $second_program_id);
                });
            }
        }
        /* elseif($second_program_id == '0')
        {
            $data->where(function($q) use ($second_program_id) {
                $q->where("second_choice_program_id", '<>', '')->where("second_choice_program_id", '<>', '0');
                });
        }*/

        if ($grade != 'All') {
            $data->where('next_grade', $grade);
        }
        if ($status != 'All') {
            $data->where('submission_status', $status);
        }
        $final_data = $data->get();

        $subjects = $terms = array();

        $eligibilityArr = $programArr =  array();

        foreach ($final_data as $key => $value) {
            if ($value->first_choice != "" && !in_array($value->first_choice, $programArr)) {
                $programArr[] = $value->first_choice;
                // $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                $eligibilityData = getEligibilitiesDynamic($value->first_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        // echo $eligibilityData[0]->id;exit;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);

                        if (!empty($content)) {
                            // if($content->scoring->type=="DD")
                            // {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $svalue;
                                }
                            }

                            // foreach($content->terms_calc as $tvalue)
                            // {
                            //     if(!in_array($tvalue, $terms))
                            //     {
                            //         $terms[] = $tvalue;
                            //     }
                            // }
                            if (isset($content->terms_calc)) {
                                foreach ($content->terms_calc as $tkey => $tvalue) {
                                    if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                        $terms[$tkey] = $tvalue;
                                    }
                                }
                            }
                            // }
                        }
                    }
                }
            }
            if ($value->second_choice != "" && !in_array($value->second_choice, $programArr)) {
                // $eligibilityData = getEligibilities($value->second_choice, 'Academic Grade Calculation');
                $eligibilityData = getEligibilitiesDynamic($value->second_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    if (!empty($content)) {
                        // if($content->scoring->type=="DD")
                        // {
                        $tmp = array();

                        foreach ($content->subjects as $svalue) {
                            if (!in_array($svalue, $subjects)) {
                                $subjects[] = $value;
                            }
                        }

                        // foreach($content->terms_calc as $tvalue)
                        // {
                        //     if(!in_array($tvalue, $terms))
                        //     {
                        //         $terms[] = $tvalue;
                        //     }
                        // }
                        if (isset($content->terms_calc)) {
                            foreach ($content->terms_calc as $tkey => $tvalue) {
                                if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                    $terms[$tkey] = $tvalue;
                                }
                            }
                        }
                        // }
                    }
                }
            }
        } // grade ends


        // $subjects = array("eng", "math", "sci", "ss");
        //$terms = array("Q4.4 Final Grade"); 

        $student_data = array();

        //$count = 0;

        foreach ($final_data as $key => $value) {

            $score = $this->collectionStudentGrade($value->id, $subjects, $terms, $value->next_grade);
            /*$cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
            if(!empty($cdi_data))
            {
                $cdiArr = array();
                $cdiArr['b_info'] = $cdi_data->b_info;
                $cdiArr['c_info'] = $cdi_data->c_info;
                $cdiArr['d_info'] = $cdi_data->d_info;
                $cdiArr['e_info'] = $cdi_data->e_info;
                $cdiArr['susp'] = $cdi_data->susp;
                $cdiArr['susp_days'] = $cdi_data->susp_days;
            }
            else
            {
                $cdiArr = array();
                $cdiArr['b_info'] = $cdiArr['c_info'] = $cdiArr['d_info'] = $cdi_data['d_info'] = $cdiArr['e_info'] = $cdiArr['susp'] = $cdiArr['susp_days'] = 0;
            }*/

            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['student_id'] = $value->student_id;
            $tmp['confirmation_no'] = $value->confirmation_no;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['score'] = $score;

            $avgSum = $avgCnt = 0;
            foreach ($score as $sk => $sv) {
                $avgSum += $sv['grade'];
                $avgCnt++;
            }

            if ($avgCnt > 0) {
                $tmp['avgGrade'] = number_format($avgSum / $avgCnt, 2);
            } else {
                $tmp['avgGrade'] = 0;
            }
            //$tmp['cdi'] = $cdiArr;
            $tmp['grade'] = $value->next_grade;
            $tmp['current_school'] = $value->current_school;
            $tmp['zoned_school'] = $value->zoned_school;
            $tmp['employee_name'] = $value->employee_first_name . " " . $value->employee_last_name;
            $tmp['race'] = $value->race;
            $tmp['address'] = $value->address;
            $tmp['city'] = $value->city;
            $tmp['state'] = $value->state;
            $tmp['zip'] = $value->zip;

            $tmp['created_at'] = $value->created_at;
            $tmp['awarded_school'] = $value->awarded_school;
            $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
            $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
            $tmp['birth_date'] = $value->birthday;

            $student_data[] = $tmp;
        }

        view()->share('student_data', $student_data);
        view()->share('subjects', $subjects);
        view()->share('terms', $terms);
        view()->share("application_data", $application_data);

        $pdf = PDF::loadView('GenerateApplicationData::pdfview', ['student_data', 'terms', 'subjects', 'application_data']);

        $path = "resources/assets/admin/application_data";
        $fileName =  "ApplicationData-" . strtotime(date("Y-m-d H:i:s")) . '.' . 'pdf';
        $pdf->save($path . '/' . $fileName);

        $insert = array();
        $insert['enrollment_id'] = $enrollment_id;
        $insert['first_program'] = ($first_program_id != '' ? $first_program_id : 0);
        $insert['second_program'] = ($second_program_id != '' ? $second_program_id : 0);
        $insert['grade'] = $grade;
        $insert['status'] = $status;
        $insert['total_records'] = count($student_data);
        $insert['file_name'] = $fileName;
        $id = GenerateApplicationDataGenerated::create($insert);


        return $pdf->download($fileName);
    }



    public function collectionStudentGrade($submission_id, $subjects, $terms, $next_grade = 0)
    {
        $gradeInfo = SubjectManagement::where("grade", $next_grade)->first();

        $config_subjects = Config::get('variables.subjects');
        $config_type = Config::get('variables.courseType');
        $score = array();
        $missing = false;
        $grade_year = [];
        if (isset($gradeInfo)) {
            $grade_year = explode(',', $gradeInfo->year);
        }
        foreach ($grade_year as $acy => $acvy) {
            if (isset($terms[$acvy])) {
                foreach ($terms[$acvy] as $tkey => $tvalue) {
                    foreach ($subjects as $skey => $svalue) {
                        $tmp = [];
                        $marks = getSubmissionAcademicScore($submission_id, $config_subjects[$svalue], $tvalue, $acvy, '');

                        $tmp['subject'] = $config_subjects[$svalue];
                        $tmp['courseType'] = array_search($config_subjects[$svalue], $config_type);
                        $tmp['academic_year'] = $acvy;
                        $tmp['academic_term'] = $tvalue;
                        $field = strtolower(str_replace(" ", "_", $config_subjects[$svalue]));
                        if ($gradeInfo->{$field} == "N") {
                            $tmp['grade'] = "NA";
                        } else {
                            $tmp['grade'] = $marks;
                        }

                        $score[] = $tmp;
                    }
                }
            }
        }

        // foreach($subjects as $value)
        // {
        //     foreach($terms as $value1)
        //     {

        //         $field = strtolower(str_replace(" ","_", $config_subjects[$value]));    
        //         if($gradeInfo->{$field} == "N")
        //         {
        //             $score[$value][$value1] = "NA"; 
        //         }
        //         else
        //         {
        //             $marks = getSubmissionAcademicScore($submission_id, $config_subjects[$value], $value1, (date("Y")-1)."-".(date("Y")), (date("Y")-1)."-".(date("y")));
        //             $score[$value][$value1] = $marks; 
        //         }
        //     }
        // }
        return $score;
    }

    public function existingData()
    {
        $data = GenerateApplicationDataGenerated::where("enrollment_id", Session::get("enrollment_id"))->orderByDesc("application_data_generated.created_at")->join("enrollments", "enrollments.id", "application_data_generated.enrollment_id")->select('application_data_generated.*', 'enrollments.school_year')->limit(10)->get();
        return view("GenerateApplicationData::generated", compact("data"));
    }

    public function downloadFile($id)
    {
        $data = GenerateApplicationDataGenerated::where("id", $id)->first();
        if (!empty($data)) {
            $file_path = 'resources/assets/admin/application_data/' . $data->file_name;
            $headers = array(
                'Content-Type: application/pdf',
            );

            return Response::download($file_path, $data->file_name, $headers);
        }
    }



    public function generateIndividual($id)
    {


        $data = Submissions::where("submissions.id", $id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->get();

        $final_data = $data;

        $subjects = $terms = array();

        $eligibilityArr = $programArr =  array();

        $enrollment_id = 0;
        $program_id = 0;
        $grade = 0;
        $status = '';
        $application_data = Application::where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->where("status", "Y")->first();


        foreach ($final_data as $key => $value) {
            if ($value->first_choice != "") {
                $enrollment_id = $value->enrollment_id;
                $program_id = $value->first_choice_program_id;
                $grade = $value->next_grade;
                $status = $value->submission_status;


                $programArr[] = $value->first_choice;
                // $eligibilityData = getEligibilities($value->first_choice, 'Academic Grade Calculation');
                $eligibilityData = getEligibilitiesDynamic($value->first_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    if (!in_array($eligibilityData[0]->id, $eligibilityArr)) {
                        $eligibilityArr[] = $eligibilityData[0]->assigned_eigibility_name;
                        $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                        if (!empty($content)) {
                            // if($content->scoring->type=="DD")
                            // {
                            $tmp = array();

                            foreach ($content->subjects as $svalue) {
                                if (!in_array($svalue, $subjects)) {
                                    $subjects[] = $svalue;
                                }
                            }

                            // foreach($content->terms_calc as $tvalue)
                            // {
                            //     if(!in_array($tvalue, $terms))
                            //     {
                            //         $terms[] = $tvalue;
                            //     }
                            // }

                            if (isset($content->terms_calc)) {
                                foreach ($content->terms_calc as $tkey => $tvalue) {
                                    if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                        $terms[$tkey] = $tvalue;
                                    }
                                }
                            }
                            // }
                        }
                    }
                }
            }

            if ($value->second_choice != "") {
                // $eligibilityData = getEligibilities($value->second_choice, 'Academic Grade Calculation');
                $eligibilityData = getEligibilitiesDynamic($value->second_choice, 'Academic Grades');
                if (count($eligibilityData) > 0) {
                    $content = getEligibilityContent1($eligibilityData[0]->assigned_eigibility_name);
                    if (!empty($content)) {
                        // if($content->scoring->type=="DD")
                        // {
                        $tmp = array();

                        foreach ($content->subjects as $svalue) {
                            if (!in_array($svalue, $subjects)) {
                                $subjects[] = $value;
                            }
                        }

                        // foreach($content->terms_calc as $tvalue)
                        // {
                        //     if(!in_array($tvalue, $terms))
                        //     {
                        //         $terms[] = $tvalue;
                        //     }
                        // }
                        if (isset($content->terms_calc)) {
                            foreach ($content->terms_calc as $tkey => $tvalue) {
                                if (! \Illuminate\Support\Arr::exists($terms, $tkey)) {
                                    $terms[$tkey] = $tvalue;
                                }
                            }
                        }
                        // }
                    }
                }
            }

            // dd($terms, $subjects, $value);


        } // grade ends

        $student_data = array();
        foreach ($final_data as $key => $value) {
            $score = $this->collectionStudentGrade($value->id, $subjects, $terms, $value->next_grade);
            // $cdi_data = DB::table("submission_conduct_discplinary_info")->where("submission_id", $value->id)->first();
            // if(!empty($cdi_data))
            // {
            //     $cdiArr = array();
            //     $cdiArr['b_info'] = $cdi_data->b_info;
            //     $cdiArr['c_info'] = $cdi_data->c_info;
            //     $cdiArr['d_info'] = $cdi_data->d_info;
            //     $cdiArr['e_info'] = $cdi_data->e_info;
            //     $cdiArr['susp'] = $cdi_data->susp;
            //     $cdiArr['susp_days'] = $cdi_data->susp_days;
            // }
            // else
            // {
            //     $cdiArr = array();
            //     $cdiArr['b_info'] = $cdiArr['c_info'] = $cdiArr['d_info'] = $cdi_data['d_info'] = $cdiArr['e_info'] = $cdiArr['susp'] = $cdiArr['susp_days'] = 0;
            // }
            // dd($score);
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['student_id'] = $value->student_id;
            $tmp['confirmation_no'] = $value->confirmation_no;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['score'] = $score;

            $avgSum = $avgCnt = 0;

            foreach ($score as $sk => $sv) {
                $avgSum += $sv['grade'];
                $avgCnt++;
            }

            if ($avgCnt > 0) {
                $tmp['avgGrade'] = number_format($avgSum / $avgCnt, 2);
            } else {
                $tmp['avgGrade'] = 0;
            }

            // $tmp['cdi'] = $cdiArr;
            $tmp['grade'] = $value->next_grade;
            $tmp['created_at'] = $value->created_at;
            $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
            $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
            $tmp['birth_date'] = $value->birthday;


            $tmp['current_school'] = $value->current_school;
            $tmp['zoned_school'] = $value->zoned_school;
            $tmp['employee_name'] = $value->employee_first_name . " " . $value->employee_last_name;
            $tmp['race'] = $value->race;
            $tmp['address'] = $value->address;
            $tmp['city'] = $value->city;
            $tmp['state'] = $value->state;
            $tmp['zip'] = $value->zip;
            $tmp['awarded_school'] = $value->awarded_school;
            $student_data[] = $tmp;
        }

        view()->share('student_data', $student_data);
        view()->share('subjects', $subjects);
        view()->share('terms', $terms);
        view()->share("application_data", $application_data);
        // return view('GenerateApplicationData::pdfview');
        // return view('GenerateApplicationData::pdfview');
        $pdf = PDF::loadView('GenerateApplicationData::pdfview', ['student_data', 'terms', 'subjects', 'application_data']);

        $path = "resources/assets/admin/application_data";
        $fileName =  "ApplicationData-" . strtotime(date("Y-m-d H:i:s")) . '.' . 'pdf';
        $pdf->save($path . '/' . $fileName);

        $insert = array();
        $insert['enrollment_id'] = $enrollment_id;
        $insert['program'] = $program_id;
        $insert['grade'] = $grade;
        $insert['status'] = $status;
        $insert['total_records'] = count($student_data);
        $insert['file_name'] = $fileName;
        $id = GenerateApplicationDataGenerated::create($insert);


        return $pdf->download($fileName);
    }

    public function generateContractIndex()
    {
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $programs = Program::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->where('status', 'Y')->orderBy('name')->get();
        $grades = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('next_grade')->select(DB::raw("DISTINCT(next_grade)"))->get();
        $submission_status = Submissions::where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(submission_status)"))->get();


        return view("GenerateApplicationData::contract_index", compact('enrollment', 'programs', 'grades', 'submission_status'));
    }

    public function generateContract(Request $request)
    {
        $enrollment_id = $request['enrollment'];
        $grade = $request['grade'];
        $application_data = Application::where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->where("status", "Y")->first();

        $data = SubmissionsFinalStatus::where('contract_status', 'Signed')
            ->join('submissions', 'submissions_final_status.submission_id', 'submissions.id')
            ->join("application", "application.id", "submissions.application_id")
            ->select('submissions.*', "submissions_final_status.*", 'application.enrollment_id')
            ->where("submissions.district_id", Session::get('district_id'))
            ->where('submissions.submission_status', 'Offered and Accepted')
            ->where("application.enrollment_id", $enrollment_id);

        if ($request['awarded_program'] != '' && $request['awarded_program'] != "0") {
            $data->where('submissions.awarded_school', $request['awarded_program']);
        }

        if ($grade != 'All') {
            $data->where('submissions.next_grade', $grade);
        }
        $submissions = $data->limit(10)->get();
        $total_record = count($submissions->toArray());
        $student_data = array();

        view()->share('submissions', $submissions);
        view()->share("application_data", $application_data);
        $pdf = PDF::loadView('GenerateApplicationData::contract_sign', ['submissions', 'application_data']);

        $path = "resources/assets/admin/online_contract";
        $fileName =  "Contract_Offered_and_Accepted-" . strtotime(date("Y-m-d H:i:s")) . '.' . 'pdf';
        $pdf->save($path . '/' . $fileName);

        $insert = array();
        $insert['enrollment_id'] = $enrollment_id;
        $insert['grade'] = $grade;
        $insert['status'] = 'Offered and Accepted';
        $insert['total_records'] = $total_record;
        $insert['file_name'] = $fileName;
        $id = GenerateContractDataGenerated::create($insert);


        return $pdf->download($fileName);
    }

    public function existingContractData()
    {
        $data = GenerateContractDataGenerated::orderByDesc("contract_data_generated.created_at")->join("enrollments", "enrollments.id", "contract_data_generated.enrollment_id")->select('contract_data_generated.*', 'enrollments.school_year')->limit(10)->get();
        return view("GenerateApplicationData::contract_generated", compact("data"));
    }

    public function downloadContractFile($id)
    {
        $data = GenerateContractDataGenerated::where("id", $id)->first();
        if (!empty($data)) {
            $file_path = 'resources/assets/admin/online_contract/' . $data->file_name;
            $headers = array(
                'Content-Type: application/pdf',
            );

            return Response::download($file_path, $data->file_name, $headers);
        }
    }


    public function allGeneratedFormsIndex()
    {
        $rs = Application::where("enrollment_id", Session::get("enrollment_id"))->select("id")->get()->toArray();
        $avail_program_ary = \App\Modules\Program\Models\ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->whereIn("eligibility_template.name", ["Recommendation Form", "Writing Prompt"])->where('program_eligibility.status', 'Y')->whereIn("program_eligibility.application_id", $rs)->pluck('program_id')->toArray();

        $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))
            ->where(function ($q) use ($avail_program_ary) {
                $q->whereIn('first_choice_program_id', $avail_program_ary);
                $q->orWhereIn('second_choice_program_id', $avail_program_ary);
            })->whereIn('submission_status', array('Active', 'Pending'))->where('enrollment_id', Session::get('enrollment_id'));

        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();

        $programs = Program::where("district_id", Session::get('district_id'))
            ->where('enrollment_id', Session::get("enrollment_id"))
            ->whereIn('id', $avail_program_ary)
            ->where('status', 'Y')
            ->orderBy('name')
            ->get();

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $grades = $submissions->clone()->orderByRaw('FIELD(submissions.next_grade,' . implode(",", $ids) . ')')->select(DB::raw("DISTINCT(next_grade)"))->get();

        $submission_status = $submissions->clone()->select(DB::raw("DISTINCT(submission_status)"))->orderBy('submission_status')->get();

        return view("GenerateApplicationData::generated_form_index", compact('enrollment', 'programs', 'grades', 'submission_status'));
    }

    public function exportAllGeneratedFormsIndex(Request $request)
    {
        $program_id = $request['program_id'];
        $enrollment_id = $request['enrollment'];
        $grade = $request['grade'];
        $status = $request['status'];
        $submitted_form = $request['submitted_form'];


        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->join("application", "application.id", "submissions.application_id")
            ->where("application.enrollment_id", $enrollment_id);

        if ($submitted_form == 'recommendation_form') {
            $data = $data->join("submission_recommendation", "submission_recommendation.submission_id", "submissions.id")
                ->select("submissions.*", "submission_recommendation.*");
        } else {
            $data = $data->join('writing_prompt', 'writing_prompt.submission_id', 'submissions.id')
                ->select("submissions.*", 'writing_prompt.*');
        }

        if ($program_id != '0' && $program_id != '') {
            $data->where(function ($q) use ($program_id) {
                $q->where('first_choice_program_id', $program_id);
                $q->orWhere('second_choice_program_id', $program_id);
            });
        }

        if ($grade != 'All') {
            $data->where('next_grade', $grade);
        }
        if ($status != 'All') {
            $data->where('submission_status', $status);
        }
        $final_data = $data->get();


        if ($submitted_form == 'recommendation_form') {
            $pdf = PDF::loadView('GenerateApplicationData::all_recommendation_form_pdf', ['final_data' => $final_data]);
            $fileName =  "AllRecommendationForm-" . strtotime(date("Y-m-d H:i:s")) . '.pdf';
        } else {
            foreach ($final_data as $key => $wp) {

                $wp_detail = \App\Modules\WritingPrompt\Models\WritingPromptDetail::where('wp_id', $wp->id)->get();
                if (!empty($wp_detail)) {
                    $final_data[$key]['writing_prompt_detail'] = $wp_detail;
                }
            }

            $pdf = PDF::loadView('GenerateApplicationData::all_writing_prompt_pdf', ['final_data' => $final_data]);
            $fileName =  "AllWritingPrompt-" . strtotime(date("Y-m-d H:i:s")) . '.pdf';
        }
        return $pdf->download($fileName);
    }
}
