@extends('layouts.front.app')

@section('title')
    <title>{{config('variables.page_title')}}</title>
@endsection

@section('content')
    @if(!Session::has("from_admin"))
        <!--include("layouts.front.common.district_header-->
    @endif
    @php global $hidefurther;  @endphp
    <form class="p-20 border mt-20 mb-20" method="post" action="{{url('/nextstep')}}" id="programform" name="programform" onsubmit="return checkDOB(event)">
        {{csrf_field()}}
        <input type="hidden" name="no_of_pages" value="{{get_form_pages($data[0]->form_id)}}">
        <input type="hidden" name="page_id" value="{{$page_id}}">
        <input type="hidden" name="form_type" id="form_type" value="{{$form_type}}">
        <input type="hidden" id="application_id" name="application_id" value="{{$application_id}}">
        <input type="hidden" name="form_id" id="form_id" value="{{$data[0]->form_id}}">
        <input type="hidden" id="bdate_validation" value="">

        <div class="box-2" style="">
            @if(session::has("zonemsg"))
                <div class="alert alert-danger">{{getAlertMsg('zone_address_not_available')}}</div>
            @endif
            <div class="box-2-1" style="">
                <div class="back-box hidden" style="">
                    <div class="form-group text-right">
                        <!--<label class="control-label col-12 col-md-4 col-xl-3"></label>-->
                        <div class="">
                            <a href="{{url('/')}}" class="btn btn-secondary back-box-1" title="">Back</a>
                        </div>
                    </div>    
                </div>
                <div class="card" id="infodiv">
                    <div class="card-header">Step {{$page_id}} - {{ getFormPageTitle($data[0]->form_id, $page_id) }}</div>
                     <div class="card-body">
                        @include("layouts.front.form_fields")

                        @if($hidefurther == "Yes")
                            @php $dclass = "d-custom-hide" @endphp
                        @else
                            @php $dclass = "" @endphp
                        @endif
                         @if(get_form_pages($data[0]->form_id) == $page_id)
                            <div class="form-group d-flex flex-wrap justify-content-between {{$dclass}}">
                                    <!--<a href="javascript:void(0);" onclick="exitWithoutSave()" class="btn btn-danger" title="">Exit<br><sm>*Your application will not be saved</a>-->
                                        <button type="submit" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="removeValidateSubmit()"><i class="fa fa-backward pr-5"></i>  Previous Page</button>
                                    <input type="submit" class="btn btn-success step-2-2-btn" id="np" title="" onclick="return submitForm(event)" value="Submit My Application">
                            </div>

                        @else
                            <div class="form-group d-flex flex-wrap justify-content-between {{$dclass}}">
                                    <label class="control-label col-12 col-md-4 col-xl-3 pl-0">
                                        @if($page_id > 1)
                                            <button type="submit" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="removeValidateSubmit()"><i class="fa fa-backward pr-5"></i>  Previous Page</button>
                                        @else
                                            <!--<button type="button" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="document.location.href='{{url('/')}}'"><i class="fa fa-backward"></i>  Previouse Page</button>-->
                                        @endif
                                    </label>
                                     <button type="submit" name="submit" class="btn btn-secondary step-2-2-btn" title="" value="Next Step" id="np" onclick="return validateAddress(event)">Next Page  <i class="fa fa-forward pl-5"></i></button>
                            </div>
                        @endif
                            
                     </div>
                        

                        @if(Config::get('variables.address_debug') == "true")
                            <div class="form-group d-flex flex-wrap justify-content-between d-none p-50" id="addressdebug">
                                                    <input type="button" name="Fetch Schools" value="Fetch Schools" onclick="fetch_zoned_school();">
                                <div class="card-body col-12" id="response">
            
                                </div>
                            </div>
                        @endif
                        
                </div>
                
            </div>
        </div>
    </form>
@endsection

@section("scripts")

	<script type="text/javascript">
        var zonedmatch = false;
        var address_selected = true;
        if($("#address").length > 0 && $("#city").length > 0 && $("#zip").length > 0)
        {
            $("#addressdebug").removeClass("d-none");
        }



        function validateAddress(event)
        {
            @if(Session::has("zone_api") && Session::has("zone_api")=="N")
                return true;
            @endif
            if($("#mcp_employee_element").length > 0)
            {
                if($("#mcp_employee_element").val() == "Yes")
                    return true;
            }

            
            if($("#address").length > 0 && $.trim($("#address").val()) != "" && $.trim($("#city").val()) != "" && $.trim($("#zip").val()) != "" && zonedmatch == false)
            {}
            if(zonedmatch == true && address_selected == false)
            {
                 swal({
                    text: "Please select appropriate address from suggestion list",
                    type: "warning",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#d62516"
                });
                return false;
            }
            return true;
        }

        function fetch_zoned_school()
        {
            $.ajax({
                    url:'{{url('/admin/ZonedSchool/search')}}',
                    type:"post",
                    data:{_token:'{{csrf_token()}}',address:$("#address").val(),grade: $("#next_grade").val(),zip: $("#zip").val(),city: $("#city").val()},
                    success:function(response){
                        $("#response").html(response);
                    }

                })
        }
		$('#studentstatus_frm').validate({
                rules: {
                    student_status: {
                        required: true,                       
                    }
                },
                messages: {
                    student_status: {
                        required: "Select Student Status"
                    }
                }
            });


    $('#programform').validate({
                rules: {

                    @foreach($data as $key=>$row)
                        @php $fieldProp = getOnlyFieldProperty($row->type,$row->id,$row->form_id, $page_id) @endphp
                    'formdata[{{$row->id}}]': {
                        @if($fieldProp['required']=="yes")
                            @if($fieldProp['type'] == "termscheck" && Session::has("from_admin"))
                            @else
                                required: true,
                            @endif                   
                        @endif
                        @if($fieldProp['type']=="email")
                            email: true,                   
                        @endif  
                        @if($fieldProp['db_field']=="confirm_email")
                            equalTo: {
                                param: '#parent_email',
                                depends: function(element) { 
                                    if($("#parent_email").length > 0)
                                    {
                                        return true;
                                        
                                    }
                                    else
                                        return false; 
                                }
                            },
                        @endif
                        @if(isset($fieldProp['validation']))
                            {{$fieldProp['validation']}}: true,                   
                        @endif    
                        },
                    @endforeach
                }, errorPlacement: function(error, element) {
                        if (element.attr("type") == "radio") {
                            $(element).parent().parent().remove( ".error" );
                            $(element).parent().parent().find(".raderror").remove();
                            $(element).parent().after( "<label class='raderror error col-12 pl-0'>"+error.text()+"</label>" );
                            //error.insertAfter(element);
                        }
                        else if (element.attr("type") == "checkbox") {
                            $(element).parent().parent().parent().find(".chkerror").remove();
                            $(element).parent().parent().after( "<label class='chkerror error col-12 pl-30'>"+error.text()+"</label>" );
                            //error.insertAfter(element);
                        } else {
                            error.insertAfter(element);
                        }
                    }


            });


    function selectAddress(val)
    {
        $("#address").val(val);
        address_selected = true;
        zonedmatch = true;
    }

        function exitWithoutSave()
        {
            var txt;
            var r = confirm("This will exit you from Application Submission process without saving data ?");
            if (r == true) {
              document.location.href = "{{url('/')}}";
            } 
        }

        function showHideCorrect()
        {
             $("#correctdiv").remove();
                            $(".d-custom-hide").removeClass("d-custom-hide");
                            $("#zoned_school_div").removeClass("d-none");
                            //$("#zoned_school_val").html("SS");


            //correctinfo
        }

        function showHideElement(element, obj)
        {
            if($("#"+element).length > 0)
            {
                if($(obj).val()=="Yes")
                    $("#"+element).removeClass("d-none");
                else
                    $("#"+element).addClass("d-none");
            }
        }
	</script>
@endsection