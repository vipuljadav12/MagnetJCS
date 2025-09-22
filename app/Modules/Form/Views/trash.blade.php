@extends('layouts.admin.app')
@section('title')
	Trash Form | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('content')	
	<div class="card shadow">
	    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
	        <div class="page-title mt-5 mb-5">Trash Form</div>
	        <div class="">
	        	<a href="{{ url('admin/Form') }}" class="btn btn-sm btn-secondary" title="">Back</a>
	        </div>
	    </div>
	</div>
	<div class="card shadow">
	    <div class="card-body">
	    	@include("layouts.admin.common.alerts")
	        <div class="table-responsive">
	            <table id="formTable" class="table table-striped mb-0">
	                    <thead>
	                        <tr>
	                            <th class="align-middle w-90 text-center">Sr. No.</th>
	                            <th class="align-middle">Form Name</th>
	                            <th class="align-middle w-20">Update Date &amp; Time</th>
	                            <th class="align-middle w-200 text-center">Action</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    	@forelse($forms as $key=>$form)
		                        <tr>
		                            <td class="text-center">{{$key+1}}</td>
		                            <td class="">{{$form->name}}</td>
		                            <td class="">{{$form->updated_at}}</td>
		                            <td class="text-center">
		                            	<a href="{{ url('admin/Form/restore',$form->id) }}" class="font-18 ml-5 mr-5" title=""><i class="fas fa-undo"></i></a>
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
	$('#formTable').DataTable({
            'columnDefs': [ {
                'targets': [3], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }]
        });	
	</script>
@endsection
