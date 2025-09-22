<div class="row editorBox">
	@include("Form::editor.title")
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-0">Field Name</label>
		<input type="text" name="label" class="form-control editorInput" data-for="label" build-id="{{$build->id}}" value="{{getContentValue($build->id,"label") ?? ""}}">
	</div>
	<div class="col-12 m-t-5 optionBox">
		<label class="m-b-0">Options</label>
		<div class="{{-- d-flex align-items-center  --}}m-t-5">
			@php
				$currentOptions = getContentCheckbox($build->id);
				$count = 0;
			@endphp
			@if(isset($currentOptions) && !empty($currentOptions))
				@foreach($currentOptions as $c=>$currentOption)					
					<div class="row m-t-5">
						<div class="col-6">
							<input type="text" name="{{$currentOption->field_property}}" class="form-control editorInput checkboxList"  data-for="{{$currentOption->field_property}}" build-id="{{$build->id}}" value="{{getContentValue($build->id,$currentOption->field_property) ?? ""}}">
						</div>
						<div class="col-6">
							<a class="btn text-primary addMoreCheckbox"><i class="fa fa-plus"></i></a>
							<a class="btn text-primary removeMoreCheckbox"  content-id="{{$currentOption->id}}"><i class="fa fa-minus"></i></a>
						</div>
					</div>
					@php $count = $count + 1; @endphp
				@endforeach
			@endif
			@if($count == 0)
				<div class="row m-t-5">
					<div class="col-6">
						<input type="text" name="checkbox_1" class="form-control  editorInput checkboxList"  data-for="checkbox_1" build-id="{{$build->id}}" value="{{getContentValue($build->id,"checkbox_1") ?? ""}}">
					</div>
					<div class="col-6">
						<a class="btn text-primary addMoreCheckbox"><i class="fa fa-plus"></i></a>
						<a class="btn text-primary removeMoreCheckbox d-none"><i class="fa fa-minus"></i></a>
					</div>
				</div>
			@endif
		</div>
	</div>
	{{-- <div class="col-12 m-t-5 mb10 m-b-5">
		@php
			$required = getContentValue($build->id,"required");
			$v = isset($required) ?  "checked" : "";
		@endphp
		<label class="m-b-0">Required</label>
		<input type="checkbox" name="required" class="editorInput js-switch" {{$v}}  data-for="required" build-id="{{$build->id}}">
	</div>
	<div class="col-12 m-t-5">
		<div class="text-right">
			<button class="btn btn-success "><i class="fa fa-save"></i></button>
		</div>
	</div> --}}
	@include("Form::editor.common")
	
</div>