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
            <div class="page-title mt-5 mb-5">Custom Communication</div>
        </div>
    </div>

    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/CustomCommunication/edit/'.$template_id)}}">Custom Communication</a></li>
            @if($type=="Letter")
                <li class="nav-item"><a class="nav-link active" href="#generated" role="tab" aria-controls="generated" aria-selected="true">Letters Logs</a></li>
            @else
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/CustomCommunication/generated/Letter/'.$template_id)}}">Letters Logs</a></li>
            @endif
            @if($type=="Email")
                <li class="nav-item"><a class="nav-link active" href="#generated" role="tab" aria-controls="generated" aria-selected="true">Emails Logs</a></li>
            @else
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/CustomCommunication/generated/Email/'.$template_id)}}">Emails Logs</a></li>
            @endif
        </ul>
          <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="generated" role="tabpanel" aria-labelledby="generated-tab">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="datatable">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">#</th>
                                    <th class="align-middle text-center">Enrollment Year</th>
                                    <th class="align-middle text-center">Program Name</th>
                                    <th class="align-middle text-center">Grade</th>
                                    <th class="align-middle text-center">Status</th>
                                    <th class="align-middle text-center">Total {{$type}}s</th>
                                    <th class="align-middle text-center">Download</th>
                                    <th class="align-middle text-center">Generated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($download_data as $key=>$value)
                                    <tr>
                                        <td class="text-center">{{$key+1}}</td>
                                        <td class="text-center">{{$value['school_year']}}</td>
                                        <td class="text-center">{{$value['program']}}</td>
                                        <td class="text-center">{{$value['grade']}}</td>
                                        <td class="text-center">{{$value['status']}}</td>
                                        <td class="text-center">{{$value['total_count']}}</td>
                                        <td class="text-center"><a href="{{url('/admin/CustomCommunication/download/'.$value['id'])}}"><i class="fa fa-download  text-success"></i></a></td>
                                        <td class="text-center">{{$value['created_at']}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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