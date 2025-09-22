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
	@if((isset($data['option_1']) && $data['option_1']!='') || (isset($data['db_field']) && $data['db_field']=="state"))
		@if(isset($data['label']))
			<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
				
		@endif
		<div class="col-12 col-md-6 col-xl-6">
			<select @if(isset($data['required_field']) && $data['required_field']=='yes') required @endif class="form-control custom-select" @if(isset($data['text_label'])) thisname="{{$data['text_label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['db_field']) && $data['db_field']=="current_grade") onchange="changeNextGrade(this)" id="current_grade" @endif  @if(isset($data['db_field']) && $data['db_field']=="next_grade") id="next_grade" @endif >
				@if(isset($data['db_field']) && $data['db_field']!="next_grade")
					<option value="">Select an Option</option>
				@endif

			@if(isset($data['db_field']) && $data['db_field']=="state")
				@php $stateArray = Config::get('variables.states') @endphp

				@foreach($stateArray as $stkey=>$stvalue)
					<option value="{{$stkey}}" @if($sel_value==$stkey) selected @endif>{{$stvalue}}</option>
				@endforeach
				

			@else

				@if(isset($data['db_field']) && $data['db_field']!="next_grade")
					@foreach($data as $key=>$value)
						@if(substr($key, 0, 7)=="option_")
							<option value="{{$value}}" @if($sel_value==$value) selected @endif>{{$value}}</option>
						@endif
					@endforeach
				@elseif($sel_value != "")
					<option value="{{$sel_value}}">{{$sel_value}}</option>
				@endif
			@endif
		</select>
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