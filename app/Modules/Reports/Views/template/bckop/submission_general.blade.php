<div class="">
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
                                            <input type="text" class="form-control" name="state_id" value="{{$submission->student_id}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">First Name <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="first_name" value="{{$submission->first_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Last Name <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="last_name" value="{{$submission->last_name}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Birthday <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12 row">
                                             @if($submission->birthday != '')
                                                @php $bdates = explode("-", $submission->birthday) @endphp
                                             @else
                                                @php $bdates = array(date("Y"), date("m"), date("d")) @endphp
                                             @endif

                                            <div class="col-4">
                                                @php $months = Config::get('variables.months') @endphp
                                                <select class="form-control changeDate" id="month">
                                                    @foreach($months as $key=>$value)
                                                        <option value="{{$key}}" @if($bdates[1]==$key) selected="selected" @endif>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-4">
                                                <select class="form-control changeDate" id="day">
                                                     @for($i=1; $i <= 31; $i++)
                                                        <option value="{{$i}}" @if($bdates[2]==$key) selected="selected" @endif>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control changeDate" id="year">
                                                    @for($i=2020; $i >= 1970; $i--)
                                                        <option value="{{$i}}" @if($bdates[0]==$key) selected="selected" @endif>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <input type="text" class="form-control" name="birthday" id="birthday" value="{{date('M d, Y', strtotime($submission->birthday))}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Race <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            @php $race_arr = array("Black/African American","White","American Indian/Alaskan Native","Asian","Native Hawaiian or Other Pacific Islander"); @endphp
                                            <select class="form-control" name="race">
                                                @foreach($race_arr as $key=>$value)
                                                    <option value="{{$value}}" @if($submission->race == $value) selected="selected" @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Address <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="address" value="{{$submission->address}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">City <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="city" value="{{$submission->city}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">State <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="state">
                                                <option value="">Select an Option</option> 
                                            @php $stateArray = Config::get('variables.states') @endphp

                                            @foreach($stateArray as $stkey=>$stvalue)
                                                <option value="{{$stvalue}}" @if($submission->state==$stvalue) selected @endif>{{$stvalue}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">ZIP <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="zip" value="{{$submission->zip}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Phone Number <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" maxlength="10" name="phone_number" value="{{$submission->phone_number}}">
                                            <div class="small">Format: 1234567890. Max 10 Characters</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Alternate Number <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="alternate_number" value="{{$submission->alternate_number}}" maxlength="10">
                                            <div class="small">Format: 1234567890. Max 10 Characters</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Current School <span class="required">*</span> : </label>
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
                                        <label class="control-label col-12 col-md-12">Current Grade <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="current_grade">
                                                @foreach($data['grades'] as $key=>$grade)
                                                	<option value="{{$grade->name}}" @if($submission->current_grade==$grade->name) selected="" @endif> {{$grade->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Next Grade <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="next_grade">
                                                @foreach($data['grades'] as $key=>$grade)
                                                	<option value="{{$grade->name}}" @if($submission->next_grade==$grade->name) selected="" @endif> {{$grade->name}}</option>
                                                @endforeach
                                            </select>
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
                                        <label class="control-label col-12 col-md-12">Parent's Email : </label>
                                        <div class="col-12 col-md-12">
                                            <input type="text" class="form-control" name="parent_email" value="{{$submission->parent_email}}">
                                            <div class="small">changes to this field will only affect new messages that go out.</div>
                                            <div class=""><a href="javascript:void(0);" class="btn btn-sm btn-primary" title=""><i class="far fa-paper-plane"></i> Resend Confirmation</a></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Open Enrollment : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="open_enrollment">
                                            	<option value="0">Select Enrollment</option>
                                                @foreach($data['enrollments'] as $key=>$enrollment)
                                                	<option value="{{$enrollment->id}}" @if($submission->open_enrollment==$enrollment->id) selected="selected" @endif> {{$enrollment->school_year}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                     <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">First Choice : </label>
                                        <div class="col-12 col-md-12">

                                            <select class="form-control custom-select" name="first_choice" id="first_choice">
                                                <option value="">Choose a First Choice</option>
                                                @foreach(getProgramDropdown($submission->application_id) as $key=>$applicationProgram)
                                                    @if($submission->next_grade == $applicationProgram->grade_name)
                                                    <option value="{{$applicationProgram->id}}" @if($submission->first_choice==$applicationProgram->id) selected="selected" @endif>{{$applicationProgram->program_name}} - Grade {{$applicationProgram->grade_name}}</option>
                                                    @endif
                                                
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if($submission->first_sibling != '')
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Sibling ID : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" disabled="disabled" value="{{$submission->first_sibling." ".getStudentName($submission->first_sibling)}}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Second Choice : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="second_choice" id="second_choice">
                                                <option value="">Choose a Second Choice</option>
                                                @foreach(getProgramDropdown($submission->application_id) as $key=>$applicationProgram)
                                                    @if($submission->next_grade == $applicationProgram->grade_name)
                                                    <option value="{{$applicationProgram->id}}" @if($submission->second_choice==$applicationProgram->id) selected="selected" @endif>{{$applicationProgram->program_name}} - Grade {{$applicationProgram->grade_name}}</option>
                                                    @endif
                                                
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if($submission->second_sibling != '')
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Sibling ID : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" disabled="disabled" class="form-control" value="{{$submission->second_sibling." ".getStudentName($submission->second_sibling)}}">
                                            </div>
                                        </div>
                                    @endif
                                     <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Is Parent / Guardian MCPSS employee ?  : </label>
                                            <div class="col-12 col-md-12">
                                                 <select class="form-control custom-select" name="second_choice" id="second_choice">
                                                    <option vlaue="Yes" @if($submission->mcp_employee=="Yes") selected="selected" @endif>Yes</option>
                                                    <option vlaue="No" @if($submission->mcp_employee=="No") selected="selected" @endif>No</option>

                                                 </select>
                                               
                                            </div>
                                        </div>
                                        @if($submission->employee_id != "")
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Employee ID  : </label>
                                            <div class="col-12 col-md-12">
                                                 <input type="text" class="form-control" name="employee_id" value="{{$submission->employee_id}}">
                                            </div>
                                        </div>
                                        @endif
                                        @if($submission->work_location != "")
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Work Location  : </label>
                                            <div class="col-12 col-md-12">
                                                 <input type="text" class="form-control" name="work_location" value="{{$submission->work_location}}">
                                            </div>
                                        </div>
                                        @endif
                                    

                                    <div class="form-group row">
                                        <label class="control-label col-12 col-md-12">Submission Status <span class="required">*</span> : </label>
                                        <div class="col-12 col-md-12">
                                            <select class="form-control custom-select" name="submission_status">
                                                <option value="">Select Status</option>
                                                <option value="Active">Active</option>
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
                                                <option value="Declined Wait List">Declined Wait List</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>