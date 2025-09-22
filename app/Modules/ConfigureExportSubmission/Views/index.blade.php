@extends('layouts.admin.app')
@section('title')Configure Export Submission | {{config('APP_NAME',env("APP_NAME"))}} @endsection

@section('styles')
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Configure Export Submission</div>
    </div>
</div>

@php
    $fields_ary = config('variables.submission_export_fields') ?? [];
@endphp

<div class="card shadow">
    <div class="card-body">
        @include("layouts.admin.common.alerts")
        <form action="{{url($module_base)}}/update" method="post" id="exportSubmission">
            {{csrf_field()}}
            <div class="form-group">
                <label for="">Fields to Export : </label>
                <div class="">
                    <div class="row flex-wrap program_grade">
                        @foreach ($fields_ary as $f_title => $f_key)
                            @php
                                $checked = '';
                                if (in_array($f_title, $export_fields)) {
                                    $checked = 'checked';
                                }
                                $key = str_replace(' ', '', $f_title);
                            @endphp
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input fields" name="fields[]" value="{{$f_title}}" id="{{$key}}" {{$checked}}>
                                    <label class="custom-control-label" for="{{$key}}">{{$f_title}}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class=""><input type="submit" class="btn btn-success generate_report" value="Save"></div>
        </form>
    </div>
</div>

</form>
@stop

@section('scripts')
@stop

