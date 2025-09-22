@extends('layouts.admin.app')
@section('title')Add Eligibility @endsection
@section('content')
<style type="text/css">
    .error {color:red;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Add Eligibility</div>
            <div class=""><a href="{{$module_url}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    <form action="{{$module_url}}/store" method="POST" id="eligibility-add" name="eligibility-add">
        {{csrf_field()}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Template 1</a></li>
            <li class="nav-item"><a class="nav-link" id="recommendation-tab" data-toggle="tab" href="#recommendation" role="tab" aria-controls="recommendation" aria-selected="true">Template 2</a></li>
            <!--<li class="nav-item"><a class="nav-link" id="preview-tab" data-toggle="tab" href="#preview" role="tab" aria-controls="preview" aria-selected="true">Template 3</a></li>-->            
        </ul>
        {{-- {{dd('test')}} --}}
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                @if($errors->first('name'))
                    <div class="mb-1 text-danger alert alert-danger">
                        {{-- {{ $errors->first('name')}} --}}
                       Something went wrong , Please try again..
                    </div>
                @endif
                <div class="">
                    <div class="">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label">Select Eligibility Template : </label>
                                    <div class="">
                                        <select class="form-control custom-select template-select" name="template">
                                            <option value="">Select Option</option>
                                                @forelse($eligibilityTemplates as $key=>$eligibilityTemplate)
                                                    <option value="{{$eligibilityTemplate->id}}">{{$eligibilityTemplate->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="optionContent"></div>
                                 <div class="form-group">
                                    <label class="control-label">Template Alias</label>
                                    <div class="">
                                        <input type="text" class="form-control" value="{{old('alias_name')}}" name="alias_name">
                                       
                                    </div>
                                </div>  

                                <div class="form-group d-flex justify-content-between pt-5 d-none" id="override">
                                        
                                        <div class="d-flex flex-wrap"><label class="control-label pr-10">Override Enabed ?</label>&nbsp;
                                           <input id="chk_acd" type="checkbox" name="override" class="js-switch js-switch-1 js-switch-xs grade_override" data-size="Small" />
                                        </div>
                                    </div>
                                <div class="form-group">
                                    <label class="control-label">Store for : </label>
                                    <div class="">
                                        <select class="form-control custom-select" name="store_for">
                                            <option value="">Select Option</option>
                                            <option value="DO" {{old('store_for')=='DO'?'selected':''}}>District Only</option>
                                            <option value="MS" {{old('store_for')=='MS'?'selected':''}}>MyPick System</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="form-group text-right"><button href="javascript:void(0);" class="btn btn-secondary btn-sm first-click" title="">Submit</button></div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="recommendation" role="tabpanel" aria-labelledby="recommendation-tab">
                <div class="">
                    <div class="card shadow">
                        <div class="card-header">Recommendation Form</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="control-label">Eligibility Name : </label>
                                <div class="">
                                    <input type="text" class="form-control" name="name" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Select Teachers to receive Recommendation Form (Select all that apply) : </label>
                                <div class="">
                                    <div class="d-flex flex-wrap">
                                        @php 
                                            $subjects = config('variables.recommendation_subject');
                                        @endphp
                                        @foreach($subjects as $s=>$subject)
                                            <div class="mr-20">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="checkbox{{$s}}" name="extra[subjects][]" value="{{$s}}">  
                                                    <label for="checkbox{{$s}}" class="custom-control-label">{{$subject}}</label></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Select Calculation of Scores : </label>
                                <div class="">
                                    <select class="form-control custom-select" name="extra[calc_score]">
                                        <option value="">Select Option</option>
                                        <option value="1">Sum Scores</option>
                                        <option value="2">Average Scores</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">Store for : </label>
                                <div class="">
                                    <select class="form-control custom-select" name="store_for">
                                        <option value="">Select Option</option>
                                        <option value="DO">District Only</option>
                                        <option value="MS">MyPick System</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                            <div class="form-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <input type="hidden" name="submit-from" id="submit-from-btn" value="general">
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
                   <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/Eligibility')}}"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
   {{--  <script type="text/javascript">
        $(document).on("click",".nav-link",function()
        {
            let currTab = $(document).find(".tab-pane.show").attr("id");
            $(document).find("#submit-from-btn").val(currTab);
            if(currTab == "recommendation")
            {
                $(document).find("#general").find("input[type=text], textarea").val("");
                $(document).find("#general").find("select").prop('selectedIndex',0);

            }
            else
            {
                $(document).find("#recommendation").find("input[type=text], textarea").val("");
                $(document).find("#recommendation").find("select").prop('selectedIndex',0);
            }
        });
        $(document).on("change",".template-select",function()
        {
            $(document).find("#optionContent").html("");
            var template_id = $(this).val();
            $.ajax({
                url:"{{$module_url}}/getTemplateHtml/"+template_id,
                type: 'GET',  // http method
                // data: { _token: '{{csrf_token()}}' }, 
                success: function (result) {
                    // console.log(result.content_html);
                    $(document).find("#optionContent").html(result);
                },
                error: function (jqXhr, textStatus, errorMessage) {
                        $('p').append('Error' + errorMessage);
                }
            });
        });
        $(document).on("change",".template-type",function(){
            var a = $(this).val();
            if(a == "YN"){
                $(".template-type-1").removeClass("d-none");
                $(".template-type-2").addClass("d-none");
            }
            else if(a == "NR"){
                $(".template-type-1").addClass("d-none");
                $(".template-type-2").removeClass("d-none");
            }
            else {
                $(".template-type-1").addClass("d-none");
                $(".template-type-2").addClass("d-none");
            }
        });
        $(document).on("click", ".add-ranking-13" , function(){
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
            // var headerInput = $(this).parent().parent().parent((".card-header").find(".headerInput").attr("id");
            var headerId = $(this).attr("data-header");
            console.log("question id: "+i);
            console.log("Header id: "+ headerId);
            var question =  '<div class="form-group border p-15">'+
                            '<label class="control-label d-flex flex-wrap justify-content-between"><span><a href="javascript:void(0);" class="mr-10 handle1" title=""><i class="fas fa-arrows-alt"></i></a>Question '+i+' : </span>'+
                            '<a href="javascript:void(0);" class="btn btn-secondary btn-sm add-option" title="" data-header="'+headerId+'" data-question="'+i+'" >Add Option</a>'+
                            '</label>'+
                            '<div class=""><input type="text" class="form-control" value="" name="extra[header]['+headerId+'][questions]['+i+']"></div>'+
                            '<div class="option-list mt-10"></div>'+
                            '</div>';
            // console.log(question);

            $(this).parent().parent(".card-body").find(".question-list").append(question);
            custsort1();
        });
        $(document).on("click", ".add-header" , function(){
            var i = $(".form-list").children(".card").length + 1;
            var header =    '<div class="card shadow">'+
                            '<div class="card-header">'+
                            '<div class="form-group">'+
                            '<label class="control-label"><a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a> Header Name '+i+': </label>'+
                            '<div class=""><input type="text" class="form-control headerInput" name="extra[header]['+i+'][name]" id="header_'+i+'"></div>'+
                            '</div>'+
                            '</div>'+
                            '<div class="card-body">'+
                            '<div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="" data-header="'+i+'">Add Question</a></div>'+
                            '<div class="question-list p-15"></div>'+
                            '</div>'+
                            '</div>';
            console.log("Header : "+i);
            $(this).parents(".card-body").find(".form-list").append(header);
            custsort();
        });
        $(document).on("click", ".add-option" , function(){
            var i = $(this).parent().parent(".form-group").children(".option-list").children(".form-group").length + 1;
            var headerId = $(this).attr("data-header");
            var questionId = $(this).attr("data-question");

            var option =    '<div class="form-group border p-10">'+
                            '<div class="row">'+
                            '<div class="col-12 col-md-7 d-flex flex-wrap align-items-center">'+
                            '<a href="javascript:void(0);" class="mr-10 handle2" title=""><i class="fas fa-arrows-alt"></i></a>'+
                            '<label for="" class="mr-10">Option '+i+' : </label>'+
                            '<div class="flex-grow-1"><input type="text" class="form-control" name="extra[header]['+headerId+'][questions]['+questionId+'][options]['+i+']"></div>'+
                            '</div>'+
                            '<div class="col-10 col-md-5 d-flex flex-wrap align-items-center">'+
                            '<label for="" class="mr-10">Point : </label>'+
                            '<div class="flex-grow-1"><input type="text" class="form-control" name="extra[header]['+headerId+'][questions]['+questionId+'][points]['+i+']"></div>'+
                            '</div>'+
                            '</div>'+
                            '</div>';
            console.log("option : "+i);
            // console.log( $(this).parent().parent(".form-group"));
            $(this).parent().parent(".form-group").children(".option-list").append(option);
            custsort2();
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
    </script> --}}
    <script type="text/javascript">
        var nameUnique = true
        $(function()
        {
            $(document).find("#recommendation").find("input[type=text], textarea").prop("disabled", true);
            $(document).find("#recommendation").find("select").prop("disabled", true);
        });
        $(document).on("click",".nav-link",function()
        {
            let currTab = $(document).find(".tab-pane.show").attr("id");
            $(document).find("#submit-from-btn").val(currTab);
            if(currTab == "recommendation")
            {
                $(document).find("#general").find("input[type=text], textarea").val("");
                $(document).find("#general").find("select").prop('selectedIndex',0);

                $(document).find("#general").find("select").prop("disabled", true);
                $(document).find("#general").find("input[type=text], textarea").prop("disabled", true);

                $(document).find("#recommendation").find("input[type=text], textarea").prop("disabled", false);
                $(document).find("#recommendation").find("select").prop("disabled", false);
            }
            else
            {
                $(document).find("#recommendation").find("input[type=text], textarea").val("");
                $(document).find("#recommendation").find("select").prop('selectedIndex',0);

                $(document).find("#recommendation").find("input[type=text], textarea").prop("disabled", true);
                $(document).find("#recommendation").find("select").prop("disabled", true);

                $(document).find("#general").find("select").prop("disabled", false);
                $(document).find("#general").find("input[type=text], textarea").prop("disabled", false);
            }
        });

        $(document).on("blur", "input[name='name']", function() {
             $.ajax({    //create an ajax request 
                type: 'POST',
                url: "{{url('admin/Eligibility/checkEligiblityName')}}", 
                dataType: "json",
                data:{
                    "_token": "{{csrf_token()}}",
                    "name": encodeURIComponent($(this).val()),
                },
                success: function(response)
                {
                    var obj = $("input[name='name']").parent();
                    if(response==false)
                    {
                        nameUnique = false;
                        $('#name-error').remove();
                        $(obj).append('<label id="name-error" class="error" for="name">Eligibility name should be unique.')
                    }
                    else
                    {
                        nameUnique = true;
                        $('#name-error').remove();
                    }
                }

            });
         });

        jQuery.validator.addMethod("unique", 
            function(value, element) {

                    return nameUnique;
            },'Eligibility name should be unique.');


        $('#eligibility-add').validate({
                rules: {
                    name: {
                        required: true,
                        unique: true
                    }
                },
                messages: {
                    name: {
                        required: "Eligibility name is required.",
                        unique: "Eligibility name should be unique."
                    }
                }
            });
    </script>
    @include("Eligibility::js")
@endsection