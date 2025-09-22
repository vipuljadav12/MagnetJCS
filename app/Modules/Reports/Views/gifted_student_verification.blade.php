@extends('layouts.admin.app')
@section('title')
	Gifted Student Verification Report
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
            <div class="page-title mt-5 mb-5">Gifted Student Verification Report</div></div>
    </div>
    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])
    </div>

    <div class="">
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
                                                    <th class="align-middle">Verification Status</th>
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
                                                    
                                                    <td class="text-center">
                                                        @php
                                                            $extra = '';
                                                            $status = $submission->gifted_verification_status;
                                                            if ($status == 'V') {
                                                                $class = 'alert-success';
                                                                $title = 'Verified';
                                                            } else if ($status == 'UV') {
                                                                $class = 'alert-danger';
                                                                $title = 'Unable to Verified';
                                                            } else {
                                                                $class = 'alert-warning';
                                                                $title = 'Not Reviewed';
                                                            }
                                                            if ($submission->gifted_verification_status_at != '') {
                                                                $extra = '<br>By ' . getUserName($submission->gifted_verification_status_by) . '&nbsp;<br>' . getDateTimeFormat($submission->gifted_verification_status_at);
                                                            }
                                                        @endphp
                                                        <a href="javascript:void(0);" title="" class="d-block mb-0 alert {{$class}} p-10 text-nowrap employeeverification" data-value="{{$submission->id}}" data-toggle="modal">{{$title}}&nbsp;</a>{!! $extra !!}
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
    <div class="modal fade" id="employeeverification" tabindex="-1" role="dialog" aria-labelledby="employeeverificationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeverificationLabel">Alert</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">If student is a gifted  then click on "Yes" button, if not then click on "No" button.</div>            
                <div class="modal-footer">
                    <button type="button" class="btn btn-success mcpssVerificationStatus" data-value="" data-dismiss="modal" onClick="giftedStudentStatusChange(this.getAttribute('data-value'), 'V')">Yes</button>
                    <button type="button" class="btn btn-danger mcpssVerificationStatus" data-value="" onClick="giftedStudentStatusChange(this.getAttribute('data-value'), 'UV')">No</button>
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
                        title: 'Gifted Student Verification Report',
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

        $(document).on('click','.employeeverification',function(){
            var submission_id = $(this).attr('data-value');
            $('.mcpssVerificationStatus').attr('data-value', submission_id);
            $('#employeeverification').modal('show');
        });

        function giftedStudentStatusChange(submission_id, $status){
            location.href = '{{url('/admin/Reports/missing/')}}/'+submission_id+'/gifted_student/verification/'+$status;
        }


	</script>

@endsection