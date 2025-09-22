@extends('layouts.front.app')

@section('content')
    @if(!Session::has("from_admin"))
        @include("layouts.front.common.district_header")
    @endif

    <div class="mt-20">
    	@include("layouts.admin.common.alerts")

        <div class="card bg-light p-20">
            <div class="text-center font-20 b-600 mb-10">Grades and CDI Upload</div>
            <div class="">
                <div class="mb-10 text-center">
                    {!! get_district_global_setting('grade_cdi_upload_text') ?? 'test' !!}
                </div>
                <form action="{{url('/Document')}}" method="GET" id="studentDetail">
	                <div class="row justify-content-center">
	                    <div class="col-12 col-lg-3">
	                        <div class="form-group">
	                            <label for="">Submission ID : </label>
	                            <input type="text" class="form-control" id="submission_id" name="submission_id" value="{{$submission_id ?? ''}}">
	                        </div>
	                    </div>
	                    <div class="col-12 col-lg-3">
	                        <div class="form-group">
	                            <label for="">Student DOB : </label>
	                            <input type="text" class="form-control" id="stu_dob" name="stu_dob" @if($stu_dob != '')  value="{{date('m/d/Y', strtotime($stu_dob)) }}" @endif>
	                        </div>
	                    </div>
	                    <div class="col-12 col-lg-auto">
	                        <div class="form-group"><label for="">&nbsp;</label>
	                            <div>
	                            	{{-- <a href="javascript:void(0);" title="" class="btn btn-secondary btn-search ml-5 mr-5" onclick="document.getElementById('studentDetail').submit();">Search</a> --}}
	                            	<input type="submit" class="btn btn-secondary btn-search ml-5 mr-5" value="Search">
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </form>
	            <form method="POST" action="{{url('/Document/store')}}" enctype="multipart/form-data" id="docUpload">
	            	{{csrf_field()}}

	            	@if(isset($data) && $data != '')
		            	<input type="hidden" name="submission_id" value="{{$data['id']}}">
		            	<input type="hidden" name="stu_dob" value="{{$data['birthday']}}">
		                <div class="table-responsive search-result">
		                    <div class="table-responsive">
		                        <table class="table table-striped table-bordered">
		                            <thead> 
		                                <tr>
		                                    <th class="">Submission ID</th>
		                                    <th class="">Student DOB</th>
		                                    <th class="">Student Name</th>
		                                    <th class="text-center w-200">Grades</th>
		                                    <th class="text-center w-200">CDI</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                @if(isset($data) && $data != '')
		                                	<tr>
		                                		<td>{{$data['id']}}</td>
		                                		<td>{{$data['birthday']}}</td>
		                                		<td>{{getStudentName($data['student_id'])}}</td>
		                                		<td class="">
		                                            <div class="text-center">
		                                                <div class="text-center">
		                                                	<input type="file" name="grades_upload[]" id="grades_upload" multiple class="btn btn-secondary btn_upoad" >
		                                                	{{-- <a href="javascript:void(0);" title="" onclick="$('#grades_upload').click();" class="btn btn-secondary btn_upoad">
		                                                		Upload
		                                                	</a> --}}
		                                                </div>
		                                            </div>
		                                        </td>
		                                        <td class="">
		                                            <div class="text-center">
		                                                <div class="text-center">
		                                                	<input type="file" name="cdi_upload[]" id="cdi_upload" multiple class="btn btn-secondary btn_upoad" >
		                                                	{{-- <a href="javascript:void(0);" onclick="$('#cdi_upload').click();" title="" class="btn btn-secondary btn_upoad">	Upload</a> --}}
		                                            	</div>
		                                            </div>
		                                        </td>
		                                	</tr>
		                                @endif
		                            </tbody>
		                        </table>
		                    </div>

		                    <div class="text-right">
		                    	{{-- <a href="javascript:void(0);" title="" class="btn btn-success">Submit</a> --}}
		                    	<input type="submit" class="btn btn-success" value="Submit">
		                    </div>
		                </div>
		            @elseif($submission_id != '' || $stu_dob != '')
		            	<div class="">
				            <div class="card aler alert-danger p-20 pt-lg-20 pb-lg-150">
				                <div class="text-center font-20 b-600 mb-10">Grades and CDI Upload</div>
				                <div class="mb-10 text-center">No application found on given detail.</div>
				            </div>
				        </div>
		            @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
	<script type="text/javascript">
		$("#stu_dob").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });

		    $.validator.addMethod('allowEXT', function(value, element, param) {
			    var length = ( element.files.length );
			    var flag = true;
	           	if (length > 0) {
	               	for (var i = 0; i < length; i++) {
	                 	fileName = element.files[i].name; // get file size
	                   	var ext = fileName.substring(fileName.lastIndexOf('.')+1);
	                   	if(ext != param){
	                   		return false;
	                   	}
	               	}
	               	return true;
	            }
			});

		$('#docUpload').validate({
		   	rules: {
		       "grades_upload[]": {
		            required: true,
		            allowEXT: "pdf",
		        },

		        "cdi_upload[]": {
		            required: true,
		            allowEXT: "pdf",
		        }
		    },
		    messages: {
		    	"grades_upload[]":{
		    		allowEXT : "Only PDF files allowed to upload."
		    	},
		    	"cdi_upload[]":{
		    		allowEXT : "Only PDF files allowed to upload."
		    	}
		    },
		    submitHandler: function(form,event) {
	            form.submit();
	            
	        }
		});

		$('#studentDetail').validate({
		   	rules: {
		       "submission_id": {
		            required: true,
		            number:true
		        },

		        "stu_dob": {
		            required: true,
		        }
		    },
		    messages: {
		    	"submission_id":{
		    		required : "Please enter Submission ID",
		    		number : "Please enter valid Submission ID"
		    	},
		    	"stu_dob":{
		    		required : "Please enter Student DOB"
		    	}
		    },
		    submitHandler: function(form,event) {
	            form.submit();
	            
	        }
		});

	</script>
@endsection
