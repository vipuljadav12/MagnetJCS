@if(isset($data['checkbox_1']) && $data['checkbox_1']!='')
	<div class="form-group d-flex">
		<div class="mr-10">
        	<input type="checkbox"  name="formdata[{{$field_id}}][]"  @if(isset($data['label'])) thisname="{{$data['label']}}" @endif class="chkbox-style form-check-input styled-checkbox" id="table{{$field_id}}"  @if(isset($data['required']) && $data['required']=='yes') required @endif value="Yes">
        	<label for="table{{$field_id}}" class="label-xs check-secondary"></label>
        	
        </div>
        <div class="">{!! $data['checkbox_1'] !!}
        </div>
    </div>
@endif
