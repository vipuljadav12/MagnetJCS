@extends('layouts.admin.app')
@section('title')
	Edit Submission
@endsection
@section('content')
	<div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Submission</div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('admin/Submissions')}}">Back</a></div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="grades0-tab" data-toggle="tab" href="#grades0" role="tab" aria-controls="grades0" aria-selected="false">Submission</a></li>
            <li class="nav-item"><a class="nav-link" id="grades-tab" data-toggle="tab" href="#grades" role="tab" aria-controls="grades" aria-selected="false">Grades</a></li>
            <li class="nav-item"><a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false">Committee Review</a></li>
            <li class="nav-item"><a class="nav-link" id="audition-tab" data-toggle="tab" href="#audition" role="tab" aria-controls="audition" aria-selected="false">Audition</a></li>
            <li class="nav-item"><a class="nav-link" id="recommendations-tab" data-toggle="tab" href="#recommendations" role="tab" aria-controls="recommendations" aria-selected="false">Recommendations</a></li>
            <li class="nav-item"><a class="nav-link" id="writing-sample-tab" data-toggle="tab" href="#writing-sample" role="tab" aria-controls="writing-sample" aria-selected="false">Writing Sample</a></li>
            <li class="nav-item"><a class="nav-link" id="profile-screen-tab" data-toggle="tab" href="#profile-screen" role="tab" aria-controls="profile-screen" aria-selected="false">Profile Screening</a></li>
            <li class="nav-item"><a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments" role="tab" aria-controls="comments" aria-selected="false">Comments</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <form method="post" action="{{ url('admin/Submissions/update',$submission->id) }}">    
                {{csrf_field()}}                    
                <div class="tab-pane fade show active" id="grades0" role="tabpanel" aria-labelledby="grades0-tab">
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
                                                    <input type="text" class="form-control" name="state_id" value="{{$submission->lottery_number}}">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">State ID : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="state_id" value="{{$submission->state_id}}">
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
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control mydatepicker" name="birthday" value="{{$submission->birthday}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Race <span class="required">*</span> : </label>
                                            <div class="col-12 col-md-12">
                                                @php $race_arr = array("American Indian/Alaskan Native","Asian","Black/African American","Multi Race - Two or More Races","Native Hawaiian or Other Pacific Islander","White","Choose Not to Answer"); @endphp
                                                <select class="form-control" name="race">
                                                    @foreach($race_arr as $key=>$value)
                                                        <option value="{{$value}}" @if($submission->race == $value) selected="selected" @endif>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Gender <span class="required">*</span> : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="gender">
                                                    <option value="">Select</option>
                                                    <option value="Male" @if($submission->gender=='Male') selected @endif>Male</option>
                                                    <option value="Female" @if($submission->gender=='Female') selected @endif>Female</option>
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
                                                <input type="text" class="form-control" name="state" value="{{$submission->state}}" maxlength="2">
                                                <div class="small">Format: AL, TN. Max 2 Characters</div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">ZIP <span class="required">*</span> : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="zip" value="{{$submission->zip}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Proof of Residency <span class="required">*</span> : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="proof_of_residency">
                                                    <option value="">Select</option>
                                                    <option value="Y" @if($submission->proof_of_residency=='Y') selected @endif>Yes</option>
                                                    <option value="N" @if($submission->proof_of_residency=='N') selected @endif>No</option>
                                                </select>
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
                                                {{-- <input type="text" class="form-control" name="current_school" value="{{$submission->current_school}}"> --}}
                                                <select class="form-control custom-select" name="current_school">
                                                    @foreach($data['schools'] as $key=>$school)
                                                        <option value="{{$school->id}}" @if($submission->current_school==$school->id) selected="" @endif> {{$school->name}}</option>
                                                    @endforeach
                                                </select>
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
                                            <label class="control-label col-12 col-md-12">Parent First Name : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="parent_first_name" value="{{$submission->parent_first_name}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Parent Last Name : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="parent_last_name" value="{{$submission->parent_last_name}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Parent Employment : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="parent_employment">
                                                    <option value="">Select</option>
                                                    <option value="">Option 01</option>
                                                    <option value="">Option 02</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Parent Employee Name : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="parent_employee_name" value="{{$submission->parent_employee_name}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Parent Employee Location : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="parent_employee_location" value="{{$submission->parent_employee_location}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Open Enrollment : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="open_enrollment">
                                                	<option value="">Select Enrollment</option>
                                                    @foreach($data['enrollments'] as $key=>$enrollment)
                                                    	<option value="{{$enrollment->id}}" @if($submission->open_enrollment==$enrollment->id) selected="selected" @endif> {{$enrollment->school_year}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{--<div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Zoned School : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="zoned_school">
                                                    <option value="">Select</option>
                                                    <option value="">Eastwood Middle School</option>
                                                    <option value="">Tuscaloose Elementary School</option>
                                                </select>
                                            </div>
                                        </div>--}}
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Late Submission : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="late_submission" value="{{$submission->late_submission}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">GPA : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" value="{{$submission->gpa}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">First Choice : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="first_choice">
                                                    <option value="0">Choose a First Choice</option>
                                                    @foreach($data['applicationPrograms'] as $key=>$applicationProgram)
                                                    	<option value="{{$applicationProgram->id}}" @if($submission->first_choice==$applicationProgram->id) selected="selected" @endif>{{$applicationProgram->program_id}} - Grade {{$applicationProgram->grade_id}}</option>
                                                    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Second Choice : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="second_choice">
                                                    <option value="0">Choose a Second Choice</option>
                                                    @foreach($data['applicationPrograms'] as $key=>$applicationProgram)
                                                    	<option value="{{$applicationProgram->id}}" @if($submission->second_choice==$applicationProgram->id) selected="selected" @endif>{{$applicationProgram->program_id}} - Grade {{$applicationProgram->grade_id}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Third Choice : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="third_choice">
                                                    <option value="">Choose a Third Choice</option>
                                                    @foreach($data['applicationPrograms'] as $key=>$applicationProgram)
                                                    	<option value="{{$applicationProgram->id}}" @if($submission->third_choice==$applicationProgram->id) selected @endif>{{$applicationProgram->program_id}} - Grade {{$applicationProgram->grade_id}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Grade Average : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="grade_avg" value="{{$submission->grade_avg}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Zoned School : </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" name="zoned_school" value="{{$submission->zoned_school}}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Special Accommodations : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="special_accommodations">
                                                    <option value="Y" @if($submission->special_accommodations=='Y') selected="" @endif>Yes</option>
                                                    <option value="N" @if($submission->special_accommodations=='N') selected="" @endif>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Gifted Status : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="gifted_status">
                                                    <option value="">Select Status</option>
                                                    <option value="" selected>Parent Identified as Gifted</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">Submission Status <span class="required">*</span> : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control custom-select" name="submission_status">
                                                    <option value="">Select Status</option>
                                                    <option value="Pending" @if($submission->submission_status=='Pending') selected="" @endif>On Hold For Additional Information</option>
                                                    <option value="Approved" @if($submission->submission_status=='Approved') selected="" @endif>Approved</option>
                                                    <option value="Disapproved" @if($submission->submission_status=='Disapproved') selected="" @endif>Disapproved</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="tab-pane fade" id="grades" role="tabpanel" aria-labelledby="grades-tab">
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
                                    <th class="align-middle">Class Name</th>
                                    <th class="align-middle">Grade</th>
                                    <th class="align-middle">Advanced Course Bonus</th>
                                    <th class="align-middle">Used to Calculate GPA</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="">
                                        <input type="checkbox" class="chkbox-style form-check-input styled-checkbox" id="table00" name="selectCheck" />
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
                                    <td class=""><input type="text" class="form-control form-control-sm" value="96"></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                </tr>
                                <tr>
                                    <td class="">
                                        <input type="checkbox" class="chkbox-style form-check-input styled-checkbox" id="table01" name="selectCheck" />
                                        <label for="table01" class="label-xs check-secondary"></label>
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
                                            <option selected>Science</option>
                                        </select>
                                    </td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value="Science"></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value="Science 4"></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value="96"></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                    <td class=""><input type="text" class="form-control form-control-sm" value=""></td>
                                </tr>
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
            </div>
            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                <div class="card shadow">
                    <div class="card-header">Committee Review Score</div>
                    <div class="card-body">
                        <div class="">
                            <div class="form-group row mb-0">
                                <label class="control-label col-12 col-md-12">Academy for Gifted and Talented - Grade 6 - Committee Score </label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Choose an Option</option>
                                        <option value="">0</option>
                                        <option value="">1</option>
                                        <option value="">2</option>
                                        <option value="">3</option>
                                        <option value="">4</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="audition" role="tabpanel" aria-labelledby="audition-tab">
                <div class="card shadow">
                    <div class="card-header">Single Audition</div>
                    <div class="card-body">
                        <div class="">
                            <div class="form-group row mb-0">
                                <label class="control-label col-12 col-md-12">Audition</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Choose an Option</option>
                                        <option value="">Exceptional</option>
                                        <option value="">Ready</option>
                                        <option value="">Not Ready</option>
                                        <option value="">Not Show</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="recommendations" role="tabpanel" aria-labelledby="recommendations-tab">
                <div class="card shadow">
                    <div class="card-header">English Teacher Recommendation Form</div>
                    <div class="card-body">
                        <div class="">

                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Teacher Name</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Teacher Email</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="">
                                    <div class="small">changes to this field will only affect new messages that go out.</div>
                                    <div class=""><a href="javascript:void(0);" class="btn btn-sm btn-primary" title=""><i class="far fa-paper-plane"></i> Resend Confirmation</a></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Average Score</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Comments</label>
                                <div class="col-12 col-md-12">
                                    <textarea class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="writing-sample" role="tabpanel" aria-labelledby="writing-sample-tab">
                <div class="card shadow">
                    <div class="card-header">Writing Prompt</div>
                    <div class="card-body">
                        <div class="">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Writing Prompt</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="In the College Academy students are required to complete rigorous high school and college courses. Students will work with a diverse group of peers who are interested in a wide variety">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Writing Sample</label>
                                <div class="col-12 col-md-12">
                                    <textarea class="form-control" rows="4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae optio possimus repudiandae, quis, voluptate consectetur, laudantium dignissimos incidunt fuga neque dolores at odit perferendis. Itaque voluptatum magnam iusto eum ipsa.</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Student Email</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="johndeo@mail.com">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Student LInk</label>
                                <div class="col-12 col-md-12">
                                    URL
                                </div>
                            </div>
                            <div class=""><a href="javascript:void(0);" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Writing Sample</a><a href="javascript:void(0);" class="btn btn-sm btn-primary" title=""><i class="far fa-paper-plane"></i> Resend Email</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="profile-screen" role="tabpanel" aria-labelledby="profile-screen-tab">
                <div class="card shadow">
                    <div class="card-header">Learner Profile Screening Device</div>
                    <div class="card-body">
                        <div class="">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Email</label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="">
                                    <div class="small">changes to this field will only affect new messages that go out.</div>
                                    <div class=""><a href="javascript:void(0);" class="btn btn-sm btn-primary" title=""><i class="far fa-paper-plane"></i> Resend Learner Profile Screening Device Email</a></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Ability in the Visual Arts</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Ability in the Performance Arts</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Leadership Qualities-Organizing-Decision Making</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Psychomotor Skills and Abilities</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Citizenship and/or Behavior</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Creative or Productive Thinking</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Use of Spatial and Abstract Thinking</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">General Intellectual Ability</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Talent Associated with Cultural Heritage</label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select">
                                        <option value="">Behavior is somewhat less frequent, etc.</option>
                                        <option value="">Behavior is typical or commonly observed in the reference group</option>
                                        <option value="">Behavior is somewhat more frequent, etc.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Form Link</label>
                                <div class="col-12 col-md-12">
                                    URL
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                <div class="card shadow">
                    <div class="card-header">Comments</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Comments : </label>
                            <div class=""><textarea class="form-control"></textarea></div>
                        </div>
                        <div class="form-group text-right">
                            <a href="javascript:void(0);" class="btn btn-secondary" title="">Submit</a>
                        </div>
                        <div class="border-top mt-30 pb-10">
                            <div class="d-flex mt-20 mb-0 card p-15 flex-row">
                                <div class="mr-20">
                                    <div class="bg-secondary text-white rounded-circle comment-short-name">MB</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-10">
                                        <div class="">Megan Brown</div>
                                        <div class="">Apr 15, 2020 10:20</div>
                                    </div>
                                    <div class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                                </div>
                            </div>
                            <div class="d-flex mt-20 mb-0 card p-15 flex-row">
                                <div class="mr-20">
                                    <div class="bg-secondary text-white rounded-circle comment-short-name">DM</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-10">
                                        <div class="">David Millar</div>
                                        <div class="">Apr 10, 2020 08:11</div>
                                    </div>
                                    <div class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quibusdam dolore vero deleniti provident necessitatibus quam pariatur corporis quae! Magni, perspiciatis possimus. Obcaecati culpa debitis soluta laudantium esse, tenetur quis consectetur!</div>
                                </div>
                            </div>
                            <div class="d-flex mt-20 mb-0 card p-15 flex-row">
                                <div class="mr-20">
                                    <div class="bg-secondary text-white rounded-circle comment-short-name">MW</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-10">
                                        <div class="">Mark Watson</div>
                                        <div class="">Apr 05, 2020 15:30</div>
                                    </div>
                                    <div class="">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae est, quos culpa nulla, repudiandae in laborum corporis minima rem id asperiores, quo! Quas fugiat in amet dolor quasi, iste sunt!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
	        <div class="row">
	            <div class="col-lg-12 text-right hidden-xs float-right">

                     <button type="Submit" class="btn btn-warning btn-xs submit"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/Submissions')}}"><i class="fa fa-times"></i> Cancel</a>
	            	<a class="btn btn-primary btn-xs" href="javascript:void(0);"><i class="far fa-file-pdf"></i> Print First Choice</a>
	            </div>
	        </div>
    	</div>
    </form>
@endsection
@section('scripts')
    <script>
        ScreenOrientation.lock;
    </script>
@endsection