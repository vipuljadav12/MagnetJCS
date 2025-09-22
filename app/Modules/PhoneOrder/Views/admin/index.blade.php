@extends("layouts.admin.app")
@section("content")
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Phone Order</div>        
    </div>
</div>
<div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="row">
            	<div class="col-12">
            		<iframe src="{{url("/")}}" style="width: 100% !important;height:80vh !important;"></iframe>
            	</div>
            </div>
        </div>
    </div>
@endsection