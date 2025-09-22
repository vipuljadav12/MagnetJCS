@extends('layouts.admin.app')
@section('title')Edit Eligibility @endsection
@section('content')
<style type="text/css">
    .error {color:red;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Eligibility</div>
            <div class=""><a href="{{$module_url}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
     <form action="{{$module_url}}/update/{{$eligibility->id}}" method="POST" id="eligibility-edit" name="eligibility-edit">
        {{csrf_field()}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0)
                <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Template 1</a></li>
            @else
                <li class="nav-item"><a class="nav-link active" id="recommendation-tab" data-toggle="tab" href="#recommendation" role="tab" aria-controls="recommendation" aria-selected="true">Template 2</a></li>

            @endif

           

        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include("layouts.admin.common.alerts")
            @if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0)
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="">
                        <div class="">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Select Eligibility Template : </label>
                                        <div class="">
                                            <select class="form-control custom-select template-select" name="template" disabled="">
                                                <option value="">Select Option</option>
                                                    @forelse($eligibilityTemplates as $key=>$eligibilityTemplate)
                                                        <option value="{{$eligibilityTemplate->id}}" @if(isset($eligibility->template_id) && $eligibility->template_id == $eligibilityTemplate->id)  selected @endif>{{$eligibilityTemplate->name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div id="optionContent">
                                        {{-- {{dd($eligibilityTemplates[$eligibility->template_id]->content_html)}} --}}
                                        @if(isset($eligibilityTemplates[$eligibility->template_id]->content_html))
                                            @include("Eligibility::templates.".$eligibilityTemplates[$eligibility->template_id]->content_html,[$eligibilityContent,$eligibility])
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Template Alias</label>
                                        <div class="">
                                            <input type="text" class="form-control" value="{{$eligibility->alias_name ?? old('alias_name')}}" name="alias_name">
                                           
                                        </div>
                                    </div>
                                   
                                    <div class="form-group d-flex justify-content-between pt-5 @if($eligibility['template_id'] != "3" && $eligibility['template_id'] != "8") d-none @endif" id="override">
                                        
                                        <div class="d-flex flex-wrap"><label class="control-label pr-10">Override Enabed ?</label>&nbsp;
                                           <input id="chk_acd" type="checkbox" name="override" class="js-switch js-switch-1 js-switch-xs grade_override" data-size="Small"  {{$eligibility->override=='Y'?'checked':''}}/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Store for : </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="store_for">
                                                <option value="">Select Option</option>
                                                <option value="DO" {{isset($eligibility->store_for) && $eligibility->store_for=='DO'?'selected':''}}>District Only</option>
                                                <option value="MS" {{isset($eligibility->store_for) && $eligibility->store_for=='MS'?'selected':''}}>MyPick System</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm first-click" title="">Submit</a></div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="interview-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Inerview Score</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="audition-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Audition</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="committee-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Committee Score</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="academic-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Academic Grade Calculation</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            @else
                {{-- @php 
                    if(isset($eligibilityContent))
                    {
                        // $allow_spreadsheet = json_decode($eligibilityContent->content)->allow_spreadsheet ?? null;
                        $mainContent = json_decode($eligibilityContent->content);
                    }
                @endphp
                <div class="tab-pane fade show active" id="recommendation" role="tabpanel" aria-labelledby="recommendation-tab">
                    <div class="">
                        <div class="card shadow">
                            <div class="card-header">Recommendation Form</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label">Eligibility Name : </label>
                                    <div class="">
                                        <input type="text" class="form-control" name="name" value="{{$eligibility->name ?? old('name')}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Select Teachers to receive Recommendation Form (Select all that apply) : </label>
                                    <div class="">
                                        <div class="d-flex flex-wrap">
                                            @php 
                                                // $subjects = array("eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");

                                                $subjects = array("eng"=>"English Teacher","math"=>"Math Teacher","sci"=>"Science Teacher","ss"=>"Social Studies Teacher","school_con"=>"School Counselor", "homeroom"=>"Homeroom Teacher", "principal"=>"Principal", "gift"=>"Gifted Teacher");
                                            @endphp
                                            @foreach($subjects as $s=>$subject)
                                                <div class="mr-20">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="checkbox{{$s}}" @if(isset($mainContent->subjects) && in_array($s, $mainContent->subjects)) checked @endif name="extra[subjects][]" value="{{$s}}">  
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
                                            <option value="1" @if(isset($mainContent->calc_score) && $mainContent->calc_score == 1) selected  @endif>Sum Scores</option>
                                            <option value="2" @if(isset($mainContent->calc_score) && $mainContent->calc_score == 2) selected  @endif>Average Scores</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Store for : </label>
                                    <div class="">
                                        <select class="form-control custom-select" name="store_for">
                                            <option >Select Option</option>
                                            <option value="DO" {{isset($eligibility->store_for) && $eligibility->store_for=='DO'?'selected':''}}>District Only</option>
                                            <option value="MS" {{isset($eligibility->store_for) && $eligibility->store_for=='MS'?'selected':''}}>MyPick System</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                <div class="form-list">
                                    @if(isset($mainContent->header))
                                        @foreach($mainContent->header as $h=>$header)
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <div class="form-group">
                                                    <label class="control-label">
                                                        <a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a>
                                                        Header Name {{$h}}: 
                                                     </label>
                                                    <div class="">
                                                        <input type="text" class="form-control headerInput" name="extra[header][{{$h}}][name]" value="{{$header->name}}" id="header_{{$h}}">
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="" data-header="{{$h}}">Add Question</a></div>
                                                    <div class="question-list p-15">
                                                        @forelse($header->questions as $q=>$question)
                                                            <div class="form-group border p-15">
                                                                <label class="control-label d-flex flex-wrap justify-content-between">
                                                                    <span><a href="javascript:void(0);" class="mr-10 handle1" title=""><i class="fas fa-arrows-alt"></i></a>Question {{$q}} : </span>
                                                                    <a href="javascript:void(0);" class="btn btn-secondary btn-sm add-option" title="" data-header="{{$h}}" data-question="{{$q}}" >Add Option</a>
                                                                </label>
                                                                <div class="">
                                                                    <input type="text" class="form-control" value="{{$question->name ?? ""}}" name="extra[header][{{$h}}][questions][{{$q}}][name]" >
                                                                </div>
                                                                <div class="option-list mt-10">
                                                                    @php
                                                                        $options = $question->options ?? null;
                                                                    @endphp
                                                                    @if(isset($options))
                                                                        @forelse($options as $o=>$option)
                                                                            <div class="form-group border p-10">
                                                                                <div class="row">
                                                                                    <div class="col-12 col-md-7 d-flex flex-wrap align-items-center">
                                                                                        <a href="javascript:void(0);" class="mr-10 handle2" title=""><i class="fas fa-arrows-alt"></i></a>
                                                                                        <label for="" class="mr-10">Option {{$o}} : </label>
                                                                                        <div class="flex-grow-1"><input type="text" class="form-control" name="extra[header][{{$h}}][questions][{{$q}}][options][{{$o}}]" value="{{$option ?? ""}}"></div>
                                                                                    </div>
                                                                                    <div class="col-10 col-md-5 d-flex flex-wrap align-items-center">
                                                                                        <label for="" class="mr-10">Point : </label>
                                                                                        <div class="flex-grow-1"><input type="text" class="form-control" name="extra[header][{{$h}}][questions][{{$q}}][points][{{$o}}]" value="{{$question->points->$o ?? ""}}"></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                        @endforelse
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @empty
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            @endif
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    @php
                        if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0)
                            $template_type = "general";
                        else
                            $template_type = "recommendation";
                    @endphp
                    <input type="hidden" name="submit-from" id="submit-from-btn" value="{{$template_type}}">
                    <button type="submit" class="btn btn-warning btn-xs" value="save" name="submit"><i class="fa fa-save"></i> Save </button>
                   <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/Eligibility')}}"><i class="fa fa-times"></i> Cancel</a>
                   {{-- <a class="btn btn-danger btn-xs" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a> --}}
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    {{-- <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript">
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
                '<div class=""><input type="text" class="form-control" name="extra[eligibility_type][NR][]"></div>'+
                '</div>';
            var cc = $(this).parents(".template-type-2").find(".mb-20");
            $(a).insertBefore(cc);
        });
    </script> --}}
    <script type="text/javascript">
        var nameUnique = true;
         $(document).on("blur", "input[name='name']", function() {
             $.ajax({    //create an ajax request 
                type: 'POST',
                url: "{{url('admin/Eligibility/checkEligiblityName')}}", 
                dataType: "json",
                data:{
                    "_token": "{{csrf_token()}}",
                    "name": encodeURIComponent($(this).val()),
                    "id": {{$eligibility->id}}
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


        $('#eligibility-edit').validate({
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
