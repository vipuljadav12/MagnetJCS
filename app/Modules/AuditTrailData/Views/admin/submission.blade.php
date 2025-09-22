<div class="table-responsive">
	<div class="row col-md-12 pb-20" id="submission_filters"></div>

    <table id="datatable" class="table table-striped mb-0">
        <thead>
        <tr>
            <th class="align-middle w-120 text-center">ID</th>
			<th class="text-center">Enrollment Year</th>
			<th class="text-center">Application Name</th>
            <th class="align-middle">Old Data</th>
            <th class="align-middle">New Data</th>
            <th class="align-middle">Updated On</th>
            <th class="align-middle text-center">User</th>
            {{-- <th class="align-middle text-center">Action</th> --}}
        </tr>
        </thead>
        <tbody>
        	@if(isset($audit_trails['submission']) && count($audit_trails['submission']) > 0)
				@foreach($audit_trails['submission'] as $a => $audit_trail)

					@if(isset($audit_trail->changed_fields) && $audit_trail->changed_fields != "[]" && $audit_trail->changed_fields != "")
					<tr>
						<td class="text-center">{{$loop->index +1}}</td>
						<td class="text-center">{{getEnrollmentYear($audit_trail->enrollment_id)}}</td>
						<td class="text-center">{{getApplicationName($audit_trail->application_id)}}</td>

						<td>
							<div>	
								@foreach($audit_trail->old_values as $o => $old)
									@if(isset($old) && isset($o))
										@if($o == "gender" || $o == "letter_body")
											@php continue; @endphp
										@endif
										@if($o=="id" || $o=="submission_id")
											<span class="text-strong">Submission ID : </span>
										
										@elseif($o == "employee_id")
											<span class="text-strong">Employee ID : </span>
										@elseif($o == "mcp_employee")
											<span class="text-strong"></span>
										@elseif($o == "given_score")
											<span class="text-strong">Academic Score Calculation: </span>	
										@elseif($o != "ts_data")
											<span class="text-strong">{{ucwords(str_replace("_"," ",($o)))}} : </span>
										@endif

										@if($o != "ts_data")
											@if($audit_trail->changed_fields != '' && in_array($o,$audit_trail->changed_fields))
												<span class="text-danger">
											@else
												<span class="text">
											@endif
											@if($o=="id" || $o=="submission_id")
												<a href="{{url('/admin/Submissions/edit/'.$old)}}"  target="_blank">{{$old}}</a>
											@elseif($o == "gender" || $o == "letter_body")
											@else
												{{$old}}
											@endif<br>

											@if($o=="id" || $o=="submission_id")
												@if(getSubmissionStudentName($old) != "")
													<span class="text-strong">Student Name : </span>
													<span class="text">{{getSubmissionStudentName($old)}}</span><br>
												@endif
											@endif
										@else
											@foreach($old as $ot=>$ov)
												@if($ov != '')
													<span class="text-strong">{{$ot}} : </span>
													@php $alclass = "" @endphp
													@if(in_array($ot, $audit_trail->changed_fields))
														@php $alclass = "text-danger" @endphp
													@endif	
													<span class="text {{$alclass}}">{{$ov}}</span><br>
												@endif
											@endforeach
										@endif
										</span>
									@endif
								@endforeach
							</div>
						</td>
						<td>
							<div>	
								@foreach($audit_trail->new_values as $n => $new)
									@if(isset($new))
										@if($n == "ts_data")
											@foreach($new as $ot=>$ov)
												@if($ov != '' && $ov != null)
													<span class="text-strong">{{$ot}} : </span>
													@php $alclass = "" @endphp
													@if(in_array($ot, $audit_trail->changed_fields))
														@php $alclass = "text-success	" @endphp
													@endif	
													<span class="text {{$alclass}}">{{$ov}}</span><br>
												@endif
											@endforeach
										@else
											@if($n=="id" || $n=="submission_id")
												<span class="text-strong">Submission ID : </span>
											@elseif($n == "gender")	
												<span class="text-strong">Comment : </span>
											@elseif($n == "letter_body")	
												<span class="text-strong">Submission Status Comment : </span>
											@elseif($n == "employee_id")
													<span class="text-strong">Employee ID : </span>
											@elseif($n == "mcp_employee")
													<span class="text-strong">MCPSS Employee : </span>
											@elseif($n == "given_score")
													<span class="text-strong">Academic Score Calculation: </span>	
												
											@else
												<span class="text-strong">{{ucwords(str_replace("_"," ",($n)))}} : </span>
											@endif

											@if(in_array($n,$audit_trail->changed_fields))
												<span class="text-success">
											@else
												<span class="text">
											@endif

											@if($n=="id" || $n =="submission_id")
												<a href="{{url('/admin/Submissions/edit/'.$new)}}"  target="_blank">{{$new}}</a>
											@else
												 {{$new}}
											@endif<br>

											@if($n=="id" || $n =="submission_id")
												@if(getSubmissionStudentName($new) != "")
													<span class="text-strong">Student Name : </span>
													<span class="text">{{getSubmissionStudentName($new)}}</span><br>
												@endif
											@endif
											</span>
										@endif
									@endif
								@endforeach
							</div>
						</td>
						<td>	
							{{getDateTimeFormat($audit_trail->created_at)}}
						</td>
						<td>	
							{{$audit_trail->user->full_name ?? ""}}
						</td>

					</tr>
					@elseif($audit_trail->changed_fields == "" && $audit_trail->old_values != "")
						<tr>
							<td class="text-center">{{$loop->index +1}}</td>
							<td class="text-center">{{getEnrollmentYear($audit_trail->enrollment_id)}}</td>
							<td class="text-center">{{getApplicationName($audit_trail->application_id)}}</td>
							<td></td>
							<td>
								<div>	
									@foreach($audit_trail->old_values as $o => $old)
										@if(isset($old) && isset($o))
											@if($o=="id" || $o=="submission_id")
												<span class="text-strong">Submission ID : </span>
											@elseif($o == "employee_id")
												<span class="text-strong">Employee ID : </span>
											@elseif($o == "mcp_employee")
												<span class="text-strong">MCPSS Employee : </span>
											@elseif($o == "given_score")
											<span class="text-strong">Academic Score Calculation: </span>	

											@elseif($o != "ts_data")
												<span class="text-strong">{{ucwords(str_replace("_"," ",($o)))}} : </span>
											@endif
											
												
												<span class="text">
												@if($o=="id" || $o=="submission_id")
													<a href="{{url('/admin/Submissions/edit/'.$old)}}" target="_blank">{{$old}}</a>
												@elseif($o == "ts_data")
													@foreach($old as $ot=>$ov)
														<span class="text-strong">{{$ot}} : </span>
													<span class="text">{{$ov}}</span><br>
													@endforeach
												@else

													 {{$old}}
												@endif
												 <br>
											</span>
											@if($o=="id" || $o=="submission_id")
												@if(getSubmissionStudentName($old) != "")
													<span class="text-strong">Student Name : </span>
													<span class="text">{{getSubmissionStudentName($old)}}</span><br>
												@endif
											@endif
										@endif
									@endforeach
								</div>
							</td>
							
							<td>	
								{{getDateTimeFormat($audit_trail->created_at)}}
							</td>
							<td>	
								{{$audit_trail->user->full_name ?? ""}}
							</td>

						</tr>
					@else
					{{$audit_trail->changed_fields}}
					@endif
				@endforeach
			@endif
        </tbody>
    </table>
</div>