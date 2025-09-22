<?php

namespace App\Modules\Role\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\Role\Models\Role;
use App\Http\Controllers\Controller;
use App\Modules\Module\Models\Module;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Modules\Permission\Models\Permission;
use App\Modules\Permission\Models\PermissionModule;
use App\Modules\Permission\Models\RolePermission;
use App\Modules\Permission\Controllers\PermissionController;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    protected $permission;
    protected $role;
    protected $user;
    protected $module;
    protected $permissionController;

    public function __construct()
    {
        $this->permission = new Permission();
        $this->role = new Role();
        $this->user = new User();
        $this->module = new Module();
        $this->permissionController = new PermissionController();
    }


    public function index()
    {
        $roles = $this->role->Index();
        return view("Role::index")->with('roles', $roles);
    }

    public function create()
    {
        // $data['modules'] = $this->module->Index();
        $data['permission'] = $this->permission::where('status', 'Y')->get();
        $data['modules'] = PermissionModule::where('status', 'Y')->orderBy('sort')->get();
        $data['display_name'] = [];
        foreach ($data['modules'] as $key => $module) {
            $data['display_name'][$module->name] = $this->permission->where('display_name', $module->name)->where('status', 'Y')->pluck('module_id', 'id')->toArray();
        }
        foreach ($data['display_name'] as $k => $v) {
            $data['display_name_2'][$k] = $this->filterDisplayOrder($v);
        }
        $data["display_name"] = $data["display_name_2"];
        // return $data;
        return view("Role::create")->with('data', $data);
    }


    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|unique:roles,name',
        );
        $message = array(
            'name.required' => 'User Role is required.',
            'name.unique' => 'User Role is already exists.',
        );
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Something is wrong.');
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            // return $request;

            $role_id = $this->role::create($request->only(['name']))->id;

            $this->createRolesPermission($role_id, $request);


            Session::flash('success', 'User Role created successfully.');
            if (isset($request['save'])) {
                return redirect('admin/Role/create');
            } else {
                return redirect('/admin/Role');
            }
        }
    }

    public function createRolesPermission($role_id, $request)
    {
        $roles_permissions = RolePermission::where('role_id', $role_id)->get();

        if ($roles_permissions->count() != 0) {
            RolePermission::where('role_id', $role_id)->delete();
        }
        if (isset($request['permission'])) {
            foreach ($request['permission'] as $key => $value) {
                RolePermission::insert(['role_id' => $role_id, 'permission_id' => $key]);
            }
        }

        return 'true';
    }

    public function getRolesPermssion($role_id = '', $slug = '')
    {
        $role = [];
        if ($role_id != '' && $slug != '') {
            $role = $this->role::join('roles_permissions', 'roles.id', 'roles_permissions.role_id')
                ->join('permissions', 'permissions.id', 'roles_permissions.permission_id')
                ->where('permissions.slug', $slug)->where('roles.id', $role_id)
                ->get();
        }
        return $role;
    }

    public function edit($id)
    {
        $data['role']   = $this->role::find($id);
        $data['roles_permissions'] = RolePermission::where('role_id', $id)->pluck('permission_id');
        $data['modules'] = PermissionModule::where('status', 'Y')->orderBy('sort')->get();
        $permission_id = array();
        if ($data['roles_permissions']->count() > 0) {
            $data['permission_id'] = $data['roles_permissions']->toArray();
        }
        $data['permission'] = $this->permission::where('status', 'Y')->orderBy('sort')->get();
        $data['display_name'] = array();
        // return $data['permission'];
        // return $data;
        foreach ($data['modules'] as $key => $module) {
            // $data['display_name'][$module->name] = $this->permission->where('display_name',$module->name)->where('status','Y')->pluck('module_id','id')->toArray();
            $data['display_name'][$module->name] = $this->permission->where('display_name', $module->name)->where('status', 'Y')->pluck('module_id', 'id')->toArray();
        }
        // return $data;
        foreach ($data['display_name'] as $k => $v) {
            $data['display_name_2'][$k] = $this->filterDisplayOrder($v);
        }
        $data["display_name"] = $data["display_name_2"];
        // return $data;
        // return $data;
        /*foreach ($data['permission'] as $key => $permission) {
            $data['display_name'][$permission->modules[0]->name] = $this->permission->where('module_id',$permission->module_id)->where('status','Y')->pluck('display_name','id')->toArray();
            
        }*/
        return view("Role::edit", ['data' => $data]);
    }
    public function filterDisplayOrder($arr)
    {
        // return $arr;
        $sort_order = Module::orderBy("sort", "ASC")->get()->pluck("id", "sort")->toArray();
        // return $sort_order;
        // return array_diff($sort_order, $arr);
        /*foreach ($sort_order as $key => $value) 
        {
            if(in_array($value, $arr))
            {            
                $newArr[$key] = $value;
            }
        }*/
        foreach ($sort_order as $j => $s) {
            foreach ($arr as $key => $value) {
                if ($s == $value) {
                    // $newArr[$key] = $values;
                    $newArr[$key] = $s;
                }
            }
        }
        return $newArr;
        $ordered_array = array_replace(array_flip($sort_order), $arr);
        return $ordered_array;
    }
    public function update(Request $request)
    {
        // return $request;
        $rules = array(
            'name' => 'required|unique:roles,id,' . $request->id,
        );
        $message = array(
            'name.required' => 'User Role is required.',
            'name.unique' => 'User Role is already exists.',
        );
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Something is wrong.');
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $this->role::where('id', $request->id)->update(['name' => $request->name]);

            $this->createRolesPermission($request->id, $request);

            Session::flash('success', 'User Role updated successfully.');
            if (isset($request['save'])) {
                return redirect('admin/Role/edit/' . $request->id);
            } else {
                return redirect('/admin/Role');
            }
        }
    }

    public function trash($id)
    {
        $roledata = $this->user::where('role_id', $id)->get();
        $roles_permissions = RolePermission::where('role_id', $id)->get();
        if (!empty($roles_permissions->toArray())) {
            Session::flash('error', 'Please remove usertype\'s permissions');
        } elseif (!empty($roledata->toArray())) {
            Session::flash('error', 'User Role can\'t be moved to trash.');
        } else {
            $update = ['status' => 'T'];
            $this->role::where('id', $id)->update($update);
            Session::flash('success', 'User Role moved to trash.');
        }
        return 'Success';
    }
    public function statusChange(Request $request, $id)
    {

        $this->role::where('id', $id)->update(['status' => $request['status']]);
        return "Success";
    }
    public function trashIndex()
    {
        $data = $this->role::where('status', 'T')->orderBy('created_at', 'DESC')->get();
        return view('Role::trash')->with('role', $data);
    }

    public function delete($id)
    {
        $this->role::where('id', $id)->delete();
        Session::flash('success', 'User Role deleted successfully.');
        return 'Success';
    }

    public function restore($id)
    {
        $this->role::where('id', $id)->update(['status' => 'Y']);
        Session::flash('success', 'User Role restored successfully.');
        return redirect('admin/Role/trashindex');
    }
}
