@extends('layouts.admin.app')
@section('title')
	Module Master
@endsection
@section('content')
<div class="card shadow">
	<div class="card-body d-flex align-items-center justify-content-between flex-wrap">
		<div class="page-title mt-5 mb-5">Modules</div>
		<div class="">
			<a href="{{url('admin/Module/create')}}" class="btn btn-sm btn-secondary" title="Add Modules">Add Modules</a>
			<a href="{{url('admin/Module/trash')}}" class="btn btn-sm btn-danger" title="View Trash">View Trash</a>
		</div>
	</div>
</div>
@include('layouts.admin.common.alerts')
<div class="card shadow">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped mb-0" id="example">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="">MODULE</th>
						<th class="">DISPLAY NAME</th>
						<th class="">SLUG</th>
						<th class="text-center">ACTION</th>
					</tr>
				</thead>
				<tbody>
					@if(isset($data['modules']))
						@foreach($data['modules'] as $key=>$value)
							<tr>
								<td class="text-center">{{$loop->index + 1}}</td>
								<td class="">{{$value->name}}</td>
								<td class="">{{$value->display_name}}</td>
								<td class="">{{$value->slug}}</td>
								<td class="text-center">
									<a href="{{url('/admin/Module/edit/'.$value->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
									<a href="javascript:void(0);" data-value="{{$value->id}}" class="font-18 ml-5 mr-5 text-danger moduleDelete" title=""><i class="far fa-trash-alt"></i></a>
								</td>
							</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	$(document).ready(function (){
        $('#example').DataTable();
    });
    $(document).on('click', '.moduleDelete', function(){
        var module_id = $(this).attr('data-value');
        // alert(module_id);
       	swal({
            title: "Are you sure you would like to move this Module to Trash ?",
            text: "",
            // type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }).then(function(){
            window.location.href = '{{url('/')}}/admin/Module/delete/'+module_id;
        });

    });
</script>
@endsection