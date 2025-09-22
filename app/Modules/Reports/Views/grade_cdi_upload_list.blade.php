@extends('layouts.admin.app')
@section('title')
	Parent Submitted Documents Report
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
            <div class="page-title mt-5 mb-5">Parent Submitted Records</div>
            <div class=""><a class="text-danger" href="{{url('upload/60/grade')}}" target="_blank"><strong>Add Parent Submitted Documents</strong></a></div>
            
        </div>
    </div>

    <div class="card shadow d-none">
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
                            <option value="offerstatus">Offer Status Report</option>
                            <option value="duplicatestudent">Student Duplicate Report</option>
                            <option value="gradecdiupload" selected>Parent Submitted Records</option>
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
                                        @if(count($gradecdilist) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                     <tr>
                                                        <th class="text-center">Submission ID</th>
                                                        <th class="text-center">Last Name</th>
                                                        <th class="text-center">First Name</th>
                                                        <th class="text-center">Next Grade</th>
                                                        <th class="text-center">Current School</th>
                                                        <th class="text-center">Program Choice 1</th>
                                                        <th class="text-center">Program Choice 2</th>
                                                        <th class="text-center">Grades</th>
                                                        <th class="text-center">Submission Updated</th>
                                                        <th class="text-center">Submission Updated</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($gradecdilist as $key => $value)
                                                        <tr>
                                                            <td class="text-center">{{$value->id}}</td>
                                                            <td class="text-center">{{$value->last_name}}</td>
                                                            <td class="text-center">{{$value->first_name}}</td>
                                                            <td class="text-center">{{$value->next_grade}}</td>
                                                            <td class="text-center">{{$value->current_school}}</td>
                                                            <td class="text-center">{{getProgramName($value->first_choice_program_id)}}</td>
                                                            <td class="text-center">{{getProgramName($value->second_choice_program_id)}}</td>
                                                            <td class="">
                                                                @php 
                                                                    $grade_docs = getGradeUploadDocs($value->id, 'grade');
                                                                @endphp
                                                                @if(isset($grade_docs) && $grade_docs != '')
                                                                    @foreach($grade_docs as $key=>$grade)
                                                                        <div>
                                                                            <a href="{{url('/resources/gradefiles/'.$grade->file_name)}}" title="" class="" @if(isset($value->grade_upload_confirmed_at) && strtotime($grade->created_at) > strtotime($value->grade_upload_confirmed_at)) style="color: red; text-decoration: underline;" @else style="color: #0000FF; text-decoration: underline;" @endif target="_blank">{{$grade->file_name}}</a>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($value->grade_upload_confirmed == 'Y')
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-success p-10">Yes</a>
                                                                    @if($value->grade_upload_confirmed_at != '')<br>By {{getUserName($value->grade_upload_confirmed_by)}}<br>{{getDateTimeFormat($value->grade_upload_confirmed_at)}} @endif
                                                                @else
                                                                    <a href="javascript:void(0);" data-value="{{$value->id}}" title="" data-type="grade" class="d-block mb-0 alert alert-danger p-10 docconfirmed_grade">No</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($value->cdi_upload_confirmed == 'Y')
                                                                    <a href="javascript:void(0);" title="" class="d-block mb-0 alert alert-success p-10">Yes</a>
                                                                    @if($value->cdi_upload_confirmed_at != '')<br>By {{getUserName($value->cdi_upload_confirmed_by)}}<br>{{getDateTimeFormat($value->cdi_upload_confirmed_at)}} @endif
                                                                @else
                                                                    <a href="javascript:void(0);" data-value="{{$value->id}}" title="" data-type="cdi" class="d-block mb-0 alert alert-danger p-10 docconfirmed_cdi">No</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
        <div class="modal fade" id="document_confirmed" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="docModalLabel">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body">test</div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal" data-value="" data-type="" id="docUploadConfirmed" onClick="yesConfirmed(this.getAttribute('data-value'), this.getAttribute('data-type'))">Yes</button>
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

        $('#datatable').DataTable();

        $(document).on('click','.docconfirmed_grade',function(){
            var submission_id = $(this).attr('data-value');
            var type = $(this).attr('data-type');
            $('#docUploadConfirmed').attr('data-value', submission_id);
            $('#docUploadConfirmed').attr('data-type', type);
            $('#document_confirmed').find('.modal-body').text('Grades update completed?');
            $('#document_confirmed').modal('show');
        });

        $(document).on('click','.docconfirmed_cdi',function(){
            var submission_id = $(this).attr('data-value');
            var type = $(this).attr('data-type');
            $('#docUploadConfirmed').attr('data-value', submission_id);
            $('#docUploadConfirmed').attr('data-type', type);
            $('#document_confirmed').find('.modal-body').text('Grades update completed?');
            $('#document_confirmed').modal('show');
        });

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


        function yesConfirmed(submission_id, $type){
            location.href = '{{url('/admin/Reports/missing/')}}/'+submission_id+'/gradecdiupload/'+ $type +'/confirmed';
        }
	</script>

@endsection