<div class="form-group" >
    <div class="card">
        <div class="card-header">
            {{-- Please select your Magnet Program choices below --}}
            {{$data['label'] ?? ""}}
        </div>
        <div class="card-body">
            <div class="b-600 font-14 mb-10">First Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        @php $progam_data = getProgramDropdown() @endphp
                        @php $next_grade_field_id = getNextGradeField($field_id) @endphp
                        @php $current_grade_field_id = getCurrentGradeField($field_id) @endphp
                        @php $current_school_field_id = getCurrentSchoolField($field_id) @endphp 
                        @php $zoned_school_field_id = getZonedSchoolField($field_id) @endphp
                        
                        {{-- <select class="form-control custom-select" name="{{$field_id}}_first"> --}}
                        @php ($next_grade_name = "") @endphp

                        @if(Session::has('form_data'))
                            @if(isset(Session::get('form_data')[0]['formdata'][$next_grade_field_id]))

                                @php ($next_grade_name = Session::get('form_data')[0]['formdata'][$next_grade_field_id]) @endphp
                                @php ($current_grade_name = Session::get('form_data')[0]['formdata'][$current_grade_field_id]) @endphp
                                @php ($current_school_name = Session::get('form_data')[0]['formdata'][$current_school_field_id]) @endphp
                                @php ($zoned_school_name = Session::get('form_data')[0]['formdata'][$zoned_school_field_id]) @endphp
                                @php $school_master_name = getSchoolMasterName($zoned_school_name) @endphp
                                {{-- {{dd($zoned_school_name, $school_master_name)}} --}}
                            @endif
                        @endif
                        <select class="form-control custom-select" name="first_choice" id="first_choice">

                            <option value="">Choose an option</option>

                            @foreach($progam_data as $key=>$value)
                                
                                @php $magnet_school = getMagnetSchool($value->program_id) @endphp
                                @php $max_grade = getMaxGrade($value->program_id) @endphp
                                @php $changed_name = checkCheckedProgram($value->program_id, $current_grade_name, $value->grade_name) @endphp

                                @if($value->grade_name == $next_grade_name)
                                    @php $zoned_schools = explode(",", $value->zoned_schools) @endphp
                                    @if(in_array($school_master_name, $zoned_schools))
                                        @if($max_grade <= $current_grade_name)
                                            @if($changed_name != "")
                                                <option value="{{$value->id}}">{{$changed_name}}</option>
                                            @else
                                                <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                            @endif
                                        @else

                                            @if(in_array($current_school_name, $magnet_school))
                                            @else
                                                @if($changed_name != "")
                                                    <option value="{{$value->id}}">{{$changed_name}}</option>
                                                @else
                                                    <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row d-none" id="first_sibling_part_1">
                    <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" value="Yes">
                            <label class="custom-control-label" for="customRadioInline1">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input" checked="" value="No">
                            <label class="custom-control-label" for="customRadioInline2">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="display: none;" id="first_sibling">
                    <label class="col-12 col-lg-4">Sibling State ID# : </label>
                    <div class="col-12 col-lg-6">
                        <input type="text" class="form-control" name="first_sibling" id="first_sibling_field" onblur="checkSibling(this)"><span class="hidden">
                        Checking Sibing State ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>
                        <span class="first_sibling_label"></span>
                    </div>
                     <div class="col-12 col-md-2 col-xl-2">
                        <span class="help" data-toggle="tooltip" data-html="true" title="If you do not know the sibling's 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office.">
                            <i class="fas fa-question"></i>
                        </span>
                    </div>
                </div>
            </div>
             @if((isset($data['second_display']) && $data['second_display'] == "Yes") || !isset($data['second_display']))
                    <div class="b-600 font-14 mb-10">Second Program Choice</div>
                    <div class="border p-20 mb-20">
                        <div class="form-group row">
                            <label class="col-12 col-lg-4">Program : </label>
                            <div class="col-12 col-lg-6">
                                {{-- <select class="form-control custom-select" name="{{$field_id}}_second"> --}}
                                <select class="form-control custom-select" name="second_choice" id="second_choice">
                                    <option value="">Choose an option</option>
                                     @foreach($progam_data as $key=>$value)
                                        @php $magnet_school = getMagnetSchool($value->program_id) @endphp
                                        @php $max_grade = getMaxGrade($value->program_id) @endphp
                                        @php $changed_name = checkCheckedProgram($value->program_id, $current_grade_name, $value->grade_name) @endphp

                                        @if($value->grade_name == $next_grade_name)
                                            @php $zoned_schools = explode(",", $value->zoned_schools) @endphp
                                            @if(in_array($school_master_name, $zoned_schools))
                                                @if($max_grade <= $current_grade_name)
                                                    @if($changed_name != "")
                                                        <option value="{{$value->id}}">{{$changed_name}}</option>
                                                    @else
                                                        <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                                    @endif
                                                    <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                                @else
                                                    @if(in_array($current_school_name, $magnet_school))
                                                    @else
                                                        @if($changed_name != "")
                                                            <option value="{{$value->id}}">{{$changed_name}}</option>
                                                        @else
                                                            <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif    
                                        @endif
                                    @endforeach

                                </select>

                            </div>
                        </div>
                        <div class="form-group row d-none" id="second_sibling_part_1">
                            <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                            <div class="col-12 col-lg-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline3" name="customRadioInline" class="custom-control-input" value="Yes">
                                    <label class="custom-control-label" for="customRadioInline3">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline4" name="customRadioInline" class="custom-control-input" checked value="No">
                                    <label class="custom-control-label" for="customRadioInline4" >No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="display: none;" id="second_sibling">
                            <label class="col-12 col-lg-4">Sibling State ID# : </label>
                            <div class="col-12 col-lg-6">
                                <input type="text" class="form-control" name="second_sibling" id="second_sibling_field" onblur="checkSibling(this)">
                                <span class="hidden">
                                Checking Sibing State ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>
                                <span class="second_sibling_label"></span>

                                {{-- <input type="text" class="form-control" name="{{$field_id}}_second_sibling"> --}}
                            </div>
                             <div class="col-12 col-md-2 col-xl-2">
                                <span class="help" data-toggle="tooltip" data-html="true" title="If you do not know the sibling's 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office.">
                                    <i class="fas fa-question"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#customRadioInline3").change(function(){
            if($(this).val()=="Yes")
            {
                $("#second_sibling").show();
            }
        })
        $("#customRadioInline4").change(function(){
            if($(this).val()=="No")
            {
                $("#second_sibling").hide();
                $("#{{$field_id}}_second_sibling").val("");
            }
        })

        $("#customRadioInline1").change(function(){
            if($(this).val()=="Yes")
            {
                $("#first_sibling").show();
            }
        })
        $("#customRadioInline2").change(function(){
            if($(this).val()=="No")
            {
                $("#first_sibling").hide();
                $("#{{$field_id}}_first_sibling").val("");
            }
        })
    })
</script>