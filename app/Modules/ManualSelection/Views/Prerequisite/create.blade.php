@extends("layouts.admin.app")
@section('title')
	Add Prerequisite  | {{config('app.name',env("APP_NAME"))}}
@endsection
@section('styles')
<link rel="stylesheet" type="text/css" href="{{url('resources/assets/admin/css/select2.css')}}">
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add Prerequisite</div>
        <div class=""><a href="{{url('admin/ManualSelection/pre_req/')}}" class="btn btn-sm btn-primary" title=""><i class="fa fa-arrow-left"></i> Back</a></div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<form class="" id="preReqForm" action="{{url('admin/ManualSelection/pre_req/store')}}" method="post" >
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label for="" class="control-label">Programs : </label>
                <div class="">
                    <select class="form-control custom-select" name="program_id">
                        <option value="">Select Program</option>
                        @forelse($programs as $r=>$program)
                            <option value="{{$program->id}}" @if($program->id == old('program_id')) selected @endif>{{($program->name)}}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                @if($errors->has("program_id"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('program_id')}}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="" class="control-label">Grade : </label>
                <div class="">
                    <select class="form-control custom-select" name="grade">
                        <option value="0" selected>All Grade</option>
                        @forelse($grades as $r=>$grade)
                            <option value="{{$grade->next_grade}}" @if($grade->next_grade == old('grade')) selected @endif>{{($grade->next_grade)}}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                @if($errors->has("grade"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('grade')}}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="" class="control-label">Course Name : </label>
                <div class="">
                    @php
                        $course_arr = old('course_name') ?? [];
                    @endphp
                    <select class="w-100 custom-sel2" multiple="" name="course_name[]">
                        @forelse($courses as $k=>$course)
                            <option value="{{$course}}" @if(in_array($course, $course_arr)) selected @endif>{{($course)}}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                @if($errors->has("course_name"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('course_name')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
                   <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                   <a class="btn btn-danger btn-xs" href="{{url('/admin/ManualSelection/pre_req')}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('resources/assets/admin/js/select2.js')}}"></script>
<script type="text/javascript">
    $(".custom-sel2").select2();
    $("#preReqForm").validate({
        ignore:[],
        rules:{
            program_id:{
                required:true,
            },
            grade:{
                required:true,
            },
            'course_name[]':{
                required:true
            }
        },
        messages:{
            program_id:{
                required: 'Program is required.',
            },
            grade:{
                required: 'Grade is required.',
            },
            'course_name[]':{
                required: 'Course(s) is required.',
            },
        },errorPlacement: function(error, element)
        {
            error.appendTo( element.parents('.form-group'));
            error.css('color','red');
        }
    });
</script>
@endsection