<?php

namespace App\Modules\Users\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Modules\Users\Models\Roles;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Hash;
use App\Modules\Program\Models\Program;
use App\Modules\District\Models\District;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    use AuditTrail;
    public $stores;
    public $programs;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // private $stores;
    public function  __construct($value = '')
    {
        $this->stores = District::all();
        $this->programs = Program::all();
    }
    public function index()
    {
        $users = User::where('role_id', '!=', 1)->where('district_id', Session::get("district_id"))->where("status", "!=", "T")->get();
        return view("Users::index")->with(["users" => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where("id", "!=", 1)->get()->all();
        if (Session::get("district_id") != '0')
            $programs = Program::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();
        else
            $programs = Program::where('status', 'Y')->get();
        return view("Users::create")->with(['roles' => $roles, "stores" => $this->stores, "programs" => $programs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $msg = [
            'first_name.required' => "First Name is required.",
            'last_name.required' => "Last Name is required.",
            'email.required' => "Email is required.",
            'email.email' => "Please enter valid Email.",
            'password.required' => "Password is required",
            'password.min' => "Please enter minimum 8 charcter password",
            'role_id.required' =>  "Please select User Type",
        ];

        $programs = Program::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();

        //all_programs
        $insertData = $request->except("_token");
        if (isset($insertData['all_programs'])) {
            $program = "";
            foreach ($programs as $key => $value) {
                $program .= $value->id . ",";
            }
            $program = trim($program, ",");
        } else {
            $program = isset($insertData['programs']) ? implode(',', $insertData['programs']) : null;
        }
        $insertData['username'] = $insertData['first_name'] . $insertData['last_name'];
        $insertData['password'] = bcrypt($insertData['password']);
        $insertData['status'] = "Y";
        $insertData['programs'] = $program;
        $insertData['district_id'] = Session::get("district_id");
        $validateData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|confirmed|unique:users|max:255',
            'password' => 'required|min:8|max:255',
            'role_id' => 'required',
        ], $msg);
        // return $insertData;

        $result =  User::create($insertData);
        $result['plain_password'] = $request->password;
        if ($result) {
            /*Mail::send('Users::registration', ['user' => $result], function ($mail) use ($result) {
                // $mail->from('hello@app.com', 'Your Application');

                $mail->to($result->email, $result->name)->subject('New User registration');
            });*/
            Session::flash("success", "User created successfully");
        } else {
            Session::flash("error", "Please Try Again");
        }
        if (isset($request->save_exit)) {
            return redirect('admin/Users');
        }
        return redirect('admin/Users/edit/' . $result->id);
        //return $request;

    }
    public function validateUserForm($request)
    {
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|confirmed|unique:users|max:255',
            'password' => 'required|min:8|max:255',
            'role_id' => 'required',
        ];
        $messages = [
            'first_name.required' => "First Name is required.",
            'last_name.required' => "Last Name is required.",
            'email.required' => "Email is required.",
            'email.email' => "Please enter valid Email.",
            'password.required' => "Password is required",
            'password.min' => "Please enter minimum 8 charcter password",
            'role_id.required' =>  "Please select User Type",
        ];
        $validatedData = $request->validate($rules, $messages);
        return true;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Roles::where("id", "!=", 1)->get()->all();
        if (Session::get("district_id") != '0')
            $programs = Program::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();
        else
            $programs = Program::where('status', 'Y')->get();
        //echo $user->district_id;exit;
        //$user->district_id = explode(',',($user->district_id!= '' ? $user->district_id : 0));
        return view("Users::edit")->with(["user" => $user, "roles" => $roles, "stores" => $this->stores, "programs" => $programs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        /// return $request;
        if ($this->validateUserUpdateForm($request)) {
            $req = $request->except("_token", 'save_exit', 'submit');

            if (isset($req['all_programs'])) {
                $programs = Program::where('status', 'Y')->where('district_id', Session::get('district_id'))->get();
                $program = "";
                foreach ($programs as $key => $value) {
                    $program .= $value->id . ",";
                }
                $program = trim($program, ",");
                unset($req['all_programs']);
            } else {
                $program = isset($req['programs']) ? implode(',', $req['programs']) : null;
            }
            $req['programs'] = $program;
            unset($request['district']);

            $userObj =  User::where('id', $user->id)->first();
            $initUser = $userObj;
            if (isset($request->password)) {
                $request->validate([
                    'password' => 'required|min:8',
                ]);
                $req['password'] = bcrypt($request->password);
            } else {
                unset($req['password']);
            }
            unset($req['password_confirmation']);

            $result =  User::where('id', $user->id)->update($req);
            // $newObj =  User::where('id',$user->id)->first();
            // $this->modelChanges($initUser,$newObj,"user");
            //$result =  $newObj;

            if ($result) {
                Session::flash("success", "User updated successfully");
            } else {
                Session::flash("error", "Please Try Again");
            }
            if (isset($request->save_exit)) {
                return redirect('admin/Users');
            } else {
                return redirect('admin/Users/edit/' . $user->id);
            }
            if (isset($request->save_edit)) {
                return redirect('admin/Users/edit/' . $user->id);
            }
            return  redirect("admin/Users");
        }
    }
    public function validateUserUpdateForm($request)
    {
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            // 'email' => 'required|email|confirmed|unique:users|max:255',
            // 'password' => 'required|min:8|max:255',
            'role_id' => 'required',
        ];
        $messages = [
            'first_name.required' => "First Name is required.",
            'last_name.required' => "Last Name is required.",
            /* 'email.required' => "Email is required.",
            'email.email' => "Please enter valid Email.",
            'password.required' => "Password is required",
            'password.min' => "Please enter minimum 8 charcter password",*/
            'role_id.required' =>  "Please select User Type",
        ];
        $validatedData = $request->validate($rules, $messages);
        return true;
    }
    public function status(Request $req)
    {
        $user = User::where('id', $req['user_id'])->first();
        $user->status = $user->status == "Y" ? "N" : "Y";
        $user->save();
        return "true";
    }
    public function trash(User $user)
    {
        $user->status = "T";
        $result = $user->save();
        if ($result) {
            Session::flash("success", "User moved to trash successfully");
        } else {
            Session::flash("error", "Please try again");
        }
        return redirect("admin/Users");
    }
    public function trashindex()
    {
        $users = User::where('status', 'T')->where('district_id', Session::get("district_id"))->get();
        return view('Users::trash', compact('users'));
    }
    public function restore(User $user)
    {
        $user->status = "Y";
        $result = $user->save();
        if ($result) {
            Session::flash("success", "User restore successfully");
        } else {
            Session::flash("error", "Please try again");
        }
        return redirect("admin/Users");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function UpdateProfile(Request $request, $id)
    {
        //        return $request;
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'username' => 'required|max:255',
            'profile' => 'mimes:jpg,png,jpeg,gif',
        ]);
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'profile' => $id . '_profile.png',
            'username' => $request->username,
        ];
        $logo = $request->file('profile');
        if ($logo) {
            $logo->move('resources/assets/admin/images/', $id . '_profile.png');
        }
        if (isset($request->password)) {
            if ($this->checkOldPass($request) == 'true') {
                $request->validate([
                    'password' => 'min:8|max:255|confirmed',
                ]);
                $data = $data + ['password' => bcrypt($request->password)];
            } else {
                return redirect()->back()->withErrors(['old_password' => 'The password is incorrect.']);
            }
        }
        User::where('id', $id)->update($data);
        return redirect()->back();
    }

    public function uniqueemail(Request $request)
    {
        $result = User::where('id', '!=', $request->id)->where('email', $request->email)->first();
        if (isset($result)) {
            return json_encode(false);
        } else {
            return json_encode(true);
        }
    }
    public function checkOldPass(Request $request)
    {
        // return $request;
        // $result= User::where('id',$request->id)->first();
        $result = User::where('id', Auth::user()->id)->first();
        if (Hash::check($request->old_password, $result->password)) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
