@extends('layouts.admin.app')

@section('title') Export Submissions Data | {{config('APP_NAME',env("APP_NAME"))}} @endsection

@section('styles')
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
    .dt-buttons {position: absolute !important; padding-top: 5px !important;}

    .select2-container {
        width: 100%;
    }
    /*.select2-container .select2-choice {border-radius: 0 !important;  height: 30px !important}
    .select2-container{width: 100% !important; height: 30px !important; border: 0 !important; padding-left:  0!important}*/

</style>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css">
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Export Submissions Data</div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form class="{{url('/Reports/export/submissions')}}" method="post" id="exportSubmission">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="">Enrollment Year: </label>
                    <div class="">
                        <select class="form-control custom-select enrollment_id" name="enrollment_id" id="enrollment_id">
                            <!--<option value="">Select Enrollment Year</option>-->
                            @foreach($enrollment as $key=>$value)
                                @if($value->id == Session::get("enrollment_id"))
                                    <option value="{{$value->id}}">{{$value->school_year}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="form-group">
                    <label for="">Frist Choice Program(s) : </label>
                    <div class="">
                        <select class="custom-sel2" id="programs" name="programs1[]" multiple="">
                            <option value="">Select Program(s)</option>
                            @foreach($prgArray1 as $key=>$value)
                                <option value="{{$value}}">{{getProgramName($value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Second Choice Program(s) : </label>
                    <div class="">
                        <select class="custom-sel2" id="programs" name="programs2[]" multiple="">
                            <option value="">Select Program(s)</option>
                            @foreach($prgArray2 as $key=>$value)
                                <option value="{{$value}}">{{getProgramName($value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Third Choice Program(s) : </label>
                    <div class="">
                        <select class="custom-sel2" id="programs" name="programs3[]" multiple="">
                            <option value="">Select Program(s)</option>
                            @foreach($prgArray3 as $key=>$value)
                                <option value="{{$value}}">{{getProgramName($value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                <div class="form-group">
                    <label for="">Program(s) : </label>
                    <div class="">
                        <select class="custom-sel2" id="programs" name="programs[]" multiple="">
                            <option value="">Select Program(s)</option>
                            @foreach($prgArray as $key=>$value)
                                <option value="{{$value}}">{{getProgramName($value)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Submissions Type : </label>
                    <div class="">
                        <select class="form-control custom-select" name="late_submission">
                            <option value="A">All</option>
                            <option value="N">Regular Submissions</option>
                            <option value="Y">Late Submissions</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Status : </label>
                    <div class="">
                        <select class="form-control custom-select" name="submission_status">
                            <option value="0">All</option>
                            @foreach($submission_status as $key=>$value)
                                <option value="{{$value->submission_status}}">{{$value->submission_status}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Fields to Export : </label>
                    <div class="">
                        <div class="row flex-wrap program_grade">
                            @php
                                $fields_ary = config('variables.submission_export_fields') ?? [];
                            @endphp
                            @foreach ($fields_ary as $f_title => $f_key)
                                @php
                                    $key = str_replace(' ', '', $f_title);
                                @endphp
                                @if (in_array($f_title, $export_fields))
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input fields" name="fields[{{$f_title}}]" value="{{$f_key}}" id="{{$key}}">
                                            <label class="custom-control-label" for="{{$key}}">{{$f_title}}</label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class=""><input type="submit" class="btn btn-success generate_report" value="Export Data"></div>
            </form>
        </div>
    </div>


    
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 2 minutes to finish. </div></div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>
     
    <script type="text/javascript">
        $(".custom-sel2").select2();

        $(function (){

            $('#exportSubmission').submit(function() {

                if($('.enrollment_id').val() == ''){
                    alert('Select Enrollment year');
                    return false;
                }

                if($('#programs').val() == ''){
                    alert('Select Program(s)');
                    return false;
                }

                if($(".fields:checkbox:checked").length <= 0)
                {
                    alert("Select at least one Field to export");
                    return false;
                }
                return true;
            });
        });
    </script>

@endsection