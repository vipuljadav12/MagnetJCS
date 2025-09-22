@extends('layouts.admin.app')
@section('title')
	Missing Submission Recommendation
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
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Admin Review</div>
        </div>
    </div>
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])

    </div>

    <div class="card shadow">
        <div class="card-body">
            @if(count($programs) > 0)
                <div class="form-group">
                    <label for="">Programs : </label>
                    <div class="">
                        <select class="form-control custom-select" id="recomm_program">
                            <option value="All">All</option>
                            @foreach($programs as $key=>$value)
                                <option value="{{$value}}">{{getProgramName($value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="">
                    <a href="javascript:void(0);" onclick="exportReport()" class="btn btn-secondary" title="Export Report">Export Missing Recommendation</a>
                </div>
            @else
                    <p class="text-center">No submission(s) found.</p>
            @endif
        </div>
    </div>

@endsection
@section('scripts')
	<script type="text/javascript">
        

        function exportReport(){
            var program = $('#recomm_program').val();   
            var enrollment = $(document).find(".enroll").val();
            var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment_option").val()+"/recommendation/export/"+program;
                document.location.href = link;         
        }
	</script>

@endsection