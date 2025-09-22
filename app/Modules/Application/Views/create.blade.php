@extends('layouts.admin.app')
@section('title')
	Add Application Dates | {{config('APP_NAME',env("APP_NAME"))}}
@endsection

@section('content')
<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">
            Add Application Dates
        </div>
        <div class="">
            <a href="{{ url('admin/Application') }}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a>
            {{-- <a href="{{ url('admin/Application/trash') }}" class="btn btn-sm btn-danger" title="Trash">Trash</a> --}}
        </div>
    </div>
</div>
<form action="{{ url('admin/Application/store')}}" method="post" name="add_application">
    {{csrf_field()}}
    <ul class="nav nav-tabs" id="myTab2" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="active-screen-tab" data-toggle="tab" href="#active-screen" role="tab" aria-controls="active-screen" aria-selected="true">Add Application Dates</a></li>
        <li class="nav-item"><a class="nav-link" id="active1-screen-tab" data-toggle="tab" href="#active1-screen" role="tab" aria-controls="active1-screen" aria-selected="true">Active Screen</a></li>
        <li class="nav-item"><a class="nav-link" id="active-email-tab" data-toggle="tab" href="#active-email" role="tab" aria-controls="active-email" aria-selected="false">Active Email</a></li>
        <li class="nav-item"><a class="nav-link" id="active1-email-tab" data-toggle="tab" href="#active1-email" role="tab" aria-controls="active1-email" aria-selected="false">Pending Screen</a></li>
        <li class="nav-item"><a class="nav-link" id="active2-email-tab" data-toggle="tab" href="#active2-email" role="tab" aria-controls="active2-email" aria-selected="false">Pending Email</a></li>
        <li class="nav-item"><a class="nav-link" id="cdi-grade-upload-tab" data-toggle="tab" href="#cdi-grade-upload" role="tab" aria-controls="cdi-grade-upload" aria-selected="false">Grade Upload Screen</a></li>
        <li class="nav-item"><a class="nav-link" id="cdi-grade-confirm-tab" data-toggle="tab" href="#cdi-grade-confirm" role="tab" aria-controls="cdi-grade-confirm" aria-selected="false">Grade Upload - Confirmation Screen</a></li>
    </ul>
    <div class="tab-content bordered" id="myTab2Content">
        <div class="tab-pane fade show active" id="active-screen" role="tabpanel" aria-labelledby="active-screen-tab">
            <div class="">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="form-group">
                            <label for="">Application Name</label>
                            <div class=""><input type="text" class="form-control" name="application_name" value="">
                            </div>
                            @if($errors->first('application_name'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('application_name')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Parent Submission Form</label>
                            <div class="">
                                <select class="form-control custom-select" name="form_id">
                                    <option value="">Select</option>
                                    @forelse($forms as $key=>$form)
                                        <option value="{{$form->id}}" {{old('form_id')==$form->id?'selected':''}}>{{$form->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            @if($errors->first('form_id'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('form_id')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Open Enrollment</label>
                            <div class="">
                                <select class="form-control custom-select" name="enrollment_id" id="enrollment_id">
                                    <option value="">Select</option>
                                    @forelse($enrollments as $key=>$enrollment)
                                        <option value="{{$enrollment->id}}" {{old('enrollment_id')==$enrollment->id?'selected':''}}>{{$enrollment->school_year}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            @if($errors->first('enrollment_id'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('enrollment_id')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Starting Date [For Parent]</label>
                            <div class="input-append date form_datetime">
                                <input class="form-control datetimepicker" name="starting_date" id="starting_date"  value="{{old('starting_date')}}" disabled=""  data-date-format="mm/dd/yyyy hh:ii" {{-- readonly="readonly" --}}>

                            </div>
                            @if($errors->first('starting_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('starting_date')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Ending Date [For Parent]</label>
                            <div class="">
                                <input class="form-control datetimepicker" name="ending_date" id="ending_date" value="{{old('ending_date')}}" disabled=""  data-date-format="mm/dd/yyyy hh:ii">
                            </div>
                            @if($errors->first('ending_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('ending_date')}}
                                </div>
                            @endif
                        </div>
                    </div>

                     <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Starting Date [For Admin]</label>
                            <div class="input-append date form_datetime">
                                <input class="form-control datetimepicker" name="admin_starting_date" id="admin_starting_date"  value="{{old('admin_starting_date')}}" disabled=""  data-date-format="mm/dd/yyyy hh:ii" {{-- readonly="readonly" --}}>

                            </div>
                            @if($errors->first('admin_starting_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('admin_starting_date')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Ending Date [For Admin]</label>
                            <div class="">
                                <input class="form-control datetimepicker" name="admin_ending_date" id="admin_ending_date" value="{{old('admin_ending_date')}}" disabled=""  data-date-format="mm/dd/yyyy hh:ii">
                            </div>
                            @if($errors->first('admin_ending_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('admin_ending_date')}}
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Recommendation Due Date</label>
                            <div class="">
                                <input class="form-control datetimepicker" name="recommendation_due_date" id="recommendation_due_date" disabled value="{{old('recommendation_due_date')}}"  data-date-format="mm/dd/yyyy hh:ii">
                            </div>
                            @if($errors->first('recommendation_due_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('recommendation_due_date')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Writing Prompt Due Date</label>
                            <div class="">
                                <input class="form-control datetimepicker" name="writing_prompt_due_date" id="writing_prompt_due_date" disabled value="{{old('writing_prompt_due_date')}}"  data-date-format="mm/dd/yyyy hh:ii">
                            </div>
                            @if($errors->first('writing_prompt_due_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('writing_prompt_due_date')}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Transcript Due Date</label>
                            <div class="">
                                <input class="form-control datetimepicker" name="transcript_due_date" id="transcript_due_date" disabled value="{{old('transcript_due_date')}}"  data-date-format="mm/dd/yyyy hh:ii">
                            </div>
                            @if($errors->first('transcript_due_date'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('transcript_due_date')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Magnet URL</label>
                            <div class="">
                                <input class="form-control" name="magnet_url" value="{{$application_url}}">
                            </div>
                        </div>
                    </div>
                     <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Application Logo</label>
                            <div class="">
                                <select class="form-control custom-select" name="district_logo" id="district_logo">
                                    <option value="district_logo">District Logo</option>
                                    <option value="magnet_program_logo">Magnet Program Logo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Submission Type</label>
                            <div class="">
                                <select class="form-control custom-select" name="submission_type" id="submission_type">
                                    <option value="Regular" selected>Regular Submission</option>
                                    <option value="Late">Late Submission</option>
                                </select>
                            </div>
                            @if($errors->first('submission_type'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('submission_type')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Recommendation Email Sent to Parent ?</label>
                            <div class="">
                                <select class="form-control custom-select" name="recommendation_email_to_parent" id="recommendation_email_to_parent">
                                    <option value="Yes" selected>Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            @if($errors->first('recommendation_email_to_parent'))
                                <div class="mb-1 text-danger">
                                    {{ $errors->first('recommendation_email_to_parent')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Application Valid For</label>
                            <div class="">
                                <select class="form-control custom-select" name="valid_for">
                                        <option value="current" {{old('valid_for')=='current' ? 'selected':''}}>Only Current</option>
                                        <option value="non-current" {{old('valid_for')=='not-current' ? 'selected':''}}>Only Not Current</option>
                                        <option value="both" {{!in_array(old('valid_for'), ['current','non-current']) ? 'selected':''}}>Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header">Available Programs</div>
                        <div class="card-body">
                            <div class="form-group">
                                @forelse($temp_programs as $key=>$program)
                                    @forelse($program['grade_info'] as $key=>$grade)
                                        <div class="">
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" id="{{$grade['id']}}{{$program['id']}}" name="program_grade_id[]" class="custom-control-input" value="{{$program['id']}},{{$grade['id']}}" {{-- {{is_array(old('program_grade_id')) && in_array($program['id'].",".$grade['id'], old('program_grade_id'))?'checked':''}} --}} checked>
                                                <label class="custom-control-label" for="{{$grade['id']}}{{$program['id']}}">{{$program['name']}} - {{$grade['name']}}</label>
                                            </div>
                                        </div>
                                    @empty
                                    @endforelse
                                @empty
                                @endforelse
                                @if($errors->first('program_grade_id'))
                                    <div class="mb-1 text-danger">
                                        {{ $errors->first('program_grade_id')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="tab-pane fade" id="active1-screen" role="tabpanel" aria-labelledby="active1-screen-tab">
            <div class="form-group">
                <label>Active Screen Title : </label>
                <div class="editor-height-210">
                    <input type="text" class="form-control" class="form-control" name="active_screen_title" value="Your Magnet Program application has been successfully submitted!">
                </div>
            </div>
            <div class="form-group">
                <label>Active Screen Subject : </label>
                <div class="editor-height-210">
                    <input type="text" class="form-control" class="form-control" name="active_screen_subject" value="Your Confirmation Number# <br> {confirm_number}">
                </div>
            </div>
            <div class="form-group">
                <label>Active Screen : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="editor00" name="active_screen">
                        <p>Please print this page and/or record this confirmation number for your records. If you provided an email address, you will receive an email confirmation at the email address provided.</p>

                        <p>Please direct all questions about the HCS Magnet Application to the Office of Magnet Program at xxx@xxx.org or by calling 123-456-7890 between the hours of 8:30 am and 4:00 pm Monday through Friday.</p>
                    </textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="active-email" role="tabpanel" aria-labelledby="active-email-tab">
            <div class="form-group">
                <label>Email Subject : </label>
                <div class="editor-height-210">
                    <input type="text" class="form-control" name="active_email_subject" class="form-control" name="active_email_subject" value="Your HCS Magnet Application # {confirm_number}">
                </div>
            </div>
            <div class="form-group">
                <label>Active Email : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="editor01" name="active_email">
                        <p>Dear {parent_name},</p>

                        <p>Your application confirmation number is {confirm_number} for {student_name}.</p>

                        <p>Your Application Status is <strong>Active.</strong><br />
                            <br />
                            Your Magnet Program application has been successfully submitted!</p>

                            <p>Please direct all questions about the HCS Magnet Application to the Office of Magnet Program at xxx@xxxx.org or by calling 123-456-7890 between the hours of 8:30 am and 4:00 pm Monday through Friday.</p>

                            <p>The office of Magnet Programs is located at Jefferson Co. County Schools</p>
                    </textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="active1-email" role="tabpanel" aria-labelledby="active1-email-tab">
            <div class="form-group">
                <label>Pending Screen : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="editor02" name="pending_screen">
                        <p>In order to complete the application process, you must submit a copy of your student's grades and discipline records by {transcript_due_date}. These records should be emailed to xxx@xxx.org. Please include your confirmation number in your email.</p>

                        <p>Please print this page and/or record this confirmation number for your records. If you provided an email address, you will receive an email confirmation at the email address provided. &nbsp;</p>

                        <p>Please direct all questions about the Jefferson Magnet Application to the Office of Magnet Program at xxx@xxxx.org or by calling 123-456-7890 between the hours of 8:30 am and 4:00 pm Monday through Friday.</p>

                        <p></p>
                    </textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="active2-email" role="tabpanel" aria-labelledby="active2-email-tab">
            <div class="form-group">
                <label>Email Subject : </label>
                <div class="editor-height-210">
                    <input type="text" class="form-control" name="active_email_subject" class="form-control" name="pending_email_subject" value="Your Pending Confirmation Number# <br> {confirm_number}">
                </div>
            </div>
            <div class="form-group">
                <label>Pending Email : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="editor03" name="pending_email">
                        <p>Dear {parent_name},<br />
                        <br />
                        Your application confirmation number is {confirm_number} for {student_name}.</p>

                        <p>Your application status is <strong>Pending.</strong><br />
                            <br />
                            Your Magnet Program application has been successfully submitted!&nbsp; In order to complete the application process, you must submit a copy of your student's grades and discipline records by {transcript_due_date}. These records should be emailed to xxx@xxx.org. Please include your confirmation number in your email.</p>

                            <p>Please direct all questions about the Jefferson Magnet Application to the Office of Magnet Program at xxx@xxx.org or by calling 256-428-6987 between the hours of 8:30 am and 4:00 pm Monday through Friday.</p>

                            <p>Please do not respond to this email. This is a no reply inbox.&nbsp;</p>                        
                    </textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="cdi-grade-upload" role="tabpanel" aria-labelledby="cdi-grade-upload-tab">
            <div class="form-group">
                <label>CDI Grade Upload Screen Text : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="grade_cdi_welcome_text" name="grade_cdi_welcome_text">
                        <div class="text-center font-20 b-600 mb-10" style="text-align: center;"><span style="font-size:20px;">Grades and CDI Upload</span></div>

                        <div>
                            <div class="mb-10 text-center">
                                <div style="text-align: center;"><span style="font-size: 16px;">Grades and CDI Upload only applicable for Non-Current Students.</span></div>

                                <div style="text-align: center;"><span style="font-size:16px;">Please upload Grades &amp; CDI in PDF format.</span></div>

                                <div style="text-align: center;"><span style="font-size:16px;">PDF File up to 5 MB for Each File.</span></div>

                                <div style="text-align: center;"><span style="font-size: 16px;">If you want to upload multiple files select all then press on "Upload Button"</span></div>

                                <div style="text-align: center;"></div>

                                <div style="text-align: center;"></div>
                            </div>
                        </div>
                    </textarea>
                </div>
            </div>
        </div>


        <div class="tab-pane fade" id="cdi-grade-confirm" role="tabpanel" aria-labelledby="cdi-grade-confirm-tab">
            <div class="form-group">
                <label>CDI Grade Upload Confirm Screen Text : </label>
                <div class="editor-height-210">
                    <textarea class="form-control editor" id="grade_cdi_confirm_text" name="grade_cdi_confirm_text">
                        <main>
                            <div class="container">
                                <div class="mt-20">
                                    <div class="card aler alert-success p-20 pt-lg-50 pb-lg-150">
                                        <div class="text-center font-20 b-600 mb-10" style="text-align: center;"><span style="font-size:22px;">Grades and CDI Upload</span></div>

                                        <div class="mb-10 text-center" style="text-align: center;"><span style="font-size:16px;">Your application to the Magnet Program for {student_name}, {confirmation_no}, Grades and CDI Submitted Successfully.</span></div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </textarea>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <input type="hidden" name="submit-from" id="submit-from-btn" value="general">
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
                   <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                   <a class="btn btn-danger btn-xs" href="{{url('/admin/Application')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript">
     $( 'textarea.editor').each( function() {

        CKEDITOR.replace( $(this).attr('id'), {
        toolbar : 'Basic',
        toolbarGroups: [
                { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
                { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
            
                '/',                                                                // Line break - next group will be placed in new line.
                { name: 'links' }
            ],
            on: {
            pluginsLoaded: function() {
                var editor = this,
                    config = editor.config;
                
                editor.ui.addRichCombo( 'my-combo', {
                    label: 'Insert Short Code',
                    title: 'Insert Short Code',
                    toolbar: 'basicstyles',
            
                    panel: {               
                        css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                        multiSelect: false,
                        attributes: { 'aria-label': 'Insert Short Code' }
                    },
        
                    init: function() {   
                        var chk = []; 
                        $.ajax({
                            url:'{{url('/admin/shortCode/list')}}',
                            type:"get",
                            async: false,
                            success:function(response){
                                chk = response;
                            }
                        }) 
                        for(var i=0;i<chk.length;i++){
                            this.add( chk[i], chk[i] );
                        }
                    },
        
                    onClick: function( value ) {
                        editor.focus();
                        editor.fire( 'saveSnapshot' );
                       
                        editor.insertHtml( value );
                    
                        editor.fire( 'saveSnapshot' );
                    }
                } );        
            }        
        }
    } );

    });


</script>
<script>
    var start_date;
    var end_date;
  $('#enrollment_id').change(function(){
     setStartEndDate(this);
  });
  setStartEndDate($('#enrollment_id'));
  function setStartEndDate(select) {
    if($(select).val()!='')
    {
        $.ajax({
            type: "get",
            url: '{{url('admin/Application/start_end_date')}}',
            data: {
                id:$(select).val(),
            },
            success: function(response) {
                setStartEndDate(response.start,response.end);
                start_date=response.start;
                end_date=response.end;
                admin_start_date=response.start;
                admin_end_date=response.end;

                $("#starting_date").datetimepicker({
                    numberOfMonths: 1,
                    autoclose: true,
                    minDate: new Date(start_date),
                    maxDate: new Date(end_date),
                    dateFormat: 'mm/dd/yy hh:ii',
                    onSelect: function(selected) {
                      $("#ending_date").datetimepicker("option","minDate", selected)
                    }
                }).removeAttr('disabled');
                $("#ending_date").datetimepicker({
                    numberOfMonths: 1,
                    autoclose: true,
                    minDate: new Date(start_date),
                    maxDate: new Date(end_date),
                    dateFormat: 'mm/dd/yy hh:ii',
                    onSelect: function(selected) {
                      $("#starting_date").datetimepicker("option","maxDate", selected)
                    }
                }).removeAttr('disabled');

                $("#admin_starting_date").datetimepicker({
                    numberOfMonths: 1,
                    autoclose: true,
                    minDate: new Date(start_date),
                    maxDate: new Date(end_date),
                    dateFormat: 'mm/dd/yy hh:ii',
                    onSelect: function(selected) {
                      $("#admin_ending_date").datetimepicker("option","minDate", selected)
                    }
                }).removeAttr('disabled');
                $("#admin_ending_date").datetimepicker({
                    numberOfMonths: 1,
                    autoclose: true,
                    minDate: new Date(start_date),
                    maxDate: new Date(end_date),
                    dateFormat: 'mm/dd/yy hh:ii',
                    onSelect: function(selected) {
                      $("#admin_starting_date").datetimepicker("option","maxDate", selected)
                    }
                }).removeAttr('disabled');


                $("#recommendation_due_date,#transcript_due_date,#writing_prompt_due_date").datetimepicker({
                    numberOfMonths: 1,
                    autoclose: true,
                    minDate: new Date(start_date),
                    maxDate: new Date(end_date),
                    dateFormat: 'mm/dd/yy hh:ii',
                }).removeAttr('disabled');
            }
        });
    }
    else{
        $( ".date_picker" ).attr('disabled','disabled');
    }
  }

  $("form[name='add_application']").validate({
    rules:{
        form_id:{
            required:true,
        },
        enrollment_id:{
          required:true,  
        },
        application_name:{
          required:true,  
        },
        starting_date:{
            required:true,
            date:true,
        },
        ending_date:{
            required:true,
            date:true,
        },
        admin_starting_date:{
            required:true,
            date:true,
        },
        admin_ending_date:{
            required:true,
            date:true,
        },
       /* recommendation_due_date:{
            required:true,
            date:true,
        },*/
        transcript_due_date:{
            required:true,
            date:true,
        },
        submission_type:{
            required:true,
        },
        'program_grade_id[]':{
            required:true,
        }
    },
    messages:{
        form_id:{
            required:'The Parent submission form field is required.'
        },
        enrollment_id:{
            required:'The Open Enrollment field is required.'
        },
        starting_date:{
            required:'The Start date field is required.',
            date:'The Date formate is not valid',
        },
        ending_date:{
            required:'The Ending date field is required.',
            date:'The Date formate is not valid',
        },
        /*recommendation_due_date:{
            required:'The Recommendation due date field is required.',
            date:'The Date formate is not valid',
        },*/
        transcript_due_date:{
            required:'The Transcript due date field is required.',
            date:'The Date formate is not valid',
        },
        submission_type:{
            required:'Submission Type field is required.',
        },
        'program_grade_id[]':{
          required:'The Program is required.',
        }
    },errorPlacement: function(error, element)
    {
        error.appendTo( element.parents('.form-group'));
        error.css('color','red');
    },
    submitHandler: function (form) {
        form.submit();
    }
  });
    
  </script> 
@endsection