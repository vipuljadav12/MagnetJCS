@extends('layouts.admin.app')

@section('title')Add Priority @stop

@section('styles')
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add Priority</div>
        <div class="text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Template</a></div>
    </div>
</div>

<!-- Show Error messsages -->
{{-- @parent --}}

<form id="priority-add" method="post" action="{{url('admin/Priority/add')}}">
    {{  csrf_field() }}
    <!--<div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">Priority Name : </label>
                <div class=""><input type="text" class="form-control" value=""></div>
            </div>
            <div class="text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Template</a></div>
        </div>
    </div>-->
    <div class="form-list">
    </div>
    <div class="card shadow">
        <div class="card-header">
            <div class="form-group">
                <label class="control-label">Template Name 1 : </label>
                <div class="">
                    <input type="text" name="name" value="{{old('name')}}" maxlength="30" class="form-control" value="">
                    @if ($errors->has('name'))
                        <span class="error">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group text-right"><a href="javascript:void(0);" class="font-18 add-question" title=""><i class="far fa-plus-square"></i></a></div>
            <div class="question-list">
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="priority">
                            <thead>
                                <tr>
                                    <td class="align-middle w-10"></td>
                                    <td class="align-middle"></td>
                                    <td class="align-middle w-20">Sibling</td>
                                    @if(isset($dcs) && $dcs!="No")
                                        <td class="align-middle w-20">Majority Race in Home Zone School</td>
                                    @endif
                                    <td class="align-middle w-20">Current Enrollment at Another Magnet School</td>
                                </tr>
                            </thead>
                            <tbody class="priority-list">
                                @for($i=1; $i<=2; $i++)
                                <tr>
                                    <td class=""><a href="javascript:void(0);" class="handle2" title=""><i class="fas fa-arrows-alt"></i></a></td>
                                    <td class=""><input type="text" class="form-control" name="description[{{$i}}]" value="{{old('description.'.$i)}}">
                                        @if ($errors->has('description.'.$i))
                                            <span class="error">
                                                <strong>{{ $errors->first('description.'.$i) }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="sibling[{{$i}}]" id="{{$i+11}}"><label for="{{$i+11}}" class="custom-control-label"></label></div></td>

                                    @if(isset($dcs) && $dcs!="No")
                                        <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="majority_race_in_home_zone_school[{{$i}}]" id="{{$i+22}}"><label for="{{$i+22}}" class="custom-control-label"></label></div>
                                        </td>
                                    @endif

                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="current_enrollment_at_another_magnet_school[{{$i}}]" id="{{$i+33}}"><label for="{{$i+33}}" class="custom-control-label"></label></div>
                                    </td>

                                </tr>
                                @endfor
                                {{-- <tr>
                                    <td class=""><a href="javascript:void(0);" class="handle2" title=""><i class="fas fa-arrows-alt"></i></a></td>
                                    <td class=""><input type="text" class="form-control" name="description[1]" value="">
                                    </td>
                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="sibling[1]" id="table01"><label for="table01" class="custom-control-label"></label></div></td>
                                    @if(isset($dcs) && $dcs!="No")
                                        <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="majority_race_in_home_zone_school[1]" id="table02"><label for="table02" class="custom-control-label"></label></div></td>
                                    @endif
                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="current_enrollment_at_another_magnet_school[1]" id="table03" value="1"><label for="table03" class="custom-control-label"></label></div></td>
                                </tr>
                                <tr>
                                    <td class=""><a href="javascript:void(0);" class="handle2" title=""><i class="fas fa-arrows-alt"></i></a></td>
                                    <td class=""><input type="text" class="form-control" name="description[2]" value="2">
                                    </td>
                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" name="sibling[2]" class="custom-control-input" id="table11"><label for="table11" class="custom-control-label"></label></div></td>
                                    @if(isset($dcs) && $dcs!="No")
                                        <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="majority_race_in_home_zone_school[2]" id="table12"><label for="table12" class="custom-control-label"></label></div></td>
                                    @endif
                                    <td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="current_enrollment_at_another_magnet_school[2]" id="table13"><label for="table13" class="custom-control-label"></label></div></td>
                                </tr> --}}
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
</form>
<div class="box content-header-floating" id="listFoot">
    <div class="row">
        <div class="col-lg-12 text-right hidden-xs float-right">
            <button type="submit" form="priority-add" class="btn btn-warning btn-xs"><i class="fa fa-save"></i> Save </button> 
            {{-- <a class="btn btn-warning btn-xs"><i class="fa fa-save"></i> Save </a> --}} 
            <a class="btn btn-success btn-xs" href="priority-master.html"><i class="fa fa-save"></i> Save &amp; Exit</a> 
            <a class="btn btn-danger btn-xs" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a> 
        </div>
    </div>
</div>
@stop

@section('scripts')
    <!-- InstanceBeginEditable name="Footer Scripts" -->
    <script type="text/javascript" src="{{url('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script>
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
    </script>
    <!--<script type="text/javascript">
    $(document).on("click", ".add-question" , function(){
        var i = $('tbody').children('tr').children('td').length + 1;
        var a = '<td class=""><input type="text" class="form-control" value=""></td>';
        var b = '<td class=""><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="'+i+'"><label for="'+i+'" class="custom-control-label"></label></div></td>';
        $('thead tr').append(a);
        $('tbody tr').append(b);
    });
    $(document).on("click", ".add-option" , function(){
        var b = $('tbody').children('tr:last-child').clone();
        $('tbody').append(b);
        custsort2();
    });
    $(document).on("click", ".add-header" , function(){
        var i = $(".form-list").children(".card").length + 1;
        var header =    '<div class="card shadow">'+
                        '<div class="card-header">'+
                        '<div class="form-group">'+
                        '<label class="control-label"><a href="javascript:void(0);" class="mr-10 handle" title=""><i class="fas fa-arrows-alt"></i></a> Template Name '+i+': </label>'+
                        '<div class=""><input type="text" class="form-control" value=""></div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="card-body">'+
                        '<div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-question" title="">Add Question</a></div>'+
                        '<div class="question-list p-15"></div>'+
                        '</div>'+
                        '</div>';
        $(".form-list").append(header);
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
    </script>-->
    <!-- InstanceEndEditable -->


    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/additional-methods.js"></script> --}}

    <script type="text/javascript">
        $.validator.addMethod("nameRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9\ \-]+$/i.test(value);
        }, "Do not use special characters");

        /*-- form validation start --*/    
            $('#priority-add').validate({
                rules: {
                    name: {
                        required: true,
                        nameRegex: true,
                        remote: {
                            type: 'post',
                            url: "{{url('admin/Priority/checkPriorityNamePresence')}}",
                            data:{
                                "_token": "{{csrf_token()}}"
                            }
                        }
                    }
                },
                messages: {
                    name: {
                        required: "Priority name is required.",
                        remote: "Priority name already present"
                    }
                }
            });
        /*-- form validation end --*/

        /*-- Description field validation start --*/    
        $('input[name^="description"]').each(function(){
            var a = $(this).length;
            $(this).rules('add', {
                required: true,
                nameRegex: true,
                messages: {
                    required: "Description is required "                    
                }
            })
        });
        /*-- Description field validation end --*/    

    </script>
@stop

