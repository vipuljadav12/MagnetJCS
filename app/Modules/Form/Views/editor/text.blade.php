<div class="row editorBox">
	@include("Form::editor.title")
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Field Name</label>
		<input type="text" name="label" class="form-control editorInput" data-for="label" build-id="{{$build->id}}" value="{{getContentValue($build->id,"label") ?? ""}}">
	</div>
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Placeholder Texts</label>
		<input type="text" name="placeholder" class="form-control editorInput"  data-for="placeholder" build-id="{{$build->id}}" value="{{getContentValue($build->id,"placeholder") ?? ""}}">
	</div>
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Number of Characters</label>
		<div class="d-flex align-items-center m-t-5">
			<input type="text" name="min" class="form-control w-30 editorInput"  data-for="min" build-id="{{$build->id}}" value="{{getContentValue($build->id,"min") ?? ""}}">
			<div class="ml-5 mr-5">to</div>
			<input type="text" name="max" class="form-control editorInput w-30"  data-for="max" build-id="{{$build->id}}" value="{{getContentValue($build->id,"max") ?? ""}}">
		</div>
	</div>
	{{-- <div class="col-12 m-t-5 editor-col-spaces">
		@php
			$required = getContentValue($build->id,"required");
			$v = isset($required) ?  "checked" : "";
		@endphp
		<label class="m-b-5">Required</label>
		<input type="checkbox" name="required" class="editorInput js-switch" {{$v}}  data-for="required" build-id="{{$build->id}}">
	</div> --}}
	@include("Form::editor.common")
	{{-- <div class="col-12 m-t-5 editor-col-spaces">
		<label class="m-b-5">Check Unique ?</label>
		<select class="form-control-sm editorInput " name="check_unique" data-for="check_unique"  build-id="{{$build->id}}">
			<option value="yes" @if(getContentValue($build->id,"check_unique") != null && getContentValue($build->id,"check_unique") == "yes") selected @endif>Yes</option>
			<option value="no" @if(getContentValue($build->id,"check_unique") != null && getContentValue($build->id,"check_unique") == "no") selected @endif>No</option>
		</select>
	</div>
	<div class="col-12 m-t-5 editor-col-spaces">
		<label class="m-b-5">Show in Existing Student ?</label>
		<select class="form-control-sm editorInput " name="show_in_exist" data-for="show_in_exist"  build-id="{{$build->id}}">
			<option value="yes" @if(getContentValue($build->id,"show_in_exist") != null && getContentValue($build->id,"show_in_exist") == "yes") selected @endif>Yes</option>
			<option value="no" @if(getContentValue($build->id,"show_in_exist") != null && getContentValue($build->id,"show_in_exist") == "no") selected @endif>No</option>
		</select>
	</div>
	<div class="col-12 m-t-5 editor-col-spaces">
		<label class="m-b-5">Map to Database Field</label>
		<select class="form-control-sm editorInput " name="db_field" data-for="db_field"  build-id="{{$build->id}}">
			<option value="yes" @if(getContentValue($build->id,"db_field") != null && getContentValue($build->id,"db_field") == "yes") selected @endif>Yes</option>
			<option value="no" @if(getContentValue($build->id,"db_field") != null && getContentValue($build->id,"db_field") == "no") selected @endif>No</option>
		</select>
	</div> --}}
	{{-- <div class="col-12 m-t-5">
		<div class="text-right">
			<button class="btn btn-success "><i class="fa fa-save"></i></button>
		</div>
	</div> --}}
</div>