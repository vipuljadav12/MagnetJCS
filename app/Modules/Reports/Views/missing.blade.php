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
    <div class="">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="needs1-tab" data-toggle="tab" href="#needs1" role="tab" aria-controls="needs1" aria-selected="true">Missing Grade Report</a></li>
            </ul>
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
                    
                    <div class="tab-content bordered" id="myTabContent1">
                        <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="text-right mb-10 d-flex justify-content-end align-items-center">
                                            
                                            <div class=""><select class="form-control custom-select w-250" onchange="changeMissingReport(this.value)">
                                                            <option value="">Select Program</option>
                                                            @foreach($programs as $key=>$value)
                                                                <option value="{{$value}}" @if($value == $program_id) selected="" @endif>{{getProgramName($value)}}</option>
                                                            @endforeach
                                                    </select></div>
                                        </div>
                                        @php $config_subjects = Config::get('variables.subjects') @endphp
                                        @if(!empty($firstdata))
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle">Submission ID</th>
                                                        <th class="align-middle">Submission Status</th>
                                                        <th class="align-middle">Race</th>
                                                        <th class="align-middle">State ID</th>
                                                        <th class="align-middle">Last Name</th>
                                                        <th class="align-middle">First Name</th>
                                                        <th class="align-middle">Next Grade</th>
                                                        <th class="align-middle">Current School</th>
                                                        <th class="align-middle">Zoned School</th>
                                                        <th class="align-middle">First Choice</th>
                                                        <th class="align-middle">Second Choice</th>
                                                        @foreach($subjects as $sbjct)
                                                            @foreach($terms as $term)
                                                                <th class="align-middle">{{$config_subjects[$sbjct]}} {{$term}}</th>
                                                            @endforeach
                                                        @endforeach
                                                        <th class="align-middle">B Info</th>
                                                        <th class="align-middle">C Info</th>
                                                        <th class="align-middle">D Info</th>
                                                        <th class="align-middle">E Info</th>
                                                        <th class="align-middle">Susp</th>
                                                        <th class="align-middle"># Days Susp</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($firstdata as $key=>$value)
                                                        <tr>
                                                            <td class="">{{$value['id']}}</td>
                                                            <td class="">{{$value['submission_status']}}</td>
                                                            <td class="">{{$value['race']}}</td>
                                                            <td class="">{{$value['student_id']}}</td>
                                                            <td class="">{{$value['first_name']}}</td>
                                                            <td class="">{{$value['last_name']}}</td>
                                                            <td class="">{{$value['next_grade']}}</td>
                                                            <td class="">{{$value['current_school']}}</td>
                                                            <td class=""></td>
                                                            <td class="">{{$value['first_program']}}</td>
                                                            <td class="text-center">{{$value['second_program']}}</td>
                                                            @foreach($value['score'] as $skey=>$sbjct)
                                                                @foreach($terms as $term)
                                                                    <td class="align-middle">
                                                                        @if(isset($sbjct[$term]))
                                                                            <div class="text-center">
                                                                                {!! $sbjct[$term] !!}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            @endforeach

                                                            @foreach($value['cdi'] as $vkey=>$vcdi)

                                                                <td class="align-middle">
                                                                    
                                                                        <div class="text-center">
                                                                            {!! $value['cdi'][$vkey] !!}
                                                                        </div>
                                                                    
                                                                    
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    @endforeach
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                            <div class="table-responsive text-center"><p>Please select program to view report.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
	<script type="text/javascript">
		$("#datatable").DataTable({"aaSorting": []});
        function changeMissingReport(id)
        {
            if(id == "")
            {
                document.location.href = "{{url('/admin/Reports/missing')}}";
            }
            else
            {
                document.location.href = "{{url('/admin/Reports/missing/')}}/"+id;
            }
        }
	</script>

@endsection