@extends('layouts.admin.app')
@section('title')
	Application Dates
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Setup Application Dates</div>
        	<div class="">
                @if((checkPermission(Auth::user()->role_id,'Application/create') == 1))
        		      <a href="{{ url('admin/Application/create') }}" class="btn btn-sm btn-secondary" title="Add">Add Application Dates</a>
                @endif
        		<a href="{{ url('admin/Application/trash') }}" class="btn btn-sm btn-danger d-none" title="Trash">Trash</a>
        	</div> 
    	</div>
    </div>
    <div class="card shadow">
        <!--<div class="card-header">Open Enrollment</div>-->
        <div class="card-body">
        @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table id="datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Application Name</th>
                            <th class="text-center">Parent Submission Form</th>
                            <th class="text-center">Open Enrollment</th>
                            <th class="text-center">Application Start Date</th>
                            <th class="text-center">Application End Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@forelse($applications as $key=>$application)
                        <tr>
                            <td class="text-center">{{$application->application_name}}</td>
                            <td class="text-center">{{$application->form_name}}</td>
                            <td class="text-center">{{$application->school_year}}</td>
                            <td class="text-center">{{getDateTimeFormat($application->starting_date)}}</td>
                            <td class="text-center">{{getDateTimeFormat($application->ending_date)}}</td>

                            <td class="text-center"><input id="{{$application->id}}" type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" @if ($application->status=='Y') checked="" @endif></td>
                            <td class="text-center">
                                @if((checkPermission(Auth::user()->role_id,'Application/edit') == 1))
                            	       <a href="{{ url('admin/Application/edit',$application->id) }}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
                                @endif
                                @if(checkApplicationStatus($application->id))
                                       <a href="javascript:void(0)" onclick="deletefunction({{$application->id}})" class="font-18 ml-5 mr-5 text-danger" title="Delete"><i class="far fa-trash-alt"></i></a>
                                @endif

                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
	<script type="text/javascript">
		$(".alert").delay(2000).fadeOut(1000);
		$('#datatable').DataTable({
                'columnDefs': [ {
                'targets': [2,3], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }]
        });

        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '{{url('admin/Application/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });
        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Application to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Application/delete/'+id;
            });
        };
	</script>
@endsection