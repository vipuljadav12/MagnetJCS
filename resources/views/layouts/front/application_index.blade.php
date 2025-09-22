@extends('layouts.front.app')
@section('title')
    <title>{{config('variables.page_title')}}</title>
@endsection

@section('content')
    @if(!Session::has("from_admin"))
         @include("layouts.front.common.district_header") 
    @endif

    @if(isset($applications) && !empty($applications))
        <div class="box-0 text-center">
            <div class="form-group text-center p-20 border mt-20 mb-20">
                <div class="card shadow">
                    <div class="card-body">
                        <p class="text-center"><strong>Please select a form:</strong></p>
                        @foreach($applications as $key=>$application)
                            <div class="col-sm-12 p-10 m-10">
                            <a href="{{url('/application/'.$application->id)}}" class="btn btn-secondary submit-btn p-20 b-600">{{$application->application_name}}</a>
                        </div>
                        @endforeach
                          <div class="col-sm-12 p-10 m-10">
                            <a href="{{url('/returning-student')}}" class="btn btn-primary submit-btn p-20 b-600">{{ getconfig()['returning_form_button_title']  ?? "Returning Student"}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          
    @else
        <div class="box-0 text-center p-20 border mt-20 mb-20">
            <div class="form-group">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="b-600 font-14 mb-10 text-danger">No Application is open for submission.</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
