@if(isset($formContents) && !empty($formContents))
	@foreach($formContents as $f=>$formContent)
		{{-- {{getFieldBox($formContent->field_id)->type}} --}}
		{{-- {{$formContent}} --}}
		@include("Form::container.".getFieldBox($formContent->field_id)->type,compact("formContent"))
	@endforeach
@endif