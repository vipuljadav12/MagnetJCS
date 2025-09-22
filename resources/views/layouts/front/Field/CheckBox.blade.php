<div class="form-group row">
	
	@php
		$sel_value = "";
		if(Session::has("form_data"))
		{
			$formdata = Session::get("form_data");
			if(isset($formdata[0]['formdata'][$field_id]))
				$sel_value = $formdata[0]['formdata'][$field_id];
		}
	@endphp
	@if(isset($d9ta['checkbox_1']) && $d9ta['checkbox_1']!='')
		@if(isset($data['label']))
			<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
				
		@endif
		<div class="col-12 col-md-6 col-xl-6">
			
				@foreach($data as $key=>$value)
					@if(substr($key, 0, 9)=="checkbox_")
						<input type="checkbox" value="{{$value}}" name="formdata[{{$field_id}}][]" @if($sel_value==$value) selected @endif>&nbsp;{{$value}}
					@endif
				@endforeach
			
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
	<!--<fieldset>-->
		
    @endif
</div>