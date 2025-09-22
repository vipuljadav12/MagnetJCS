<div class="row editorBox">
	@include("Form::editor.title")
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Title Text</label>
		<input type="text" name="label" class="form-control editorInput" data-for="label" build-id="{{$build->id}}" value="{{getContentValue($build->id,"label") ?? ""}}">
	</div>
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Second Program Display</label>
		<select class="form-control editorInput " name="second_display" data-for="second_display"  build-id="{{$build->id}}">
			<option value="yes" @if(getContentValue($build->id,"second_display") != null && getContentValue($build->id,"second_display") == "yes") selected @endif>Yes</option>
			<option value="no" @if(getContentValue($build->id,"second_display") != null && getContentValue($build->id,"second_display") == "no") selected @endif>No</option>

	</select>
	</div>
	<div class="col-12 m-t-5 editor-col-spaces p-10">
		<label class="m-b-5">Choice Count Display</label>
		<select class="form-control editorInput " name="choice_count" data-for="choice_count"  build-id="{{$build->id}}">
			<option value="1" @if(getContentValue($build->id,"choice_count") != null && getContentValue($build->id,"choice_count") == "1") selected @endif>1</option>
			<option value="2" @if(getContentValue($build->id,"choice_count") != null && getContentValue($build->id,"choice_count") == "2") selected @endif>2</option>
			<option value="3" @if(getContentValue($build->id,"choice_count") != null && getContentValue($build->id,"choice_count") == "3") selected @endif>3</option>
	</select>
	</div>
	@include("Form::editor.common")
</div>