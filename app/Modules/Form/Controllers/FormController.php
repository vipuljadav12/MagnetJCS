<?php

namespace App\Modules\Form\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Form\Models\Form;
use App\Modules\Form\Models\FormFields;
use App\Modules\Form\Models\FormBuild;
use App\Modules\Form\Models\FormContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FormController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Session::get("district_id") != '0') {
            $forms = Form::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->get();
        } else {
            $forms = Form::where('status', '!=', 'T')->get();
        }
        return view("Form::index", compact('forms'));
    }
    public function getField(Request $request)
    {
        $form_id = $request['form_id'];
        $field = FormFields::where('id', $request['id'])->first();
        return view("Form::fields." . $field->type, compact("field", "form_id"));
    }
    public function saveBuild(Request $req)
    {
        return $req;
    }
    public function insertField(Request $req)
    {
        // $insert = array("form_id"=>$req["form_id"]);
        $req = $req->all();
        $last = FormBuild::where("form_id", $req['form_id'])->where("page_id", $req['page_id'])->orderBy("sort", "DESC")->pluck("sort")->first();
        $req['sort'] = isset($last) ? $last + 1 : 1;
        $result = FormBuild::create($req);
        if (isset($result)) {
            return "true";
        } else {
            return "false";
        }
    }
    public function removeOption(FormContent $option)
    {
        if (isset($option)) {
            $option->delete();
            return "true";
        }
    }
    public function getFormContent(Request $req)
    {
        // $form_id = $req['form_id'];
        // return $req;
        $formContents = FormBuild::where("form_id", $req["form_id"])->where('page_id', $req['page_id'])->orderBy("sort")->get();
        // return $formContents;
        return view("Form::formcontent", compact("formContents"));
    }
    public function removeField(FormBuild $build)
    {
        if (isset($build)) {
            $build->delete();
            return "true";
        }
    }
    public function fieldEditor(FormBuild $build)
    {
        // return $build;
        $content = FormContent::where("build_id", $build->id)->get();
        $field = FormFields::where('id', $build->field_id)->first();
        return view("Form::editor." . $field->type, compact("field", "build", "content"));
    }
    public function saveSingle(Request $req)
    {
        $checkExist = FormContent::where("build_id", $req["build_id"])->where("form_id", $req['form_id'])->where("field_property", $req["field_property"])->first();
        if (isset($checkExist) && !empty($checkExist)) {
            $checkExist->update(array("sort_option" => $req['sort_option'], "field_value" => $req["field_value"]));
        } else {
            $result = FormContent::create($req->except("_token"));
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $formFields = FormFields::all();
        // return $formFields;
        return view("Form::create", compact("formFields"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $currentdate = date("Y-m-d h:m:s", time());
        $msg = ['to_mail.regex' => 'The Email address is not valid', 'thank_you_msg.required' => 'The thnk you message field is required'];
        $request->validate([
            'name' => 'required|max:255',
            'no_of_pages' => 'required|numeric',
            'confirmation_style' => 'required|max:10'

            /*'url'=>'required|max:255|unique:form',
            'description'=>'required|max:500',
            'thank_you_url'=>'required|max:255',
            'thank_you_msg'=>'required|max:500',
            'to_mail'=>['required','regex:/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/','max:255'],*/
        ], $msg);
        $data = [
            'district_id' => Session::get('district_id'),
            'name' => $request->name,
            'url' => $request->url,
            'description' => $request->description,
            'thank_you_url' => $request->thank_you_url,
            'thank_you_msg' => $request->thank_you_msg,
            'to_mail' => $request->to_mail,
            'show_logo' => $request->show_logo == 'on' ? 'y' : 'n',
            'captcha' => $request->captcha == 'on' ? 'y' : 'n',
            'form_source_code' => $request->form_source_code,
            'no_of_pages' => $request->no_of_pages,
            'created_at' => $currentdate,
            'updated_at' => $currentdate,
        ];
        $result = Form::create($data);
        if (isset($result)) {
            Session::flash('success', 'Form data store successfully');
        } else {
            Session::flash('error', 'Please try again');
        }

        if (isset($request->save_exit)) {
            return redirect('admin/Form');
        }
        return redirect('admin/Form/edit/1/' . $result->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($page_id, $id)
    {
        $form = Form::where('id', $id)->first();
        $formFields = FormFields::all();
        if (isset($form)) {
            return view('Form::edit', compact('form', "formFields", "page_id"));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $page_id, $id)
    {
        //
        //        return $request;
        $currentdate = date("Y-m-d h:m:s", time());
        $msg = ['to_mail.regex' => 'The Email address is not valid', 'thank_you_msg.required' => 'The thank you message field is required'];
        $request->validate([
            'name' => 'required|max:255',
            'no_of_pages' => 'required|numeric',
            'confirmation_style' => 'required|max:10'
            /*'url'=>'required|max:255',
            'description'=>'required|max:500',
            'thank_you_url'=>'required|max:255',
            'thank_you_msg'=>'required|max:500',
            'to_mail'=>['required','regex:/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/','max:255'],*/
        ], $msg);
        $data = [
            'name' => $request->name,
            'url' => $request->url,
            'district_id' => Session::get('district_id'),
            'description' => $request->description,
            'confirmation_style' => $request->confirmation_style,
            'thank_you_url' => $request->thank_you_url,
            'thank_you_msg' => $request->thank_you_msg,
            'to_mail' => $request->to_mail,
            'show_logo' => $request->show_logo == 'on' ? 'y' : 'n',
            'captcha' => $request->captcha == 'on' ? 'y' : 'n',
            'form_source_code' => $request->form_source_code,
            'no_of_pages' => $request->no_of_pages,
            'updated_at' => $currentdate,
        ];
        // return $data;
        $result = Form::where('id', $id)->update($data);
        if (isset($result)) {
            // return;
            Session::flash('success', 'Form data updated successfully');
        } else {
            Session::flash('error', 'Please try again');
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Form');
        }
        return redirect('admin/Form/edit/' . $page_id . '/' . $id);
    }
    public function delete($id)
    {
        $currentdate = date("Y-m-d h:m:s", time());
        $result = Form::where('id', $id)->update(['status' => 'T', 'updated_at' => $currentdate]);
        if (isset($result)) {
            Session::flash('success', 'Form data move into trash successfully');
        } else {
            Session::flash('error', 'Please try again');
        }
        return redirect('admin/Form');
    }

    public function trash()
    {
        if (Session::get("district_id") != '0') {
            $forms = Form::where('status', 'T')->where('district_id', Session::get('district_id'))->get();
        } else {
            $forms = Form::where('status', 'T')->get();
        }
        // return $forms;
        return view("Form::trash", compact('forms'));
    }
    public function restore($id)
    {
        $currentdate = date("Y-m-d h:m:s", time());
        $result = Form::where('id', $id)->update(['status' => 'Y', 'updated_at' => $currentdate]);
        if (isset($result)) {
            Session::flash('success', 'Form data restore successfully');
        } else {
            Session::flash('error', 'Please try again');
        }
        return redirect('admin/Form');
    }
    public function status(Request $request)
    {
        //        return $request;
        $currentdate = date("Y-m-d h:m:s", time());
        $result = Form::where('id', $request->id)->update(['status' => $request->status, 'updated_at' => $currentdate]);
        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }
    public function uniqueurl(Request $request)
    {
        $result = Form::where('id', '!=', $request->id)->where('url', $request->url)->first();
        // return json_encode($result);
        if (isset($result)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }
    public function changeTitle(Request $req, $form_id, $page_id)
    {
        $exist = DB::table("form_page")->where("form_id", $form_id)->where("page_id", $page_id)->first();
        // dd( $exist);
        if (isset($exist->id)) {
            // $exist->page_title = $req['page_title'];
            // $exist->update();
            $result = DB::table("form_page")->where("form_id", $form_id)->where("page_id", $page_id)->update(array("page_title" => $req['page_title']));
        } else {
            $result = DB::table("form_page")->insert(array("page_title" => $req['page_title'], "form_id" => $form_id, "page_id" => $page_id));
        }
        return "true";
    }
    public function registerSort(Request $req)
    {
        if (isset($req['data']) && !empty($req['data'])) {
            $data = $req['data'];
            $data = array_filter($data);
            foreach ($data as $k => $v) {
                FormBuild::where("id", $v)->update(['sort' => $k]);
            }
            return "true";
        }
    }
}
