<?php

namespace App\Modules\Files\Controllers;

use Illuminate\Http\Request;
use App\Modules\Files\Models\Files;
use App\Modules\District\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{
    public function index()
    {
        $district = District::where("id", Session::get("district_id"))->first();
        $files = Files::where('district_id', Session::get('district_id'))
            ->orderBy('sort_order')
            ->get();
        return view("Files::index", compact('files', 'district'));
    }

    public function create()
    {
        return view("Files::create");
    }

    public function store(Request $request)
    {
        // return $request;
        $district = District::where("id", Session::get("district_id"))->first();
        $req = $request->all();

        Validator::extend('unique_title', function ($attr, $value, $params) use ($request) {
            $check_unique = $this->uniqueTitle($request);
            if ($check_unique != "false") {
                return true;
            }
            return false;
        });
        $rules = [
            'link_title' => 'required|unique_title'
        ];

        if ($req['link_type'] == "file") {
            $rules['link_filename'] = 'required|mimetypes:application/pdf,image/jpeg,image/gif,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation';
            // $rules['link_filename'] = 'required|mimes:pdf,jpg,gif,xls,xlsx,ppt,pptx,docx';
        }
        if ($req['link_type'] == "text") {
            $rules['popup_text'] = 'required';
        }
        if ($req['link_type'] == "link") {
            $rules['link_url'] = 'required';
        }
        $messages = [
            'link_title.required' => 'Link title is required',
            'link_title.unique_title' => 'Link title already present',
            'link_filename.required' => 'File is required',
            'popup_text.required' => 'Text is required',
            'link_url.required' => 'Link URL is required',
            'link_filename.mimetypes' => 'The File must be a file of type: pdf,jpg,gif,docx,xls,xlsx,ppt.',
        ];
        $validator = Validator::make($req, $rules, $messages);
        if ($validator->fails()) {
            Session::flash('error', 'Please fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $data = array();
            $data['link_title'] = $req['link_title'];
            if ($req['link_type'] == "file") {
                $link_file = $request->file('link_filename');
                $file_name = str_replace(" ", "_", strtolower($data['link_title']));
                $file_extension = $link_file->getClientOriginalExtension();


                $link_file->move('resources/filebrowser/' . $district->district_slug . '/documents/', $file_name . "." . $file_extension);
                $data['link_filename'] = $file_name . "." . $file_extension;
            }
            if ($req['link_type'] == "text") {
                $data['popup_text'] = $req['popup_text'];
            }
            if ($req['link_type'] == "link") {
                $data['link_url'] = $req['link_url'];
            }
            $data['district_id'] = Session::get('district_id');

            $result = Files::create($data);
            if (isset($result)) {
                Session::flash("success", "File added successfully.");
            } else {
                Session::flash("error", "Please Try Again.");
            }
            if (isset($req['save_exit'])) {
                return redirect('admin/Files');
            }
        }
        return redirect('admin/Files/create');
    }

    public function uniqueTitle(Request $request)
    {
        $file = Files::where('district_id', Session::get('district_id'))
            ->where('link_title', $request->link_title)
            ->first();
        if (empty($file)) {
            return 'true';
        } elseif (isset($request->id) && ($request->id == $file->link_id)) {
            return 'true';
        }
        return 'false';
    }

    public function statusUpdate(Request $request)
    {
        Files::where('link_id', $request->id)->update(['status' => $request->status]);
        return 'true';
    }

    public function delete($id)
    {
        $district = District::where("id", Session::get("district_id"))->first();
        $fileData = Files::where("link_id", $id)->first();
        if ($fileData->link_filename != "") {
            File::delete(base_path() . "/resources/filebrowser/" . $district->district_slug . "/documents/" . $fileData->link_filename);
        }
        Files::where("link_id", $id)->delete();
        Session::flash("success", "File deleted successfully.");
        return redirect('admin/Files');
    }

    public function edit($id)
    {
        $district = District::where("id", Session::get("district_id"))->first();
        $files = Files::where('link_id', $id)->first();
        $link_type = '';
        if (isset($files)) {
            $link_type = isset($files->link_url) == true ? 'link' : '';
            $link_type .= isset($files->popup_text) == true ? 'text' : '';
            $link_type .= isset($files->link_filename) == true ? 'file' : '';
        }
        // return $link_type;
        return view("Files::edit", compact('files', 'id', 'district', 'link_type'));
    }

    public function update(Request $request, $id)
    {
        // return $request;
        $district = District::where("id", Session::get("district_id"))->first();
        $Files = Files::where("link_id", $id)->first();
        $req = $request->all();
        $rules = [
            //        'link_title' => 'required'
        ];
        $request['id'] = $id;
        Validator::extend('unique_title', function ($attr, $value, $params) use ($request) {
            $check_unique = $this->uniqueTitle($request);
            if ($check_unique != "false") {
                return true;
            }
            return false;
        });
        $rules = [
            'link_title' => 'required|unique_title'
        ];
        if ($req['link_type'] == "file") {
            $rules['link_filename'] = 'required|mimetypes:application/pdf,image/jpeg,image/gif,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation';
            // $rules['link_filename'] = 'mimetypes:application/pdf,image/jpeg,image/gif';
        }
        if ($req['link_type'] == "text") {
            $rules['popup_text'] = 'required';
        }
        if ($req['link_type'] == "link") {
            $rules['link_url'] = 'required';
        }
        $messages = [
            'link_title.required' => 'Link title is required',
            'link_title.unique_title' => 'Link title already present',
            'link_filename.required' => 'File is required',
            'popup_text.required' => 'Text is required',
            'link_url.required' => 'Link URL is required',
            'link_filename.mimetypes' => 'The File must be a file of type: pdf,jpg,gif,docx,xls,xlsx,ppt.',
        ];

        $validator = Validator::make($req, $rules, $messages);
        if ($validator->fails()) {
            Session::flash('error', 'Please fill all required fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $data = array();
            $data['link_title'] = $req['link_title'];
            $data['popup_text'] = null;
            $data['link_url'] = null;

            // Delete old file
            $file_valid = $req['link_type'] == "file" ? $request->hasFile('link_filename') : true;
            if ($file_valid) {
                $data['link_filename'] = null;
                $file_data = Files::where("link_id", $id)->first();
                if ($file_data->link_filename != "") {
                    $file_name = $file_data->link_filename ?? '';
                    $file_path = 'resources/filebrowser/' . $district->district_slug . '/documents/' . $file_name;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }

            if ($req['link_type'] == "file") {
                if ($request->hasFile('link_filename')) {
                    $link_file = $request->file('link_filename');
                    $file_name = str_replace(" ", "_", strtolower($Files->link_title));
                    $file_extension = $link_file->getClientOriginalExtension();
                    $link_file->move('resources/filebrowser/' . $district->district_slug . '/documents/', $file_name . "." . $file_extension);
                    $data['link_filename'] = $file_name . "." . $file_extension;
                }
            }
            if ($req['link_type'] == "text") {
                $data['popup_text'] = $req['popup_text'];
            }
            if ($req['link_type'] == "link") {
                $data['link_url'] = $req['link_url'];
            }
            $data['district_id'] = Session::get('district_id');

            $result = Files::where("link_id", $id)->update($data);
            if (isset($result)) {
                Session::flash("success", "File updated successfully.");
            } else {
                Session::flash("error", "Please Try Again.");
            }
            if (isset($req['save_exit'])) {
                return redirect('admin/Files');
            }
        }
        return redirect('admin/Files/edit/' . $id);
    }

    public function showPopupText($id)
    {
        $rsInfo = Files::where("link_id", $id)->first();
        echo $rsInfo->popup_text;
    }

    public function sortUpdate(Request $request)
    {
        $req = $request->all();
        $data = $req['data'];
        foreach ($data as $key => $value) {
            echo $value . " - " . $key . "<BR>";
            Files::where("link_id", $value)->update(array("sort_order" => $key));
        }
    }
}
