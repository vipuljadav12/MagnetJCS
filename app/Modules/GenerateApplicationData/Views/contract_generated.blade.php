@extends('layouts.admin.app')
@section('title')
	Generate Contract Data Sheets
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
            <div class="page-title mt-5 mb-5">Generate Contract Sheets</div>
        </div>
    </div>

    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/GenerateApplicationData/contract')}}">Generate Contract Sheets</a></li>
            <li class="nav-item"><a class="nav-link active" id="generated-tab" data-toggle="tab" href="#generated" role="tab" aria-controls="generated" aria-selected="true">Generated Contract Sheet Log</a></li>
        </ul>
          <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="generated" role="tabpanel" aria-labelledby="generated-tab">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="datatable">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">#</th>
                                    <th class="align-middle text-center">Enrollment Year</th>
                                    <th class="align-middle text-center">Grade</th>
                                    <th class="align-middle text-center">Status</th>
                                    <th class="align-middle text-center">Total Data Sheets</th>
                                    <th class="align-middle text-center">Download</th>
                                    <th class="align-middle text-center">Generated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data))
                                    @foreach($data as $key=>$value)
                                        <tr>
                                            <td class="text-center">{{$key+1}}</td>
                                            <td class="text-center">{{$value->school_year}}</td>
                                            <td class="text-center">{{$value->grade}}</td>
                                            <td class="text-center">{{$value->status}}</td>
                                            <td class="text-center">{{$value->total_records}}</td>
                                            <td class="text-center"><a href="{{url('/admin/GenerateApplicationData/contract/download/'.$value->id)}}"><i class="fa fa-download  text-success"></i></a></td>
                                            <td class="text-center">{{getDateTimeFormat($value->created_at)}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
   


    
@endsection
@section('scripts')
	<script type="text/javascript">

        $('#datatable').DataTable();
		
        function showReport()
        {
            if($("#enrollment").val() == "")
            {
                alert("Please select enrollment year");
            }
            else if($("#program").val() == "")
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
            else
            {
                $("#generateform").submit();
            }
        }

	</script>

@endsection