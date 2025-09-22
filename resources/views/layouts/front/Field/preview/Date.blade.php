<div class="form-group row">
	@if(isset($data['label']))
		<label class="control-label col-12 col-md-4 col-xl-3">{{$data['label']}}@if(isset($data['required']) && $data['required']=='yes')<span class="text-danger">*</span>@endif
			</label>
		
	@endif
	{{-- @if($data['db_field']) --}}
	{{-- {{$data['db_field']}} --}}
	<div class="col-12 col-md-6 col-xl-6">
		<input type="hidden" class="form-control" id="birthdayFiller" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @if(isset($data['required']) && $data['required']=='yes') required @endif value="{{ Session::get("form_data")[0]['formdata'][$field_id]  ?? ''}}">
		@if($data['db_field'] == "birthday")
			@php $months = Config::get('variables.months') @endphp
			<div class="row">
				@php
					$birthday_cut_off = date("Y-m-d");
					$year = date("Y",strtotime($birthday_cut_off));
					$month = date("m",strtotime($birthday_cut_off));
					$day = date("d",strtotime($birthday_cut_off));
				@endphp
				
				<input type="hidden" name="" value="{{$day}}" id='limitDay'>
				<input type="hidden" name="" value="{{$year}}" id='limitYear'>
				<input type="hidden" name="" value="{{$month}}" id='limitMonth'>
				<div class="col-4">
					<select class="form-control">
						 <option>Month</option>
						@for($m=1;$m<= 12;$m++)								
							<option value="{{$m}}">{{$months[$m]}}</option>
						@endfor 
					</select>
				</div>

				<div class="col-4">
					<select class="form-control">
						<option>Day</option>
						@for($d=1;$d<= 31;$d++)
							<option value="{{$d}}">{{$d}}</option>
						@endfor
					</select>
				</div>
				<div class="col-4">
					<select class="form-control changeDate" id="year">
						 <option>Year</option>
						@for($y=date("Y");$y > 1990;$y--)								
							<option value="{{$y}}">{{$y}}</option>
						@endfor
					</select>
				</div>
			</div>
		@else
			<input type="text" class="form-control mydatepicker" @if(isset($data['label'])) thisname="{{$data['label']}}" @endif name="formdata[{{$field_id}}]" @if(isset($data['placeholder'])) placeholder="{{$data['placeholder']}}" @endif @if(isset($data['required']) && $data['required']=='yes') required @endif>
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