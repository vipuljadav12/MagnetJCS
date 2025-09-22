@extends('layouts.admin.app')
@section('title')Edit Zone Address | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')

<style type="text/css">
    .loader
    {
        background: url("{{url('/resources/assets/front/images/loader.gif')}}");
        background-repeat: no-repeat;
        background-position: right;
    }
</style>

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Zone Address</div>
            <div class=""><a href="{{url('admin/ZonedSchool')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    <form action="{{url('admin/ZonedSchool/update',$zonedschool->id)}}" method="post" name="edit_district" enctype= "multipart/form-data">
        {{csrf_field()}}
        
        <div class="raw">
            <div class="card shadow">
                <div class="card-body">
                    <div class="form-group">
                        <label class="control-label">Bldg No : </label>
                        <div class=""><input type="text" class="form-control" name="bldg_num" value="{{$zonedschool->bldg_num}}"></div>
                        @if($errors->first('bldg_num'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('bldg_num')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Street Address : </label>
                        <div class=""><input type="text" class="form-control" name="street_name" value="{{$zonedschool->street_name}}"></div>
                        @if($errors->first('street_name'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('street_name')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Street Type : </label>
                        <div class=""><input type="text" class="form-control" name="street_type" value="{{$zonedschool->street_type}}"></div>
                        @if($errors->first('street_type'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('street_type')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">City : </label>
                        <div class=""><input type="text" class="form-control" name="city" value="{{$zonedschool->city}}"></div>
                        @if($errors->first('city'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('city')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Zip : </label>
                        <div class=""><input type="text" class="form-control" name="zip" value="{{$zonedschool->zip}}"></div>
                        @if($errors->first('zip'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('zip')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Elementary School : </label>
                        <div class=""><input type="text" class="form-control" name="elementary_school" value="{{$zonedschool->elementary_school}}"></div>
                        @if($errors->first('elementary_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('elementary_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Intermediate School : </label>
                        <div class=""><input type="text" class="form-control" name="intermediate_school" value="{{$zonedschool->intermediate_school}}"></div>
                        @if($errors->first('intermediate_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('intermediate_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Middle School : </label>
                        <div class=""><input type="text" class="form-control" name="middle_school" value="{{$zonedschool->middle_school}}"></div>
                        @if($errors->first('middle_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('middle_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">High School : </label>
                        <div class=""><input type="text" class="form-control" name="high_school" value="{{$zonedschool->high_school}}"></div>
                        @if($errors->first('high_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('high_school')}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <button type="Submit" class="btn btn-warning btn-xs submit"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/ZonedSchool')}}"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
@endsection