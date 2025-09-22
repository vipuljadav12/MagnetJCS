@extends('layouts.admin.app')
@section('title')
	Generated Recommendation Form/ Writing Prompt Data Sheets
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
            <div class="page-title mt-5 mb-5">Generated Recommendation Form/ Writing Prompt Data Sheets</div>
        </div>
    </div>

  
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="new-tab" data-toggle="tab" href="#new" role="tab" aria-controls="new" aria-selected="false">Generated Recommendation Forms/Writing Prompt</a></li>
            {{-- <li class="nav-item"><a class="nav-link" href="{{url('/admin/GenerateApplicationData/generated')}}">Application Data Sheet Log</a></li> --}}
        </ul>
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new-tab">
                    <form class="" action="{{url('/admin/GenerateApplicationData/generated/form/export')}}" method="post" id="generateform">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="">Select Enrollment Year : </label>
                            <div class="">
                                <select class="form-control custom-select" id="enrollment" name="enrollment">
                                    <option value="">Select</option>
                                    @foreach($enrollment as $key=>$value)
                                        @if($value->id == Session::get("enrollment_id"))
                                            <option value="{{$value->id}}">{{$value->school_year}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Program : </label>
                            <div class="">
                                <select class="form-control custom-select" id="program_id" name="program_id" onchange="changeGrade(this.value)">
                                    <option value="">Select</option>
                                    <option value="0">All Programs</option>
                                    @foreach($programs as $key=>$value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Specific Grade : </label>
                            <div class="">
                                <select class="form-control custom-select" id="grade" name="grade">
                                    <option value="">Select</option>
                                    <option value="All">All Grades</option>
                                    @foreach($grades as $value)
                                        <option value="{{$value->next_grade}}">{{$value->next_grade}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Status : </label>
                            <div class="">
                                <select class="form-control custom-select" id="status" name="status">
                                    <option value="">Select</option>
                                    <option value="All">All</option>
                                     @foreach($submission_status as $key=>$value)
                                        <option value="{{$value->submission_status}}">{{$value->submission_status}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Select Form : </label>
                            <div class="">
                                <select class="form-control custom-select" id="submitted_form" name="submitted_form">
                                    <option value="">Select</option>
                                    <option value="recommendation_form">Recommendation Form</option>
                                    <option value="writing_prompt">Writing Prompt</option>
                                </select>
                            </div>
                        </div>
                        <div class=""><a href="javascript:void(0);" onclick="showReport()" title="" class="btn btn-secondary generate_report">Generate Data Sheets</a></div>
                    </form>
                </div>
            </div>

@endsection
@section('scripts')
	<script type="text/javascript">
        var arr = new Array();
		@foreach($programs as $key=>$value)
            arr["{{$value->name}}"] = {{$value->id}};
        @endforeach
        ;
        function showReport()
        {
            if($("#enrollment").val() == "")
            {
                alert("Please select enrollment year");
            }
            else if($("#program_id").val() == "")
            {
                alert("Please select program");
            }
            else if($("#grade").val() == "")
            {
                alert("Please select grade");
            }
            else if($("#status").val() == "")
            {
                alert("Please select status");
            }
            else if($("#submitted_form").val() == "")
            {
                alert("Please select Form");
            }
            else
            {
                $("#generateform").submit();
            }
        }

       function changeGrade(value)
        {
            var val1 = $("#program_id").val();
            if(val1 == "")
                val1 = 0;
            // var val2 = $("#second_program").val();
            // if(val2 == "")
            //     val2 = 0;
            $.ajax({
                url:'{{url('/admin/Submissions/get/grades/program/')}}/'+val1,
                type:"get",
                async: false,
                success:function(response){
                    $('#grade').children('option').remove();
                    var data = JSON.parse(response);
                    $("#grade").append('<option value="All">All</option>');
                    for(i=0; i < data.length; i++)
                    {
                     
                         $("#grade").append('<option value="'+data[i].next_grade+'">'+data[i].next_grade+'</option>');
                    }
                    chk = response;
                }
            })
        }
	</script>

@endsection