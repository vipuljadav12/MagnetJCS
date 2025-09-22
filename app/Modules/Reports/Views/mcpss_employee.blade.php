@extends('layouts.admin.app')
@section('title')
	MCPSS Employee Submission Report
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
            <div class="page-title mt-5 mb-5">MCPSS Employee Submission Report</div></div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form class="">
                <div class="form-group">
                    <label for="">Enrollment Year : </label>
                    <div class="">
                        <select class="form-control custom-select" id="enrollment">
                            <option value="">Select Enrollment Year</option>
                            @foreach($enrollment as $key=>$value)
                                <option value="{{$value->id}}" @if($enrollment_id == $value->id) selected @endif>{{$value->school_year}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Report : </label>
                    <div class="">
                        <select class="form-control custom-select" id="reporttype">
                            <option value="">Select Report</option>
                            <option value="grade">Missing Grade Report</option>
                            <option value="cdi">Missing CDI Report</option>
                            <option value="mcpss" selected>Employee Verification Report</option>
                        </select>
                    </div>
                </div>
                <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
            </form>
        </div>
    </div>

    <div class="">
            
            <div class="tab-content bordered" id="myTabContent">
                @include("layouts.admin.common.alerts")
                <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
                    
                    <div class="tab-content" id="myTabContent1">
                        <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class=" mb-10">
                                            <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
                                            
                                        </div>
                                        @if(!empty($firstdata))
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                     <tr>
                                                        <th class="align-middle">Submission ID</th>
                                                        <th class="align-middle">State ID</th>
                                                        <th class="align-middle">Enrollment Year</th>
                                                        <th class="align-middle">Student Name</th>
                                                        <th class="align-middle">Parent Name</th>
                                                        <th class="align-middle">Phone</th>
                                                        <th class="align-middle">Parent Email</th>
                                                        <th class="align-middle">Race</th>
                                                        <th class="align-middle">Date of Birth</th>
                                                        <th class="align-middle">Current School</th>
                                                        <th class="align-middle">Current Grade</th>
                                                        <th class="align-middle">Next Grade</th>
                                                        <th class="align-middle">First Program Choice</th>
                                                        <th class="align-middle">Second Program Choice</th>
                                                        <th class="align-middle">Submitted at</th>
                                                        <th class="align-middle">Form</th>
                                                        <th class="align-middle">Application Status</th>
                                                        <th class="align-middle">Zoned School</th>
                                                        <th class="align-middle">Student Type</th>
                                                        <th class="align-middle">Confirmaion No</th>
                                                        <th class="align-middle">MCPSS Employee</th>
                                                        <th class="align-middle">Employee ID</th>
                                                        <th class="align-middle">Employee Work Location</th>
                                                        <th class="align-middle">Employee First Name</th>
                                                        <th class="align-middle">Employee Last Name</th>
                                                        <th class="align-middle">Verification Status</th>
                                                        <th class="align-middle">Magnet Program Employee</th>
                                                         
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($firstdata as $key=>$submission)
                                                        <tr>
                                                            <td class="text-center">

                                                                <a href="{{ url('admin/Submissions/edit',$submission->id)}}" title="edit">
                                                                    {{$submission->id}}</a>
                                                            </td>
                                                            <td class="">{{$submission->student_id}}</td>
                                                            <td class="">{{$submission->school_year}}</td>
                                                            <td class="">{{$submission->first_name}} {{$submission->last_name}}</td>
                                                            <td class="">{{$submission->parent_first_name}} {{$submission->parent_last_name}}</td>
                                                            <td class="">{{$submission->phone_number}}</td>
                                                            <td class="">{{$submission->parent_email}}</td>
                                                            <td class="">{{$submission->race}}</td>
                                                            <td class="">{{getDateFormat($submission->birthday)}}</td>
                                                            <td class="">{{$submission->current_school}}</td>
                                                            <td class="">{{$submission->current_grade}}</td>
                                                            <td class="">{{$submission->next_grade}}</td>
                                                            <td>{{getProgramName($submission->first_choice_program_id)}}</td>
                                                            <td>{{getProgramName($submission->second_choice_program_id)}}</td>
                                                            <td class="">{{getDateTimeFormat($submission->created_at)}}</td>
                                                            <td class="">{{findSubmissionForm($submission->application_id)}}</td>
                                                            <td class="">
                                                                    @if($submission->submission_status == "Active")
                                                                        <div class="alert1 alert-success p-10 text-center d-block">{{$submission->submission_status}}</div> 
                                                                    @elseif($submission->submission_status == "Application Withdrawn")
                                                                        <div class="alert1 alert-danger p-10 text-center d-block">{{$submission->submission_status}}</div> 
                                                                    
                                                                    @else
                                                                            <div class="alert1 alert-warning p-10 text-center d-block">{{$submission->submission_status}}</div>
                                                                    @endif
                                                            </td>
                                                            <td>{{$submission->zoned_school}}</td>
                                                            <td class="text-center">
                                                                @if($submission->student_id != "")
                                                                    <div class="alert1 alert-success p-10 text-center d-block">Current</div> 
                                                                @else
                                                                    <div class="alert1 alert-warning p-10 text-center d-block">New</div>
                                                                @endif
                                                            </td>
                                                            <td>{{$submission->confirmation_no}}</td>
                                                            <td>{{$submission->mcp_employee}}</td>
                                                            <td>{{$submission->employee_id}}</td>
                                                            <td>{{$submission->work_location}}</td>
                                                            <td>{{$submission->employee_first_name}}</td>
                                                            <td>{{$submission->employee_last_name}}</td>
                                                            <td class="text-center">

                                                                @if($submission->mcpss_verification_status == 'V')
                                                                     <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-success p-10 text-nowrap @if($display_outcome == 0) employeeverification @endif" @if($display_outcome == 0) data-value="{{$submission->id}}" data-toggle="modal" @endif>Verified</a>@if($submission->mcpss_verification_status_at != '')<br>By {{getUserName($submission->mcpss_verification_status_by)}}<br>{{getDateTimeFormat($submission->mcpss_verification_status_at)}} @endif
                                                                @elseif($submission->mcpss_verification_status == 'UV')
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-danger p-10 text-nowrap @if($display_outcome == 0) employeeverification @endif"  @if($display_outcome == 0) data-value="{{$submission->id}}" data-toggle="modal" @endif>Unable to Verified</a>@if($submission->mcpss_verification_status_at != '')<br>By {{getUserName($submission->mcpss_verification_status_by)}}<br>{{getDateTimeFormat($submission->mcpss_verification_status_at)}} @endif
                                                                @else
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-warning p-10 text-nowrap @if($display_outcome == 0) employeeverification @endif"  @if($display_outcome == 0)  data-value="{{$submission->id}}" data-toggle="modal" @endif>Not Reviewed</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($submission->magnet_program_employee == "Y")
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-success p-10"  @if($display_outcome == 0)  data-status="{{$submission->mcpss_verification_status}}" data-value="{{$submission->id}}" data-toggle="modal"   data-toggle="modal @endif">Yes</a>
                                                                @else
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-danger p-10 employeepending"  @if($display_outcome == 0)  data-status="{{$submission->mcpss_verification_status}}" data-value="{{$submission->id}}" data-toggle="modal" @endif>No</a>
                                                                @endif
                                                                @if($submission->magnet_program_employee_at != '')<br>By {{getUserName($submission->magnet_program_employee_by)}}<br>{{getDateTimeFormat($submission->magnet_program_employee_at)}} @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                            <div class="table-responsive text-center"><p>No Records found.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="employeeverification" tabindex="-1" role="dialog" aria-labelledby="employeeverificationLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeverificationLabel">Alert</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body">If employee is a part of MCPSS then click on "Verified" button, if not then click on "Not Verified" button.</div>            
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success mcpssVerificationStatus" data-value="" data-dismiss="modal" onClick="employeeVerificationStatusChange(this.getAttribute('data-value'), 'V')">Verified</button>
                        <button type="button" class="btn btn-danger mcpssVerificationStatus" data-value="" onClick="employeeVerificationStatusChange(this.getAttribute('data-value'), 'UV')">Not Verified</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="employeepending" tabindex="-1" role="dialog" aria-labelledby="employeependingLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeependingLabel">Alert</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body">Are you sure this employee is a part of MCPSS Magnet Program ?</div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal" data-value="" id="magnetProgramEmployee" onClick="yesEmployee(this.getAttribute('data-value'))">Yes</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
	<script type="text/javascript">

        var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'MCPSS-Employee',
                        text:'Export to Excel',

                        //Columns to export
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }
                ]
            });

        dtbl_submission_list.columns([1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 19]).visible(false);
        function changeMissingReport(id)
        {
            if(id == "")
            {
                document.location.href = "{{url('/admin/Reports/missing/cdi')}}";
            }
            else
            {
                document.location.href = "{{url('/admin/Reports/missing/cdi/')}}/"+id;
            }
        }

        function showReport()
        {
            if($("#enrollment").val() == "")
            {
                alert("Please select enrollment year");
            }
            else if($("#reporttype").val() == "")
            {
                alert("Please select report type");
            }
            else
            {
                var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment").val()+"/"+$("#reporttype").val();
                document.location.href = link;
            }
        }


        function editRow(id)
        {
            $("#edit"+id).addClass("d-none");
            $("#save"+id).removeClass("d-none");
            $("#cancel"+id).removeClass("d-none");

            $("#row"+id).find("span.scorelabel").addClass('d-none');
            $("#row"+id).find("input.scoreinput").removeClass('d-none');
        }

        function hideEditRow(id)
        {
            $("#edit"+id).removeClass("d-none");
            $("#save"+id).addClass("d-none");
            $("#cancel"+id).addClass("d-none");

            $("#row"+id).find("span.scorelabel").removeClass('d-none');
            $("#row"+id).find("input.scoreinput").addClass('d-none');
        }

        function saveScore(id)
        {

            var data = {};
            var keyArr = new Array();
            var valid = true;
            $("#row"+id).find("input.scoreinput").each(function(e)
            {
                if($.trim($(this).val()) != "")
                {
                    if(parseInt($.trim($(this).val())) > 100)
                    {
                        alert("Maximum value allowed 100");
                        valid = false;
                    }
                    data[$(this).attr("id")] = $(this).val();
                    $(this).parent().find(".scorelabel").html($(this).val());
                    keyArr[keyArr.length] = $(this).attr("id");
                }
            })

            if (!$.isEmptyObject(data) && valid == true) { 
                data['_token'] = "{{csrf_token()}}";
                $.ajax({
                    url : "{{url('/admin/Reports/missing/cdi/save/')}}/"+id,
                    type: "POST",
                    data : data,
                    success: function(data)
                    {
                        $("#edit"+id).removeClass("d-none");
                        $("#save"+id).addClass("d-none");
                        $("#cancel"+id).addClass("d-none");

                        $("#row"+id).find("span.scorelabel").removeClass('d-none');
                        $("#row"+id).find("input.scoreinput").addClass('d-none');

                        alert("CDI updated successfully");
                       // $("#row"+id).find("span.scorelabel").html($("#row"+id).find("input.scoreinput").val());

                       
                        //data - response from server
                    }
                });
            }    
        }


        function exportMissing()
        {
            if($("#filter_option").val() == "")
                document.location.href="{{url('/admin/Reports/export/missingcdi')}}";
            else
                document.location.href="{{url('/admin/Reports/export/missingcdi')}}/"+$("#filter_option").val();

        }

        $(document).on('click','.employeeverification',function(){
            // alert('test');
            var submission_id = $(this).attr('data-value');
            $('.mcpssVerificationStatus').attr('data-value', submission_id);
            $('#employeeverification').modal('show');
            // $("#employeeverification").dialog({ show: 'fade' });
        }); 

        $(document).on('click','.employeepending',function(){
            if($(this).attr('data-status') == 'V'){
                var submission_id = $(this).attr('data-value');
                $('#magnetProgramEmployee').attr('data-value', submission_id);
                $('#employeepending').modal('show');
            }
        }); 


        function employeeVerificationStatusChange(submission_id, $status){
            location.href = '{{url('/admin/Reports/missing/')}}/'+submission_id+'/mcpss/verification/'+$status;
            // alert(submission_id);
        }

        function yesEmployee(submission_id){
            location.href = '{{url('/admin/Reports/missing/')}}/'+submission_id+'/mcpss/changeStatus';
            // alert(submission_id);
        }


	</script>

@endsection