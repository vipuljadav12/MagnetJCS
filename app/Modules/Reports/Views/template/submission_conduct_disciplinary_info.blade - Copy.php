@php 
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $type = $eligibility_data->scoring->method;
    $options = isset($eligibility_data->scoring->$type) ? $eligibility_data->scoring->$type : array();
    $data = getConductDisciplinaryInfo($submission->id);
    $Ainfo = $Binfo = $Cinfo = $Dinfo = $Einfo = 0;

    if($submission->student_id != "")
    {
        $exist_data = getStudentConductData($submission->student_id);
        //print_r($exist_data);exit; 
        if(!empty($exist_data))
        {
            $Ainfo = $exist_data->Ainfo;
            $Binfo = $exist_data->Binfo;
            $Cinfo = $exist_data->Cinfo;
            $Dinfo = $exist_data->Dinfo;
            $Einfo = $exist_data->Einfo;
        }
    } 

@endphp
<form class="form" id="#insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">{{$value->eligibility_name}} [{{getProgramName($value->program_id)}}]</div>
        <div class="card-body">

            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">A info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="Ainfo" value="{{$Ainfo}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">B info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="Binfo" value="{{$Binfo}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">C info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="Cinfo" value="{{$Cinfo}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">D info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="Dinfo" value="{{$Dinfo}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">E info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="Einfo" value="{{$Einfo}}">
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

@if($submission->second_choice != '')
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
                            <label class="control-label col-12 col-md-12">A info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="Ainfo" value="{{$Ainfo}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">B info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="Binfo" value="{{$Binfo}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">C info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="Cinfo" value="{{$Cinfo}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">D info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="Dinfo" value="{{$Dinfo}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">E info</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="Einfo" value="{{$Einfo}}">
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
                  

                
            </div>
        </div>
        <div class="text-right d-none1"> 
            <button class="btn btn-success">    
                <i class="fa fa-save"></i>
            </button>
        </div>
    </form>
    @endforeach
    @endif