<div class="card-body">
    <div class="row col-md-12 pull-left pb-10">
        <select class="form-control custom-select d-none" onchange="loadSubmissionData(this.value)"> 
            <option value="0" @if($late_submission == 0) selected @endif>Submission</option>
            <option value="1" @if($late_submission == 1) selected @endif>Late Submission</option>
        </select>
    </div>

    <div class=" mb-10">
        <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
        <div class="text-right">    
        @if($display_outcome == 0)
         <a href="{{url('admin/Reports/import/missing/'.$enrollment_id.'/grade')}}" title="Import Missing Grades" class="btn btn-secondary">Import  Missing Grade</a>
         @endif                                        
            <a href="javascript:void(0)" onclick="exportMissing()" title="Export Missing Grade" class="btn btn-secondary">Export Missing Grade</a>
            </div>
    </div>
    
    @php 
        $config_subjects = Config::get('variables.subjects');
        $subject_count = count($subjects) ?? 0;
        $colspan = 8;
    @endphp
    
    @if(!empty($firstdata))

    <div class="table-responsive">
        <table class="table table-striped mb-0 w-100" id="datatable">
            <thead>
                <tr>
                    <th class="align-middle" colspan="{{$colspan}}" rowspan="2"></th>
                    @foreach ($terms as $tyear => $tvalue)
                        <th class="align-middle text-center" colspan="{{$subject_count}}">{{$tyear}}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($terms as $tyear => $tvalue)
                        @foreach($subjects as $value)
                            @php
                                $sub = $config_subjects[$value] ?? $value;
                            @endphp
                            <th class="align-middle">{{$sub}}</th>
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    <th class="align-middle">Submission ID</th>
                    <th class="align-middle">State ID</th>
                    <th class="align-middle notexport">Student Type</th>
                    <th class="align-middle">Last Name</th>
                    <th class="align-middle">First Name</th>
                    <th class="align-middle">Next Grade</th>
                    <th class="align-middle">Current School</th>
                    <th class="align-middle notexport">Action</th>
                    @foreach ($terms as $tyear => $tvalue)
                        @foreach($subjects as $value)
                            @foreach($tvalue as $value1)
                                <th class="align-middle">{{$value1}}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($firstdata as $key=>$value)
                    <tr id="row{{$value['submission_id']}}">
                        <td class="text-center"><a href="{{url('/admin/Submissions/edit/'.$value['id'])}}">{{$value['id']}}</a></td>
                        <td class="">{{$value['student_id']}}</td>
                        <td class="notexport">{{($value['student_id'] != "" ? "Current" : "Non-Current")}}</td>
                        <td class="">{{$value['first_name']}}</td>
                        <td class="">{{$value['last_name']}}</td>
                        <td class="text-center">{{$value['next_grade']}}</td>
                        <td class="">{{$value['current_school']}}</td>
                        <td class="text-center notexport"><div>
                                <a href="javascript:void(0)" id="edit{{$value['submission_id']}}" onclick="editRow({{$value['submission_id']}})" title="Edit"><i class="far fa-edit"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" onclick="saveScore({{$value['submission_id']}})" id="save{{$value['submission_id']}}" title="Save"><i class="fa fa-save"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" id="cancel{{$value['submission_id']}}" onclick="hideEditRow({{$value['submission_id']}})" title="Cancel"><i class="fa fa-times"></i></a>
                            </div>
                        </td>
                        @foreach ($terms as $tyear => $tvalue)
                            @foreach($subjects as $svalue)
                                @foreach($tvalue as $tvalue1)
                                    <td class="align-middle">
                                        @php
                                            $marks = $value['score'][$tyear][$svalue][$tvalue1] ?? '';
                                        @endphp
                                        <div class="text-center">
                                            <span @if(!is_numeric($marks)) class="scorelabel" @endif>
                                                    {!! $marks !!}
                                            </span> 
                                            @if(!is_numeric($marks))
                                                <input type="text"  class="form-control numbersOnly d-none scoreinput" value="0" maxlength="3" min="0" max="100" id="{{$value['submission_id'].','.$svalue.','.$tvalue1.','.$tyear}}">
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            @endforeach
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