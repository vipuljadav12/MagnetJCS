<div class="card-body">

    <div class=" mb-10">
        <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
        {{-- <div class="text-right">    
                <a href="{{url('admin/Reports/import/missing/'.$enrollment_id.'/committee_score')}}" title="Import Missing Committee Score" class="btn btn-secondary">Import Missing Committee Score</a>
            <a href="javascript:void(0)" onclick="exportMissing()" title="Export Missing Grade" class="btn btn-secondary">Export Missing Grade</a>
        </div> --}}
    </div>
    
    @if(!empty($data))

    <div class="table-responsive">
        <table class="table table-striped mb-0 w-100" id="tbl_test_score">
            <thead>
                <tr>
                    <th class="align-middle">Submission ID</th>
                    <th class="align-middle">State ID</th>
                    <th class="align-middle notexport">Student Type</th>
                    <th class="align-middle">Last Name</th>
                    <th class="align-middle">First Name</th>
                    <th class="align-middle">Next Grade</th>
                    <th class="align-middle">Current School</th>
                    <th class="align-middle notexport">Action</th>
                    @foreach($ts_name_fields as $ts_name)
                        <th class="align-middle">{{ $ts_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $choices = ['first', 'second'];
                @endphp
                @foreach($data as $key=>$value)
                    <tr id="row{{$value['submission_id']}}">
                        <td class="text-center"><a href="{{url('/admin/Submissions/edit/'.$value['id'])}}">{{$value['id']}}</a></td>
                        <td class="">{{$value['student_id']}}</td>
                        <td class="notexport">{{($value['student_id'] != "" ? "Current" : "Non-Current")}}</td>
                        <td class="">{{$value['first_name']}}</td>
                        <td class="">{{$value['last_name']}}</td>
                        <td class="text-center">{{$value['next_grade']}}</td>
                        <td class="">{{$value['current_school']}}</td>
                        <td class="text-center notexport">
                            <div>
                                <a href="javascript:void(0)" id="edit{{$value['submission_id']}}" onclick="editRow({{$value['submission_id']}})" title="Edit"><i class="far fa-edit"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" onclick="saveScore({{$value['submission_id']}}, {{$value['test_scores']['program_id']}})" id="save{{$value['submission_id']}}" title="Save"><i class="fa fa-save"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" id="cancel{{$value['submission_id']}}" onclick="hideEditRow({{$value['submission_id']}})" title="Cancel"><i class="fa fa-times"></i></a>
                            </div>
                        </td>

                        @foreach($ts_name_fields as $ts_name)
                            @php
                                $ts_rank = $value['test_scores']['data'][$ts_name] ?? 'NA';
                                // if ($value['id'] == 3176) {
                                //     dd($ts_rank);
                                // }
                            @endphp
                            <td class="text-center">
                                <span @if($ts_rank === 404) class="scorelabel" @endif>
                                    @if($ts_rank === 404)
                                        {!! '<i class="fas fa-exclamation-circle text-danger"></i>' !!}
                                    @else
                                        {{ $ts_rank }}
                                    @endif
                                </span> 
                                @if($ts_rank === 404)
                                    <input type="text"  class="form-control numbersOnly d-none scoreinput" value="0" maxlength="3" min="0" max="100" id="{{$value['test_scores']['program_id'].','.$ts_name}}">
                                @endif
                            </td>
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="table-responsive text-center"><p>No records found.</div>
    @endif
</div>