@extends('layouts.admin.app')

@section('title')Add Priority @stop

@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add Priority</div>
        <div class=""><a href="{{url('admin/Priority')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
    </div>
</div>

<form id="priority-add" method="post" action="{{url('admin/Priority/store')}}">
    {{  csrf_field() }}
    <div class="form-list">
        @include("layouts.admin.common.alerts")
    </div>
    <div class="card shadow">
        <div class="card-header">
            <div class="form-group">
                <label class="control-label">Template Name : </label>
                <div class="">
                    <input type="text" name="name" value="{{old('name')}}" maxlength="30" class="form-control" value="">
                    @if ($errors->has('name'))
                        <span class="error">
                            {{ $errors->first('name') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <div class="question-list">
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="priority">
                            <thead>
                                <tr>
                                    <td class="align-middle w-10"></td>
                                    <td class="align-middle"></td>
                                    @foreach($dynamic_fields as $field=>$value)
                                        @if($district->{$field}=="Yes")
                                            <td class="align-middle text-center w-150">{{$value[0]}}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="priority-list">
                                @for($i=1; $i<=1; $i++)
                                <tr>
                                    <td class="">
                                        <a href="javascript:void(0);" class="handle2" title=""><i class="fas fa-arrows-alt"></i></a>
                                    </td>
                                    <td class="">
                                        <input type="text" class="form-control description" name="description[{{$i}}]" maxlength="50" value="Priority 1">
                                        @if ($errors->has('description.'.$i))
                                            <span class="error">
                                                <strong>{{ $errors->first('description.'.$i) }}</strong>
                                            </span>
                                        @endif
                                    </td>

                                    @foreach($dynamic_fields as $field=>$value)
                                        @if($district->{$field}=="Yes")
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="{{$value[1]}}[{{$i}}]" id="{{$value[1]}}_{{$i}}">
                                                <label for="{{$value[1]}}_{{$i}}" class="custom-control-label"></label>
                                            </div>
                                        </td>
                                        @endif
                                    @endforeach

                                </tr>
                                @endfor
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class=""><a href="javascript:void(0);" class="font-18 add-option" title=""><i class="far fa-plus-square"></i></a></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs"  form="priority-add" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
               <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                <a class="btn btn-danger btn-xs" href="{{url('/admin/Priority')}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@stop

@section('scripts')
    <script>

    $(document).on('change', 'input[type="checkbox"]', function(){
        validatePriorityCombinations();
    });

    $('button[type="submit"]').on('click', function(){
        validatePriorityCombinations();
    });

    // Validates prrmutation combination of priorities
    function validatePriorityCombinations(){
        var combinations = new Array();
        var flag = 1;
        var blank = 0;
        $('tbody>tr').each(function(){
            var c = '';
            var errorlabel = $(this).find('.error.combination');
            $(this).find('input[type="checkbox"]').each(function(){            
                c = c.concat($(this).prop('checked')==true?'1':'0');
            });
            if (c != '') {
                if(c.search(/1/i)<0){
                    // if blank entry found add error message                
                    if(errorlabel.length){
                        errorlabel.remove();
                    }
                    // Skip first blank entry
                    if(blank>0){
                        flag = 0;                    
                        $(this).find('>:nth-child(2)').append('<p class="error combination">Do not leave more than one blanks!!</p>');
                    }
                    blank++;
                } 
                else if($.inArray(c, combinations)>-1){
                    // if duplicate entry arrive add error message
                    flag = 0;
                    if(errorlabel.length){
                        errorlabel.remove();
                    }
                    $(this).find('>:nth-child(2)').append('<p class="error combination">Priority should not repeat again!!</p>');
                } 
                else{
                    // no duplicate then remove error message if present
                    if(errorlabel.length>0){
                        errorlabel.remove();
                    }
                }
            }
            combinations.push(c);
        });
        return flag;
    }

    function custsort() {
        $(".form-list").sortable({
            handle: ".handle"
        });
        $(".form-list").disableSelection();
    };
    function custsort2() {
        $(".priority-list").sortable({
            handle: ".handle2"
        });
        $(".priority-list").disableSelection();
    };

    custsort2();
    $(document).on("click", ".add-option" , function(){
        this.newIndex = $(document).find("tbody").find("tr").length + 1;
        var self = this;
        var b = $('tbody').children('tr:last-child').clone();
        
        $(b).find("input").each(function(){
            var input = $(this).attr("name");
            var inputSplit = input.split("[");
            // var inputSplitLast = inputSplit[1].split("]");
            // var newIndex =parseInt(inputSplitLast[0])+parseInt(1); 
            var fieldName = inputSplit[0];
            var newName = fieldName+"["+self.newIndex+"]";
            
            if($(this).attr("type") == "checkbox")
            {
                $(this).prop("checked", false);
            }

            $(this).attr("name",newName);
            $(this).val("");
            $(this).attr("id",fieldName+"_"+self.newIndex);
            $(this).parent().find(".custom-control-label").attr("for",fieldName+"_"+self.newIndex);
        });
        $(b).find(".description").val('Priority '+self.newIndex);
        $(b).find("label.error").remove();
        $(b).find(".error.combination").remove();//25-6-20
        $('tbody').append(b);
        custsort2();
       // setValidationRule();
        validatePriorityCombinations();
    });
    </script>

    <script type="text/javascript">
        $.validator.addMethod("nameRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9\ \-]+$/i.test(value);
        }, "Do not use special characters");

        /*-- form validation start --*/    
            $('#priority-add').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 30,
                        nameRegex: true,
                        remote: {
                            type: 'post',
                            url: "{{url('admin/Priority/checkname')}}",
                            data:{
                                "_token": "{{csrf_token()}}"
                            }
                        }
                    }
                },
                messages: {
                    name: {
                        required: "Priority name is required.",
                        remote: "Priority name already present.",
                        maxlength: "No more than 30 characters."
                    }
                },
                submitHandler: function (form) {
                    // form.submit();
                    var validate = validatePriorityCombinations();
                    if ( validate === 1 ) {
                        form.submit();
                    }
                }
            });
        /*-- form validation end --*/

        /*-- Description field validation start --*/ 
        setValidationRule();    
        function setValidationRule()
        {
            $(document).find('input[name^="description"]').each(function(){
                var a = $(this).length;
                $(this).rules('add', {
                    required: true,
                    maxlength: 50,
                    nameRegex: true,
                    messages: {
                        required: "Description is required ",
                        maxlength: "No more than 50 characters."                    
                    }
                })
            });
        }
        /*-- Description field validation end --*/
    </script>
@stop

