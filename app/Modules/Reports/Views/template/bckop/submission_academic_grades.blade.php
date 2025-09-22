    @if($submission->student_id != "")
        @php $grade_data = getStudentGradeData($submission->student_id) @endphp
    @else
        @php $grade_data = array() @endphp
    @endif 


    <div class="card shadow">
        <div class="card-header">Grades</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="grade-table">
                    <thead>
                        <tr>
                            <th class="align-middle">Delete</th>
                            <th class="align-middle">Academic year</th>
                            <th class="align-middle w-120">Academic Term</th>
                            <th class="align-middle w-120">Course Type ID</th>
                            <th class="align-middle">Core Name</th>
                            <th class="align-middle">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($grade_data))
                            @php $courses = Config::get('variables.courses') @endphp
                            @php $goodTerms = Config::get('variables.goodTerms') @endphp
                            @php $academic_years = getAcademicYearGrade($grade_data) @endphp
                            @php $academic_terms = array_merge($goodTerms, getAcademicTerms($grade_data)) @endphp
                            @foreach($grade_data as $key=>$value)
                                <tr>
                                    <td class="">
                                        <input type="checkbox" id="table00" name="selectCheck" />
                                        <label for="table00" class="label-xs check-secondary"></label>
                                    </td>
                                    <td class="">
                                        <select class="form-control custom-select form-control-sm">
                                            <option>Choose an Option</option>
                                            @foreach($academic_years as $akey=>$avalue)
                                                <option @if($value->academicYear==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="">
                                        <select class="form-control form-control-sm">
                                            <option>Choose an Option</option>
                                            @foreach($academic_terms as $akey=>$avalue)
                                                <option @if($value->academicTerm==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                            @endforeach
                                        </select>
                                        
                                    </td>
                                    <td class="">
                                        <select class="form-control custom-select form-control-sm">
                                            <option>Choose an Option</option>
                                            @foreach($courses as $akey=>$avalue)
                                                <option @if($value->courseType==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value="{{$value->courseName}}"></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value="{{$value->numericGrade}}"></td>
                                </tr>
                                
                            @endforeach
                        @else
                            <tr>
                                <td class="">
                                    <input type="checkbox" id="table00" name="selectCheck" />
                                    <label for="table00" class="label-xs check-secondary"></label>
                                </td>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        <option>Choose Year</option>
                                        <option selected>2019</option>
                                        <option>2020</option>
                                    </select>
                                </td>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        <option>SEM-3</option>
                                        <option>SEM-4</option>
                                    </select>
                                </td>
                                <td class="">
                                    <select class="form-control custom-select form-control-sm">
                                        <option>Math</option>
                                        <option>Science</option>
                                    </select>
                                </td>
                                <td class=""><input type="text" class="form-control form-control-sm" value="Math"></td>
                                <td class=""><input type="text" class="form-control form-control-sm" value="Math 4"></td>

                            </tr>
                        @endif
                    
                    
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="9"><a href="javascript:void(0);" class="btn btn-secondary add-grade" title="">Add New</a></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
