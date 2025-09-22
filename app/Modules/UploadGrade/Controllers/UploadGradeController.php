<?php

namespace App\Modules\UploadGrade\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Submissions\Models\Submissions;
use App\Modules\UploadGrade\Models\Grade;
use App\Modules\Application\Models\ApplicationConfiguration;

class UploadGradeController extends Controller
{
    public $submission;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->submission = new Submissions();
    }
    public function index($application_id)
    {
        //echo 'eretedd';
        $data = ApplicationConfiguration::where("application_id", $application_id)->first();
        if (!empty($data))
            return view("UploadGrade::index", compact("data"));
        else
            return redirect(url('/'));
    }
    public function checkSubmissionId(Request $request)
    {
        $dob = $request->input('dob');
        $dob = date("Y-m-d", strtotime($dob));

        $submission_id = $request->input('submission_id');
        $submission_data = $this->submission->where(array('id' => $submission_id, 'birthday' => $dob))->get()->toArray();
        if (!empty($submission_data)) {
            if ($submission_data[0]['student_id'] != '')
                return "SID";
            else
                return $submission_data;
        } else
            return "comb";
    }

    public function uploadFiles(Request $req)
    {
        $submission_id = $req->input('submission_id');
        $dobdatepicker = $req->input('dobdatepicker');
        $application_id = $req->input("application_id");
        $grade_files = $req->file('grade_file');
        //echo $submission_id;exit;
        if ($grade_files != '') {
            $count = Grade::where('submission_id', $submission_id)->where('file_type', 'grade')->count();
            foreach ($grade_files as  $grade_file) {
                $count++;
                $extension = $grade_file->getClientOriginalExtension();
                if ($extension == 'pdf') {
                    $filenamewithext = $grade_file->getClientOriginalName();
                    $filename = pathinfo($filenamewithext, PATHINFO_FILENAME);

                    $fileNameToStore = $submission_id . "-" . $count . '.' . $extension;
                    $saveFileName = 'Custom Sub Module Image';
                    $path = $grade_file->move(resource_path('gradefiles/'), $fileNameToStore);
                    Grade::create(['submission_id' => $submission_id, 'file_name' => $fileNameToStore, 'file_type' => 'grade']);
                }
            }
        }

        // $cdi_files = $req->file('cdi_file');

        // if($cdi_files!=''){
        //     $count = Grade::where('submission_id', $submission_id)->where('file_type', 'cdi')->count();
        //     foreach ($cdi_files as  $cdi_file) {
        //         $count++;
        //         $extension = $cdi_file->getClientOriginalExtension();
        //         if($extension=='pdf'){
        //             $filenamewithext = $cdi_file->getClientOriginalName();
        //             $filename = pathinfo($filenamewithext,PATHINFO_FILENAME);

        //             //$fileNameToStore = $filename.'_'.time().'.'.$extension;
        //             $fileNameToStore = $submission_id."-".$count.'.'.$extension;
        //             //$saveFileName = 'Custom Sub Module Image';
        //             $path = $cdi_file->move(resource_path('cdifiles/'),$fileNameToStore);
        //             Grade::create(['submission_id'=>$submission_id,'file_name'=>$fileNameToStore,'file_type'=>'cdi']);

        //         }
        //     }
        // }

        $submission_data = $this->submission->where(array('id' => $submission_id))->first();
        $data = ApplicationConfiguration::where("application_id", $application_id)->first();

        $msg = "";
        if (!empty($data) && $data->grade_cdi_confirm_text != '') {
            $msg = $data->grade_cdi_confirm_text;
            $tmp = generateShortCode($submission_data);
            $msg = find_replace_string($msg, $tmp);
        }
        return view("UploadGrade::accept_contract", compact('submission_data', 'data', "msg"));
    }
}
