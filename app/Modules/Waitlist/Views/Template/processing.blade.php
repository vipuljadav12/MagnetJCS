<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<form action="{{ url('admin/Waitlist/store')}}" method="post" name="process_selection" id="process_selection">
    {{csrf_field()}}

<div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
    <div class="">
            <div class="form-group">
                <label for="">Select Application Form : </label>
                <div class="">
                    <select class="form-control custom-select" id="application_id" name="application_id">
                        <option value="">Select</option>
                        @foreach($applications as $key=>$value)
                            <option value="{{$value->id}}">{{$value->application_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        <div class="text-right">@if($display_outcome == 0)<input type="submit" class="btn btn-success" title="Save Form" value="Save Form">@else <button type="button" class="btn btn-danger disabled" title="Save Form">Save Form</button>@endif</div>
    </div>
</div>
</form>