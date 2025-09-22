<div class="form-group">
	@if(isset($data['choice']) && $data['choice']!='')
	<!--<fieldset>-->
		@if(isset($data['text_label']))
			<label>
				{{$data['text_label']}}@if(isset($data['required_field']) && $data['required_field']=='yes')<span class="text-danger">*</span>@endif
				<span class="help" data-toggle="tooltip" title="{{$data['help_text']}}">
			        @if($data['help_text'] != "")
			            <i class="fas fa-info-circle"></i>
			        @endif
			    </span>
			</label>
		@endif
		@php($choices = explode(',', $data['choice']))
		<select @if(isset($data['required_field']) && $data['required_field']=='yes') required @endif class="form-control" @if(isset($data['text_label'])) thisname="{{$data['text_label']}}" @endif name="formdata[{{$field_id}}]">
			<option value="">- Please Select -</option>
			@foreach($choices as $key=>$value)		
			<option @if(isset($data['selected']) && $data['selected']== $value) selected="selected" @endif value="{{$value}}">{{$value}}</option>
	    	@endforeach
		</select>
	<!--</fieldset>-->
    @endif
</div>