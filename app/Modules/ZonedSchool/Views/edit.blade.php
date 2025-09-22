@extends('layouts.admin.app')
@section('title')Edit Zone Address | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css">

<style type="text/css">
    .loader
    {
        background: url("{{url('/resources/assets/front/images/loader.gif')}}");
        background-repeat: no-repeat;
        background-position: right;
    }
        .select2-container .select2-choice {border-radius: 0 !important;  height: 30px !important}
    .select2-container{width: 100% !important; height: 30px !important}

</style>

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Zone Address</div>
            @php
                ($zonedschool->added_by == 'manual') ? $url = url('admin/ZonedSchool/overrideAddress') : $url = url('admin/ZonedSchool') ;
            @endphp
            
                <div class=""><a href="{{$url}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div>
        </div>
    </div>
    @include("layouts.admin.common.alerts")
    <form action="{{url('admin/ZonedSchool/update',$zonedschool->id)}}" method="post" name="add_zone_address" enctype= "multipart/form-data">
        {{csrf_field()}}

        <input type="hidden" name="added_by" value="{{$zonedschool->added_by}}">
        
        <div class="raw">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-right"><em>Please enter all information as provided on the district's zoning map page.</em>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Building/House No<span class="required">*</span> : </label>
                        <div class=""><input type="text" class="form-control" name="bldg_num" value="{{$zonedschool->bldg_num}}"></div>
                        @if($errors->first('bldg_num'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('bldg_num')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Prefix Direction : <small><em>(If Applicable)</em></small> </label>
                        <div class="">
                             <select name="prefix_dir" id="prefix_dir" class="custom-sel2">
                                <option value="">Select Direction</option>
                                <option value="N"  @if("N" == $zonedschool->prefix_dir) selected @endif>North</option>
                                <option value="S" @if("S" == $zonedschool->prefix_dir) selected @endif>South</option>
                                <option value="E" @if("E" == $zonedschool->prefix_dir) selected @endif>East</option>
                                <option value="W" @if("W" == $zonedschool->prefix_dir) selected @endif>West</option>
                            </select>
                        </div>
                        @if($errors->first('prefix_dir'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('prefix_dir')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Street Name<span class="required">*</span> : </label>
                        <div class="">
                            <input type="text" class="form-control" name="street_name"  id="street_name" value="{{$zonedschool->street_name}}"></div>
                        @if($errors->first('street_name'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('street_name')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">Street Type : </label>
                        <div class="">
                            <select name="street_type" id="street_type" class="custom-sel2" onchange="showOther('street_type');"> 
                                <option value="">Select Street Type</option>
                                @foreach($street_type as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->street_type) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="street_type_other"  id="street_type_other"></div>
                        @if($errors->first('street_type'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('street_type')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Unit Info : </label>
                        <div class=""><input type="text" class="form-control" name="unit_info" value="{{$zonedschool->unit_info}}"></div>
                        @if($errors->first('unit_info'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('unit_info')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Suffix Direction : <small><em>(If Applicable)</em></small></label>
                        <div class="">
                            <select name="suffix_dir" id="suffix_dir" class="custom-sel2">
                                <option value="">Select Direction</option>
                                <option value="N"  @if("N" == $zonedschool->suffix_dir) selected @endif>North</option>
                                <option value="S" @if("S" == $zonedschool->suffix_dir) selected @endif>South</option>
                                <option value="E" @if("E" == $zonedschool->suffix_dir) selected @endif>East</option>
                                <option value="W" @if("W" == $zonedschool->suffix_dir) selected @endif>West</option>
                            </select>
                        </div>
                        @if($errors->first('suffix_dir'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('suffix_dir')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">City<span class="required">*</span> : </label>
                        <div class="">
                            <select name="city" id="city" class="custom-sel2" onchange="showOther('city');">
                                <option value="">Select City</option>
                                @foreach($city as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if(strtolower($value) == strtolower($zonedschool->city)) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="city_other"  id="city_other">
                        </div>
                        @if($errors->first('city'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('city')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">State<span class="required">*</span> : </label>
                        <div class="">
                            <select name="state" id="state" class="custom-sel2">
                                @php $stateArray = Config::get('variables.states') @endphp

                                @foreach($stateArray as $stkey=>$stvalue)
                                    <option value="{{$stkey}}" @if($stvalue == $zonedschool->state) selected @endif>{{$stvalue}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($errors->first('state'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('state')}}
                            </div>
                        @endif
                    </div>
                
                    <div class="form-group">
                        <label class="control-label">ZIP Code<span class="required">*</span> : </label>
                        <div class="">
                            <select name="zip" id="zip" class="custom-sel2" onchange="showOther('zip');">
                                <option value="">Select ZIP Code</option>
                                @foreach($zip as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->zip) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="zip_other"  id="zip_other">

                        </div>
                        @if($errors->first('zip'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('zip')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Elementary School<span class="required">*</span> : </label>
                        <div class="">
                            <select name="elementary_school" id="elementary_school" class="custom-sel2" onchange="showOther('elementary_school');">
                                <option value="">Select Elementary School</option>
                                @foreach($elementary_school as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->elementary_school) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="elementary_school_other"  id="elementary_school_other">
                        </div>
                        @if($errors->first('elementary_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('elementary_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Intermediate School<span class="required">*</span> : </label>
                        <div class="">
                            <select name="intermediate_school" id="intermediate_school" class="custom-sel2" onchange="showOther('intermediate_school');">
                                <option value="">Select Intermediate School</option>
                                @foreach($intermediate_school as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->intermediate_school) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="intermediate_school_other"  id="intermediate_school_other">
                        </div>
                        @if($errors->first('intermediate_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('intermediate_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">Middle School<span class="required">*</span> : </label>
                        <div class="">
                            <select name="middle_school" id="middle_school" class="custom-sel2" onchange="showOther('middle_school');">
                                <option value="">Select Middle School</option>
                                @foreach($middle_school as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->middle_school) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="middle_school_other"  id="middle_school_other">
                        </div>
                        @if($errors->first('middle_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('middle_school')}}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">High School<span class="required">*</span> : </label>
                        <div class="">
                            <select name="high_school" id="high_school" class="custom-sel2" onchange="showOther('high_school');">
                                <option value="">Select High School</option>
                                @foreach($high_school as $key=>$value)
                                    @if($value != "")
                                        <option value="{{$value}}" @if($value == $zonedschool->high_school) selected @endif>{{$value}}</option>
                                    @endif
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-10 d-none" name="high_school_other"  id="high_school_other">
                        </div>
                        @if($errors->first('high_school'))
                            <div class="mb-1 text-danger">
                                {{ $errors->first('high_school')}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <button type="Submit" class="btn btn-warning btn-xs submit" title="Save"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/ZonedSchool')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>

    <script type="text/javascript">
        $("form[name='add_zone_address']").validate({
            ignore: "",
            rules:{
                bldg_num:{
                    required:true,
                },
                "street_name":{
                  required: true
                },
                street_type:{
                  required:true,  
                },
                street_type_other:{
                  required: function(){
                    if($("#street_type").val()=="Other")
                        return true;
                    else
                        return false;
                  },  
                },
                city:{
                    required:true,
                },
                city_other:{
                  required: function(){
                    if($("#city").val()=="Other")
                        return true;
                    else
                        return false;
                  },  
                },
                zip:{
                    required:true,
                    
                },
                zip_other:{
                  required: function(){
                    if($("#zip").val()=="Other")
                        return true;
                    else
                        return false;
                  },  
                  maxlength: 11,
                    minlength: 5,
                    },
                state:{
                    required:true,
                },
                elementary_school:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#intermediate_school").val() && !$("#middle_school").val())
                            return true;
                        else
                            return false;
                     },
                },
                elementary_school_other:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#intermediate_school").val() && !$("#middle_school").val() && !$("#high_school_other").val() && !$("#intermediate_school_other").val() && !$("#middle_school_other").val() && $("#elementary_school").val() == "Other")
                            return true;
                        else
                            return false;
                     },
                },
                middle_school:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#intermediate_school").val() && !$("#elementary_school").val())
                            return true;
                        else
                            return false;
                     },
                },
                middle_school_other:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#intermediate_school").val() && !$("#elementary_school").val() && !$("#high_school_other").val() && !$("#intermediate_school_other").val() && !$("#elementary_school_other").val() && $("#middle_school").val() == "Other")
                            return true;
                        else
                            return false;
                     },
                },
                intermediate_school:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#elementary_school").val() && !$("#middle_school").val())
                            return true;
                        else
                            return false;
                     },
                },
                intermediate_school_other:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#high_school").val() && !$("#elementary_school").val() && !$("#middle_school").val() && !$("#high_school_other").val() && !$("#elementary_school_other").val() && !$("#middle_school_other").val() && $("#intermediate_school").val() == "Other")
                            return true;
                        else
                            return false;
                     },
                },
                high_school:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#intermediate_school").val() && !$("#elementary_school").val() && !$("#middle_school").val())
                            return true;
                        else
                            return false;
                     },
                },
                high_school_other:{
                    required: function() {
                        //returns true if email is empty
                        if(!$("#intermediate_school").val() && !$("#elementary_school").val() && !$("#middle_school").val() && !$("#intermediate_school_other").val() && !$("#elementary_school_other").val() && !$("#middle_school_other").val() && $("#high_school").val() == "Other")
                            return true;
                        else
                            return false;
                     },
                },
            },
            messages:{
                elementary_school:{
                    required:'Atleast one school is required.'
                },
                intermediate_school:{
                    required:'Atleast one school is required.'
                },
                middle_school:{
                    required:'Atleast one school is required.'
                },
                higl_school:{
                    required:'Atleast one school is required.'
                },
                /*recommendation_due_date:{
                    required:'The Recommendation due date field is required.',
                    date:'The Date formate is not valid',
                },*/
                transcript_due_date:{
                    required:'The Transcript due date field is required.',
                    date:'The Date formate is not valid',
                },
                'program_grade_id[]':{
                  required:'The Program is required.',
                }
            },errorPlacement: function(error, element)
            {
                error.appendTo( element.parents('.form-group'));
                error.css('color','red');
            },
            submitHandler: function (form) {
                form.submit();
            }
          });
         $(".custom-sel2").select2();

         
         function showOther(objid)
         {
            if($("#"+objid).val() == "Other")
            {
                $("#"+objid+"_other").removeClass("d-none");
            }
            else
            {
                $("#"+objid+"_other").val("");
                $("#"+objid+"_other").addClass("d-none");
            }

         }

    </script>
@endsection