<?php

namespace App\Modules\Priority\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Priority\Models\Priority;
use App\Modules\Priority\Models\PriorityDetail;
use App\Modules\District\Models\District;
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PriorityController extends Controller
{
    use AuditTrail;
    protected $dynamic_fields;

    public function __construct()
    {
        /* District status Field, Field for View, Field for DB */
        /* Note : District status Field is a key in dynamic fields array 
            so, it will be unique.. */
        $this->dynamic_fields = [
            'feeder' => ['Feeder', 'feeder'],
            'sibling' => ['Sibling', 'sibling'],
            'magnet_student' => ['Magnet Student', 'magnet_student'],
            'desegregation_compliance' => ['Majority Race in Home Zone School', 'majority_race_in_home_zone_school'],
            'magnet_employee' => ['Magnet Employee', 'magnet_employee'],
            'current_over_new' => ['Current Over New', 'current_over_new'],
        ];
    }

    public function index()
    {
        if (Session::get('district_id') != 0) {
            $priorities = Priority::where('district_id', session('district_id'))->where('enrollment_id', session('enrollment_id'))
                ->where('status', '!=', 'T');
        } else {
            $priorities = Priority::where('status', '!=', 'T');
        }
        $priorities = $priorities->orderBy('created_at', 'desc')->get();
        return view('Priority::index', compact('priorities'));
    }

    public function create()
    {
        if (Session('district_id') != 0) {
            $dynamic_fields = $this->dynamic_fields;
            $district = District::where('id', Session("district_id"))
                ->first(array_keys($dynamic_fields));
            return view('Priority::create', compact('district', 'dynamic_fields'));
        }
        return redirect('admin/Priority');
    }

    /**
     * get checkbox boolean value
     */
    public function getCheckboxBooleanValue($key, $value)
    {
        if (isset($value) && array_key_exists($key, $value)) {
            return 1;
        }
        return 0;
        // if(isset($value)){
        //     return array_key_exists($key,$value)?1:0;
        // }
        // return '';
    }

    /**
     * Check redundant entries
     */
    public function checkRedundancy(Request $request)
    {
        $blank = 0;
        $combinatios = [];
        $district = District::where('id', Session("district_id"))
            ->first(array_keys($this->dynamic_fields));

        foreach ($request->description as $key => $val) {
            // creating combination
            $combination[$key] = '';
            foreach ($this->dynamic_fields as $field => $value1) {
                if (isset($district->{$field}) && $district->{$field} == 'Yes') {
                    $combination[$key] .= $this->getCheckboxBooleanValue($key, $request->{$value1[1]});
                }
            }
            // Find redundant entries
            if ($combination[$key] != '') {
                if (array_search($combination[$key], $combinatios)) {
                    $redundant[] = $key;
                }
            }
            // Blank entries
            // if(preg_match('/1/i', $combination[$key]) == 0){
            //     // It will skips first blank entry
            //     if($blank > 0){
            //         $blank[] = $key;
            //     }
            //     $blank++;
            // }
            $combinatios = $combination;
        }
        if (isset($redundant)) {
            return 0;
        }
        return 1;
    }

    public function setDetails($id, $request)
    {
        $priority_id = $id;
        $priority_detail = array();

        foreach ($this->dynamic_fields as $field => $value) {
            if (isset($request->{$value[1]}))
                ${$value[1] . 'Keys'} = array_keys($request->{$value[1]});
            else
                ${$value[1] . 'Keys'} = array();
        }

        PriorityDetail::where('priority_id', $priority_id)->delete();

        foreach ($request->description as $key => $val) {
            // $id = ['id' => $request->priority_detail_id[$key]];
            $priority_detail['priority_id'] = $priority_id;

            if (isset($request->description[$key])) {
                $priority_detail['description'] = $request->description[$key];
            } else {
                $priority_detail['description'] = '';
            }

            foreach ($this->dynamic_fields as $field => $value) {
                if (count(${$value[1] . 'Keys'}) > 0 && in_array($key, ${$value[1] . 'Keys'})) {
                    $priority_detail[$value[1]] = 'Y';
                } else {
                    $priority_detail[$value[1]] = 'N';
                }
            }
            PriorityDetail::create($priority_detail);

            // Check priodity detail is present or not
            // $priority_detail_presence = PriorityDetail::where('id', $id)
            //     ->first();
            // if(!isset($priority_detail_presence)){
            //     // New priority detail
            //     $priority_detail['priority_id'] = $priority_id;
            //     $priority_detail_update = PriorityDetail::create($priority_detail);
            // }else{
            //     // old update priority detail
            //     $priority_detail_update = PriorityDetail::where('id', $id)
            //         ->update($priority_detail);
            // }
        }
    }

    public function store(Request $request)
    {
        Validator::extend('unique_priority', function ($attribute, $value, $parameters) {
            $priority = Priority::where('name', $value)->where('district_id', Session('district_id'))->where('enrollment_id', Session('enrollment_id'))->first();
            if (!isset($priority)) {
                return true;
            }
            return false;
        });

        $rules = [
            'name' => 'required|string|max:30|unique_priority',
            'description.*' => 'required|string|max:50',
        ];
        $messages = [
            'name.required' => 'Priority name is required.',
            'description.*.required' => 'Description is required.',
            'unique_priority' => 'Priority already present.'
        ];
        $this->validate($request, $rules, $messages);

        // check redundant entries
        $redundant = $this->checkRedundancy($request);
        // return $redundant;
        if ($redundant == 1) {
            $priority = Priority::create([
                'district_id' => Session("district_id"),
                'name' => $request->name,
            ]);

            if (isset($priority)) {
                // Creating details for new priority
                $store_details = $this->setDetails($priority->id, $request);

                session()->flash('success', 'Priority added successfully.');
                if (isset($request->save_exit))
                    return redirect('admin/Priority');
                else
                    return redirect("admin/Priority/edit/" . $priority->id);
            } else {
                session()->flash('error', 'Priority not added.');
            }
        } else {
            session()->flash('error', 'Do not enter redundant or more than one blank priorities.');
        }
        return redirect('admin/Priority/create');
    }

    public function updateStatus(Request $request)
    {
        $update_status = Priority::where('id', $request->id)->update(['status' => $request->status]);
        if (isset($update_status)) {
            return 'true';
        }
        return 'false';
    }

    public function checkName(Request $request)
    {
        $priority = Priority::where('name', $request->name)
            ->where("district_id", Session('district_id'))
            ->first();
        if (!isset($priority)) {
            return 'true';
        } elseif ($priority->id == $request->id) {
            return 'true';
        }
        return 'false';
    }

    public function edit($id)
    {
        $priority = Priority::where('id', $id)
            ->where('district_id', Session("district_id"))
            ->first();
        $priority_details = PriorityDetail::where('priority_id', $id)->get();
        $dynamic_fields = $this->dynamic_fields;
        $district = District::where('id', Session("district_id"))
            ->first(array_keys($dynamic_fields));
        return view('Priority::edit', compact('priority', 'priority_details', 'id', 'dynamic_fields', 'district'));
    }

    public function update(Request $request)
    {
        // return $request;
        Validator::extend('unique_priority', function ($attribute, $value, $parameters) {
            $priority = Priority::where('name', $value)->where('district_id', Session('district_id'))->where('enrollment_id', Session('enrollment_id'))->first();
            if (!isset($priority)) {
                return true;
            } elseif (isset($parameters[0]) && $parameters[0] == $priority->id) {
                return true;
            }
            return false;
        });
        $rules = [
            'name' => 'required|string|max:30|unique_priority:' . $request->id,
            'description.*' => 'required|string|max:50',
        ];
        $messages = [
            'name.required' => 'Priority name is required.',
            'description.*.required' => 'Description is required.',
            'unique_priority' => 'Priority already present.'

        ];
        $this->validate($request, $rules, $messages);

        // check redundant entries
        $redundant = $this->checkRedundancy($request);

        if ($redundant == 1) {
            $initObj = Priority::where('id', $request->id)->first();
            $priority = Priority::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                ]);
            $newObj = Priority::where('id', $request->id)->first();

            if (isset($priority)) {
                // Updating or Creating priorities
                $this->modelChanges($initObj, $newObj, "priority");

                $update = $this->setDetails($request->id, $request);

                session()->flash('success', 'Priority updated successfully.');
                if (isset($request->save_exit)) {
                    return redirect('admin/Priority');
                }
            } else {
                session()->flash('error', 'Priority not updated.');
            }
        } else {
            session()->flash('error', 'Do not enter redundant or more than one blank priorities.');
        }
        // return redirect()->back();
        return redirect("admin/Priority/edit/" . $request->id);
    }

    public function delete($id)
    {
        $priority_status = Priority::where('id', $id)
            ->update(['status' => 'T']);
        if (isset($priority_status)) {
            session()->flash('success', "Priority deleted successfully");
        } else {
            session()->flash('error', "Priority not deleted");
        }
        return redirect('admin/Priority');
    }

    public function showTrash()
    {
        if (Session::get('district_id') != 0) {
            $priorities = Priority::where('district_id', session('district_id'))->where('enrollment_id', session('enrollment_id'))->where('status', 'T');
        } else {
            $priorities = Priority::where('status', 'T')->where('enrollment_id', session('enrollment_id'));
        }
        $priorities = $priorities->orderBy('created_at', 'desc')->get();
        return view('Priority::trash', compact('priorities'));
    }

    public function restoreFromTrash($id)
    {
        $priority_status = Priority::where('id', $id)
            ->update(['status' => 'Y']);
        if (isset($priority_status)) {
            session()->flash('success', "Priority restored successfully");
        } else {
            session()->flash('error', "Priority not restored");
        }
        return redirect('admin/Priority/trash');
    }
}
