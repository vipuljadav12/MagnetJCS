<div class="box_control">
	<textarea class="" name="description">{!! getPropertyValue($data->form_field_id,'description') !!}</textarea>
@foreach($data['child'] as $key=>$value)
	@if($value->field_property != "description")
		<input type="hidden" name="{{ $value->field_property ?? '' }}" class="{{ $value->field_property ?? '' }}" value="{{ $value->field_value ?? '' }}">
	@endif
@endforeach
<input type="hidden" name="form_field_id" class="form_field_id" value="{{ $data->form_field_id }}">
<input type="hidden" name="sort" class="sort" value="{{ $data->sort }}">
</div>
<!-- 
<input type="hidden" name="text_label" class="text_label">
<input type="hidden" name="format_validation" class="format_validation">
<input type="hidden" name="minimum" class="minimum">
<input type="hidden" name="maximum" class="maximum">
<input type="hidden" name="help_text" class="help_text">
<input type="hidden" name="required_field" class="required_field">
<input type="hidden" name="type_id" class="type_id" value="1"> -->