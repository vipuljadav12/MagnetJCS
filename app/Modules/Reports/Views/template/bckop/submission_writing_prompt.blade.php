@php
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $data = getSubmissionWritingPrompt($submission->id);
    // print_r($data);
@endphp
@if($eligibility_data->eligibility_type->type=="YN")
    @php
        $options = $eligibility_data->eligibility_type->YN ;
    @endphp
@else
    @php
        $options = $eligibility_data->eligibility_type->NR; 
    @endphp
@endif 
<form class="form" id="#audition_form" method="post" action="{{url('admin/Submissions/update/WritingPrompt/'.$submission->id)}}">  
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">{{$value->eligibility_name}}</div>
        <div class="card-body">
            <div class="form-group custom-none">
                <div class="">
                    <select class="form-control custom-select template-type" name="data">
                        <option value="">Select Option</option>
                        @foreach($options as $k=>$v)
                            <option @if(isset($data->data) && $data->data == $v) selected="" @endif>{{$v}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="text-right"> 
                <button class="btn btn-success">    
                    <i class="fa fa-save"></i>
                </button>
            </div>
        </div>
    </div>
</form>
