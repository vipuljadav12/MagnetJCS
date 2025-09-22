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
	<small></small>
	<div class="input-group date"   id="datetimepicker1">
	<input type="text" class="form-control form-showtext"  placeholder="{{ getPropertyValue($data->form_field_id,'place_holder_text') }}" disabled="disabled">
	<div class="input-group-append input-group-addon">
		<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	</div>
</div>
	
	@foreach($data['child'] as $key=>$value)
		<input type="hidden" name="{{ $value->field_property ?? '' }}" class="{{ $value->field_property ?? '' }}" value="{{ $value->field_value ?? '' }}">	
	@endforeach
	<input type="hidden" name="form_field_id" class="form_field_id" value="{{ $data->form_field_id }}">
	<input type="hidden" name="sort" class="sort" value="{{ $data->sort }}">
</div>

{{-- <div class="input-group date"   id="datetimepicker1">
	<input type="text" class="form-control" />
	<div class="input-group-append input-group-addon">
		<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
	</div>
</div> --}}