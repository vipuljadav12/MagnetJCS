@extends('layouts.admin.app')
@section('title')
	Edit Module
@endsection
@section('content')
	<div class="card shadow">
		<div class="card-body d-flex align-items-center justify-content-between flex-wrap">
			<div class="page-title mt-5 mb-5">Edit Modules</div>
			<div class=""><a href="{{url('admin/Module')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div>
		</div>
	</div>
	@include('layouts.admin.common.alerts')
	<form class="" action="{{url('admin/Module/update')}}" method="POST" enctype="multipart/form-data" id="districtForm">
		{{csrf_field()}}
		<input type="hidden" name="id" value="{{$data['module']->id}}">
		<div class="tab-content bordered" id="myTabContent">
			<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
				<div class="">
					<div class="table-responsive">
						<table class="table mb-0">
							<tbody>
								<tr>
									<td class="w-50">
										<div class="">
											<label class="">Module Name : </label>
											<div class="">
												<input type="text" class="form-control" value="{{$data['module']->name}}" name="name">
												@if($errors->has('name'))
													<span class="help-block">
														<strong>{{ $errors->first('name') ?? ''}}</strong>
													</span>
												@endif
											</div>
										</div>
									</td>
									<td class="w-50">
										<div class="">
											<label class="">Display Name : </label>
											<div class="">
												<input type="text" class="form-control" value="{{$data['module']->display_name}}" name="display_name">
												@if($errors->has('display_name'))
													<span class="help-block">
														<strong>{{ $errors->first('display_name') ?? ''}}</strong>
													</span>
												@endif
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="w-50">
										<div class="">
											<label class="">Slug : </label>
											<div class="">
												<input type="text" class="form-control" value="{{$data['module']->slug}}" name="slug">
												@if($errors->has('slug'))
													<span class="help-block">
														<strong>{{ $errors->first('slug') ?? ''}}</strong>
													</span>
												@endif
											</div>
										</div>
									</td>
									<td class="w-50">
										<div class="">
											<label class="">Sort : </label>
											<div class="">
												<input type="text" class="form-control" value="{{$data['module']->sort}}" name="sort">
												@if($errors->has('sort'))
													<span class="help-block">
														<strong>{{ $errors->first('sort') ?? ''}}</strong>
													</span>
												@endif
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	
		<div class="box content-header-floating" id="listFoot">
	        <div class="row">
	            <div class="col-lg-12 text-right hidden-xs float-right">
	            	<button class="btn btn-warning btn-xs" type="submit" name="save" value="save"><i class="fa fa-save mr-2"></i>Save Module</button>
	            	<button class="btn btn-success btn-xs" type="submit" name="save_exit" value="save_exit"><i class="fa fa-save mr-2"></i>Save &amp; Exit</button>
	            		
	            	<a class="btn btn-danger btn-xs" href="{{url('admin/Module/')}}">
	            		<i class="far fa-trash-alt"></i> Cancel
	            	</a> 
	            </div>
	        </div>
	    </div>
	</form>
@endsection
@section('scripts')
<script type="text/javascript">
	
</script>
@endsection