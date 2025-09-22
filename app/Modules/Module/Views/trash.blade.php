@extends('layouts.admin.app')
@section('title')
	Module Masters
@endsection
@section('content')
<div class="card shadow">
	<div class="card-body d-flex align-items-center justify-content-between flex-wrap">
		<div class="page-title mt-5 mb-5">Trash Modules</div>
		<div class="">
			<a href="{{url('admin/Module')}}" class="btn btn-sm btn-success" title="Go Back">Go Back</a>
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
								<td class="">{{$value->slug}}</td>
								<td class="text-center">
									<a href="{{url('/admin/Module/restore/'.$value->id)}}" class="font-18 ml-5 mr-5" title=""><i class="fas fa-undo"></i></a>
									<a href="{{url('/admin/Module/destroy/'.$value->id)}}" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
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
</script>
@endsection