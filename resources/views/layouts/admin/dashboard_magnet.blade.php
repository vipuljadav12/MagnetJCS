@extends('layouts.admin.app')
@section('title')
	Dashboard
@endsection
@section('styles')
<style type="text/css">
	.modal-body{
		max-height: 550px;
		overflow-y: auto !important;
		/*background-color: */
	}
    .dt-buttons{float: right !important; padding-bottom: 5px;}
</style>
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Dashboard</div>
        <div class="">
        @if((checkPermission(Auth::user()->role_id,'dashboard') == 1))
            <div class="">
                <select class="form-control custom-select w-200" id="switch_dashboard">
                    <option value="magnet" selected>Super Admin - Application</option>
                    <option value="mcpss">District Admin - Application</option>
                    <option value="superoffer">Super Admin - Offer</option>
                    <option value="districtoffer">District Admin - Offer</option>
                </select>
                <select class="form-control custom-select w-200" id="late_submission">
                    <option value="submissions" @if($late_submission == 'N') selected @endif>Submissions</option>
                    <option value="late_submissions" @if($late_submission == 'Y') selected @endif>Late Submissions</option>
                </select>
            </div>
        @endif
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <div class="box-1">
            <div class="table-responsive mb-20" style="height: 480px; overflow-y: auto;">
                <table class="table table-striped" id="student_applications1">
                    <thead>
                        <tr>
                            <th class="" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Student Applications</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Non Current Students</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Current Students</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Total Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@isset($data_ary['student_applications1'])
                    		@foreach($data_ary['student_applications1'] as $value)
                    		@php
                    			$total = $value['current_student_count'] + $value['non_current_student_count'];
                    		@endphp
	                        <tr>
	                            <td class="">{{getDateFormat($value['created_at'])}}</td>
	                            <td class=""><div class="alert1 alert-warning text-center">{{$value['non_current_student_count']}}</div></td>
	                            <td class=""><div class="alert1 alert-info text-center">{{$value['current_student_count']}}</div></td>
	                            <td class=""><div class="alert1 alert-success text-center">{{$total}}</div></td>
	                        </tr>
	                		@endforeach
	                	@endisset
                    </tbody>
                </table>
            </div>
            {{-- <div class="d-flex justify-content-between">
                <div class=""><a href="javascript:void(0);" title="" class="btn btn-secondary btn-cust-prev1 disabled">Prev</a></div>
                <div class="paginate_button page-item next" id="student_applications1_next"><a href="javascript:void(0);" title="" class="btn btn-secondary btn-cust-next1">Next</a></div>
            </div> --}}
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <div class="">
            <div class="table-responsive mb-20" style="height: 480px; overflow-y: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Student Applications</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Step 1</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Step 2</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Step 3</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($data_ary['submission_steps'])
                            @foreach($data_ary['submission_steps'] as $value)
                            <tr>
                                <td class="">{{getDateFormat($value['created_at'])}}</td>
                                <td class=""><div class="alert1 alert-danger text-center">{{$value['step1']}}</div></td>
                                <td class=""><div class="alert1 alert-warning text-center">{{$value['step2']}}</div></td>
                                <td class=""><div class="alert1 alert-info text-center">{{$value['step3']}}</div></td>
                                <td class=""><div class="alert1 alert-success text-center">{{$value['step4']}}</div></td>
                                
                            </tr>
                            @endforeach
                        @endisset

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <div class="">
            <div class="table-responsive mb-20" style="height: 480px; overflow-y: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 999 !important">No Zone School Found Address</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 999 !important">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@isset($data_ary['no_zoned_school_found_addresses'])
                    		@foreach($data_ary['no_zoned_school_found_addresses'] as $value)
		                        <tr>
		                            <td class="created_at">{{getDateFormat($value['created_at'])}}</td>
		                            <td class="">
		                            	<div class="modal_no_zoned alert1 alert-success text-center">View ({{$value['count']}})</div>
		                            </td>
		                        </tr>
	                        @endforeach
	                    @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="card shadow">
    <div class="card-body">
        <div class="">
            <div class="table-responsive mb-20" style="height: 480px; overflow-y: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Override Addresses</th>
                            <th class="text-center w-200" style="position: sticky; top: 0; background-color: #E5E5E5 !important; z-index: 9999 !important">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($data_ary['manual_address'])
                            @foreach($data_ary['manual_address'] as $value)
                                <tr>
                                    <td class="created_at">{{getDateFormat($value['created_at'])}}</td>
                                    <td class="">
                                        <div class="modal_override alert1 alert-success text-center">View ({{$value['count']}})</div>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modals start -->
