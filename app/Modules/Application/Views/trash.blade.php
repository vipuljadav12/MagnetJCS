@extends('layouts.admin.app')
@section('title')
	Trash Application Dates | {{config('APP_NAME',env("APP_NAME"))}}
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Trash Application Dates</div>
        	<div class="">
        		<a href="{{ url('admin/Application') }}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a>
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
                            <th class="">Parent Submission Form</th>
                            <th class="">Open Enrollment</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@forelse($applications as $key=>$application)
                        <tr>
                            <td class="">{{$application->form_name}}</td>
                            <td class="">{{$application->school_year}}</td>
                            <td class="text-center">
                            	<a href="{{ url('admin/Application/restore',$application->id) }}" class="font-18 ml-5 mr-5" title="Trash"><i class="fas fa-undo"></i></a>
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
                'targets': [2], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }]
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