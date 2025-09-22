<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Help Text?</label>
	<textarea class="form-control editorInput " name="help_text" data-for="help_text"  build-id="{{$build->id}}">{{getContentValue($build->id,"help_text")}}</textarea>

</div>
<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Is Mandatory?</label>
	<select class="form-control editorInput " name="required" data-for="required"  build-id="{{$build->id}}">
		<option value="no" @if(getContentValue($build->id,"required") != null && getContentValue($build->id,"required") == "no") selected @endif>No</option>
		<option value="yes" @if(getContentValue($build->id,"required") != null && getContentValue($build->id,"required") == "yes") selected @endif>Yes</option>
	</select>
</div>
<div class="col-12 m-t-5">
		<label class="m-b-5">Field ID</label>
		<input type="text" name="field_id" class="form-control editorInput" data-for="field_id" build-id="{{$build->id}}" value="{{getContentValue($build->id,"field_id") ?? ""}}">
	</div>
	<div class="col-12 m-t-5">
		<label class="m-b-5">Initial Display Off</label>
		<select class="form-control editorInput " name="initial_display" data-for="initial_display"  build-id="{{$build->id}}">
		<option value="no" @if(getContentValue($build->id,"initial_display") != null && getContentValue($build->id,"initial_display") == "no") selected @endif>No</option>
		<option value="yes" @if(getContentValue($build->id,"initial_display") != null && getContentValue($build->id,"initial_display") == "yes") selected @endif>Yes</option>
	</select>
	</div>
<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Is Unique Field?</label>
	<select class="form-control editorInput " name="check_unique" data-for="check_unique"  build-id="{{$build->id}}">
		<option value="no" @if(getContentValue($build->id,"check_unique") != null && getContentValue($build->id,"check_unique") == "no") selected @endif>No</option>
		<option value="yes" @if(getContentValue($build->id,"check_unique") != null && getContentValue($build->id,"check_unique") == "yes") selected @endif>Yes</option>
	</select>
</div>
<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Select Student Type</label>
	<select class="form-control editorInput " name="show_in_exist" data-for="show_in_exist"  build-id="{{$build->id}}">
		<option value="new" @if(getContentValue($build->id,"show_in_exist") != null && getContentValue($build->id,"show_in_exist") == "new") selected @endif>New Student</option>

		<option value="exist" @if(getContentValue($build->id,"show_in_exist") != null && getContentValue($build->id,"show_in_exist") == "exist") selected @endif>Exist Only</option>

		<option value="both" @if(getContentValue($build->id,"show_in_exist") != null && getContentValue($build->id,"show_in_exist") == "both") selected @endif>Both Form</option>

	</select>
</div>
<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Display in View?</label>
	<select class="form-control editorInput " name="display_view" data-for="display_view"  build-id="{{$build->id}}">
		<option value="no" @if(getContentValue($build->id,"display_view") != null && getContentValue($build->id,"display_view") == "no") selected @endif>No</option>
		<option value="yes" @if(getContentValue($build->id,"display_view") != null && getContentValue($build->id,"display_view") == "yes") selected @endif>Yes</option>
	</select>
</div>
<div class="col-12 m-t-5 editor-col-spaces p-10">
	<label class="m-b-5">Select Database Field</label>
	@php
		/*$db_fields = array(
			"s_id",
			"s_first_name",
			"s_last_name",
			"s_email",
			"s_contact",
			"s_address",
			"s_grades",
			"s_subjects",
		);	*/
		$columns = Schema::getColumnListing("submissions");
		$db_fields = array_diff($columns, ["id","created_at","updated_at"]);
		// print_r($columns);
	@endphp
	<select class="form-control editorInput " name="db_field" data-for="db_field"  build-id="{{$build->id}}">
		<option value="">Map Database Field</option>
		@foreach($db_fields as $d=>$db_field)
			<option value="{{$db_field}}" @if(getContentValue($build->id,"db_field") != null && getContentValue($build->id,"db_field") == $db_field) selected @endif>{{$db_field}}</option>
		@endforeach
	</select>

	
</div>
<div class="col-12 m-t-5 editor-col-spaces text-center p-10">
	<a href="javascript:void(0)" onclick="saveFieldInfo()" class="btn btn-success"><i class="fa fa-save"></i> Save Field Info</a>
</div>