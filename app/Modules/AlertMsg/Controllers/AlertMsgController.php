<?php

namespace App\Modules\AlertMsg\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\AlertMsg\Models\AlertMsg;
use App\Modules\Configuration\Models\Configuration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AlertMsgController extends Controller
{

  public function index()
  {
    $msgs = AlertMsg::get();
    return view("AlertMsg::index", compact('msgs'));
  }

  public function create()
  {
    return view("AlertMsg::create");
  }

  public function store(Request $request)
  {
    /*$req = $request->all(); 
      $rules = array(
            'config_name' => 'required',
            'config_value' => 'required',
      );
      $message = array(
            'config_name.required'=>'Description is required.',
            'config_value.required'=>'Text is required.',
      );
      $validator = Validator::make($req, $rules,$message);
        if ($validator->fails()) {
             Session::flash('error','Somthing is wrong');
             $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
        $district_id = Session::get("district_id");
        $data=[
          'config_name'  =>$req['config_name'],
          'config_value' =>$req['config_value'],
          'district_id'  => $district_id
        ];
        $exist = Configuration::where("config_name",$data["config_name"])->where("district_id",$district_id)->first();
        if (isset($exist->id))
        {
          $exist->config_value = $data['config_value'];
          $exist->save();
          Session::flash("success","Data Updated successfully");
        }
        else
        {
          $Configuration = Configuration::create($data);
        }
        
       if(isset($Configuration))
        {
            Session::flash("success","Translation Data Added successfully");
        }
        else
        {
            Session::flash("error","Something went wrong please try again.");
        }
        if(isset($req['save'])){
        return redirect('admin/Configuration/create');
        }else{
        return redirect('/admin/Configuration/');
        }
      }  */
  }
  public function edit($id)
  {
    $msg = AlertMsg::where('id', $id)->first();
    return view("AlertMsg::edit", compact('msg'));
  }

  public function update(Request $request, $id)
  {
    $req = $request->all();
    $rules = array(
      'msg_txt' => 'required',
    );
    $message = array(
      'msg_txt.required' => 'Text is required.',
    );
    $validator = Validator::make($req, $rules, $message);
    if ($validator->fails()) {
      Session::flash('error', 'Somthing is wrong');
      $messages = $validator->messages();
      return redirect()->back()->withErrors($validator)->withInput();
    } else {
      $data = [
        'msg_txt' => $req['msg_txt'],
      ];
      $msg = AlertMsg::where('id', $id)->update($data);
      if (isset($msg)) {
        Session::flash("success", "Translation Data Upadated successfully");
      } else {
        Session::flash("error", "Something went wrong please try again.");
      }
      return redirect('/admin/AlertMsg/');
    }
  }

  public function changeStatus(Request $request)
  {
    $configuration = Configuration::where('id', $request->id)->update(['status' => $request->status]);
    if (isset($configuration)) {
      return json_encode(true);
    } else {
      return json_encode(false);
    }
  }
  public function delete($id)
  {
    $configuration = Configuration::where('id', $id)->delete();
    if (isset($configuration)) {
      Session::flash('success', "Translation Data delete successfully.");
    } else {
      Session::flash('error', 'Please try again');
    }
    return  redirect('admin/Configuration');
  }
}
