<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<form action="{{ url('admin/Process/Selection/store')}}" method="post" name="process_selection" id="process_selection">
    {{csrf_field()}}

<div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
    <div class="">
            <div class="form-group">
                <label for="">Select Application Form : </label>
                <div class="">
                    <select class="form-control custom-select" id="form_field" name="form_field">
                        <option value="">Select</option>
                        @foreach($applications as $key=>$value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" id="error_msg">
            </div>
        
        
        <div class="text-right d-none" id="submit_btn"><input type="submit" class="btn btn-success" title="Seletc Form" value="Select Form"></div>
    </div>
</div>
</form>