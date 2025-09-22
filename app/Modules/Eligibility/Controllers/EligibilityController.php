<?php

namespace App\Modules\Eligibility\Controllers;

use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Eligibility\Models\EligibilityContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Eligibility\Models\SubjectManagement;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class EligibilityController extends Controller
{
    use AuditTrail;
    protected $url;
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
    public function index()
    {
        // dd(Session::get('district_id'));
        $eligibilityTemplates = EligibilityTemplate::where('status', '!=', 'T')->get()->keyBy("id");
        if (Session::get("district_id") != "0")
            $eligibilities = Eligibility::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->orderBy('status', 'asc')->get();
        else
            $eligibilities = Eligibility::where('status', '!=', 'T')->where('enrollment_id', Session::get('enrollment_id'))->get();        //print_r($eligibilities);exit;
        return view("Eligibility::index1", compact('eligibilities', 'eligibilityTemplates'));
    }
    public function create()
    {
        $eligibilityTemplates = EligibilityTemplate::where("status", "Y")->get();
        // return $this->url;
        return  view('Eligibility::create', compact('eligibilityTemplates'));
    }
    public function store(Request $request)
    {
        // return $request;
        $validateData = $request->validate([
            'name' => 'required|max:255',
        ]);
        $data['name'] = $request["name"];
        $data['store_for'] = $request["store_for"];
        $data['district_id'] = Session::get("district_id");
        $data['enrollment_id'] = Session::get("enrollment_id");
        $data['alias_name'] = $request["alias_name"];

        if (isset($request['submit-from']) && $request['submit-from'] == "recommendation") {
            $data['template_id'] = 11;
        } else {
            $data['template_id'] = $request["template"];
        }
        $data['override'] = $request->override == 'on' ? 'Y' : 'N';
        // return $request;
        // return $data;
        $eligibility = Eligibility::create($data);
        $dataContent['eligibility_id'] = $eligibility->id;
        $dataContent['content'] = json_encode($request['extra'], true);
        $eligibilityContent = EligibilityContent::create($dataContent);
        if (isset($eligibilityContent) && isset($eligibility)) {
            Session::flash("success", "Eligibility Added successfully");
        } else {
            Session::flash("error", "Something went wrong please try again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Eligibility');
        } else {
            return redirect("admin/Eligibility/edit/" . $eligibility->id);
        }
    }
    public function show($id)
    {
        //
    }
    public function edit(Eligibility $eligibility)
    {
        // return $eligibility;    
        $eligibilityTemplates = EligibilityTemplate::where("status", "Y")->get()->keyBy("id");
        $eligibilityContent = EligibilityContent::where("eligibility_id", $eligibility->id)->first();

        $data = array();
        $data['grades'] = Grade::all();
        $data['subjects'] = Config::get('variables.courseType');
        $data['subjectManagement'] = SubjectManagement::get()->keyBy('grade');
        //return view("Eligibility::subject_management",compact('data'));
        // dd($data['subjectManagement']);
        // return  json_decode($eligibilityContent->content,1);
        return view('Eligibility::edit', compact("eligibility", "eligibilityContent", "eligibilityTemplates", 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request;


        $validateData = $request->validate([
            'name' => 'required|max:255'
        ]);
        $data['name'] = $request["name"];
        $data['store_for'] = $request["store_for"];
        $data['alias_name'] = $request["alias_name"];
        //$data['district_id'] = 1;
        /*if(isset($request['submit-from']) && $request['submit-from'] == "recommendation")
        {
            $data['template_id'] = 0;
        }
        else
        {
            $data['template_id'] = $request["template"];
        }*/
        // return $data;
        $data['override'] = $request['override'] == 'on' ? 'Y' : 'N';

        $initObj = Eligibility::where('id', $id)->first();
        $eligibility = Eligibility::where('id', $id)->update($data);
        $newObj = Eligibility::where('id', $id)->first();

        $dataContent['eligibility_id'] = $id;
        $dataContent['content'] = json_encode($request['extra'], true);
        EligibilityContent::where('eligibility_id', $id)->delete();
        $eligibilityContent = EligibilityContent::create($dataContent);
        if (isset($eligibilityContent) && isset($eligibility)) {
            $el_content = EligibilityContent::where('id', $eligibilityContent->id)->first();
            $this->modelCreate($el_content, "eligibility_content");
            $this->modelChanges($initObj, $newObj, "eligibility");

            Session::flash("success", "Eligibility Updated successfully");
        } else {
            Session::flash("error", "Something went wrong please try again.");
        }

        if (isset($request->gradeSubject)) {
            foreach ($request->gradeSubject as $key => $cgrade) {
                SubjectManagement::updateOrCreate(['grade' => $key], $cgrade);
            }
        }

        if (isset($request['gradeYear'])) {
            foreach ($request->gradeYear as $key => $year) {
                $yearData = implode(',', $year);
                // dd($yearData);
                SubjectManagement::updateOrCreate(['grade' => $key], ['year' => $yearData]);
            }
        }

        /*if(isset($request["submit"]) && $request["submit"] == "edit")
        {
            return redirect()->back();
        }*/
        if (isset($request->save_exit)) {
            return redirect('admin/Eligibility');
        } else {
            return redirect()->back();
        }
        return redirect('admin/Eligibility');
    }
    public function getTemplateHtml(Request $request, EligibilityTemplate $id)
    {
        return view("Eligibility::templates." . $id->content_html);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
        $result = Eligibility::where('id', $id)->update(['status' => 'T', 'updated_at' => date("Y-m-d h:m:s", time())]);
        if (isset($result)) {
            Session::flash('success', "Eligibility Data Move into Trash successfully.");
        } else {
            Session::flash('error', 'Please try again');
        }
        return  redirect('admin/Eligibility');
    }
    public function trash()
    {
        $eligibilities = Eligibility::where('status', 'T')->get();
        return view("Eligibility::trash", compact('eligibilities'));
    }
    public function restore($id)
    {
        $result = Eligibility::where('id', $id)->update(['status' => 'Y', 'updated_at' => date("Y-m-d h:m:s", time())]);
        if (isset($result)) {
            Session::flash('success', "Eligibility Data restore successfully.");
        } else {
            Session::flash('error', 'Please try again');
        }
        return redirect('admin/Eligibility');
    }
    public function status(Request $request)
    {
        $result = Eligibility::where('id', $request->id)->update(['status' => $request->status]);
        // $result=EligibilityTemplate::where('id',$request->id)->update(['status'=> $request->status]);
        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }
    public function view($id)
    {
        $eligibility = Eligibility::join('eligibility_content', 'eligibility_content.eligibility_id', '=', 'eligibiility.id')
            ->where('eligibiility.id', $id)
            ->select('eligibiility.*', 'eligibility_content.content')
            ->first();
        // return $eligibility;
        $eligibilityTemplate = EligibilityTemplate::where('id', $eligibility->template_id)->first();
        return view('Eligibility::view', compact('eligibility', 'eligibilityTemplate'));
    }


    public function checkEligiblityUnique(Request $request)
    {
        $req = $request->all();

        if (isset($req['id'])) {
            $eligibility = Eligibility::where('name', urldecode($req['name']))->where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->where("id", "!=", $req['id'])->first();
        } else {
            $eligibility = Eligibility::where('name', urldecode($req['name']))->where("district_id", Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->first();
        }

        if (!empty($eligibility))
            return "false";
        else
            return "true";
    }

    public function editGradeSubject()
    {
        $data['grades'] = Grade::all();
        $data['subjects'] = Config::get('variables.courseType');
        $data['subjectManagement'] = SubjectManagement::get()->keyBy('grade');
        return view("Eligibility::subject_management", compact('data'));
    }

    public function storeGradeSubject(Request $request)
    {
        if (isset($request->gradeSubject)) {
            foreach ($request->gradeSubject as $key => $cgrade) {
                SubjectManagement::updateOrCreate(['grade' => $key], $cgrade);
            }
        }
        Session::flash('success', 'Subject update successfully.');
        if (isset($request->save_edit))
            return redirect('admin/Eligibility/edit/grade/subject');
        return redirect('admin/Eligibility');
    }
}
