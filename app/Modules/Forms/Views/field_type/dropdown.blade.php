<div class="box_control">
	<label class="label_view form-label">
		{{ getPropertyValue($data->form_field_id,'text_label') }}
	</label>
	@if(getPropertyValue($data->form_field_id,'required_field') == "Yes")
		<span class="field-required text-danger ml-5">*</span>
	@endif
	<span class="help_text_view" data-toggle="tooltip" title="{{ getPropertyValue($data->form_field_id,'help_text') }}">
		@if(getPropertyValue($data->form_field_id,'help_text') != "")
			<i class="fas fa-info-circle"></i>
		@endif
	</span>
	@php
		$selected = getPropertyValue($data->form_field_id,'selected');
	@endphp
	<div class="all-choice">
		@php
			$choice_data = explode(',',getPropertyValue($data->form_field_id,'choice'));
		@endphp
		<select class="form-control custom-select form-showtext">
			@foreach($choice_data as $key=>$value)
				@if($value == $selected)
					@php
						$selected = "selected=selected";
					@endphp
				@else
					@php
						$selected = "";
					@endphp
				@endif
				<option value="{{ $value }}" {{ $selected }}>{{ $value }}</option>
			@endforeach
		</select>
	</div>
	@foreach($data['child'] as $key=>$value)
		<input type="hidden" name="{{ $value->field_property ?? '' }}" class="{{ $value->field_property ?? '' }}" value="{{ $value->field_value ?? '' }}">	
	@endforeach
	<input type="hidden" name="form_field_id" class="form_field_id" value="{{ $data->form_field_id }}">
	<input type="hidden" name="sort" class="sort" value="{{ $data->sort }}">
</div>