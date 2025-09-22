@extends('layouts.admin.app')
@section('title')Add Program  | {{config('APP_NAME',env("APP_NAME"))}}  @endsection
@section('styles')
    <script type="text/javascript">var BASE_URL = '{{url('/')}}';</script>
    <style>
        input[type="checkbox"].styled-checkbox + label.label-xs {padding-left: 1.5rem;}
        .tooltip1 {
            position: relative;
            display: inline-block;
        }
        .tooltip1 .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 5px 0;
            border-radius: 6px;
            position: absolute;
            z-index: 1;
            margin-left: -60px;
            bottom: 115%;
            left: 50%;
        }
        .tooltip1:hover .tooltiptext {
            visibility: visible;
        }
        .tooltip1 .tooltiptext::after {
            content: " ";
            position: absolute;
            top: 100%; /* At the bottom of the tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }
        .tooltip1 .tooltiptext-btm {top: 115%; bottom: auto;}
        .tooltip1 .tooltiptext-btm::after {
            content: "";
            position: absolute;
            top: -9px;
            bottom: auto;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent black transparent;
        }
    /*.select2-container .select2-choice {border-radius: 0 !important;  height: 30px !important}
    .select2-container{width: 100% !important; height: 30px !important; border: 0 !important;}*/

    </style>
        <link rel="stylesheet" type="text/css" href="{{url('resources/assets/admin/css/select2.css')}}">
        {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css"> --}}

@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Add Program</div>
            <div class=""><a href="{{url('admin/Program')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div>
        </div>
    </div>
    <form action="{{url('admin/Program/store')}}" method="post" class="">
        {{csrf_field()}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="">
                    <div class="card shadow">
                        <div class="card-header">Program Set Up</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">Program Name : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="name" value="{{old('name')}}">
                                        </div>
                                        @if($errors->first('name'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('name')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 1 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter1" value="{{old('applicant_filter1')}}">
                                        </div>
                                        @if($errors->first('applicant_filter1'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('applicant_filter1')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 2 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter2" value="{{old('applicant_filter2')}}">
                                        </div>
                                        @if($errors->first('applicant_filter2'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('applicant_filter2')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 3 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter3" value="{{old('applicant_filter3')}}">
                                        </div>
                                        @if($errors->first('applicant_filter3'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('applicant_filter3')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>
                                        <div class="row flex-wrap">
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="PreK" class="custom-control-input" id="table25" {{is_array(old('grade_lavel')) && in_array('PreK', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table25" class="custom-control-label">PreK</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="K" class="custom-control-input" id="table06" {{is_array(old('grade_lavel')) && in_array('K', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table06" class="custom-control-label">K</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="1" class="custom-control-input" id="table07" {{is_array(old('grade_lavel')) && in_array('1', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table07" class="custom-control-label">1</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="2" class="custom-control-input" id="table08" {{is_array(old('grade_lavel')) && in_array('2', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table08" class="custom-control-label">2</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="3" class="custom-control-input" id="table09" {{is_array(old('grade_lavel')) && in_array('3', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table09" class="custom-control-label">3</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="4" class="custom-control-input" id="table10" {{is_array(old('grade_lavel')) && in_array('4', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table10" class="custom-control-label">4</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="5" class="custom-control-input" id="table11" {{is_array(old('grade_lavel')) && in_array('5', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table11" class="custom-control-label">5</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="6" class="custom-control-input" id="table12" {{is_array(old('grade_lavel')) && in_array('6', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table12" class="custom-control-label">6</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="7" class="custom-control-input" id="table13" {{is_array(old('grade_lavel')) && in_array('7', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table13" class="custom-control-label">7</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="8" class="custom-control-input" id="table14" {{is_array(old('grade_lavel')) && in_array('8', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table14" class="custom-control-label">8</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="9" class="custom-control-input" id="table15" {{is_array(old('grade_lavel')) && in_array('9', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table15" class="custom-control-label">9</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="10" class="custom-control-input" id="table16" {{is_array(old('grade_lavel')) && in_array('10', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table16" class="custom-control-label">10</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="11" class="custom-control-input" id="table17" {{is_array(old('grade_lavel')) && in_array('11', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table17" class="custom-control-label">11</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="12" class="custom-control-input" id="table18" {{is_array(old('grade_lavel')) && in_array('12', old('grade_lavel'))?'checked':''}}>
                                                    <label for="table18" class="custom-control-label">12</label></div>
                                            </div>
                                        </div>
                                        @if($errors->first('grade_lavel'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('grade_lavel')}}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><strong>Parent Submission Form :</strong> </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="parent_submission_form">
                                                <option value="">Choose an Option</option>
                                                @foreach($forms as $key=>$value)
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($errors->first('parent_submission_form'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('parent_submission_form')}}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Sibling Enabled : </label>
                                        <div class=""><input id="chk_100" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="sibling_enabled"  /></div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between d-none" id="sibling_check">
                                        <label for="" class="control-label">Sibling Program Check : </label>
                                        <div class=""><input id="chk_03" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="silbling_check" checked /></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label"><strong>Select Sibling School(s) :</strong> </label>
                                        <div class="">
                                            <select class="w-100 custom-sel2" name="sibling_schools[]" multiple="">
                                                <option value="">Choose an Option</option>
                                                @foreach($schools as $key=>$value)
                                                    @if($value->name != '')
                                                        <option value="{{$value->name}}">{{$value->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Existing Program Check : </label>
                                        <div class=""><input id="chk_03" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="existing_magnet_program_alert" checked /></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label"><strong>Select School :</strong> </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="magnet_school">
                                                <option value="">Select Magnet School</option>
                                                @foreach($schools as $key=>$value)
                                                    <option value="{{$value->name}}">{{$value->name}}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="">Select Zoned School(s) : </label>
                                        <div class="">
                                            <select class="w-100" id="zoned_schools" name="zoned_schools[]" multiple="">
                                                    @foreach($zoned_schools as $value)
                                                        <option value="{{$value['name']}}">
                                                            {{$value['name']}}
                                                        </option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="">Select Default Status : </label>
                                        <div class="">
                                            <select class="w-100 form-control custom-select" id="submission_status" name="submission_status">
                                                @php
                                                  $status = ['Pending','Active']
                                                @endphp
                                                @foreach($status as $value)
                                                    <option value="{{$value}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow">
                        <div class="card-header">Priority Set Up</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Select Priority :</strong> </label>
                                        <div class="d-flex flex-wrap">
                                            <div class="mr-20 w-90">
                                                <div class="custom-control  custom-radio"><input type="radio" name="priority[]" value="none" class="custom-control-input" id="table28" checked>
                                                    <label for="table28" class="custom-control-label">None</label></div>
                                            </div>
                                            {{-- <div class="mr-20 w-90">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="priority[]" value="1" class="custom-control-input" id="table29" {{is_array(old('priority')) && in_array('1', old('priority'))?'checked':''}}>
                                                    <label for="table29" class="custom-control-label">1</label></div>
                                            </div>
                                            <div class="mr-20 w-90">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="priority[]" value="2" class="custom-control-input" id="table30" {{is_array(old('priority')) && in_array('2', old('priority'))?'checked':''}}>
                                                    <label for="table30" class="custom-control-label">2</label></div>
                                            </div>
                                            <div class="mr-20 w-90">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="priority[]" value="3" class="custom-control-input" id="table31" {{is_array(old('priority')) && in_array('3', old('priority'))?'checked':''}}>
                                                    <label for="table31" class="custom-control-label">3</label></div>
                                            </div> --}}
                                            @forelse($priorities as $p=>$priority)
                                                <div class="mr-20 w-90">
                                                    <div class="custom-control  custom-radio">
                                                        <input type="radio" name="priority[]" value="{{$priority->id}}" class="custom-control-input" id="priority{{$p}}" {{is_array(old('priority')) && in_array($priority->id, old('priority'))?'checked':''}}>
                                                        <label for="priority{{$p}}" class="custom-control-label">{{$priority->name}}</label></div>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                        @if($errors->first('priority'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('priority')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                 <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Select School For Feeder Priorities (Select All that Apply) :</strong> </label>
                                        <div class="">
                                            <select class="w-100 custom-sel2" multiple="" name="feeder_priorities[]">
                                                <option value="">Choose an Option</option>
                                                @foreach($schools as $key=>$value)
                                                    @if($value->name != '')
                                                        <option value="{{$value->name}}">{{$value->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><strong>Select Program For Magnet Priorities (Select All that Apply) :</strong> </label>
                                        <div class="">
                                            <select class="w-100 custom-sel2" multiple="" name="magnet_priorities[]">
                                                <option value="">Choose an Option</option>
                                                @foreach($schools as $key=>$value)
                                                    @if($value->name != '')
                                                        <option value="{{$value->name}}">{{$value->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right hidden-xs float-right">
                            <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                            <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save" ></i> Save &amp; Exit</button>
                            <a class="btn btn-danger btn-xs" href="{{url('/admin/Program')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{asset('resources/assets/admin/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{url('resources/assets/admin/js/select2.js')}}"></script>
     {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script> --}}

    <script type="text/javascript">
        var select2_conf = {
            placeholder: 'Choose an Option'
        };
        $('#zoned_schools').select2(select2_conf);
        $(".custom-sel2").select2();

        $(document).on("click",".add-option-list-custome",function(){
            var i = $(this).parent().siblings(".option-list-custome").children(".form-group").length + 1;
            var a = '<div class="form-group">'+
                '<label class="control-label">Option '+i+' : </label>'+
                '<div class=""><input type="text" class="form-control" value=""></div>'+
                '</div>';
            $(this).parent().siblings(".option-list-custome").append(a);
        });
        $(".chk_6").on("change", function(){
            if($(this).is(":checked")) {
                $(".custom-field-list").show();
            }
            else {
                $(".custom-field-list").hide();
            }
        })
        $("#chk_100").on("change", function(){
            if($(this).is(":checked")) {
                $("#sibling_check").removeClass("d-none");
            }
            else {
                $("#sibling_check").addClass("d-none");
            }
        })
        $(".chk_7").on("change", function(){
            if($(this).is(":checked")) {
                $(".option-list-outer").show();
            }
            else {
                $(".option-list-outer").hide();
            }
        })
        $(document).on("click", ".add-new" , function(){
            var cc = $("#first").clone().addClass('list').removeAttr("id");
            $("#inowtable tbody").append(cc);
        });
        function del(id){
            $(id).parents(".list").remove();
        }
        //$(document).ready(function(){
        //$('#cp2').colorpicker({
        //
        //});

        $(function () {
            $('#cp2').colorpicker().on('changeColor', function (e) {
                $('#chgcolor')[0].style.backgroundColor = e.color.toHex();
            });
        });
        $("#chk_03").on("change",function(){
            if($("#chk_03").is(":checked")) {
                $("#zone").show();
            }
            else {
                $("#zone").hide();
            }
        });

        //});
    </script>
    <script>
        $(".template-select").on("change",function(){
            var a = $(this).val();
            if(a == 4){
                $(".option4").addClass("d-none");
            }
            else {
                $(".option4").removeClass("d-none");
            }
        });
        $(".template-type").on("change",function(){
            var a = $(this).val();
            if(a == 1){
                $(".template-type-1").removeClass("d-none");
                $(".template-type-2").addClass("d-none");
            }
            else if(a == 2){
                $(".template-type-1").addClass("d-none");
                $(".template-type-2").removeClass("d-none");
            }
            else {
                $(".template-type-1").addClass("d-none");
                $(".template-type-2").addClass("d-none");
            }
        });
        $(document).on("click",".first-click",function(){
            var a = $(".template-select").val();
            if(a == 1) {
                $(".interview-list").removeClass('d-none');
                $(".audition-list").addClass('d-none');
                $(".committee-list").addClass('d-none');
                $(".academic-list").addClass('d-none');
            }
            else if(a == 2) {
                $(".interview-list").addClass('d-none');
                $(".audition-list").removeClass('d-none');
                $(".committee-list").addClass('d-none');
                $(".academic-list").addClass('d-none');
            }
            else if(a == 3) {
                $(".interview-list").addClass('d-none');
                $(".audition-list").addClass('d-none');
                $(".committee-list").removeClass('d-none');
                $(".academic-list").addClass('d-none');
            }
            else if(a == 4) {
                $(".interview-list").addClass('d-none');
                $(".audition-list").addClass('d-none');
                $(".committee-list").addClass('d-none');
                $(".academic-list").removeClass('d-none');
            }
            else {
                $(".interview-list").addClass('d-none');
                $(".audition-list").addClass('d-none');
                $(".committee-list").addClass('d-none');
                $(".academic-list").addClass('d-none');
            }
        });
        function custsort() {
            $(".form-list").sortable({
                handle: ".handle"
            });
            $(".form-list").disableSelection();
        };
        function custsort1() {
            $(".question-list").sortable({
                handle: ".handle1"
            });
            $(".question-list").disableSelection();
        };
        function custsort2() {
            $(".option-list").sortable({
                handle: ".handle2"
            });
            $(".option-list").disableSelection();
        };


        $(document).on("click", ".add-ranking" , function(){
            var i = $(this).parents(".template-type-2").find(".form-group").length + 1;
            var a = '<div class="form-group">'+
                '<label class="">Numeric Ranking '+i+' : </label>'+
                '<div class=""><input type="text" class="form-control"></div>'+
                '</div>';
            var cc = $(this).parents(".template-type-2").find(".mb-20");
            $(a).insertBefore(cc);
        });
        $(document).on("click", ".add-question" , function(){
            var i = $(this).parent().parent(".card-body").find(".question-list").children(".form-group").length + 1;
            var question =  '<div class="form-group border p-15">'+
                '<label class="control-label d-flex flex-wrap justify-content-between"><span><a href="javascript:void(0);" class="mr-10 handle1" title=""><i class="fas fa-arrows-alt"></i></a>Question '+i+' : </span>'+
                '<a href="javascript:void(0);" class="btn btn-secondary btn-sm add-option" title="">Add Option</a>'+
                '</label>'+
                '<div class=""><input type="text" class="form-control" value=""></div>'+
                '<div class="option-list mt-10"></div>'+
                '</div>';
            $(this).parent().parent(".card-body").find(".question-list").append(question);
            custsort1();
        });
        $(document).on("click", ".add-header" , function(){
            var i = $(".form-list").children(".card").length + 1;
            var header =    '<div class="card shadow">'+
                '<div class="card-header">'+
                '<div class="form-group">'+
                '<label class="control-label"><a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a> Header Name '+i+': </label>'+
                '<div class=""><input type="text" class="form-control" value=""></div>'+
                '</div>'+
                '</div>'+
                '<div class="card-body">'+
                '<div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="">Add Question</a></div>'+
                '<div class="question-list p-15"></div>'+
                '</div>'+
                '</div>';
            $(this).parents(".card-body").find(".form-list").append(header);
            custsort();
        });
        $(document).on("click", ".add-option" , function(){
            var i = $(this).parent().parent(".form-group").children(".option-list").children(".form-group").length + 1;
            var option =    '<div class="form-group border p-10">'+
                '<div class="row">'+
                '<div class="col-12 col-md-7 d-flex flex-wrap align-items-center">'+
                '<a href="javascript:void(0);" class="mr-10 handle2" title=""><i class="fas fa-arrows-alt"></i></a>'+
                '<label for="" class="mr-10">Option '+i+' : </label>'+
                '<div class="flex-grow-1"><input type="text" class="form-control"></div>'+
                '</div>'+
                '<div class="col-10 col-md-5 d-flex flex-wrap align-items-center">'+
                '<label for="" class="mr-10">Point : </label>'+
                '<div class="flex-grow-1"><input type="text" class="form-control"></div>'+
                '</div>'+
                '</div>'+
                '</div>';
            $(this).parent().parent(".form-group").children(".option-list").append(option);
            custsort2();
        });
    </script>
    <script>


        ///method slection in selection tab
        $(function () {
            selectionMethod($('#table27:checked'));
            selectionMethod($('#table23:checked'));
            selectionMethod($('#table24:checked'));
        });
        $("input[name='selection_method']").click(function () {
            selectionMethod(this);
        });
        function selectionMethod(method) {
            if (($(method).attr('id') == 'table24' && $(method).is(":checked"))) {
                $('#seat_availability_enter_by').find("option[value='Manual Entry']").css('display','');
            } else if (($(method).attr('id') == 'table23' && $(method).is(":checked")) || $(method).attr('id') == 'table27' && $(method).is(":checked")) {

                $('#seat_availability_enter_by').find("option[value='Manual Entry']").css('display','none');
            }
        }
        var committee_score_id;
        var rating_priority_id;
        var lottery_number_id;
        var combine_score_id;
        var audition_score_id;
        var final_score_id;
        $(function () {
            committee_score_id= $('#committee_score').children("option:selected").text();
            rating_priority_id = $('#rating_priority').children("option:selected").text();
            lottery_number_id = $('#final_score').children("option:selected").text();
            combine_score_id = $('#lottery_number').children("option:selected").text();
            audition_score_id = $('#combine_score').children("option:selected").text();
            final_score_id = $('#audition_score').children("option:selected").text();
            $('option').each(function () {
                $(this).removeClass('d-none');
            });
            $("option[value=" + rating_priority_id + "] ,option[value=" + committee_score_id + "],option[value=" + lottery_number_id + "],option[value=" + combine_score_id + "],option[value=" + audition_score_id + "],option[value=" + final_score_id + "] ").each(function () {
                $(this).addClass('d-none');
            });
        });
        $('.ranking_system').on('change',function () {
            rakingSystem(this);
        });
        function rakingSystem(attr)
        {
            if($(attr).attr('id')=='committee_score') {
                committee_score_id = $("#committee_score option:selected").text();
            }
            else if($(attr).attr('id')=='rating_priority')
            {
                rating_priority_id = $("#rating_priority option:selected").text();
            }
            else if($(attr).attr('id')=='final_score')
            {
                final_score_id = $("#final_score option:selected").text();
            }
            else if($(attr).attr('id')=='lottery_number')
            {
                lottery_number_id = $("#lottery_number option:selected").text();
            }
            else if($(attr).attr('id')=='combine_score')
            {
                combine_score_id = $("#combine_score option:selected").text();
            }
            else if($(attr).attr('id')=='audition_score')
            {
                audition_score_id = $("#audition_score option:selected").text();
            }
            $('option').each(function () {
                $(this).removeClass('d-none');
            });
            $("option[value=" + rating_priority_id + "] ,option[value=" + committee_score_id + "],option[value=" + lottery_number_id + "],option[value=" + combine_score_id + "],option[value=" + audition_score_id + "],option[value=" + final_score_id + "] ").each(function () {
                $(this).addClass('d-none');
            });
        }
        //eligibility tab
        $(function () {
            disableCombinedScoring($('#combined_scoring'));
            $('#combined_scoring').on('change',function () {
                if ($('#basic_method_only').prop('checked')!=true)
                {
                    $('#basic_method_only').trigger('click');
                }
                disableCombinedScoring(this);
            });
            function disableCombinedScoring(checkbox) {
                if ($(checkbox).prop('checked')!=true)
                {
                    $("input[name='weight[]']").attr('disabled','disabled').val('');
                    $("select[name='determination_method[]']").each(function () {
                        $(this).find("option[value='Combined']").addClass('d-none').prop("selected", false);
                    });
                    $("#combined_eligibility").find("option[value='Weighted Scores']").addClass('d-none').prop("selected", false);
                }
                else{
                    $('.determination_method').each(function () {
                        if ($(this).val() != 'Basic') {
                            $(this).parent().parent().find('.weight').removeAttr('disabled');
                        }
                    });
                   /* $("input[name='weight[]']").removeAttr('disabled');*/
                    $("select[name='determination_method[]']").each(function () {
                        $(this).find("option[value='Combined']").removeClass('d-none');
                    });
                    $("#combined_eligibility").find("option[value='Weighted Scores']").removeClass('d-none');
                }
            }

            disableBasicMethod($('#basic_method_only'));
            $('#basic_method_only').on('change',function () {
                if ($('#combined_scoring').prop('checked')!=true)
                {
                    $('#combined_scoring').trigger('click');
                }
                disableBasicMethod(this);
            });
            function disableBasicMethod(checkbox) {
                if ($(checkbox).prop('checked')!=true)
                {
                    $("select[name='determination_method[]']").each(function () {
                        $(this).find("option[value='Basic']").addClass('d-none').prop("selected", false);
                    });
                    $("#combined_eligibility").find("option[value='Weighted Scores']").addClass('d-none').prop("selected", false);
                }
                else{
                    $("select[name='determination_method[]']").each(function () {
                        $(this).find("option[value='Basic']").removeClass('d-none');
                    });
                    $("#combined_eligibility").find("option[value='Weighted Scores']").removeClass('d-none');
                }
            }

           
        });
        $('.determination_method').each(function () {
            disableWeight1($(this).children("option:selected"));
        });
        $('.determination_method').change(function () {
            if ($(this).val() == 'Basic') {
                $(this).parent().parent().find('.weight').attr('disabled', 'disabled');
            }
            else{
                $(this).parent().parent().find('.weight').removeAttr('disabled');
            }
        });
        function disableWeight1(select) {
            if ($(select).val() == 'Basic') {
                // alert($(select).val())
                $(select).parent().parent().parent().find('.weight').attr('disabled', 'disabled');
            }
            else{
                $(select).parent().parent().parent().find('.weight').removeAttr('disabled');
            }
        }
    </script>
    <script src="{{asset('resources/assets/admin/js/program_eligibility.js')}}"></script>

@endsection
