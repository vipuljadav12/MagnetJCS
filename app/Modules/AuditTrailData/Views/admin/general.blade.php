<div class="table-responsive">
    <table id="datatable-general" class="table table-striped mb-0">
        <thead>
        <tr>
            <th class="align-middle w-120 text-center">ID</th>
			<th class="text-center">Module</th>
            <th class="align-middle">Old Data</th>
            <th class="align-middle">New Data</th>
            <th class="align-middle">Updated On</th>
            <th class="align-middle text-center">User</th>
            {{-- <th class="align-middle text-center">Action</th> --}}
        </tr>
        </thead>
        <tbody>
        	@if(isset($audit_trails['general']) && count($audit_trails['general']) > 0)
				@foreach($audit_trails['general'] as $a => $audit_trail)
					@if(isset($audit_trail->changed_fields) && $audit_trail->changed_fields != "[]")
					<tr>
						<td class="text-center">{{$loop->index +1}}</td>
						<td class="text-center">{{$audit_trail->module ?? '-'}}</td>
						<td>
							<div>	
								@foreach($audit_trail->old_values as $o => $old)
									@if(isset($old) && isset($o))
										<span class="text-strong">{{ucwords(str_replace("_"," ",($o)))}} : </span>
										@if(in_array($o,$audit_trail->changed_fields))
											<span class="text-danger">
										@else
											<span class="text">
										@endif
											 {{$old}}<br>
										</span>
									@endif
								@endforeach
							</div>
						</td>
						<td>
							<div>	
								@foreach($audit_trail->new_values as $n => $new)
									@if(isset($new))
										<span class="text-strong">{{ucwords(str_replace("_"," ",($n)))}} : </span>
										@if(in_array($n,$audit_trail->changed_fields))
											<span class="text-success">
										@else
											<span class="text">
										@endif
											 {{$new}}<br>
										</span>
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
					@endif
				@endforeach
			@endif
        </tbody>
    </table>
</div>