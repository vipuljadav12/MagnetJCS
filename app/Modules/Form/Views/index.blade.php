@extends('layouts.admin.app')
@section('title')
	Form | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('content')	
	<div class="card shadow">
	    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
	        <div class="page-title mt-5 mb-5">Form Master</div>
	        <div class="">
	        	<a href="{{ url('admin/Form/create') }}" class="btn btn-sm btn-secondary" title="Add">Add Form</a>
	        	<a href="{{ url('admin/Form/trash') }}" class="btn btn-sm btn-danger" title="Trash">Trash</a>
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
	                            <th class="align-middle w-120 text-center">Status</th>
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
		                            	<input type="checkbox" id="{{$form->id}}" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" {{$form->status=='y'?'checked':''}} />
		                            </td>
		                            <td class="text-center">
		                            	<a href="{{url('/previewform/1'.'/'.$form->id)}}" target="_blank" class="font-18 ml-5 mr-5" title="Preview"><i class="fas fa-external-link-alt"></i></a>
		                            	<a href="{{ url('admin/Form/edit/1',$form->id)}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
		                            	<a href="javascript:void(0)" onclick="deletefunction({{$form->id}})" class="font-18 ml-5 mr-5 text-danger" title="Trash"><i class="far fa-trash-alt"></i></a>
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
                'targets': [3,4], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }]
        });	
	 //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Form to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Form/delete/'+id;
            });
        };

        $('.status').change(function(){
        	var click=$(this).prop('checked')==true ? 'y' : 'n' ;
            $.ajax({
                type: "get",
                url: '{{url('admin/Form/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });

	</script>
@endsection
