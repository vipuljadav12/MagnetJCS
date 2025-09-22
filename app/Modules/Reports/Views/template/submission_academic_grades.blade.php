@php $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name) @endphp
@php $tmpdata = getSubmissionGradeData($submission->id) @endphp
@if(count($tmpdata) > 0)
    @php 
        $academic_years = array();
        for($i=1; $i <= $eligibility_data->terms_pulled[0]; $i++)
        {
            $academic_years[] = (date("Y")-$i)."-".(date("y")-($i-1)); 
        } 

    @endphp

@elseif($submission->student_id != "" && count($tmpdata) <= 0)
    @php 
        //$grade_data = getStudentGradeData($submission->student_id);
        

        //$currentYear = (date("Y")-1)."-".(date("y"));
        //$term_arr_grade = array();

        $tmpdata = array();
        $count = 0;
        $academic_years = array();
        for($i=1; $i <= $eligibility_data->terms_pulled[0]; $i++)
        {
            $completed_data = array();
            $academic_years[] = (date("Y")-$i)."-".(date("y")-($i-1)); 
            $term = (date("Y")-$i)."-".(date("y")-($i-1));
            $fterm = (date("Y")-$i)."-".(date("Y")-($i-1));

            $data = getStudentGradeDataYear($submission->student_id,  (date("Y")-$i)."-".(date("y")-($i-1)), (date("Y")-$i)."-".(date("Y")-($i-1)),Config::get('variables.subjects'));
           // echo $data['type'] . "!=". $eligibility_data->academic_term."<BR>";
            
            
           /* if($data['type'] == "SEM" && $eligibility_data->academic_term == "9W")
            {
                $eligibility_data->academic_term = "SEM";
            }
            if($data['type'] == "YE" && ($eligibility_data->academic_term == "9W" || $eligibility_data->academic_term == "SEM"))
            {
                $eligibility_data->academic_term = "YE";
            }

            
            if($data['type'] != $eligibility_data->academic_term)
            {
                if($data['type'] == "9W" && $eligibility_data->academic_term == "SEM")
                {
                    foreach($data['data'] as $ekey=>$evalue)
                    {
                        if(!in_array($evalue->courseTypeID, $completed_data) )//&& in_array($evalue->courseType, Config::get('variables.courses')))
                        {
                            $sem_data = getConsolidatedGradeWeekData($data['data'], $evalue->courseTypeID, "SEM");
                            $tmpdata[$count]['stateID'] = $evalue->stateID;
                            $tmpdata[$count]['academicYear'] = $term;
                            $tmpdata[$count]['sAcademicYear'] = $fterm;
                            $tmpdata[$count]['academicTerm'] = 'Semester 1';
                            $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID;
                            $tmpdata[$count]['courseType'] = $evalue->courseType;
                            $tmpdata[$count]['courseName'] = $evalue->courseName;
                            $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber;
                            $tmpdata[$count]['numericGrade'] = $sem_data['Semester 1'];
                            $count++;
                            $tmpdata[$count]['stateID'] = $evalue->stateID;
                            $tmpdata[$count]['academicYear'] = $term;
                            $tmpdata[$count]['sAcademicYear'] = $fterm;
                            $tmpdata[$count]['academicTerm'] = 'Semester 2';
                            $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID;
                            $tmpdata[$count]['courseType'] = $evalue->courseType;
                            $tmpdata[$count]['courseName'] = $evalue->courseName;
                            $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber;
                            $tmpdata[$count]['numericGrade'] = $sem_data['Semester 2'];
                            $count++;

                            $completed_data[] = $evalue->courseTypeID;
                        }
                    }
                }
                if($data['type'] == "9W" && $eligibility_data->academic_term == "YE")
                {
                    foreach($data['data'] as $ekey=>$evalue)
                    {
                        if(!in_array($evalue->courseTypeID, $completed_data))// && in_array($evalue->courseType, Config::get('variables.courses')))
                        {
                            $sem_data = getConsolidatedGradeWeekData($data['data'], $evalue->courseTypeID, "YE");
                            $tmpdata[$count]['stateID'] = $evalue->stateID;
                            $tmpdata[$count]['academicYear'] = $term;
                            $tmpdata[$count]['sAcademicYear'] = $fterm;
                            $tmpdata[$count]['academicTerm'] = 'Q 4.4 Final Grade';//'Year End';
                            $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID;
                            $tmpdata[$count]['courseType'] = $evalue->courseType;
                            $tmpdata[$count]['courseName'] = $evalue->courseName;
                            $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber;
                            $tmpdata[$count]['numericGrade'] = $sem_data['Yearly'];
                            $count++;
                            $completed_data[] = $evalue->courseTypeID;
                        }
                    }
                }

                if($data['type'] == "SEM" && $eligibility_data->academic_term == "YE")
                {
                    foreach($data['data'] as $ekey=>$evalue)
                    {
                        if(!in_array($evalue->courseTypeID, $completed_data))// && in_array($evalue->courseType, Config::get('variables.courses')))
                        {
                            $sem_data = getConsolidatedGradeWeekData($data['data'], $evalue->courseTypeID, "YE");
                            $tmpdata[$count]['stateID'] = $evalue->stateID;
                            $tmpdata[$count]['academicYear'] = $term;
                            $tmpdata[$count]['sAcademicYear'] = $fterm;
                            $tmpdata[$count]['academicTerm'] = 'Q 4.4 Final Grade';//'Year End';
                            $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID;
                            $tmpdata[$count]['courseType'] = $evalue->courseType;
                            $tmpdata[$count]['courseName'] = $evalue->courseName;
                            $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber;
                            $tmpdata[$count]['numericGrade'] = $sem_data['Yearly'];
                            $count++;
                            $completed_data[] = $evalue->courseTypeID;
                        }
                    }
                }
            }
            else
            {*/
                foreach($data['data'] as $ekey=>$evalue)
                {
                    /*if(in_array($evalue->courseType, Config::get('variables.courses')))
                    {*/
                        $tmpdata[$count]['stateID'] = $evalue->stateID;
                        $tmpdata[$count]['academicYear'] = $term;
                        $tmpdata[$count]['sAcademicYear'] = $fterm;
                        $tmpdata[$count]['academicTerm'] = $evalue->GradeName;
                        $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID;
                        $tmpdata[$count]['courseType'] = $evalue->courseType;
                        $tmpdata[$count]['courseName'] = $evalue->courseName;
                        $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber;
                        $tmpdata[$count]['numericGrade'] = $evalue->numericGrade;
                        $count++;
                        //$completed_data[] = $evalue->courseTypeID;
                   // }
                }
            //}
        }
        
    @endphp

