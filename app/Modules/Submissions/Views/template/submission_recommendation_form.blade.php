@php
    $recommendation = getRecommendationFormData($submission->id);
    $doneArr = array();
@endphp
@if(isset($recommendation) && !empty($recommendation))
    @foreach($recommendation as $k=>$value)
        @php 
            $doneArr[] = $value->config_value; 
            // dd($value);
        @endphp
        <div class="card shadow">

            @php
                $subject = explode('.', $value->config_value)[0];
                $ans_content = json_decode($value->answer);
                // dd($ans_content);
            @endphp
            <div class="card-header">Recommendation - {{config('variables.recommendation_subject')[$subject]}}</div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Name : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$value->teacher_name}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Email : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$value->teacher_email}}" disabled>
                    </div>
                </div>
                <div class="form-group row d-none">
                    <label class="control-label col-12 col-md-12">Average Score : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$value->avg_score}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Comment : </label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" rows="3" disabled>{{$value->comment}}</textarea>
                    </div>
                </div>

                @if(isset($ans_content->answer))
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">Question-Ans : </label>
                        <div class="col-12 col-md-12">
                            @foreach($ans_content->answer as $h=>$header)
                                <div class="card">
                                    <div class="card-header">{{$header->name}}</div>
                                    <div class="card-body">
                                    @foreach($header->answers as $ak=>$avalue)
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">{{$ak ?? ''}} : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control" disabled="">
                                                @foreach($header->points as $pk=>$point)
                                                    @if($point == $avalue)
                                                        {{-- <span class="form-control" style="background-color: #e9ecef; opacity: 1">{{$point.'. '.$header->options[$pk] }}</span> --}}
                                                        <option>{{$point.'. '.$header->options[$pk] }}</option>
                                                    @endif
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Student LInk</label>
                    <div class="col-12 col-md-12">
                        <span style="color: blue;">{{url('/recommendation/'.$value->config_value)}}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">
                        <a href="{{url('/admin/Submissions/recommendation/pdf/'.$value->id)}}" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Recommendation Form</a>
                    </label>
                </div>
            </div>
        </div>

    @endforeach
@endif


@php
    $recommendationUrl = getRecommendationLinks($submission->id);    
@endphp
@if(isset($recommendationUrl) && !empty($recommendationUrl))
    <div class="card shadow">
        <div class="card-header">Pending Recommendation </div>
        <div class="card-body">
            @foreach($recommendationUrl as $key => $value)
                @if(!in_array($value->config_value, $doneArr))
                    <form method="post" action="{{url('admin/Submissions/recommendation/send/manual')}}" onsubmit="return validateTeacherEmail(this)">
                    {{csrf_field()}}    
                    <input type="hidden" name="submission_id" value="{{$submission->id}}">
                    <input type="hidden" name="config_id" value="{{$value->id}}">
                    <div class="form-group row pt-10">
                        <label class="control-label col-12 col-md-12">
                            @php
                                $subject_title = str_replace("recommendation_", "", $value->config_name);
                                $subject_title = str_replace("_url", "", $subject_title);
                                $rsubjects = config('variables.recommendation_subject');
                                echo "<strong>".$rsubjects[$subject_title] ." Recommendation Form"."</strong>";
                            @endphp
                        </label>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Email:</strong></label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="email" value="">
                        </div>
                    </div>
                     
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Recommendation Form Link:</strong></label>
                        <div class="col-12 col-md-12">
                            <span style="color: blue;"><a href="{{url('/recommendation/'.$value->config_value)}}">{{url('/recommendation/'.$value->config_value)}}</a></span>
                        </div>
                    </div>
                    <div class="form-group row pb-20" style="border-bottom: 1px solid #ccc;">
                        
                        <div class="col-12 col-md-12">
                            <input type="submit" class="btn btn-success" value="Send Recommendation Email Link"></span>
                        </div>
                    </div>
                    </form>
                @endif
            @endforeach
        </div>
    </div>
@endif
