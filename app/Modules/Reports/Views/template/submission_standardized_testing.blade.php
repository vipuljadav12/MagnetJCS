@php 

    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $data = getSubmissionStandardizedTesting($submission->id);

    $reading = $english = $social_study = $science = $social_study = $math = 0;
    
    $subject_array = array("re"=>"Reading", "eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Study");

    if($submission->student_id != "")
    {
        $exist_data = getStudentStandardData($submission->student_id);
        //print_r($exist_data);exit; 
        if(!empty($exist_data))
        {
            $reading = $exist_data->reading;
            $english = $exist_data->english;
            $social_study = $exist_data->social_study;
            $science = $exist_data->science;
            $math = $exist_data->math;
        }
    }
    
@endphp
<form class="form" id="#standardized_testing" method="post" action="{{url('admin/Submissions/update/StandardizedTesting/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">{{$value->eligibility_name}} [{{getProgramName($value->program_id)}}]</div>
        <div class="card-body">
            <div class="form-group custom-none">
                @foreach($eligibility_data->subjects as $sbkey=>$sbvalue)
                    <div class="row">
                         <label class="control-label col-12 col-md-12">{{$subject_array[$sbvalue]}}</label>
                         <div class="col-12  col-md-6 ">
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    @php 
                                        $val_key = str_replace(" ", "_", strtolower($subject_array[$sbvalue]));
                                        $real_value = ${$val_key};

                                    @endphp
                                    <input type="text" class="form-control" value="{{$real_value}}" name="data[]">
                                    <input type="hidden" class="form-control" value="{{$sbvalue}}" name="subject[]">
                                </div> 
                            </div>
                        </div>
                        @if($eligibility_data->scoring->type=="SC")
                            <div class="col-12 col-lg-6">
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        @if($eligibility_data->scoring->method=="CO")
                                             <select class="form-control custom-select template-type" name="method[]">
                                                <option value="">Select Option</option>
                                                @if($eligibility_data->scoring->CO=="NP")
                                                    <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == "National Percentage") selected="" @endif>National Percentage</option>
                                                @else
                                                    <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == "Raw Score") selected="" @endif>Raw Score</option>
                                                @endif
                                            </select>    
                                        @else
                                            @if($eligibility_data->scoring->method=="YN")
                                                @php $options = $eligibility_data->scoring->YN ;@endphp
                                            @else
                                                @php $options = $eligibility_data->scoring->NR; @endphp
                                            @endif
                                            <select class="form-control custom-select template-type" name="method[]">
                                                <option value="">Select Option</option>
                                                @foreach($options as $k=>$v)
                                                    <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == $v) selected="" @endif>{{$v}}</option>
                                                @endforeach
                                            </select> 
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                        
                @endforeach
                
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
            $data = getSubmissionStandardizedTesting($submission->id);
        @endphp
        <form class="form" id="#standardized_testing" method="post" action="{{url('admin/Submissions/update/StandardizedTesting/'.$submission->id)}}">
            {{csrf_field()}}
            <div class="card shadow">
                <div class="card-header">{{$svalue->eligibility_name}} [{{getProgramName($svalue->program_id)}}]</div>
                <div class="card-body">
                    <div class="form-group custom-none">
                        @foreach($eligibility_data->subjects as $sbkey=>$sbvalue)
                            <div class="row">
                                 <label class="control-label col-12 col-md-12">{{$subject_array[$sbvalue]}}</label>
                                 <div class="col-12  col-md-6 ">
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            @php 
                                                $val_key = str_replace(" ", "_", strtolower($subject_array[$sbvalue]));
                                                $real_value = ${$val_key};

                                            @endphp
                                            <input type="text" class="form-control" value="{{$real_value}}" name="data[]">
                                            <input type="hidden" class="form-control" value="{{$sbvalue}}" name="subject[]">
                                        </div> 
                                    </div>
                                </div>
                                @if($eligibility_data->scoring->type=="SC")
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row">
                                            <div class="col-12 col-md-12">
                                                @if($eligibility_data->scoring->method=="CO")
                                                     <select class="form-control custom-select template-type" name="method[]">
                                                        <option value="">Select Option</option>
                                                        @if($eligibility_data->scoring->CO=="NP")
                                                            <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == "National Percentage") selected="" @endif>National Percentage</option>
                                                        @else
                                                            <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == "Raw Score") selected="" @endif>Raw Score</option>
                                                        @endif
                                                    </select>    
                                                @else
                                                    @if($eligibility_data->scoring->method=="YN")
                                                        @php $options = $eligibility_data->scoring->YN ;@endphp
                                                    @else
                                                        @php $options = $eligibility_data->scoring->NR; @endphp
                                                    @endif
                                                    <select class="form-control custom-select template-type" name="method[]">
                                                        <option value="">Select Option</option>
                                                        @foreach($options as $k=>$v)
                                                            <option @if(isset($data[$sbvalue]->method) && $data[$sbvalue]->method == $v) selected="" @endif>{{$v}}</option>
                                                        @endforeach
                                                    </select> 
                                                @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                                
                        @endforeach
                        
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
