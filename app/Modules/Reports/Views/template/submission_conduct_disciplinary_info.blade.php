@php 
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $type = $eligibility_data->scoring->method;
    $options = isset($eligibility_data->scoring->$type) ? $eligibility_data->scoring->$type : array();
    $data = array();//getConductDisciplinaryInfo($submission->id);
    $b_info = $c_info = $d_info = $e_info = $susp = $susp_days = 0;

    if($submission->student_id != "")
    {
        $exist_data = getStudentConductData($submission->student_id);
        //print_r($exist_data);exit; 
        if(!empty($exist_data))
        {
            $b_info = $exist_data->b_info;
            $c_info = $exist_data->c_info;
            $d_info = $exist_data->d_info;
            $e_info = $exist_data->e_info;
            $susp = $exist_data->susp;
            $susp_days = $exist_data->susp_days;
        }
    } 

@endphp
<form class="form" id="#insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">{{$value->eligibility_name}} <span class="d-none">[{{getProgramName($value->program_id)}}]</span></div>
        <div class="card-body">

            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">B info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="b_info" value="{{$b_info}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">C info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="c_info" value="{{$c_info}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">D info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="d-info" value="{{$d_info}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">E info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="e_info" value="{{$e_info}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="susp" value="{{$susp}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Days Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="susp_days" value="{{$susp_days}}">
                        </div>
                    </div>
                </div>
                @if($eligibility_data->scoring->type!='DD')
                    <div class="col-12 col-lg-6">
                        <div class="form-group custom-none row">
                            <label class="control-label col-12 col-md-12">Select : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                                    <option value="">Select Option</option>
                                    @if($eligibility_data->scoring->method=='YN')
                                        @forelse($eligibility_data->scoring->YN as $yn)
                                            <option>{{$yn}}</option>
                                        @empty
                                        @endforelse
                                    @else
                                        @forelse($eligibility_data->scoring->NR as $nr)
                                            <option>{{$nr}}</option>
                                        @empty
                                        @endforelse
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
            </div>
              

            
            <div class="text-right d-none"> 
                <button class="btn btn-success">    
                    <i class="fa fa-save"></i>
                </button>
            </div>
        </div>
    </div>
</form>

@if($submission->second_choice == 'ss')
    @php $second_eligibilities = getEligibilities($submission->second_choice, $value->eligibility_ype) @endphp


    @foreach($second_eligibilities as $skey=>$svalue)
        @php
            $eligibility_data = getEligibilityContent1($svalue->assigned_eigibility_name);
            $type = $eligibility_data->scoring->method;

        @endphp
        <form class="form" id="#insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
            
        {{csrf_field()}}
        <div class="card shadow">
            <div class="card-header">{{$svalue->eligibility_name}} [{{getProgramName($svalue->program_id)}}]</div>
            <div class="card-body">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">B info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="b_info" value="{{$b_info}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">C info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="c_info" value="{{$c_info}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">D info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="d_info" value="{{$d_info}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">E info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="e_info" value="{{$e_info}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"># Susp</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="susp" value="{{$susp}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"># Days Susp</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="susp_days" value="{{$susp_days}}">
                            </div>
                        </div>
                    </div>
                    @if($eligibility_data->scoring->type!='DD')
                        <div class="col-12 col-lg-6">
                            <div class="form-group custom-none row">
                                <label class="control-label col-12 col-md-12">Select : </label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                                        <option value="">Select Option</option>
                                        @if($eligibility_data->scoring->method=='YN')
                                            @forelse($eligibility_data->scoring->YN as $yn)
                                                <option>{{$yn}}</option>
                                            @empty
                                            @endforelse
                                        @else
                                            @forelse($eligibility_data->scoring->NR as $nr)
                                                <option>{{$nr}}</option>
                                            @empty
                                            @endforelse
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                </div>
                  

                
                <div class="text-right d-none"> 
                    <button class="btn btn-success">    
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endforeach
    @endif