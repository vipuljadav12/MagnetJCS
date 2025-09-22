<?php

namespace App\Modules\Module\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Module\Models\Module;
use App\Modules\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{

    public $module;
    public $permission;
    public function __construct()
    {
        $this->module = new Module();
        $this->permission = new Permission();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $district_id = Session::get('district_id');
        $data['modules'] = $this->module->Index();
        return view("Module::index")->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('Module::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:modules',
        ];

        $message = [
            'name.required' => 'Module Name is required',
            'name.unique' => 'Module name must be unique',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            Session::flash('error', 'Plese fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            // return $request->except(['_token','save','save_exit']);
            // $request['district_id'] = Session::get('district_id');
            $module_id = $this->module::create($request->except(['_token', 'save', 'save_exit']))->id;

            Session::flash('success', 'Module created successfully.');

            if (isset($request['save'])) {
                return redirect('admin/Module/create');
            } else {
                return redirect('/admin/Module');
            }
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

        $data['module'] = $this->module->where('id', $id)->first();
        return view('Module::edit')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return $request;
        $rules = [
            'name' => 'required|unique:modules,id,' . $request->id,
        ];

        $message = [
            'name.required' => 'Module Name is required',
            'name.unique' => 'Module name must be unique',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            Session::flash('error', 'Plese fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            $this->module::where('id', $request->id)->update($request->except(['_token', 'save', 'logo', 'save_exit']));

            Session::flash('success', 'Module updated successfully.');

            if (isset($request['save'])) {
                return redirect('admin/Module/edit/' . $request->id);
            } else {
                return redirect('/admin/Module');
            }
        }
    }

    public function trash()
    {
        $data['modules'] = $this->module::onlyTrashed()->get();
        // return $data;
        return view('Module::trash')->with('data', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $module = $this->module->where('id',$id)->onlyTrashed()->first();
    //     if(!empty($module)){
    //         $module->forceDelete();
    //         $flag = 'true';
    //     // return $module;
    //     }
    //     \Session::flash('warning','Module has been deleted');
    //     return redirect()->back();
    // }

    public function destroy($id)
    {
        /*$modules = $this->module->get();
        foreach ($modules as $key => $value) 
        {
            $value->forceDelete();
        }
        return $id;*/
        $permission = $this->permission->where('module_id', $id)->first();
        if (isset($permission->id)) {
            // Session::flash('error','Please remove usertype\'s this permissions'); 
            Session::flash('error', 'Permission assigned to module, Please remove user permission first');
        } else {

            $module = $this->module->where('id', $id)->onlyTrashed()->first();
            if (!empty($module)) {
                $module->forceDelete();
                $flag = 'true';
                Session::flash('success', 'module has been deleted');
            }
            return redirect()->back();
            // return $id;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function delete($id)
    // {
    //     $module = $this->module::where('id',$id)->first();
    //     if(!empty($module)){
    //         $module->delete();
    //     // return $module;
    //         $flag = 'true';
    //     }
    //     \Session::flash('warning','Module has been moved to trash');
    //     return redirect()->back();
    // }

    public function delete($id)
    {
        $permission = $this->permission->where('module_id', $id)->first();
        if (isset($permission->id)) {
            Session::flash('error', 'Permission assigned to module, Please remove user permission first');
        } else {
            $data = ['status' => 'T'];
            $this->module::where('id', $id)->delete();
            // $this->module::where('id', $id)->update($data);
            Session::flash('success', 'Module has been moved to trash.');
        }

        return redirect()->back();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $module = $this->module::where('id', $id)->onlyTrashed()->first();
        if (!empty($module)) {
            $module->restore();
            // return $module;
            $flag = 'true';
        }
        Session::flash('success', 'Module has been Restored');
        return redirect('/admin/Module');
    }
}
