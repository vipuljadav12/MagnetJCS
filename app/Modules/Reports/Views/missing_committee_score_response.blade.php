<div class="card-body">

    <div class=" mb-10">
        <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
        <div class="text-right">    
                <a href="{{url('admin/Reports/import/missing/'.$enrollment_id.'/committee_score')}}" title="Import Missing Committee Score" class="btn btn-secondary">Import Missing Committee Score</a>
            {{-- <a href="javascript:void(0)" onclick="exportMissing()" title="Export Missing Grade" class="btn btn-secondary">Export Missing Grade</a> --}}
        </div>
    </div>
    
    @if(!empty($data))

    <div class="table-responsive">
        <table class="table table-striped mb-0 w-100" id="tbl_committee_score">
            <thead>
                <tr>
                    <th class="align-middle">Submission ID</th>
                    <th class="align-middle">State ID</th>
                    <th class="align-middle notexport">Student Type</th>
                    <th class="align-middle">First Name</th>
                    <th class="align-middle">Last Name</th>
                    <th class="align-middle">Next Grade</th>
                    <th class="align-middle">Current School</th>
                    <th class="align-middle notexport">Action</th>
                    <th class="align-middle">First Choice Program</th>
                    <th class="align-middle">First Choice Committee Score</th>
                    <th class="align-middle">Second Choice Program</th>
                    <th class="align-middle">Second Choice Committee Score</th>
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
                                <a href="javascript:void(0)" id="edit{{$value['submission_id']}}" onclick="editRow({{$value['submission_id']}})" title="Edit"><i class="far fa-edit"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" onclick="saveScore({{$value['submission_id']}})" id="save{{$value['submission_id']}}" title="Save"><i class="fa fa-save"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" id="cancel{{$value['submission_id']}}" onclick="hideEditRow({{$value['submission_id']}})" title="Cancel"><i class="fa fa-times"></i></a>
                            </div>
                        </td>

                        @foreach($choices as $choice)
                            @php
                                $is_valid = in_array($choice, $value['has_valid']);
                                $program_name = 'NA';
                                if ($value[$choice.'_program'] != '') {
                                    $program_name = $value[$choice.'_program'];
                                }
                            @endphp 
                            <td class="programfilter">{{$program_name}}</td>
                            <td class="text-center">
                                <span @if(!is_numeric($value[$choice.'_choice_committee_score']) && $is_valid) class="scorelabel" @endif>
                                        {!! $value[$choice.'_choice_committee_score'] !!}
                                </span> 
                                @if(!is_numeric($value[$choice.'_choice_committee_score']) && $is_valid)
                                    <input type="text"  class="form-control numbersOnly d-none scoreinput" value="0" maxlength="3" min="0" max="100" id="{{$value['submission_id'].','.$value[$choice.'_choice_program_id']}}">
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