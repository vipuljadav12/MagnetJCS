<div class="form-group row">
	
	
	@if((isset($data['option_1']) && $data['option_1']!='') || (isset($data['db_field']) && $data['db_field']=="state"))
		@if(isset($data['label']))
			<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
				
		@endif
		<div class="col-12 col-md-6 col-xl-6">
			<select @if(isset($data['required_field']) && $data['required_field']=='yes') required @endif class="form-control custom-select" @if(isset($data['text_label'])) thisname="{{$data['text_label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['db_field']) && $data['db_field']=="current_grade") onchange="changeNextGrade(this)" @endif  @if(isset($data['db_field']) && $data['db_field']=="next_grade") id="next_grade" @endif >
				<option value="">Select an Option</option>

			@if(isset($data['db_field']) && $data['db_field']=="state")
				@php $stateArray = Config::get('variables.states') @endphp

				@foreach($stateArray as $stkey=>$stvalue)
					<option value="{{$stvalue}}">{{$stvalue}}</option>
				@endforeach
				

			@else
				@foreach($data as $key=>$value)
					@if(substr($key, 0, 7)=="option_")
						<option value="{{$value}}">{{$value}}</option>
					@endif
				@endforeach
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