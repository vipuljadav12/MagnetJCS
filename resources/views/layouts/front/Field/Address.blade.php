<div class="form-group">
	@if(isset($data['text_label']))
		<label>{{$data['text_label']}}@if(isset($data['required_field']) && $data['required_field']=='Yes')<span class="text-danger">*</span>@endif</label>
	@endif
	
</div>