<div class="">
    <form method="post" action="{{ url('admin/Submissions/update',$submission->id) }}" id="generalSubmission">    
    {{csrf_field()}}   
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="card shadow">
                    <div class="card-header">Student Information</div>
                    <div class="card-body">
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Confirmation No: </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="" value="{{$submission->confirmation_no}}" disabled>
                            </div>
                        </div>
                        @if($district->lottery_number_display == "Yes")
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Lottery Number : </label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="{{$submission->lottery_number}}" disabled>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">State ID : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="student_id" value="{{$submission->student_id}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('first_name') ?? 'First Name'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="first_name" value="{{$submission->first_name}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('last_name') ?? 'Last Name'}}<span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="last_name" value="{{$submission->last_name}}">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('birthday') ?? 'Date of Birth'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12 row">
                                 @if($submission->birthday != '')
                                    @php $bdates = explode("-", $submission->birthday) @endphp
                                 @else
                                    @php $bdates = array(date("Y"), date("m"), date("d")) @endphp
                                 @endif
                                 

                                <div class="col-4">
                                    @php 
                                        $months = Config::get('variables.months');
                                        // print_r($months);
                                    @endphp
                                    <select class="form-control changeDate" id="month">
                                        @foreach($months as $key=>$value)
                                            <option value="{{$key}}" @if(isset($bdates[1]) && $bdates[1]==$key) selected="selected" @endif>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <select class="form-control changeDate" id="day">
                                         @for($i=1; $i <= 31; $i++)
                                            @php 
                                                if($i < 10)
                                                    $day = "0".$i;
                                                else
                                                    $day = $i;

                                            @endphp
                                            <option value="{{$day}}" @if(isset($bdates[2]) && $bdates[2]==$day) selected="selected" @endif>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control changeDate" id="year">
                                        @for($i=2020; $i >= 1970; $i--)
                                            <option value="{{$i}}" @if($bdates[0]==$i) selected="selected" @endif>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <input type="hidden" class="form-control" name="birthday" id="birthday" value="{{date('Y-m-d', strtotime($submission->birthday))}}">
                                {{-- {{$submission->birthday}} --}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('race') ?? 'Race'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                @php $race_arr = array("American Indian/Alaskan Native - Hispanic","American Indian/Alaskan Native - Non-Hispanic","Asian - Hispanic","Asian - Non-Hispanic","Black/African American - Hispanic","Black/African American - Non-Hispanic","Native Hawaiian or Other Pacific Islander - Hispanic","Native Hawaiian or Other Pacific Islander - Non-Hispanic","White - Hispanic","White - Non-Hispanic","Multi Race- two or more races  - Hispanic","Multi Race- two or more races- Non-Hispanic","Caucasian - Hispanic","Caucasian - Non-Hispanic","African-American - Hispanic","African-American - Non-Hispanic","American Indian - Hispanic","American Indian - Non-Hispanic","Asian - Hispanic","Asian - Non-Hispanic","Other - Hispanic","Other - Non-Hispanic","Two or More Races - Hispanic","Two or More Races - Non-Hispanic","Pacific Islander - Hispanic","Pacific Islander - Non-Hispanic","Hispanic - Hispanic","Hispanic - Non-Hispanic"); @endphp
                                <select class="form-control" name="race">
                                    <option value="">Select Race</option>
                                    @foreach($race_arr as $key=>$value)
                                        <option value="{{$value}}" @if($submission->race == $value) selected="selected" @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('gender') ?? 'Gender'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                @php $gender_arr = array("Male","Female","Choose Not to Answer"); @endphp
                                @php 
                                        if($submission->gender == "F")
                                            $gender = "Female";
                                        elseif($submission->gender == "M")
                                            $gender = "Male";
                                        else
                                            $gender = $submission->gender;
                                    @endphp
                                <select class="form-control" name="gender">
                                    <option value="">Select Gender</option> 
                                    @foreach($gender_arr as $key=>$value)
                                        <option value="{{$value}}" @if($gender == $value) selected="selected" @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('address') ?? 'Address'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="address" value="{{$submission->address}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('city') ?? 'City'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="city" value="{{$submission->city}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('state') ?? 'State'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="state">
                                    <option value="">Select an Option</option> 
                                @php $stateArray = Config::get('variables.states') @endphp

                                @foreach($stateArray as $stkey=>$stvalue)
                                    <option value="{{$stkey}}" @if(strtolower($submission->state)==strtolower($stkey)) selected @endif>{{$stvalue}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('zip') ?? 'ZIP'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="zip" value="{{$submission->zip}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('phone_number') ?? 'Phone Number'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" maxlength="15" name="phone_number" value="{{$submission->phone_number}}">
                                <div class="small">Max 15 Characters</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('alternate_number') ?? 'Alternate Number'}} <span class="required">&nbsp;</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="alternate_number" value="{{$submission->alternate_number}}" maxlength="15">
                                <div class="small">Max 15 Characters</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('current_school') ?? 'Current School'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="current_school" value="{{$submission->current_school}}">
                                <!--<select class="form-control custom-select" name="current_school">
                                    @foreach($data['schools'] as $key=>$school)
                                        <option value="{{$school->id}}" @if($submission->current_school==$school->id) selected="" @endif> {{$school->name}}</option>
                                    @endforeach
                                </select>-->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('zoned_school') ?? 'Zoned School'}} <span class="required">&nbsp;</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="zoned_school" value="{{$submission->zoned_school}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('current_grade') ?? 'Current Grade'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="current_grade" disabled>
                                    @foreach($data['grades'] as $key=>$grade)
                                        <option value="{{$grade->name}}" @if($submission->current_grade==$grade->name) selected="" @endif> {{$grade->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('next_grade') ?? 'Next Grade'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="next_grade" disabled>
                                    @foreach($data['grades'] as $key=>$grade)
                                        <option value="{{$grade->name}}" @if($submission->next_grade==$grade->name) selected="" @endif> {{$grade->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                       <div class="form-group d-flex justify-content-between pt-5 d-none">
                            <label for="" class="control-label">Allow Manual Grade Change : </label>
                            <div class="">
                                <input id="chk_grade_change" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="manual_grade_change" {{$submission->manual_grade_change=='Y' ? 'checked' : ''}}/>
                            </div>
                        </div>
                        <div class="form-group row d-none" id="grade_change_comment">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="grade_change_comment" id="grade_add_comment" class="form-control"></textarea>
                            </div>
                        </div>

                       

                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card shadow">
                    <div class="card-header">Submission Information</div>
                    <div class="card-body">


                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Late Submission  : </label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select" disabled>
                                        @if($submission->late_submission == "Y")
                                            <option>Yes</option>
                                        @else
                                            <option>No</option>
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('parent_first_name') ?? 'Parent First Name'}} <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_first_name" value="{{$submission->parent_first_name}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('parent_last_name') ?? 'Parent Last Name'}}<span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_last_name" value="{{$submission->parent_last_name}}">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('parent_email') ?? "Parent's Email"}} : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_email" value="{{$submission->parent_email}}">
                                <div class="small">changes to this field will only affect new messages that go out.</div>
                                <div class=""><a href="{{url('/admin/Submissions/confirmation/resend/'.$submission->id)}}" class="btn btn-sm btn-success" title="Resend Confirmation"><i class="far fa-paper-plane"></i> Resend Confirmation</a></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('open_enrollment') ?? 'Open Enrollment'}} : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="open_enrollment">
                                    <option value="0">Select Enrollment</option>
                                    @foreach($data['enrollments'] as $key=>$enrollment)
                                        <option value="{{$enrollment->id}}" @if($submission->open_enrollment==$enrollment->id) selected="selected" @endif> {{$enrollment->school_year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @php
                            $applicationPrograms = getProgramDropdown($submission->application_id);
                            $school_master_name = getSchoolMasterName($submission->zoned_school);
                        @endphp
                        
                        @foreach (getChoiceAry() as $choicekey => $choicetxt)
                            @php
                                $choice_uctxt = ucwords($choicetxt);
                            @endphp
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">{{getFieldLabel($choicetxt.'_choice') ?? $choice_uctxt.' Choice'}} : </label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select choice_selection" name="{{$choicetxt}}_choice" id="{{$choicetxt}}_choice">
                                        <option value="">Choose a {{$choice_uctxt}} Choice</option>
                                        @foreach($applicationPrograms as $key=>$applicationProgram)
                                            @php
                                                $program_zoned_schools = ($applicationProgram->zoned_schools != '') ? explode(',', $applicationProgram->zoned_schools) : [];
                                            @endphp
                                            @if($submission->next_grade == $applicationProgram->grade_name)
                                                
                                                    <option value="{{$applicationProgram->id}}" @if($submission->{$choicetxt.'_choice'}==$applicationProgram->id) selected="selected" @endif>{{$applicationProgram->program_name}} - Grade {{$applicationProgram->grade_name}}</option>
                                                
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Sibling ID : </label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="{{($submission->{$choicetxt.'_sibling'})}}" name="{{$choicetxt}}_sibling" id="{{$choicetxt}}_sibling_field" onblur="checkSibling(this)">
                                </div>
                                <label class="control-label col-12 col-md-12 {{$choicetxt}}_sibling_label">{{str_replace("-", "", trim(getStudentName($submission->{$choicetxt.'_sibling'})))}}</label>
                            </div>
                        @endforeach

                        <div class="form-group row" id="choice_comment" style="display: none;">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="choice_comment" id="add_comment" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group row" style="display: none;">
                            <label class="control-label col-12 col-md-12">{{getFieldLabel('special_accommodations') ?? 'Special Accommodations '}}  : </label>
                            <div class="col-12 col-md-12">
                                 <select class="form-control custom-select" name="special_accommodations" id="special_accommodations">
                                    <option vlaue="No" @if($submission->special_accommodations=="No") selected="selected" @endif>No</option>
                                    <option vlaue="Yes" @if($submission->special_accommodations=="Yes") selected="selected" @endif>Yes</option>
                                 </select>
                            </div>
                        </div>

                        @if($submission->form_id == 2)
                         <div class="form-group row">
                                <label class="control-label col-12 col-md-12">{{getFieldLabel('gifted_student') ?? 'Gifted Status ?'}}  : </label>
                                <div class="col-12 col-md-12">
                                     <select class="form-control custom-select" name="gifted_student" id="gifted_student">
                                        <option vlaue="">Choose an Option</option>
                                        <option vlaue="Gifted" @if($submission->gifted_student=="Gifted") selected="selected" @endif>Gifted</option>
                                        <option vlaue="Not Gifted" @if($submission->gifted_student=="Not Gifted") selected="selected" @endif>Not Gifted</option>
                                        <option vlaue="Parent Identified as Gifted" @if($submission->gifted_student=="Parent Identified as Gifted") selected="selected" @endif>Parent Identified as Gifted</option>
                                        

                                     </select>
                                   
                                </div>
                            </div>
                        @endif
                             @if($submission->mcp_employee != "Yes") 
                                @php $mcp_class = "d-none" @endphp
                             @else
                                @php $mcp_class = "" @endphp
                             @endif
                                <div class="form-group row {{$mcp_class}}" id="employee_id_div">
                                    <label class="control-label col-12 col-md-12">{{getFieldLabel('employee_id') ?? 'Employee ID'}}  : </label>
                                    <div class="col-12 col-md-12">
                                         <input type="text" class="form-control" name="employee_id" id="employee_id" value="{{$submission->employee_id}}">
                                    </div>
                                </div>
                            <div class="form-group row {{$mcp_class}}" id="work_location_div">
                                <label class="control-label col-12 col-md-12">{{getFieldLabel('work_location') ?? 'Work Location'}}  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="work_location" id="work_location" value="{{$submission->work_location}}">
                                </div>
                            </div>
                            <div class="form-group row {{$mcp_class}}" id="employee_first_name_div">
                                <label class="control-label col-12 col-md-12">{{getFieldLabel('employee_first_name') ?? 'Employee First Name'}}  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="employee_first_name" id="employee_first_name" value="{{$submission->employee_first_name}}">
                                </div>
                            </div>
                            <div class="form-group row {{$mcp_class}}" id="employee_last_name_div">
                                <label class="control-label col-12 col-md-12">{{getFieldLabel('employee_last_name') ?? 'Employee Last Name'}}  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="employee_last_name" id="employee_last_name" value="{{$submission->employee_last_name}}">
                                </div>
                            </div>
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Submission Status <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                @php
                                    $onlyStatus = false;
                                    $offered_program = "";
                                    if(/*$display_outcome == 0 || */in_array($submission->submission_status, array("Active", "Pending", "Application Withdrawn")))
                                    {
                                        $all_options = array(
                                            "Active",
                                            "Pending",
                                            "Application Withdrawn",
                                            "Denied due to Ineligibility",
                                            "Offered",
                                            "Offered and Accepted",
                                            /*"Inactive",
                                            
                                            
                                            "Inactive Due to No Transcript",
                                            
                                            "Offered And Declined",
                                            "Wait List",
                                            "Denied Due to Space",
                                            "Denied",
                                            "Application Withdrawn",
                                            "Declined Wait List"*/
                                        );
                                    }
                                    else
                                    {
                                        $arr = array("Offered and Accepted", "Offered and Declined",  "Declined / Waitlist for other",  "Waitlisted", "Denied due to Ineligibility");
                                        if(in_array($submission->submission_status, $arr))
                                        {
                                            if($submission->submission_status == "Offered and Accepted")
                                            {
                                                $all_options = array(
                                                    "Offered and Accepted",
                                                    "Offered and Declined",
                                                    "Application Withdrawn"
                                                );
                                                if($submission->second_choice != '' && $submission->second_choice != '0')
                                                {
                                                    array_push($all_options, "Application Withdrawn", "Declined / Waitlist for other");
                                                }
                                                if($offer_data->first_choice_final_status == "Offered")
                                                {
                                                    $offered_program = getProgramName($submission->first_choice_program_id);
                                                }   
                                                else
                                                {
                                                    $offered_program = getProgramName($submission->second_choice_program_id);
                                                }   

                                                if(!empty($waitlist_data))
                                                {
                                                    if($waitlist_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }

                                                if(!empty($late_submission_data))
                                                {
                                                    if($late_submission_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }
                                  
                                            }
                                            else if($submission->submission_status == "Offered and Declined")
                                            {
                                                $all_options = array(
                                                    "Offered and Declined",
                                                    "Offered",
                                                    "Application Withdrawn"
                                                ); 
                                                if($submission->second_choice != '' && $submission->second_choice != '0')
                                                {
                                                    array_push($all_options, "Declined / Waitlist for other");
                                                }

                                                if($offer_data->first_choice_final_status == "Offered")
                                                {
                                                    $offered_program = getProgramName($submission->first_choice_program_id);
                                                }   
                                                else
                                                {
                                                    $offered_program = getProgramName($submission->second_choice_program_id);
                                                } 
                                                if(!empty($waitlist_data))
                                                {
                                                    if($waitlist_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                } 


                                                if(!empty($late_submission_data))
                                                {
                                                    if($late_submission_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }                  
                                            }
                                            else if($submission->submission_status == "Declined / Waitlist for other")
                                            {
                                                $all_options = array(
                                                    "Declined / Waitlist for other",
                                                    "Offered and Declined",
                                                    "Offered",
                                                    "Application Withdrawn"
                                                );   
                                                if($offer_data->first_choice_final_status == "Offered")
                                                {
                                                    $offered_program = getProgramName($submission->first_choice_program_id);
                                                }   
                                                else
                                                {
                                                    $offered_program = getProgramName($submission->second_choice_program_id);
                                                } 
                                                if(!empty($waitlist_data))
                                                {
                                                    if($waitlist_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }


                                                if(!empty($late_submission_data))
                                                {
                                                    if($late_submission_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }                                        
                                            }
                                            else if($submission->submission_status == "Waitlisted")
                                            {
                                                $all_options = array(
                                                    "Waitlisted",
                                                    "Offered",
                                                    "Application Withdrawn"
                                                );                                        
                                            }
                                            else if($submission->submission_status == "Denied due to Ineligibility")
                                            {
                                                $all_options = array(
                                                    "Offered",
                                                    "Denied due to Ineligibility"
                                                );  
                                            }
                                            else
                                            {
                                                $all_options = array(
                                                    "Offered and Accepted",
                                                    "Offered and Declined",
                                                    "Declined / Waitlist for other",
                                                    "Application Withdrawn"
                                                );                                        
                                            }
                                        }
                                        else
                                        {
                                            $onlyStatus = true;
                                            $all_options = array(
                                                "Offered",
                                                "Denied due to Eligibility",
                                                "Denied due to Incomplete Records",
                                                "Waitlisted",
                                                "Application Withdrawn"
                                            );                 
                                            if($submission->submission_status == "Offered" || $submission->submission_status == "Offered and Declined" || $submission->submission_status == "Auto Declined")
                                            {
                                                if($offer_data->first_choice_final_status == "Offered")
                                                {
                                                    $offered_program = getProgramName($submission->first_choice_program_id);
                                                }   
                                                else
                                                {
                                                    $offered_program = getProgramName($submission->second_choice_program_id);
                                                } 

                                                if(!empty($waitlist_data))
                                                {
                                                     if($waitlist_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    }
                                                } 


                                                if(!empty($late_submission_data))
                                                {
                                                    if($late_submission_data->first_choice_final_status == "Offered")
                                                    {
                                                        $offered_program = getProgramName($submission->first_choice_program_id);
                                                    }   
                                                    else
                                                    {
                                                        $offered_program = getProgramName($submission->second_choice_program_id);
                                                    } 
                                                }
                                            }                       
                                        }
                                    }


                                @endphp
                                <select class="form-control custom-select" name="submission_status" id="submission_status">
                                    @if($onlyStatus)
                                        <option value="{{$submission->submission_status}}">{{$submission->submission_status}}</option>
                                    @else
                                        <!--<option value="">Select Status</option>-->
                                        @foreach($all_options as $o=>$option)
                                            <option value="{{$option}}" @if(isset($submission->submission_status) && $submission->submission_status == $option) selected="" @endif>{{$option}}</option>
                                        @endforeach
                                    @endif
                                    {{-- <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Inactive Due To Ineligibility">Inactive Due To Ineligibility</option>
                                    <option value="Inactive Due to No Transcript">Inactive Due to No Transcript</option>
                                    <option value="Offered">Offered</option>
                                    <option value="Offered And Accepted">Offered And Accepted</option>
                                    <option value="Offered And Declined">Offered And Declined</option>
                                    <option value="Wait List">Wait List</option>
                                    <option value="Denied Due to Space">Denied Due to Space</option>
                                    <option value="Denied">Denied</option>
                                    <option value="Application Withdrawn">Application Withdrawn</option>
                                    <option value="Declined Wait List">Declined Wait List</option> --}}
                                </select>
                            </div>
                        </div>

                        <div class="d-none" id="changeprograms">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12" id="newstatus_label">New Offered Program : </label>
                                <div class="col-12 col-md-12">

                                    <select class="form-control custom-select" name="newofferprogram" id="newofferprogram">
                                        <option value="">Select Program</option>
                                        <option value="{{$submission->first_choice_program_id}}">{{getProgramName($submission->first_choice_program_id)  . " - Grade ".$submission->next_grade}}</option>
                                        @if($submission->second_choice_program_id > 0)
                                            <option value="{{$submission->second_choice_program_id}}">{{getProgramName($submission->second_choice_program_id)  . " - Grade ".$submission->next_grade}}</option>
                                        @endif
                                        @if($submission->third_choice_program_id > 0)
                                            <option value="{{$submission->third_choice_program_id}}">{{getProgramName($submission->third_choice_program_id)  . " - Grade ".$submission->next_grade}}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>


                        </div>
                         <div class="card shadow d-none" id="acpt_offer">
                                    <div class="card-header">Acceptance Window</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                <label class="">Last day and time to accept ONLINE</label>

                                                <div class="input-append date form_datetime">
                                                <input class="form-control datetimepicker" name="last_date_online_acceptance" id="last_date_online_acceptance1"  value="{{$last_date_online_acceptance}}" data-date-format="mm/dd/yyyy hh:ii">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label class="">Last day and time to accept OFFLINE</label>
                                                <div class="input-append date form_datetime"> <input class="form-control datetimepicker" name="last_date_offline_acceptance" id="last_date_offline_acceptance1"  value="{{$last_date_offline_acceptance}}" data-date-format="mm/dd/yyyy hh:ii"></div>
                                            </div>
                                        </div>    
                                    </div>
                                </div>


                        @if($offered_program != "")
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Offered Program : </label>
                                <div class="col-12 col-md-12">

                                    <select class="form-control custom-select" disabled>
                                            <option>{{$offered_program . " - Grade ".$submission->next_grade}}</option>
                                    </select>
                                </div>
                            </div>
                        @endif


                        <div class="form-group row" id="status_comment" style="display: none;">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="status_comment" id="status_comment_box" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-between d-none" style="display: none !important">
                            <label for="" class="control-label">Override Student : </label>
                            <div class=""><input id="chk_99" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="override_student" {{$district->override_student=='Y'?'checked':''}}/></div>
                        </div>




                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">Download</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">Student Application Data Sheet : </label>
                            <div class="col-3 col-md-3 text-right"><a href="{{url('/admin/GenerateApplicationData/generate/individual/'.$submission->id)}}"><i class="fa fa-download  text-success"></i></a>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Additional Questions -->

                @if($submission->additional_questions != '')
                    @php $data_submission = json_decode($submission->additional_questions) @endphp

                        <div class="card shadow">
                            <div class="card-header">Additional Questions</div>
                            <div class="card-body">
                                @foreach($data_submission as $dk=>$dv)
                                   <div class="form-group row">
                                        <label class="control-label col-8 col-md-8">{{$dk}} : </label>
                                        <div class="col-4 col-md-4 text-right">{{$dv}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                @endif

                <!-- Priority Tab -->
                <div class="card shadow">
                    <div class="card-header">Exceptionality Information</div>
                    <div class="card-body">
                        
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">SP Exception : </label>
                            <div class="col-12 col-md-12">
                               <input class="form-control" name="sp_exception" id="sp_exception"  value="{{$submission->sp_exception}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">504 Eligible : </label>
                            <div class="col-12 col-md-12">
                               <input class="form-control" name="is_504_eligible" id="is_504_eligible"  value="{{$submission->is_504_eligible}}">
                            </div>
                        </div>
                        
                    </div>
                </div>

                @if(!empty($manual_email))
                            <div class="form-group text-right">
                                 <a href="{{url('/')}}/admin/Submissions/general/send/offer/email/{{$manual_email->process_type}}/{{$submission->id}}/preview" class="btn btn-success mr-10" title="Submit">Preview Offer Email</a>
                            </div>
                        
                        @endif

                        @if(!empty($offer_data) && $offer_data->offer_slug != "")
                            <div class="card shadow">
                                <div class="card-header">Offered</div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="control-label col-9 col-md-9">Offered Link for HCS Admin [Offline] : </label>
                                        <div class="col-3 col-md-3 text-right"><a href="{{url('/admin/Offers/'.$offer_data->offer_slug)}}" target="_blank"><i class="fa fa-link text-success"></i></a>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-5 col-md-5">Offered / Contract Link for Parents [Online] : </label>
                                        <div class="col-7 col-md-7 text-right"><a href="{{url('/Offers/'.$offer_data->offer_slug)}}" target="_blank">{{url('/Offers/'.$offer_data->offer_slug)}}</a>
                                        </div>
                                    </div>

                                   

      
                                    
                                </div>
                            </div>
                       
                        @endif



                @if($submission->mcp_employee == "Yes") 
                <div class="card shadow">
                    <div class="card-header">HCS Employee Status</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">HCS Employee Verification Status : </label>
                            <div class="col-3 col-md-3 text-right">
                                @if($submission->mcpss_verification_status == "V")
                                    <span class="text-success">YES</span>
                                @else
                                    <span class="text-danger">NO</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">HCS Magnet Program Employee Status : </label>
                            <div class="col-3 col-md-3 text-right">
                                @if($submission->magnet_program_employee == "Y")
                                    <span class="text-success">YES</span>
                                @else
                                    <span class="text-danger">NO</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @php $grade_docs = getGradeUploadDocs($submission->id, 'grade') @endphp

                @if(count($grade_docs) > 0)
                 <div class="card shadow">
                                                <div class="card-header">Grade Upload Data</div>
                                                <div class="card-body">
                                                    @if(count($grade_docs) > 0)
                                                        <div class="form-group row">
                                                            <label class="control-label col-9 col-md-9">Grade Upload : </label>
                                                            <div class="col-3 col-md-3 text-right">
                                                                @foreach($grade_docs as $key=>$grade)
                                                                    <div>
                                                                        <a href="{{url('/resources/gradefiles/'.$grade->file_name)}}" title="" class="" style="color: #0000FF; text-decoration: underline;" target="_blank">{{$grade->file_name}}</a>
                                                                    </div>
                                                                @endforeach

                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                </div>
                                            </div>
                                             @endif

            </div>
        </div>
        <div class="text-right"> 
            {{-- <button class="btn btn-success">    
                <i class="fa fa-save"></i> Save
            </button> --}}
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                        <a class="btn btn-danger btn-xs" href="{{url('/admin/Submissions')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>