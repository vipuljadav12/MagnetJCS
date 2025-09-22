@extends('layouts.admin.app')
@section('title')
	Program Pre-Req Report
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
            <div class="page-title mt-5 mb-5">Program Prerequisite Report</div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])
    </div>

    
    <div class="card shadow">
        <div class="card-body">
            <div class="row pull-left pb-10">
                <div class="col-md-6">
                    <select class="form-control custom-select custom-select2 awarded_school">
                        <option value="0">Select Program</option>
                         @foreach($all_data['awarded_school'] as $awarded_programs)
                            @if($awarded_programs->name != '')
                                    <option value="{{$awarded_programs->id}}">{{$awarded_programs->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 pl-0">
                    <select class="form-control custom-select custom-select2 next_grade">
                        <option value="0">All Grade</option>
                        @foreach($all_data['next_grade'] as $next_grade)
                            <option value="{{$next_grade->next_grade}}">{{$next_grade->next_grade}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 pl-0">
                    <button class="filter_apply btn btn-success h-100 w-100" type="button">Apply</button>
                </div>
            </div>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="pre-req-tab" data-toggle="tab" href="#pre-req" role="tab" aria-controls="pre-req" aria-selected="false">Prerequisites</a></li>
                <li class="nav-item"><a class="nav-link" id="non-pre-req-tab" data-toggle="tab" href="#non-pre-req" role="tab" aria-controls="non-pre-req" aria-selected="false">No Prerequisites</a></li>
            </ul>
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="pre-req" role="tabpanel" aria-labelledby="pre-req-tab">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 w-100" id="datatable">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">Submission ID</th>
                                    <th class="align-middle">StateID</th>
                                    <th class="align-middle">Student Name</th>
                                    <th class="align-middle">Program Name</th>
                                    <th class="align-middle text-center">Current Grade</th>
                                    <th class="align-middle text-center">Academic Year</th>
                                    {{-- <th class="align-middle text-center">Academic Term</th> --}}
                                    <th class="align-middle text-center">Course Type</th>
                                    <th class="align-middle">Course Name</th>
                                    {{-- <th class="align-middle">Section Number</th> --}}
                                    {{-- <th class="align-middle text-center">Percent (%)</th> --}}
                                    <th class="align-middle text-center">Course Grade Level</th>
                                    <th class="align-middle">School Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="non-pre-req" role="tabpanel" aria-labelledby="non-pre-req-tab">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 w-100" id="no_pre_req">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">Submission ID</th>
                                    <th class="align-middle">StateID</th>
                                    <th class="align-middle">Student Name</th>
                                    <th class="align-middle">Program Name</th>
                                    <th class="align-middle text-center">Current Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>


@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script type="text/javascript">

    var next_grade = 0
    var awarded_school = $(".awarded_school").val();

    var program_pre_req_list = $("#datatable").DataTable({
        "aaSorting": [],
        "aoColumnDefs": [
            { "className": 'text-center', "aTargets": [ 0, 3 ] }, 
            // { "bSearchable": false, "aTargets": [ 0 ] }
        ],
        'ajax': {
            url: "{{url('admin/Reports/missing/'.$enrollment_id.'/program_pre_req/response')}}",
            "data": function ( d ) {
                d.awarded_school = awarded_school;
                d.next_grade = next_grade;
            },
        },
        columns: [
            { data: 'submissin_id' },
            { data: 'state_id' },
            { data: 'student_name' },
            { data: 'program_name' },
            { data: 'current_grade' },
            { data: 'academic_year' },
            { data: 'course_type' },
            { data: 'course_name' },
            { data: 'grade_level' },
            { data: 'school_name' },
        ],
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            // 'processing': '<div class="loader"></div>'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Program Prerequisite Report',
                text:'Export to Excel',
                //Columns to export
                // action: newexportaction,
            }
        ]
    });

    var program_no_pre_req_list = $("#no_pre_req").DataTable({
        "aaSorting": [],
        "aoColumnDefs": [
            { "className": 'text-center', "aTargets": [ 0, 3 ] }, 
            // { "bSearchable": false, "aTargets": [ 0 ] }
        ],
        'ajax': {
            url: "{{url('admin/Reports/missing/'.$enrollment_id.'/program_no_pre_req/response')}}",
            "data": function ( d ) {
                d.awarded_school = awarded_school;
                d.next_grade = next_grade;
            },
        },
        columns: [
            { data: 'submissin_id' },
            { data: 'stateID' },
            { data: 'student_name' },
            { data: 'program_name' },
            { data: 'current_grade' },
        ],
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            // 'processing': '<div class="loader"></div>'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Program No Prerequisite Report',
                text:'Export to Excel',
                //Columns to export
                // action: newexportaction,
            }
        ]
    });

    $(".awarded_school").change(function(){
        awarded_school = $(this).val();
    });

    $(".next_grade").change(function(){
        next_grade = $(this).val();
    });

    $(document).on('click','.filter_apply',function(){
        program_pre_req_list.ajax.reload();
        program_no_pre_req_list.ajax.reload();
    });
</script>
@endsection