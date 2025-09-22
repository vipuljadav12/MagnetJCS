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
    </style>
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Add Program</div>
            <div class=""><a href="{{url('admin/Program')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    <form action="{{url('admin/Program/store')}}" method="post" class="">
        {{csrf_field()}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a></li>
            <li class="nav-item"><a class="nav-link" id="eligibility-tab" data-toggle="tab" href="#eligibility" role="tab" aria-controls="eligibility" aria-selected="false">Eligibility</a></li>
            <!--<li class="nav-item"><a class="nav-link" id="config-tab" data-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="false">Configurations</a></li>-->
            <li class="nav-item"><a class="nav-link" id="process-tab" data-toggle="tab" href="#process" role="tab" aria-controls="process" aria-selected="false">Selection</a></li>
            <!--<li class="nav-item"><a class="nav-link" id="recommendation-tab" data-toggle="tab" href="#recommendation" role="tab" aria-controls="recommendation" aria-selected="true">Add Recommendation</a></li>-->
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
                                                {{--<option value="HCS Basic Magnet">HCS Basic Magnet</option>
                                                <option value="HCS College Academy">HCS College Academy</option>
                                                <option value="HCS AGT">HCS AGT</option>
                                                <option value="TCS Specialty Form">TCS Specialty Form</option>--}}
                                                <option value="MCPSS Magnet Form" {{old('parent_submission_form')=='MCPSS Magnet Form'?'selected':''}}>MCPSS Magnet Form</option>
                                                <option value="MPS Magnet Form" {{old('parent_submission_form')=='MPS Magnet Form'?'selected':''}}>MPS Magnet Form </option>
                                            </select>
                                        </div>
                                        @if($errors->first('parent_submission_form'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('parent_submission_form')}}
                                            </div>
                                        @endif
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
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="priority[]" value="none" class="custom-control-input" id="table28" {{is_array(old('priority')) && in_array('none', old('priority'))?'checked':''}}>
                                                    <label for="table28" class="custom-control-label">None</label></div>
                                            </div>
                                            <div class="mr-20 w-90">
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
                                            </div>
                                        </div>
                                        @if($errors->first('priority'))
                                            <div class="mb-1 text-danger">
                                                {{ $errors->first('priority')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right hidden-xs float-right">
                            <button type="submit" class="btn btn-warning btn-xs"><i class="fa fa-save"></i> Save </button>
                            <button type="submit" class="btn btn-success btn-xs" name="save_edit" value="save_edit"><i class="fa fa-save" ></i> Save &amp; Edit</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="eligibility" role="tabpanel" aria-labelledby="eligibility-tab">
                @include("Program::Template.eligibility_create")
            </div>
            <div class="tab-pane fade" id="process" role="tabpanel" aria-labelledby="process-tab">
                @include("Program::Template.selection_create")
            </div>
           
        </div>

        @forelse($eligibilities as $key=>$eligibility)

            @include("Program::Template.eligibility_modal_interview")
            @if($eligibility['name']=='Recommendation Form111')
                <div class="modal fade" id="modal_4" tabindex="-1" role="dialog" aria-labelledby="modal_4Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-title" id="modal_4Label">Edit Eligibility - Recommendation Form 1</div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                            </div>
                            <div class="modal-body">
                                <div class="card shadow mb-20 d-none">
                                    <div class="card-header">Used in Determination Method</div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap">
                                            <div class="d-flex mb-10 mr-30">
                                                <div class="mr-10">Basic Method Only Active : </div>
                                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                            </div>
                                            <div class="d-flex mb-10 mr-30">
                                                <div class="mr-10">Combined Scoring Active : </div>
                                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                                            </div>
                                            <div class="d-flex mb-10">
                                                <div class="mr-10">Final Scoring Active : </div>
                                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="form-group">
                                                <label class="control-label">Select Prior Developed Recommendation Form: : </label>
                                                <div class="">
                                                    <select class="form-control custom-select" name="eligibility_grade_lavel[{{$eligibility['id']}}][]">
                                                        <option value="HCS STEM Teacher Recommendation">HCS STEM Teacher Recommendation</option>
                                                        <option value="HCS Principal Recommendation">HCS Principal Recommendation</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @empty
        @endforelse

       
    </form>
@endsection
@section('scripts')
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script type="text/javascript">
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
//                changeIconInterViewScore();
//                changeIconAcademic();
//               changeIconAudition();
//                changeIconCommittee();
//                changeIconConduct();
//                changeIcongrade();
//                changeIconRecform();
//                changeIconSpecial();
//                changeIconStandardized();
//                changeIconWriting();
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

            //interview Score Icon
            //changeIconInterViewScore();
            $('#interview_score_deter_meth,#interview_score_eligi_name,#interview_score_weight').on('change input',function () {
            //    changeIconInterViewScore();
            });
            function changeIconInterViewScore() {
              /*  if(($('#interview_score_deter_meth').val()!='' && $('#interview_score_eligi_name').val()!='' && $('#interview_score_weight').val()!='')||($('#combined_scoring').prop('checked')!=true && $('#interview_score_deter_meth').val()!='' && $('#interview_score_eligi_name').val()!='') ){
                    $('#interview_score_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#interview_score_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#interview_score_deter_meth').val()!='' || $('#interview_score_eligi_name').val()!='' || $('#interview_score_weight').val()!='')
                {
                    $('#interview_score_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#interview_score_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#interview_score_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#interview_score_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }*/
            }
            //grade Icon
            changeIcongrade();
            $('#grade_deter_meth,#grade_eligi_name,#grade_weight').on('change input',function () {
                changeIcongrade();
            });
            function changeIcongrade() {
                if(($('#grade_deter_meth').val()!='' && $('#grade_eligi_name').val()!='' && $('#grade_weight').val()!='')||($('#combined_scoring').prop('checked')!=true && $('#grade_deter_meth').val()!='' && $('#grade_eligi_name').val()!='')  ){
                    $('#grade_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#grade_img').parent().find($("input[name='eligibility_define[]']")).val('right');

                }
                else if($('#grade_deter_meth').val()!='' || $('#grade_eligi_name').val()!='' || $('#grade_weight').val()!='')
                {
                    $('#grade_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#grade_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#grade_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#grade_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Academic Grade Calculation Icon
            changeIconAcademic()
            $('#acadmic_deter_meth,#acadmic_eligi_name,#acadmic_weight').on('change input',function () {
                changeIconAcademic();
            });
            function changeIconAcademic() {
                if(($('#acadmic_deter_meth').val()!='' && $('#acadmic_eligi_name').val()!='' && $('#acadmic_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#acadmic_deter_meth').val()!='' && $('#acadmic_eligi_name').val()!='')){
                    $('#acadmic_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#acadmic_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#acadmic_deter_meth').val()!='' || $('#acadmic_eligi_name').val()!='' || $('#acadmic_weight').val()!='')
                {
                    $('#acadmic_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#acadmic_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#acadmic_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#acadmic_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Recommendation Form Icon
            changeIconRecform()
            $('#recform_deter_meth,#recform_eligi_name,#recform_weight').on('change input',function () {
                changeIconRecform();
            });
            function changeIconRecform() {
                if(($('#recform_deter_meth').val()!='' && $('#recform_eligi_name').val()!='' && $('#recform_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#recform_deter_meth').val()!='' && $('#recform_eligi_name').val()!='')){
                    $('#recform_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#recform_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#recform_deter_meth').val()!='' || $('#recform_eligi_name').val()!='' || $('#recform_weight').val()!='')
                {
                    $('#recform_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#recform_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#recform_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#recform_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //writing prompt Icon
            changeIconWriting();
            $('#writing_deter_meth,#writing_eligi_name,#writing_weight').on('change input',function () {
                changeIconWriting();
            });
            function changeIconWriting() {
                if(($('#writing_deter_meth').val()!='' && $('#writing_eligi_name').val()!='' && $('#writing_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#writing_deter_meth').val()!='' && $('#writing_eligi_name').val()!='')){
                    $('#writing_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#writing_img').parent().find($("input[name='eligibility_define[]']")).val('right');

                }
                else if($('#writing_deter_meth').val()!='' || $('#writing_eligi_name').val()!='' || $('#writing_weight').val()!='')
                {
                    $('#writing_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#writing_img').parent().find($("input[name='eligibility_define[]']")).val('alert');

                }
                else{
                    $('#writing_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#writing_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Audition Icon
            changeIconAudition();
            $('#audition_deter_meth,#audition_eligi_name,#audition_weight').on('change input',function () {
                changeIconAudition();
            });
            function changeIconAudition() {
                if(($('#audition_deter_meth').val()!='' && $('#audition_eligi_name').val()!='' && $('#audition_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#audition_deter_meth').val()!='' && $('#audition_eligi_name').val()!='')){
                    $('#audition_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#audition_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#audition_deter_meth').val()!='' || $('#audition_eligi_name').val()!='' || $('#audition_weight').val()!='')
                {
                    $('#audition_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#audition_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#audition_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#audition_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Committee Score Icon
            changeIconCommittee();
            $('#comm_deter_meth,#comm_eligi_name,#comm_weight').on('change input',function () {
                changeIconCommittee();
            });
            function changeIconCommittee() {
                if(($('#comm_deter_meth').val()!='' && $('#comm_eligi_name').val()!='' && $('#comm_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#comm_deter_meth').val()!='' && $('#comm_eligi_name').val()!='')){
                    $('#comm_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#comm_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#comm_deter_meth').val()!='' || $('#comm_eligi_name').val()!='' || $('#comm_weight').val()!='')
                {
                    $('#comm_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#comm_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#comm_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#comm_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Conduct Icon
            changeIconConduct();
            $('#conduct_deter_meth,#conduct_eligi_name,#conduct_weight').on('change input',function () {
                changeIconConduct();
            });
            function changeIconConduct() {
                if(($('#conduct_deter_meth').val()!='' && $('#conduct_eligi_name').val()!='' && $('#conduct_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#conduct_deter_meth').val()!='' && $('#conduct_eligi_name').val()!='')){
                    $('#conduct_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#conduct_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#conduct_deter_meth').val()!='' || $('#conduct_eligi_name').val()!='' || $('#conduct_weight').val()!='')
                {
                    $('#conduct_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#conduct_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#conduct_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#conduct_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }
            //Conduct Icon
            changeIconSpecial();
            $('#special_deter_meth,#special_eligi_name,#special_weight').on('change input',function () {
                changeIconSpecial();
            });
            function changeIconSpecial() {
                if(($('#special_deter_meth').val()!='' && $('#special_eligi_name').val()!='' && $('#special_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#special_deter_meth').val()!='' && $('#special_eligi_name').val()!='')){
                    $('#special_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#special_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#special_deter_meth').val()!='' || $('#special_eligi_name').val()!='' || $('#special_weight').val()!='')
                {
                    $('#special_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#special_img').parent().find($("input[name='eligibility_define[]']")).val('alert');

                }
                else{
                    $('#special_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#special_img').parent().find($("input[name='eligibility_define[]']")).val('close');

                }
            }
            //Standardized Icon
            changeIconStandardized();
            $('#standard_deter_meth,#standard_eligi_name,#standard_weight').on('change input',function () {
                changeIconStandardized();
            });
            function changeIconStandardized() {
                if(($('#standard_deter_meth').val()!='' && $('#standard_eligi_name').val()!='' && $('#standard_weight').val()!='' )||($('#combined_scoring').prop('checked')!=true && $('#standard_deter_meth').val()!='' && $('#standard_eligi_name').val()!='')){
                    $('#standard_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'right.png'}}").attr('alt','Assignment Complate').parent().find('.tooltiptext').text('Assignment Complate');
                    $('#standard_img').parent().find($("input[name='eligibility_define[]']")).val('right');
                }
                else if($('#standard_deter_meth').val()!='' || $('#standard_eligi_name').val()!='' || $('#standard_weight').val()!='')
                {
                    $('#standard_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'alert.png'}}").attr('alt','Awaiting Assignment').parent().find('.tooltiptext').text('Awaiting Assignment');
                    $('#standard_img').parent().find($("input[name='eligibility_define[]']")).val('alert');
                }
                else{
                    $('#standard_img').attr('src',"{{url('resources/assets/admin/images/').'/'.'close.png'}}").attr('alt','Not Applicable').parent().find('.tooltiptext').text('Not Applicable');
                    $('#standard_img').parent().find($("input[name='eligibility_define[]']")).val('close');
                }
            }

            //
            // gradeLavel($('#table25:checked,#table06:checked,#table07:checked,#table08:checked,#table09:checked,#table10:checked,#table11:checked,#table12:checked,#table13:checked,#table14:checked,#table15:checked,#table16:checked,#table17:checked,#table18:checked'));
            gradeLavel($('#table25:checked'));
            gradeLavel($('#table06:checked'));
            gradeLavel($('#table07:checked'));
            gradeLavel($('#table08:checked'));
            gradeLavel($('#table09:checked'));
            gradeLavel($('#table10:checked'));
            gradeLavel($('#table11:checked'));
            gradeLavel($('#table12:checked'));
            gradeLavel($('#table13:checked'));
            gradeLavel($('#table14:checked'));
            gradeLavel($('#table15:checked'));
            gradeLavel($('#table16:checked'));
            gradeLavel($('#table17:checked'));
            gradeLavel($('#table18:checked'));
            $('#table25,#table06,#table07,#table08,#table09,#table10,#table11,#table12,#table13,#table14,#table15,#table16,#table17,#table18').change(function () {
                gradeLavel(this);
            });
            function gradeLavel(check) {
                if($(check).prop('checked')==true)
                {
                    $("."+$(check).val()).each(function () {
                        $(this).removeClass('d-none');
                    });
                }
                else{
                    $("."+$(check).val()).each(function () {
                        $(this).addClass('d-none');
                    });
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
