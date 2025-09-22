<?php

namespace App\Modules\School\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\School\Models\School;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Session;

class SchoolController extends Controller
{
    use AuditTrail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schools = School::select('school.*')

            ->whereNotIn('school.status', ['T'])
            //->orderBy('grade','asc')
            ->get();
        // return $schools;
        return view("School::index", compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grades = Grade::all();
        return view("School::create", compact('grades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:255|unique:school',
            'grade' => 'required',
            'magnet' => 'required',
            //'zoning_api_name' => 'required|max:255',
            //'sis_name' => 'required|max:255',
        ]);
        $data['name'] = $request["name"];
        $data['grade_id'] =  implode(',', $request->grade);
        $data['magnet'] = $request["magnet"];
        $data['zoning_api_name'] = $request["zoning_api_name"];
        $data['sis_name'] = $request["sis_name"];
        $data['district_id'] = Session::get("district_id");

        $school = School::create($data);

        if (isset($school)) {
            Session::flash("success", "School Data Added successfully");
        } else {
            Session::flash("error", "Something went wrong please try again.");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/School');
        } else {
            return redirect("admin/School/edit/" . $school->id);
        }
        // return redirect('admin/School');
    }

    public function unique(Request $request)
    {
        $school = School::where('id', '!=', $request->id)->where('name', $request->name)->first();
        if (isset($school)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $school = School::where('id', $id)->first();
        $grades = Grade::all();
        $list = explode(',', $school->grade_id);
        //print_r($list);exit;

        return view("School::edit", compact('school', 'grades', 'list'));
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
        $validate = $request->validate([
            'name' => 'required|max:255', //|unique:school,name,'.$id,
            //'grade_id' => 'required',
            'magnet' => 'required',
            // 'zoning_api_name' => 'required|max:255',
            // 'sis_name' => 'required|max:255',
        ]);
        $data['name'] = $request["name"];
        $data['grade_id'] =  implode(',', $request->grade);
        $data['magnet'] = $request["magnet"];
        $data['zoning_api_name'] = $request["zoning_api_name"];
        $data['sis_name'] = $request["sis_name"];
        $data['district_id'] = Session::get("district_id");

        $initObj = School::where('id', $id)->first();
        $school = School::where('id', $id)->update($data);
        $newObj = School::where('id', $id)->first();

        $this->modelChanges($initObj, $newObj, "School");

        if (isset($school)) {
            Session::flash("success", "School Data Upadated successfully");
        } else {
            Session::flash("error", "Something went wrong please try again.");
        }
        return redirect('admin/School');
    }

    public function changeStatus(Request $request)
    {
        $school = School::where('id', $request->id)->update(['status' => $request->status]);
        if (isset($school)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }
    public function trash()
    {
        $schools = School::select('school.*')
            ->whereIn('school.status', ['T'])
            ->get();
        return view("School::trash", compact('schools'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $school = School::where('id', $id)->update(['status' => 'T', 'updated_at' => date("Y-m-d h:m:s", time())]);
        if (isset($school)) {
            Session::flash('success', "School Data Move into Trash successfully.");
        } else {
            Session::flash('error', 'Please try again');
        }
        return  redirect('admin/School');
    }

    public function restore($id)
    {
        $school = School::where('id', $id)->update(['status' => 'Y', 'updated_at' => date("Y-m-d h:m:s", time())]);
        if (isset($school)) {
            Session::flash('success', "School Data restore successfully.");
        } else {
            Session::flash('error', 'Please try again');
        }
        return  redirect('admin/School/trash');
    }
}
