<div class="form-group row">
	@if(isset($data['label']))
		<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
		
	@endif
	<div class="col-12 col-md-6 col-xl-6">
		<input type="email" class="form-control" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif  name="formdata[{{$field_id}}]" @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @if(isset($data['required']) && $data['required']=='yes') required @endif value="{{ Session::get("form_data")[0]['formdata'][$field_id]  ?? ''}}"  id="{{$data['db_field']}}">
	</div>
	@if(isset($data['help_text']))
		<div class="col-12 col-md-2 col-xl-3">
			<span class="help" data-toggle="tooltip" data-html="true" title="{{$data['help_text']}}">
		        @if($data['help_text'] != "")
		            <i class="fas fa-question"></i>
		        @endif
		    </span>
		</div>
	@endif
	
</div>