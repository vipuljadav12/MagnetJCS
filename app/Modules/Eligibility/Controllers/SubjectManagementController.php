<?php

namespace App\Modules\Eligibility\Controllers;

use App\Modules\Application\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Eligibility\Models\SubjectManagement;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SubjectManagementController extends Controller
{
    use AuditTrail;
    public $url;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->url = url("admin/Eligibility");
        View::share(["module_url" => $this->url]);
    }
    public function index($id = '')
    {
        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $data = [];

        if ($id != '') {
            $data['grades'] = Grade::all();
            $data['subjects'] = Config::get('variables.courseType');

            $data['subjectManagement'] = SubjectManagement::where('application_id', $id)->get()->keyBy('grade');
        }

        return view("Eligibility::SubjectManagement.subject_management", compact('data', 'id', 'applications'));
    }


    public function updateSubjectManagement(Request $request)
    {
        // return $request;
        if (isset($request->gradeSubject)) {
            foreach ($request->gradeSubject as $key => $cgrade) {
                $data = [];
                $data['application_id'] = $request->application_id;
                $data['grade'] = $key;
                $data = array_merge($data, $cgrade);
                SubjectManagement::updateOrCreate(['grade' => $key, 'application_id' => $request->application_id], $data);
            }
        }
        Session::flash('success', 'Grade/Subjects requirements updated successfully.');
        if (!isset($request->save_exit))
            return redirect('admin/Eligibility/subjectManagement/' . $request->application_id);
        return redirect('admin/Eligibility/subjectManagement');
    }
}
