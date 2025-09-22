<div class="form-group row">
	
	@if(isset($data['radio_1']) && $data['radio_1']!='')
		@if(isset($data['label']))
			<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif 
			</label>
				
		@endif
		<div class="col-12 col-md-6 col-xl-6">
				<div class="custom-control custom-radio custom-control-inline">
				@foreach($data as $key=>$value)
					@if(substr($key, 0, 6)=="radio_")
						<input type="radio" style="left: 0 !important; opacity: 1 !important; position: inherit !important;" value="{{$value}}" name="formdata[{{$field_id}}]" @if($data['db_field']=="mcp_employee") onchange="showHideEmployee(this)" @endif>&nbsp;{{$value}}&nbsp;&nbsp;
					@endif
				@endforeach
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
	<!--<fieldset>-->
		
    @endif
</div>