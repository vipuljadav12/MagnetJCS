@php 
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $type = $eligibility_data->scoring->method;
    $options = isset($eligibility_data->scoring->$type) ? $eligibility_data->scoring->$type : array();
    $data = getConductDisciplinaryInfo($submission->id);
@endphp
<form class="form" id="#insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
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