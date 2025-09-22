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
.custom-select{
    margin: 5px !important;
}
.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 20px 0;
    border-radius: 4px
}

.pagination>li {
    display: inline
}

.pagination>li>a,
.pagination>li>span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.428571429;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd
}

.pagination>li:first-child>a,
.pagination>li:first-child>span {
    margin-left: 0 !important;
    border-bottom-left-radius: 4px !important;
    border-top-left-radius: 4px !important;
}

.pagination>li:last-child>a,
.pagination>li:last-child>span {
    border-top-right-radius: 4px !important;
    border-bottom-right-radius: 4px !important;
}

.pagination>li>a:hover,
.pagination>li>span:hover,
.pagination>li>a:focus,
.pagination>li>span:focus {
    background-color: #eee
}

.pagination>.active>a,
.pagination>.active>span,
.pagination>.active>a:hover,
.pagination>.active>span:hover,
.pagination>.active>a:focus,
.pagination>.active>span:focus {
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: #428bca !important;
    border-color: #428bca !important
}

.pagination>.disabled>span,
.pagination>.disabled>a,
.pagination>.disabled>a:hover,
.pagination>.disabled>a:focus {
    color: #999;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #ddd
}

.pagination-lg>li>a,
.pagination-lg>li>span {
    padding: 10px 16px;
    font-size: 18px
}

.pagination-lg>li:first-child>a,
.pagination-lg>li:first-child>span {
    border-bottom-left-radius: 6px;
    border-top-left-radius: 6px
}

.pagination-lg>li:last-child>a,
.pagination-lg>li:last-child>span {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px
}

.pagination-sm>li>a,
.pagination-sm>li>span {
    padding: 5px 10px;
    font-size: 12px
}

.pagination-sm>li:first-child>a,
.pagination-sm>li:first-child>span {
    border-bottom-left-radius: 3px;
    border-top-left-radius: 3px
}

.pagination-sm>li:last-child>a,
.pagination-sm>li:last-child>span {
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px
}

.pager {
    padding-left: 0;
    margin: 20px 0;
    text-align: center;
    list-style: none
}

.pager:before,
.pager:after {
    display: table;
    content: " "
}

.pager:after {
    clear: both
}

.pager:before,
.pager:after {
    display: table;
    content: " "
}

.pager:after {
    clear: both
}

.pager li {
    display: inline
}

.pager li>a,
.pager li>span {
    display: inline-block;
    padding: 5px 14px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 15px
}

.pager li>a:hover,
.pager li>a:focus {
    text-decoration: none;
    background-color: #eee
}

.pager .next>a,
.pager .next>span {
    float: right
}

.pager .previous>a,
.pager .previous>span {
    float: left
}

.pager .disabled>a,
.pager .disabled>a:hover,
.pager .disabled>a:focus,
.pager .disabled>span {
    color: #999;
    cursor: not-allowed;
    background-color: #fff
}

.loader {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
    from {-webkit-transform:rotate(0deg);}
    to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
    from {transform:rotate(0deg);}
    to {transform:rotate(360deg);}
}

