@extends('layouts.admin.app')
@section('title')
	Missing Writing Prompt Report
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
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Missing Writing Prompt Report</div></div>
    </div>
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])

    </div>

    <div class="">
        @include("layouts.admin.common.alerts")
            <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                        <div class="">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class=" mb-10">
                                        @if(count($programs) > 0)
                                            <div class="form-group">
                                                 <label for="">Select Program to Download Report : </label> 
                                                <div class="">
                                                    <select class="form-control custom-select" id="wp_program">
                                                        <option value="" selected>Select Program</option>
                                                        <option value="all">All Programs</option>
                                                        @forelse($programs as $program)
                                                            <option value="{{$program->id}}">{{$program->name}}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-center">No submission(s) found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </div>
@endsection
@section('scripts')
    {{-- <script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script> --}}
    {{-- <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script> --}}
	<script type="text/javascript">

        $(document).on('change', '#wp_program', function() {
            if ($(this).val() != '') {
                extra = '?req_program='+$(this).val();
                var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment_option").val()+"/writing_prompt"+extra;
                document.location.href = link;
            }
        }); 

	</script>

@endsection