@else
    @php $grade_data = array() @endphp
@endif 



@section('styles')
<style type="text/css">
    .error {
        color: red;
    }
</style>
@endsection

<div class="card shadow">
    <form id="store-grades" method="post" action="{{ url('admin/Submissions/storegrades',$submission->id) }}">
        {{csrf_field()}}
        <div class="card-header">{{$value->eligibility_name}}</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="grade-table">
                    <thead>
                        <tr>
                            <th class="align-middle">Delete</th>
                            <th class="align-middle">Academic year</th>
                            <th class="align-middle">Academic Term</th>
                            <th class="align-middle">Course Type ID</th>
                            <th class="align-middle">Course Name</th>
                            <th class="align-middle">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($tmpdata))
                            @php $courses = Config::get('variables.courses') @endphp
                            @php $goodTerms = Config::get('variables.goodTerms') @endphp
                            
                            @php $academic_terms = array_merge($goodTerms, getAcademicTerms($tmpdata)) @endphp

                            @foreach($tmpdata as $ekey=>$evalue)
                            <tr>
                                <input type="text" name="sectionNumber[{{$ekey}}]" value="{{$evalue['sectionNumber']}}" hidden>
                                <input type="text" name="courseTypeID[{{$ekey}}]" value="{{$evalue['courseTypeID']}}" hidden>
                                <td class="">                                        
                                    <input type="checkbox" id="table00" name="selectCheck" />
                                    <label for="table00" class="label-xs check-secondary"></label>
                                </td>
                                <td class="">
                                    <select name="academicYear[{{$ekey}}]" class="form-control custom-select form-control-sm">
                                        <option value="">Choose an Option</option>
                                        @foreach($academic_years as $akey=>$avalue)
                                            <option @if($evalue['academicYear']==$avalue || $evalue['sAcademicYear']==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="">
                                    <select name="academicTerm[{{$ekey}}]" class="form-control form-control-sm">
                                        <option value="">Choose an Option</option>
                                        @foreach($academic_terms as $akey=>$avalue)
                                            <option @if($evalue['academicTerm']==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                    
                                </td>
                                <td class="">
                                    <input type="text" class="form-control" name="courseType[{{$ekey}}]" value="{{$evalue['courseTypeID']}}">
                                    <!--<select name="courseType[{{$ekey}}]" class="form-control custom-select form-control-sm">
                                        <option value="">Choose an Option</option>
                                        @foreach($courses as $akey=>$avalue)
                                            <option @if($evalue['courseType']==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>-->         


                                </td>
                                <td class=""><input name="courseName[{{$ekey}}]" maxlength="100" type="text" class="form-control form-control-sm" value="{{$evalue['courseName']}}"></td>
                                <td class=""><input name="numericGrade[{{$ekey}}]" maxlength="3" type="text" class="form-control form-control-sm" value="{{$evalue['numericGrade']}}"></td>
                            </tr>
                            @endforeach
                        @else
                            @php $courses = Config::get('variables.courses') @endphp
                            @php $academic_terms = Config::get('variables.goodTerms') @endphp
                            <tr>
                                <td class="">
                                    <input type="checkbox" id="table00" name="selectCheck" />
                                    <label for="table00" class="label-xs check-secondary"></label>
                                </td>
                                <td class="">
                                    <select name="academicYear[0]" class="form-control custom-select form-control-sm">

                                        <option value="">Choose an Option</option>
                                        @foreach($academic_years as $akey=>$avalue)
                                            <option value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                        
                                </td>
                                <td class="">
                                    <select name="academicTerm[0]" class="form-control form-control-sm">
                                        <option value="">Choose an Option</option>
                                        @foreach($academic_terms as $akey=>$avalue)
                                            <option value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="">
                                    <select name="courseType[0]" class="form-control custom-select form-control-sm">
                                        <option value="">Choose an Option</option>
                                        @foreach($courses as $akey=>$avalue)
                                            <option value="{{$avalue}}">{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                   
                                </td>
                                <td class=""><input name="courseName[0]" maxlength="100" type="text" class="form-control form-control-sm"></td>
                                <td class=""><input name="numericGrade[0]" maxlength="3" type="text" class="form-control form-control-sm"></td>

                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                    <tr class="hidden">
                        <td colspan="9" class="text-right">
                            <a href="javascript:void(0);" class="btn btn-secondary add-grade" title="">Add New</a>
                            <button type="submit" form="store-grades" class="btn btn-success"><i class="fa fa-save"> </i></button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>

@section('scripts')

<script type="text/javascript">

    $('#store-grades').validate();

    gradeValidation();    
    function gradeValidation()
    {
        $(document).find('select[name^="academicYear"]').each(function(){
            $(this).rules('add', {
                required: true,
                messages: {
                    required: "Academic Year is required. "
                }
            })
        });
        $(document).find('select[name^="academicTerm"]').each(function(){
            $(this).rules('add', {
                required: true,
                messages: {
                    required: "Academic Term is required. "
                }
            })
        });
        $(document).find('select[name^="courseType"]').each(function(){
            $(this).rules('add', {
                required: true,
                messages: {
                    required: "Course Type is required. "
                }
            })
        });
        $(document).find('input[name^="courseName"]').each(function(){
            $(this).rules('add', {
                required: true,
                maxlength: 100,
                messages: {
                    required: "Course Name is required. ",
                    maxlength: "No more than 100 characters."
                }
            })
        });
        $(document).find('input[name^="numericGrade"]').each(function(){
            $(this).rules('add', {
                required: true,
                digits: true,
                maxlength: 3,
                messages: {
                    required: "Grade is required. ",
                    maxlength: "No more than 3 digits"
                }
            })
        });
    }

    $('.add-grade').click(function(){
        // alert();
        var nextIndex = $('#grade-table').find('tbody>tr').length;
        var gradeclone = $('#grade-table').find('tbody>tr:first-child').clone();
        
        // Replacing dynamic array name value
        gradeclone.find('input,select').each(function(){
            // Removing error class from field
            $(this).removeClass('error');
            var name = $(this).attr('name');
            var name = name.match(/\w+/);
            // var name = name.replace('[', '').replace(']', '').replace(/\d+/, '');
            var newName = name+'['+nextIndex+']';
            $(this).attr('name', newName);
        });

        // removing hidden fields
        gradeclone.find('input[name^="sectionNumber"]').remove();
        gradeclone.find('input[name^="courseTypeID"]').remove();
        // removing error label
        gradeclone.find('label.error').remove();
        gradeclone.find('input').val('');
        // Select default option of drop down
        gradeclone.find('select').each(function(){
            $(this).val($('option:first', this).val());
        });
        $('#grade-table').append(gradeclone);
        // Adding validation
        gradeValidation();
    });

</script>

@endsection