.loader::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:#3498db;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}
</style>
<link href="{{url('/resources/assets/admin/css/buttons.dataTables.min.css')}}" rel="stylesheet" />
	<div class="card shadow w-100">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">View/Edit Submissions</div>
            <div class="">
                <a href="{{url('/')}}/admin/submission" class="btn btn-sm btn-secondary" title="">Add Submission</a>
            </div>
            <div class=" d-none">
                <div class="d-inline-block position-relative">
                    <a href="javascript:void(0);" onClick="custfilter();" class="d-inline-block border pt-5 pb-5 pl-10 pr-10" title=""><span class="d-inline-block mr-10">Filter</span> <i class="fas fa-caret-down"></i></a>
                    <div class="filter-box border shadow" style="display: none;">
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline01" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline01">Submission ID</label>
                            </div>
                        </div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline001" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline001">State ID</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline02" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline02">Open Enrollment</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline03" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline03">First Name</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline04" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline04">Last Name</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline05" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline05">Race</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline06" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline06">Birthday</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline07" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline07">Current School</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline08" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline08">Current Grade</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline09" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline09">Next Grade</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline10" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline10">Status</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline11" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline11">Awarded School</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline12" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline12">First Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline13" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline13">Second Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline14" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline14">Third Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline15" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline15">Form</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline16" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline16">Student Status</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline17" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline17">Special Accommodations</label></div></div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    <div class="card shadow">
        <div class="card-body">
    	@include("layouts.admin.common.alerts")
        <div class="row col-md-12" id="submission_filters">
            
                <select class="form-control col-md-3 custom-select custom-select2 sel_race">
                    <option value="">Select Race</option>
                    @foreach($all_data['race'] as $ra)
                        @if($ra->race != "")
                            <option value="{{$ra->race}}">{{$ra->race}}</option>
                        @endif
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 curr_school">
                    <option value="">Select Current School</option>
                    @foreach($all_data['current_school'] as $curr_school)
                        <option value="{{$curr_school->current_school}}">{{$curr_school->current_school}}</option>
                    @endforeach
                    
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 curr_grade">
                    <option value="">Select Current Grade</option>
                    @foreach($all_data['current_grade'] as $curr_grade)
                        <option value="{{$curr_grade->current_grade}}">{{$curr_grade->current_grade}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 next_grade">
                    <option value="">Select Next Grade</option>
                    @foreach($all_data['next_grade'] as $next_grades)
                        <option value="{{$next_grades->next_grade}}">{{$next_grades->next_grade}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 first_choice_program">
                    <option value="">Select First Choice Program</option>
                    @foreach($all_data['first_programs'] as $first_program)
                        <option value="{{$first_program->id}}">{{$first_program->name}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 second_choice_program">
                    <option value="">Select Second Choice Program</option>
                    @foreach($all_data['second_programs'] as $second_program)
                        <option value="{{$second_program->id}}">{{$second_program->name}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 third_choice_program">
                    <option value="">Select Third Choice Program</option>
                    @foreach($all_data['third_programs'] as $third_program)
                        <option value="{{$third_program->id}}">{{$third_program->name}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 forms">
                    <option value="">Select Form</option>
                    @foreach($all_data['forms'] as $form)
                        <option value="{{$form->form_id}}">{{findFormName($form->form_id)}}</option>
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 app_status">
                    <option value="">Select Application Status</option>
                     @foreach($all_data['submission_status'] as $sub_status)
                        <option value="{{$sub_status->submission_status}}">{{$sub_status->submission_status}}</option>
                    @endforeach
                    
                </select>
                @php $blank = false @endphp
                <select class="form-control col-md-3 custom-select custom-select2 zoned_school">
                    <option value="">Select Zoned School</option>
                     @foreach($all_data['zoned_school'] as $zoned_schools)
                        @if($zoned_schools->zoned_school != '' || !$blank)
                                <option value="{{$zoned_schools->zoned_school}}">{{$zoned_schools->zoned_school}}</option>
                        @endif
                         @if($zoned_schools->zoned_school == "")
                            @php $blank = true @endphp
                        @endif
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 stu_type">
                    <option value="">Select Student Type</option>
                    <option value="current">Current</option>
                    <option value="new">New</option>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 awarded_school">
                    <option value="">Select Awarded Program</option>
                     @foreach($all_data['awarded_school'] as $awarded_programs)
                        @if($awarded_programs->name != '')
                                <option value="{{$awarded_programs->name}}">{{$awarded_programs->name}}</option>
                        @endif
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 gender">
                    <option value="">Select Gender</option>
                     @foreach($all_data['gender'] as $gender)
                        @if($gender->gender != '')
                                <option value="{{$gender->gender}}">{{$gender->gender}}</option>
                        @endif
                    @endforeach
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 contract_status">
                    <option value="">Select Contract Status</option>
                    <option value="Signed">Completed</option>
                    <option value="UnSigned">Pending</option>
                </select>
                 <select class="form-control col-md-3 custom-select custom-select2 late_submission">
                    <option value="">Select Submission Type</option>
                    <option value="">All</option>
                    <option value="N">Submissions</option>
                    <option value="Y">Late Submissions</option>
                </select>

        </div>
            <div class="loader" id="loader" style="display:none;"></div>

            <div class="pt-20 pb-20">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped mb-0 w-100">
                        <thead>
                            <tr>
                                <th class="align-middle">Submission ID</th> <!--0-->
                                <th class="align-middle">State ID</th><!--1-->
                                <th class="align-middle">Enrollment Year</th><!--2-->
                                <th class="align-middle">Student Name</th><!--3-->
                                <th class="align-middle">Parent Name</th><!--4-->
                                <th class="align-middle">Phone</th><!--5-->
                                <th class="align-middle">Address</th><!--6-->
                                <th class="align-middle">Parent Email</th><!--7-->
                                <th class="align-middle">Race</th><!--8-->
                                <th class="align-middle">Date of Birth</th><!--9-->
                                <th class="align-middle">Current School</th><!--10-->
                                <th class="align-middle">Current Grade</th><!--11-->
                                <th class="align-middle">Next Grade</th><!--12-->
                                <th class="align-middle">First Program Choice</th><!--13-->
                                <th class="align-middle">Second Program Choice</th><!--14-->
                                <th class="align-middle">Third Program Choice</th><!--15-->
                                <th class="align-middle">Submitted at</th><!--16-->
                                <th class="align-middle">Form</th><!--17-->
                                <th class="align-middle">Application Status</th><!--18-->
                                <th class="align-middle">Zoned School</th><!--19-->
                                <th class="align-middle">Student Type</th><!--20-->
                                <th class="align-middle">Confirmation No</th><!--21-->
                                <th class="align-middle">Awarded Program</th><!--22-->
                                <th class="align-middle">Late Submission</th><!--23-->
                                <th class="align-middle">Gender</th><!--24-->

                            </tr>
                        </thead>
                        <tbody>
                            <tr></tr>
                            {{--
                            @forelse($submissions as $key=>$submission)
                                <tr>
                                    <td class="text-center">
                                        @php $no = explode("-", $submission->confirmation_no) @endphp
                                        @if((checkPermission(Auth::user()->role_id,'Submissions/edit') == 1))
                                        <a href="{{ url('admin/Submissions/edit',$submission->id)}}" title="edit">
                                            {{$submission->id}}</a>
                                        <div class="">
                                            <a href="{{ url('admin/Submissions/edit',$submission->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                        </div>
                                        @else
                                            {{$submission->id}}
                                        @endif
                                    </td>
                                    <td class="">{{$submission->student_id}}</td>
                                    <td class="">{{$submission->school_year}}</td>
                                    <td class="">{{$submission->first_name}} {{$submission->last_name}}</td>
                                    <td class="">{{$submission->parent_first_name}} {{$submission->parent_last_name}}</td>
                                    <td class="">{{$submission->phone_number}}</td>
                                    <td class="">{{$submission->parent_email}}</td>
                                    <td class="">{{$submission->race}}</td>
                                    <td class="">{{getDateFormat($submission->birthday)}}</td>
                                    <td class="">{{$submission->current_school}}</td>
                                    <td class="">{{$submission->current_grade}}</td>
                                    <td class="">{{$submission->next_grade}}</td>
                                    <td>{{getProgramName($submission->first_choice_program_id)}}</td>
                                    <td>{{getProgramName($submission->second_choice_program_id)}}</td>
                                    <td class="">{{getDateTimeFormat($submission->created_at)}}</td>
                                    <td class="">{{findSubmissionForm($submission->application_id)}}</td>
                                    <td class="">
                                             @if($submission->submission_status == "Active" || $submission->submission_status == "Offered")
                                                <div class="alert1 alert-success p-10 text-center d-block">{{$submission->submission_status}}</div> 
                                            @elseif($submission->submission_status == "Application Withdrawn" || $submission->submission_status == "Denied due to Incomplete Records")
                                                <div class="alert1 alert-danger p-10 text-center d-block">{{$submission->submission_status}}</div> 
                                            @elseif($submission->submission_status == "Denied due to Eligibility")
                                                <div class="alert1 alert-info p-10 text-center d-block">{{$submission->submission_status}}</div> 
                                            @else
                                                    <div class="alert1 alert-warning p-10 text-center d-block">{{$submission->submission_status}}</div>
                                            @endif
                                    </td>
                                    <td>{{$submission->zoned_school}}</td>
                                    <td class="text-center">
                                        @if($submission->student_id != "")
                                            <div class="alert1 alert-success p-10 text-center d-block">Current</div> 
                                        @else
                                                <div class="alert1 alert-warning p-10 text-center d-block">New</div>
                                        @endif
                                    </td>
                                    <td>{{$submission->confirmation_no}}</td>
                                    <td>{{$submission->employee_id}}</td>
                                    <td>{{$submission->mcp_employee}}</td>
                                    <td>{{$submission->work_location}}</td>
                                    <td>{{$submission->employee_first_name}}</td>
                                    <td>{{$submission->employee_last_name}}</td>
                                    
                                </tr>
                            @empty
                            @endforelse --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<!--<script src="{{url('/resources/assets/admin')}}/js/bootstrap/jszip.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/pdfmake.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/vfs_fonts.js"></script>-->
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
	<script type="text/javascript">
        var enroll_yr=sel_race=curr_school=curr_grade=next_grade=app_status=zoned_school=stu_type=first_choice_program=second_choice_program=third_choice_program=form=awarded_school=contract_status=late_submission=gender="";
        var dtbl_submission_list = $("#datatable").DataTable({
            "aaSorting": [],
            'serverSide': true,
                'ajax': {
                    url: "{{url('admin/Submissions/getsubmissions')}}",
                    "data": function ( d ) {
                        d.enroll_yr = enroll_yr;
                        d.sel_race = sel_race;
                        d.curr_school = curr_school;
                        d.curr_grade = curr_grade;
                        d.next_grade = next_grade;
                        d.first_choice_program = first_choice_program;
                        d.second_choice_program = second_choice_program;
                        d.third_choice_program = third_choice_program;
                        d.app_status = app_status;
                        d.zoned_school = zoned_school;
                        d.stu_type = stu_type;
                        d.form = form;
                        d.awarded_school = awarded_school;
                        d.contract_status = contract_status;
                        d.late_submission = late_submission;
                        d.gender = gender;

                    }
                    //type: 'POST'
                },
             dom: 'Bfrtip',
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Submissions',
                        text:'Export to Excel',
                        //Columns to export
                       action: newexportaction,
                   }
                ]
            });

         // Each column dropdown filter
        /*$("#datatable thead th").each( function ( i ) {
            // Disable dropdown filter for disalble_dropdown_ary (index=0)
            var disalble_dropdown_ary = [0, 1, 3, 5, 9, 14, 15, 16, 17, 18 ,19];//13
            if ($.inArray(i, disalble_dropdown_ary) == -1) {
                var column_title = $(this).text();
                if(column_title=="Status")
                    column_title = "Application Status";
                if(column_title=="New/Current")
                    column_title = "Student Status";
                if(column_title=="Awarded School")
                    column_title = "Zoned School";
                
                var select = $('<select class="form-control col-md-3 custom-select custom-select2"><option value="">Select '+column_title+'</option></select>')
                    .appendTo( $('#submission_filters') )
                    .on( 'change', function () {
                        dtbl_submission_list.column( i )
                            .search($(this).val())
                            .draw();
                    } );
         
                dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                    str = d.replace('<div class="alert1 alert-success p-10 text-center d-block">', "");
                    str = str.replace('<div class="alert1 alert-danger p-10 text-center d-block">', "");
                    str = str.replace('<div class="alert1 alert-warning p-10 text-center d-block">', "");
                    str = str.replace('</div>', "");
                    select.append( '<option value="'+str+'">'+str+'</option>' )
                } );
            }
        } );*/
        // Hide Columns
        dtbl_submission_list.columns([2, 4, 5, 6, 7, 8, 9, 13, 14, 15, 17, 19, 21, 22, 23, 24]).visible(false);

        $(".enrollment_yr").change(function(){
            enroll_yr = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".sel_race").change(function(){
            sel_race = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".curr_school").change(function(){
            curr_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".curr_grade").change(function(){
            curr_grade = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".next_grade").change(function(){
            next_grade = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".app_status").change(function(){
            app_status = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
         $(".forms").change(function(){
            form = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".zoned_school").change(function(){
            zoned_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".stu_type").change(function(){
            stu_type = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".first_choice_program").change(function(){
            first_choice_program = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".second_choice_program").change(function(){
            second_choice_program = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".third_choice_program").change(function(){
            third_choice_program = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".awarded_school").change(function(){
            awarded_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".contract_status").change(function(){
            contract_status = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".late_submission").change(function(){
            late_submission = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".gender").change(function(){
            gender = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        
        
function newexportaction(e, dt, button, config) {
    $('#loader').show();
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function (e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
    
            $('#loader').hide();
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
    </script>

@endsection