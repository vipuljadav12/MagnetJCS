@extends('layouts.admin.app')
@section('title')
    Selection Report Master
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
                    <label for="">Select Application: </label>
                    <div class="">
                        <select class="form-control custom-select" id="application_id">
                            <option value="">Select Application</option>
                            @foreach($applications as $key=>$value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class=""><a href="javascript:void(0);" onclick="showApplicationReport()" class="btn btn-success generate_report" title="Generate Report">Generate Report</a></div>
            </form>
        </div>
    </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        function showApplicationReport()
        {
            if($("#application_id").val() == "")
            {
                alert("Please select application");
            }
            else
            {
                var link = "{{url('/')}}/admin/Reports/Waitlist/Selection/"+$("#application_id").val();
                document.location.href = link;
            }
        }
    </script>

@endsection