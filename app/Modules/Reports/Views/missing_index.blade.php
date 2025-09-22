@extends('layouts.admin.app')
@section('title')
	View/Edit Submissions
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
            <div class="page-title mt-5 mb-5">Report</div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <form class="">
                <div class="form-group">
                    <label for="">Enrollment Year: </label>
                    <div class="">
                        <select class="form-control custom-select" id="enrollment">
                            <option value="">Select Enrollment Year</option>
                            @foreach($enrollment as $key=>$value)
                                <option value="{{$value->id}}">{{$value->school_year}}</option>
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
                            <option value="courtreport">Court Report</option>
                            {{-- <option value="gradecdiupload">Parent Submitted Records</option> --}}
                            <!--<option value="seatstatus">Seat Status Report</option>
                            <option value="populationchange">Population Changes</option>
                            <option value="results">Submission Results</option>-->
                        </select>
                    </div>
                </div>
                <div class=""><a href="javascript:void(0);" onclick="showReport()" class="btn btn-success generate_report" title="Generate Report">Generate Report</a></div>
            </form>
        </div>
    </div>

@endsection
@section('scripts')
	<script type="text/javascript">
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
            else if($("#reporttype").val() == "courtreport")
            {
                var link = "{{url('/')}}/admin/Reports/"+$("#reporttype").val();
                document.location.href = link;
            }
            else
            {
                var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment").val()+"/"+$("#reporttype").val();
                document.location.href = link;
            }
        }
	</script>

@endsection