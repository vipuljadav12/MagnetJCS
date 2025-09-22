<div class="form-group row">
	@if(isset($data['label']))
		<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
		
	@endif
	<div class="col-12 col-md-6 col-xl-6">
		<textarea class="form-control" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @if(isset($data['required']) && $data['required']=='yes') required @endif></textarea>
	</div>
	
	
</div>