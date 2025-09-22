@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)

        @php
            $str = $choice."_choice_program_id";
            $pid = $submission->{$str};
            if ($choice == 'first' || count($choice_ary) == 1) {
                $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
            } else{
                $eligibility_data = getEligibilityContent1($value_2->assigned_eigibility_name);
            }
            $data = getSubmissionCommitteeScore($submission->id, $pid);
            
            if($eligibility_data->eligibility_type->type=="YN")
                $options = $eligibility_data->eligibility_type->YN;
            else
                $options = $eligibility_data->eligibility_type->NR;
            
        @endphp

        <form class="form" id="insterview_score_form_{{$choice}}" method="post" action="{{url('admin/Submissions/update/CommitteeScore/'.$submission->id)}}">
        {{csrf_field()}}
            <input type="hidden" name="program_id" value="{{$pid}}">
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    <div class="form-group custom-none">
                        <div class="">
                            <select class="form-control custom-select template-type" name="{{$choice}}_data">
                                <option value="">Select Option</option>
                                @foreach($options as $k=>$v)
                                    <option @if($data == $v) selected="" @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-right"> 
                        <button type="submit" form="insterview_score_form_{{$choice}}" class="btn btn-success">    
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
@endif