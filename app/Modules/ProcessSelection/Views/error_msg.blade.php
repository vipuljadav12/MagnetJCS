@extends('layouts.admin.app')
@section('title')
	Selection Report Master
@endsection
@section('content')
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
.dt-buttons {position: absolute !important;}
.w-50{width: 50px !important}
.content-wrapper.active {z-index: 9999 !important}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection</div>
        </div>
    </div>

    
        
    <div class="">
            <div class="">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="alert1 alert-danger">
                                Process Selection cannot be executed due to following error:<br><br>
                                <ul>
                                    {!! $error_msg !!}
                                </ul>
                                @if($type == '')
                                    @php $link = url('/admin/Process/Selection/run') @endphp
                                @else
                                    @php $link = url('/admin/Process/Selection/run') @endphp
                                @endif
                                <p><a href="{{$link}}" class="btn btn-primary">Back to Selection Process</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
