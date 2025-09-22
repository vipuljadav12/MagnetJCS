@extends('layouts.admin.app')
@section('title')
    Writing Prompt Report
@endsection
@section('styles')
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
    .custom-select2{
    margin: 5px !important;
}
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>
@endsection
@section('content')

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5"> Missing Writing Prompt Report</div>
        <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/missing')}}" title="Go Back">Go Back</a></div>

    </div>
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
                        <option value="offerstatus">Offer Status Report</option>
                        <option value="student">Student Duplicate Report</option>
                        <option value="writingprompt" selected>Missing Writing Prompt Report</option>
                        {{--<option value="gradecdiupload">Parent Submitted Records</option> --}}
                        {{-- <option value="populationchange">Population Changes</option> --}}
                            {{-- <option value="results">Submission Results</option> --}}
                        
                    </select>
                </div>
            </div>
            <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
        </form>
    </div>
</div>
<div class="card shadow">
        <div class="card-body">
            <div class=" mb-10">
                <div class="text-right"> 
                    <a href="javascript:void(0)" onclick="exportMissing()" class="btn btn-secondary" title="Export Prompt">Export Writing Prompt</a>
                </div>
                <div class="form-group">
                    <label for="">Report : </label>
                <div class="">
                    <select class="form-control custom-select" id="filter_option" onchange="reloadData(this.value)">
                    @foreach($programs as $key =>$program) 

                        <option value={{$program->id}} @if($program->id == $program_id) selected @endif>{{$program->name}}</option>
                    @endforeach
                    </select>
                </div>

                </div>
            </div>
                <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div>

            @if(!empty($dispData))
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="datatable">
                    <thead>
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">Submission ID</th>
                            <th class="align-middle">State ID</th>
                            <th class="align-middle">First Name</th>
                            <th class="align-middle">Last Name</th>
                            <th class="align-middle">Next Grade</th>
                            <th class="align-middle">Current School</th>
                            <th class="align-middle">Program Choice 1</th>
                            <th class="align-middle">Program Choice 2</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Created_at</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @foreach($dispData as $key=>$value)
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;" rowspan="">{{$key+1}}</td>
                                    <td class="text-center"><a href="{{url('/')}}/admin/Submissions/edit/{{$value['submission_id']}}">{{$value['submission_id']}}</a></td>
                                    <td class="text-center">{{$value['student_id']}}</td>
                                    <td class="text-center">{{$value['first_name']}}</td>
                                    <td class="text-center">{{$value['last_name']}}</td>
                                    <td class="text-center">{{$value['next_grade']}}</td>
                                    <td class="text-center">{{$value['current_school']}}</td>
                                    <td class="text-center">{{$value['first_program']}} </td>
                                    <td class="text-center">{{$value['second_program']}}</td>
                                    <td class="text-center">
                                        @if($value['submission_status']  == "Active" || $value['submission_status'] == "Offered and Accepted")
                                            @php $class = "alert-success" @endphp
                                        @elseif($value['submission_status'] == "Auto Decline")
                                            @php $class = "alert-secondary" @endphp
                                        @elseif($value['submission_status'] == "Application Withdrawn" || $value['submission_status'] == "Offered and Declined" || $value['submission_status'] == "Denied due to Ineligibility")
                                            @php $class = "alert-danger" @endphp
                                        @elseif($value['submission_status'] == "Denied due to Incomplete Records")
                                            @php $class = "alert-info" @endphp
                                        @else
                                            @php $class = "alert-warning" @endphp
                                        @endif
                                        <div class="alert1 {{$class}} p-10 text-center d-block">{{$value['submission_status']}}</div>
                                    </td>
                                    <td class="text-center">{{$value['created_at']}}</td>
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
    

@endsection
@section('scripts')
 <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

<script type="text/javascript">

       $('#datatable').dataTable();
       
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

    function reloadData(val)
    {
        var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment").val()+"/writingprompt/"+val;
        document.location.href = link;
    }

     function exportMissing()
        {
            if($("#filter_option").val() == "")
                document.location.href="{{url('/admin/Reports/export/missingwritingprompt')}}/{{$enrollment_id}}";
            else
                document.location.href="{{url('/admin/Reports/export/missingwritingprompt')}}/{{$enrollment_id}}/"+$("#filter_option").val();

        }

</script>

@endsection