@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)

        @php
            $program_id = $submission->{$choice.'_choice_program_id'};

            if ($choice == 'first' || count($choice_ary) == 1) {
                $data = getTestScoreData($submission->id, $value, $submission->late_submission);
            } else{
                $data = getTestScoreData($submission->id, $value_2, $submission->late_submission);
                $value = $value_2;
            }

            $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
            $option = [];
            if($eligibility_data->eligibility_type->type == "NR")
            {
                $options = $eligibility_data->eligibility_type->NR;
            }
            // if (!empty($data)) {
            //     $data = json_decode($data, true);
            // }
        @endphp

        <form class="form" id="frm_test_score_{{$choice}}" method="post" action="{{url('admin/Submissions/update/TestScore/'.$submission->id.'/'.$program_id)}}">
            {{csrf_field()}}
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    <div class="">
                        @if(!empty($data))
                            @php
                                ${$choice.'_count'} = 0;
                            @endphp
                            @foreach($data as $ckey => $cvalue)
                                <div class="form-group row">
                                    <label class="control-label col-2 col-md-2 font-weight-bold">{{$ckey or ''}}</label>
                                    <div class="col-5 col-md-5">
                                        <input type="hidden" name="test_score_name[]" value="{{$ckey}}">
                                        <input id="ts_{{ $choice.'_'.${$choice.'_count'} }}" type="text" name="test_score_value[]" class="form-control" value="{{ $cvalue['score'][$ckey] ?? ''}}">
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group custom-none">
                                            <div class="">
                                                <select class="form-control custom-select template-type" name="test_score_rank[]">
                                                    <option value="">Select Option</option>
                                                    @foreach($options as $k=>$v)
                                                        <option value="{{$v}}" @if(isset($cvalue['scorerank'][$ckey]) && $cvalue['scorerank'][$ckey] == $v) selected="" @endif>{{$v}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @php
                                    ${$choice.'_count'}++;
                                @endphp
                            @endforeach
                        @endif
                    </div>
                    <div class="text-right"> 
                        <button type="submit" form="frm_test_score_{{$choice}}" class="btn btn-success">    
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
    
@endif

@section('submission_test_score_script')
@endsection
