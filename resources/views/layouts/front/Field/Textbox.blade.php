<div class="form-group row" @if($data['db_field']=="employee_id" || $data['db_field']=="work_location" || $data['db_field']=="employee_first_name" || $data['db_field']=="employee_last_name" || $data['db_field']=="zoned_school") id="{{$data['db_field']}}" style="display: none" @endif>
	@if(isset($data['label']))
		<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif

			</label>
		
	@endif
	<div class="col-12 col-md-6 col-xl-6">
		@if($data['db_field'] == "address")
			<div class="mb-10">
		@endif
		<input type="text" class="form-control" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif name="formdata[{{$field_id}}]"  @if(isset($data['required']) && $data['required']=='yes' && $data['db_field'] != "zoned_school") required @endif value="{{ Session::get("form_data")[0]['formdata'][$field_id]  ?? ''}}" @if(isset($data['db_field']) && ($data['db_field']=="phone_number" || $data['db_field']=="alternate_number")) placeholder="(___) ___-____" @else @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @endif @if($data['db_field']=="student_id") onblur="checkStudentID(this)" @endif id="{{$data['db_field']}}" > @if($data['db_field']=="student_id") <span class="hidden">
                        Checking Student ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span> @endif
		{{-- <input type="text" class="form-control" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @if(isset($data['required']) && $data['required']=='yes') required @endif> --}}

		@if($data['db_field'] == "address")
			</div>
		@endif
		@if($data['db_field'] == "address")
			<div class="alert alert-danger d-none" id="address_text">
				<p>{!! getConfig()['address_selection_option'] !!}</p>
				<div class=""  id="address_options">
				</div>
				
			</div>
		@endif
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

	
</div>