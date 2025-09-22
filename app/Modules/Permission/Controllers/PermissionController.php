<?php

namespace App\Modules\Permission\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Module\Models\Module;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Modules\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PermissionController extends Controller
{
    protected $permission;
    protected $module;
    public function __construct()
    {
        $this->permission = new Permission();
        $this->module = new Module();
    }

    public function index()
    {
        // return "here";
        $permission = $this->permission::where('status', 'Y')->get();
        return view('Permission::index', ['permission' => $permission]);
    }

    public function create()
    {
        $data['module'] = $this->module->Index();
        return view("Permission::create")->with('data', $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'slug' => 'required|unique:permissions,slug',
            'display_name' => 'required',
            'module_id' => 'required',
        );
        $message = array(
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug is already exist.',
            'display_name.required' => 'Display Name is required.',
            'module_id.required' => 'Module Name is required.',
        );

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Something is wrong.');
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            $permission_id = $this->permission::create($request->except(['_token', 'save', 'save_exit']));
            Session::flash('success', 'Permission created successfully.');
            if (isset($request['save'])) {
                return redirect('admin/Permission/create');
            } else {
                return redirect('admin/Permission');
            }
        }
    }

    public function edit($id)
    {
        $data['permission'] = $this->permission::where('id', $id)->first();
        $data['module']     = $this->module->Index();
        return view('Permission::edit', ['data' => $data,]);
    }

    public function update(Request $request, $id)
    {
        $rules = array(
            'slug' => 'required|unique:permissions,slug,' . $id . ',id',
            'display_name' => 'required',
            'module_id' => 'required',
        );
        $message = array(
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug is already exist.',
            'display_name.required' => 'Display Name is required.',
            'module_id.required' => 'Module Name is required.',
        );
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Something is wrong.');
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            // return $request->except(['_token','save','save_exit']);
            $this->permission::where('id', $id)->update($request->except(['_token', 'save', 'save_exit']));
            Session::flash('success', 'Permission update successfully.');
            if (isset($req['save'])) {
                return redirect('admin/Permission/edit/' . $id);
            } else {
                return redirect('/admin/Permission');
            }
        }
    }
    public function trash($id)
    {
        $roles_permissions = DB::table('roles_permissions')->where('permission_id', $id)->first();
        if (isset($roles_permissions->id)) {
            // Session::flash('error','Please remove usertype\'s this permissions'); 
            Session::flash('error', 'Permission assigned to Users, Please remove user permission first');
        } else {
            $data = ['status' => 'T'];
            $this->permission::where('id', $id)->update($data);
            Session::flash('success', 'Permission moved to trash.');
        }
        /*if(!empty($roles_permissions->toArray())){
           Session::flash('error','Please remove usertype\'s this permissions'); 
        }else{
            $data = ['status'=>'T'];
            $this->permission::where('id', $id)->update($data);
            Session::flash('success','Permission moved to trash.');
            
        }*/
        // return 'Success';
        return redirect()->back();
    }
    public function trashIndex()
    {
        $data = $this->permission::where('status', 'T')->orderBy('created_at', 'DESC')->get();
        return view('Permission::trash')->with('permission', $data);
    }

    public function delete($id)
    {
        $this->permission::where('id', $id)->delete();
        Session::flash('success', 'Permission deleted successfully.');
        // return 'Success';
        return redirect()->back();
    }

    public function permissionCreate(Request $request)
    {
        $permission_ids = [];
        if (isset($request['module'])) {
            foreach ($request['module'] as $key => $value) {
                $module = $this->module::where('id', $key)->first();
                if (!empty($module)) {
                    foreach ($value as $key => $slug) {
                        $data['slug'] = $module->slug . '/' . $key;
                        $data['module_id'] = $module->id;
                        $data['display_name'] = ucfirst($key);
                        $permission = $this->permission::where('module_id', $module->id)->where('slug', $data['slug'])->first();
                        if (empty($permission)) {
                            $data['created_at'] = date('Y-m-d H:i:s');
                            $permission_ids[] = $this->permission::insertGetId($data);
                        }
                    }
                }
            }
        }
        return $permission_ids;
    }

    public function restore($id)
    {
        $data = [
            'status' => 'Y'
        ];
        $this->permission::where('id', $id)->update($data);
        Session::flash('success', 'Permission restored successfully.');
        return redirect('admin/Permission/trashindex');
    }
}
