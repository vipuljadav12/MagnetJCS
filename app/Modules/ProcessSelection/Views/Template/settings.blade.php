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
        
        
        
        <div class="text-right"><input type="button" class="btn btn-success" title="Select Form" value="Select Form" id="selectform_settings"></div>
    </div>
</div>