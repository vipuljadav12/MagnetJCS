@extends('layouts.admin.app')

@section('title') Header & Footer Configuration @endsection

@section('content')

	<div class="card shadow">
	    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
	        <div class="page-title mt-5 mb-5">Header & Footer Configuration</div>
	    </div>
	</div>

    @include("layouts.admin.common.alerts")

    <form class="" action="{{url('admin/HeaderFooterConfig/store')}}" method="post">
    	{{csrf_field()}}
        <div class="card shadow">
            <div class="card-header">District Header</div>
            <div class="card-body">
                <textarea class="form-control ckeditor" name="config[district_header]" id="editor01">{!! $config_data['district_header'] or ''!!}</textarea>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">District Footer</div>
            <div class="card-body">
                <textarea class="form-control ckeditor" name="config[district_footer]" id="editor02">{!! $config_data['district_footer'] or '' !!}</textarea>
            </div>
        </div>
		<div class="box content-header-floating" id="listFoot">
	    	<div class="row">
		        <div class="col-lg-12 text-right hidden-xs float-right">
		            <button type="submit" class="btn btn-warning btn-xs submitBtn" name="save" value="save"><i class="fa fa-save"></i> Save
                	</button>
		        </div>
	    	</div>
		</div>
    </form>
@endsection

@section('scripts')
	<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
@endsection

