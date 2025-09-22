@extends('layouts.admin.app')
@section('title')
	Returning Students Report
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
            <div class="page-title mt-5 mb-5">Returning Students Report</div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0 w-100" id="datatable">
                    <thead>
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">SSID</th>
                            <th class="align-middle">Student Name</th>
                            <th class="align-middle">Parent First Name</th>
                            <th class="align-middle">Parent Last Name</th>
                            <th class="align-middle">Phone</th>
                            <th class="align-middle">Alternate Phone</th>
                            <th class="align-middle">Parent Email</th>
                            <th class="align-middle">Returning Student</th>
                            <th class="align-middle">Reason</th>
                            <th class="align-middle">Date Of Birth</th>
                            <th class="align-middle">Next Grade</th>
                            <th class="align-middle">Race</th>
                            <th class="align-middle">Current School</th>
                            <th class="align-middle">Current Signature Academy</th>
                            <th class="align-middle">Created At</th>
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

    var dtbl_submission_list = $("#datatable").DataTable({
        "aaSorting": [],
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 0 ] }, 
            { "bSearchable": false, "aTargets": [ 0 ] }
        ],
        'ajax': {
            url: "{{url('admin/Reports/missing/'.$enrollment_id.'/'.$selection.'/response')}}"
        },
        columns: [
            {data: 'index'},
            {data: 'ssid'},
            {data: 'student_name'},
            {data: 'parent_first_name'},
            {data: 'parent_last_name'},
            {data: 'phone'},
            {data: 'alternate_phone'},
            {data: 'parent_email'},
            {data: 'returning_customer'},
            {data: 'reason'},
            {data: 'birthday'},
            {data: 'next_grade'},
            {data: 'race'},
            {data: 'current_school'},
            {data: 'current_signature_academy'},
            {data: 'created_at'}
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
                title: '{{ucwords(str_replace('_', ' ', $selection))}} Report',
                text:'Export to Excel',
                //Columns to export
                // action: newexportaction,
            }
        ]
    });
</script>
@endsection