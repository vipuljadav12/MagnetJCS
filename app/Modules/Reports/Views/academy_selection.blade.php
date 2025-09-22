@extends('layouts.admin.app')
@section('title')
	Academy Selection Report
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
            <div class="page-title mt-5 mb-5">Academy Selection Report</div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="row pull-left pb-10">
                <div class="col-md-3">
                    <select class="form-control custom-select custom-select2 awarded_school">
                        <option value="0">All Programs</option>
                         @foreach($all_data['awarded_school'] as $awarded_programs)
                            @if($awarded_programs->name != '')
                                    <option value="{{$awarded_programs->id}}">{{$awarded_programs->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 pl-0">
                    <select class="form-control custom-select custom-select2 next_grade">
                        <option value="0">All Grade</option>
                        @foreach($all_data['next_grade'] as $next_grade)
                            <option value="{{$next_grade->next_grade}}">{{$next_grade->next_grade}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 pl-0">
                    <select class="form-control custom-select custom-select2 program_choice">
                        <option value="0" selected>All Choice</option>
                        <option value="first_choice_program_id">First Choice</option>
                        <option value="second_choice_program_id">Second Choice</option>
                        <option value="third_choice_program_id">Third Choice</option>
                    </select>
                </div>
                <div class="col-md-3 pl-0">
                    <select class="form-control custom-select custom-select2 app_status">
                        <option value="0">All Status</option>
                        @foreach($all_data['submission_status'] as $sub_status)
                            <option value="{{$sub_status->submission_status}}">{{$sub_status->submission_status}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group text-right">
                <button class="filter_apply btn btn-success" type="button">Apply</button>
            </div>

            <form action="{{ url('admin/Reports/missing/academy_selection/update') }}" method="POST" class="academy_selection_form">
                <div class="row pull-left pb-10">
                    {{ csrf_field() }}
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment_id }}">
                    <div class="col-md-4 d-flex">
                        <select class="form-control custom-select custom-select2 update_selection_status" name="submission_status">
                            <option value="0" selected>Select Submission Status</option>
                            <option value="Offered">Offered</option>
                            <option value="Offered and Accepted">Offered and Accepted</option>
                            <option value="Waitlisted">Waitlisted</option>
                            <option value="Denied due to Ineligibility">Denied due to Ineligibility</option>
                        </select>
                        <button class="btn btn-secondary ml-5 btn-check-update">Update</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped mb-0 w-100" id="datatable">
                    <thead>
                        <tr>
                            <th class="align-middle"># <input type="checkbox" class="main_check"></th>
                            <th class="align-middle">Submission ID</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">SSID</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Program Name</th>
                            <th class="align-middle">Grade</th>
                            <th class="align-middle">Current School</th>
                            <th class="align-middle">Zoned School</th>
                            <th class="align-middle">Race</th>
                            <th class="align-middle">Gender</th>
                            <th class="align-middle">Choice Number</th>
                            <th class="align-middle">Awarded Program</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script type="text/javascript">

    var program_choice = next_grade = 0
    var awarded_school = $(".awarded_school").val();
    var app_status = 0;

    var dtbl_submission_list = $("#datatable").DataTable({
        "aaSorting": [],
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 0 ] }, 
            { "bSearchable": false, "aTargets": [ 0 ] }
        ],
        'ajax': {
            url: "{{url('admin/Reports/missing/'.$enrollment_id.'/academy_selection/response')}}",
            "data": function ( d ) {
                d.awarded_school = awarded_school;
                d.next_grade = next_grade;
                d.program_choice = program_choice;
                d.app_status = app_status;
            },
        },
        columns: [
            { data: 'check_id' },
            { data: 'submission_id' },
            { data: 'status' },
            { data: 'ssid' },
            { data: 'name' },
            { data: 'program_name' },
            { data: 'next_grade' },
            { data: 'zoned_school' },
            { data: 'current_school' },
            { data: 'race' },
            { data: 'gender' },
            { data: 'choice_number' },
            { data: 'awarded_school' },
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
                title: 'Academy Selection Report',
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

    $(".program_choice").change(function(){
        program_choice = $(this).val();
    });

    $(".app_status").change(function(){
        app_status = $(this).val();
    });

    $(document).on('click','.filter_apply',function(){
        dtbl_submission_list.ajax.reload();
    });

    $(document).on('click', '.main_check',function(){
        dtbl_submission_list.$(".submission_check", {"page": "all"}).prop( "checked", $(this).is(':checked') );
    });
    
    var form =  $('.academy_selection_form').submit(function(event){
        let is_check = dtbl_submission_list.$(".submission_check:checked", {"page": "all"});

        if(is_check.length > 0){

            let update_status = $('.update_selection_status').val();
            let awarded_school_id = $('.awarded_school').val();
            let submission_ids = [];

            if(update_status != '0'){

                is_check.each(function(index,elem){
                    submission_ids.push($(elem).val());
                });

                $("<input>").attr({'type':'hidden','name':'submission_ids'}).val(submission_ids).appendTo(form);
                $("<input>").attr({'type':'hidden','name':'awarded_school_id'}).val(awarded_school_id).appendTo(form);

            }else{
                alert('Please Select Submissin Status.');
                event.preventDefault();
            }

        }else{
            alert("Plese Select Any Records to update.");
            event.preventDefault();
            // swal({title: "Plese Select Any Records to update."});
        }
    });

</script>
@endsection