<div class="modal fade" id="viewmodal" tabindex="-1" role="dialog" aria-labelledby="viewmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="viewmodalLabel">No Zone School Found</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table mb-0 table-striped" id="tbl_no_zoned_school_found">
                        <thead>
                            <tr>
                                <th class="">Home Address</th>
                                <th class="">City</th>
                                <th class="">ZIP Code</th>
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

<div class="modal fade" id="viewmodal1" tabindex="-1" role="dialog" aria-labelledby="viewmodalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="viewmodalLabel1">Overide Addresses</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table mb-0 table-striped" id="tbl_no_zoned_school_found1">
                        <thead>
                             <tr>
                                <th class="">Building/Housing No</th>
                                <th class="">Prefix Direction</th>
                                <th class="">Street Address</th>
                                <th class="">Street Type</th>
                                <th class="">Unit Info</th>
                                <th class="">Suffix Direction</th>
                                <th class="">City</th>
                                <th class="">ZIP Code</th>
                                <th class="">Elementary School</th>
                                <th class="">Middle School</th>
                                <th class="">Intermediate School</th>
                                <th class="">High School</th> 
                                <th class="">Added By</th> 
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

<!-- Modals end -->
@endsection
@section('scripts')

<script type="text/javascript">

    $('#late_submission').change(function() {
        var url = "{{url('')}}/admin/magnet_dashboard/";
        if ($(this).val() == "late_submissions") {
            url = url+'late_submissions';
        }
        window.location = url;
    });
	
	/* switch dashboard start */
	$('#switch_dashboard').change(function() {
		if ($(this).val() == "magnet") {
			window.location = "{{url('admin/magnet_dashboard')}}";
		}
	});
	/* switch dashboard end */

	/* Datatable start */
	var dtbl_student_applications1 = "student_applications1";
	$('#'+dtbl_student_applications1).DataTable({
		"ordering": false,
        "paging":   false,
        "searching": false,
        "bInfo": false,
        "pagingType": 'simple',
        "lengthChange": false,
        "language": {
			"paginate": {
				"previous": "Prev"
			}
		},
		"dom": 'lrtip'
	});

var dtbl_no_zoned1 = $('#tbl_no_zoned_school_found1').DataTable({
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching":     false,
        "columnDefs": [{
            "defaultContent": '-',
            "targets": '_all'
        }],
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Export',
                extend: 'excel',
                title: '',
                filename: 'Override_Address',
                messageTop: 'Override Address'
            }
        ]
    });


     var today = new Date();
    var day = today.getDate();
    var month = today.toLocaleString('default', { month: 'short' });
    var year = today.getFullYear();
    var no_zoned_excel_file_name =  day + '_' + month + '_' + year;
    
    // No Zone School Found Address
    var dtbl_no_zoned = $('#tbl_no_zoned_school_found').DataTable({
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching":     false,
        "columnDefs": [{
            "defaultContent": '-',
            "targets": '_all'
        }],
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Export',
                extend: 'excel',
                title: '',
                filename: 'No_Zone_School_Found_' + no_zoned_excel_file_name,
                messageTop: 'No Zone School Found'
            }
        ]
    });
	/* Datatable start */

	/* Datatable pagination position start */	
	$(document).on('click', '.paginate_button', function() {
		positionDtablePagination();
	});

	positionDtablePagination();
	function positionDtablePagination() {
		var pagination = $('#'+dtbl_student_applications1+'_paginate');
		pagination.find('.pagination').addClass('justify-content-between');
		// $('#'+dtbl_student_applications1+'_previous').css('background', 'red');
		// $('#'+dtbl_student_applications1+'_previous').css('background-color', 'red');
	}
	/* Datatable pagination position end */	

	/* Load No Zone School Modal start */	
	$(document).on('click', '.modal_no_zoned', function(){
		var date = $(this).parent().parent().find('.created_at').text();
		$.ajax({
			type: 'get',
			url: "{{url('admin/load_addresses')}}",
			data: {
				'date': date
			},
			success: function(response) {
                dtbl_no_zoned.rows().remove().draw();
                dtbl_no_zoned.rows.add(JSON.parse(response)).draw(false);
                $('#viewmodal').modal('show');
			}
		});
	});
	/* Load No Zone School Modal end */	

   $(document).on('click', '.modal_override', function(){
        var date = $(this).parent().parent().find('.created_at').text();
        $.ajax({
            type: 'get',
            url: "{{url('admin/show_override_addresses')}}",
            data: {
                'date': date
            },
            success: function(response) {
                dtbl_no_zoned1.rows().remove().draw();
                dtbl_no_zoned1.rows.add(JSON.parse(response)).draw(false);
                $('#viewmodal1').modal('show');
            }
        });
    });

</script>
@endsection