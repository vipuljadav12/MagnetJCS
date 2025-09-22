    @php 
            $grade_average_data = \App\Modules\Submissions\Models\SubmissionAcademicGradeCalculation::where('submission_id',$submission->id)->first();
            if(!empty($grade_average_data))
                $academic_score = $grade_average_data->given_score;
            else
                $academic_score = '';
            $acd_eligibility_id = getEligibilitiesDynamic($submission->first_choice, 'Academic Grades');
            if(count($acd_eligibility_id) <= 0)
            {
                $acd_eligibility_id = getEligibilitiesDynamic($submission->second_choice, 'Academic Grades');
            }

            $acd_eligibility_data = getEligibilityContent1($acd_eligibility_id[0]->assigned_eigibility_name); 
            $term_calc = $term_calc_n = [];
            if(isset($acd_eligibility_data->terms_calc))
            {
                foreach($acd_eligibility_data->terms_calc as $akey => $tvalue){
                    foreach($tvalue as $k1 => $term){
                        if(isset($term_calc_n[$akey]))
                            array_push($term_calc_n[$akey], $term);
                        else
                            $term_calc_n[$akey] = array($term);
                    }
                }
            }
            $acdTerms = $term_calc_n;
            $grade_year = [];
            if(isset($gradeInfo)){
                $grade_year = explode(',', $gradeInfo->year);
            }


        $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
        $content = $eligibility_data ?? null;
        $scoring = $eligibility_data->scoring ?? null;
       

        $avgSum = $avgCnt = 0;
        foreach($acdTerms as $acdkey=>$acdvalue)
        {
            if(in_array($acdkey, $grade_year))
            {
                $submission_data = DB::table("submission_grade")->where("submission_id", $submission->id)->where('academicYear', $acdkey)->where("academicTerm", $acdvalue)->whereIn('courseTypeID', array(3,4,9,7))->get();
                if(count($submission_data) > 0)
                {
                    $avgSum += $submission_data->sum('numericGrade');
                    $avgCnt += $submission_data->count();
                }

            }
        }


        $finalAvg = 0;
        if($avgCnt > 0 && $avgSum > 0)
        {
            $finalAvg = number_format($avgSum/$avgCnt, 2);
        }

    @endphp


    @if(isset($scoring->type) && ($scoring->type == "DD" || $scoring->type == "GA" || $scoring->type == "CLSG"))

            @if(isset($submission_data) && $submission_data != '')
            <form id="store_grades_form" method="post" action="{{ url('admin/Submissions/update/AcademicGradeCalculation',$submission->id) }}">
                {{csrf_field()}}
                <div class="card shadow">
                    <div class="card-header">{{$value->eligibility_name}}</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Grade Average : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="gpa" value="{{$finalAvg}}">
                            </div>
                        </div>

                        @if(isset($scoring->method) && $scoring->method == "NR")
                            @php $options = $scoring->NR @endphp
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Grade Average Score: </label>
                                        <div class="col-12 col-md-12">
                                                <select class="form-control custom-select template-type" name="given_score">
                                                    <option value="">Select Option</option>
                                                    @foreach($options as $k=>$v)
                                                        <option value="{{$v}}" @if($v==$academic_score) selected="" @endif>{{$v}}</option>
                                                    @endforeach
                                                </select>

                                        </div>
                                    </div>
                                
                        @endif

                        <div class="text-right"> 
                        <button type="submit" class="btn btn-success">    
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                    </div>
                     
                </div>
                </form>
            @endif

           

    @endif

        <div class="modal fade" id="overrideAcademicGrade" tabindex="-1" role="dialog" aria-labelledby="employeependingLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="employeependingLabel">Alert</h5>
                            <button type="button" class="close overrideAcademicGradeNo" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Comment : </label>
                                    <textarea class="form-control" name="grade_override_comment" id="grade_override_comment"></textarea>
                                    <input type="hidden" name="grade_override_status" id="grade_override_status">
                                </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-value="" id="overrideAcademicGradeYes" onclick="overrideAcademicGrade()">Submit</button>
                            <button type="button" class="btn btn-danger overrideAcademicGradeNo">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>