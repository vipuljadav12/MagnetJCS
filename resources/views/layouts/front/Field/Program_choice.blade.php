<div class="form-group" >
    <div class="card">
        <div class="card-header">
            {{-- Please select your Magnet Program choices below --}}
            {{$data['label'] ?? ""}}
        </div>
        
        @php 

            $cnt_array = array("1"=>"first", "2"=>"second", "3"=>"third");
            $progam_data = getProgramDropdown();
            $next_grade_field_id = getNextGradeField($field_id);
            $current_grade_field_id = getCurrentGradeField($field_id);
            $current_school_field_id = getCurrentSchoolField($field_id); 
            $zoned_school_field_id = getZonedSchoolField($field_id);
            $next_grade_name = "";

            if(Session::has('form_data'))
            {
                if(isset(Session::get('form_data')[0]['formdata'][$next_grade_field_id]))
                {
                    $next_grade_name = Session::get('form_data')[0]['formdata'][$next_grade_field_id];
                    $current_grade_name = Session::get('form_data')[0]['formdata'][$current_grade_field_id];
                    $current_school_name = Session::get('form_data')[0]['formdata'][$current_school_field_id];
                    $zoned_school_name = Session::get('form_data')[0]['formdata'][$zoned_school_field_id];
                    $school_master_name = getSchoolMasterName($zoned_school_name);
                }
            }
            if(isset($data['choice_count']))
                $choice_count = $data['choice_count'];
            else
                $choice_count = 2;
        @endphp

        <div class="card-body">
        @for($i=1; $i <= $choice_count; $i++)
            @php $cb = $cnt_array[$i]; @endphp
            <div class="b-600 font-14 mb-10">{{ucfirst($cb)}} Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        
                        <select class="form-control custom-select choice_option" name="{{$cb}}_choice" id="{{$cb}}_choice">

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
                <div class="form-group row d-none" id="{{$cb}}_sibling_part_1">
                    <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline{{$cb}}" name="customRadioInline{{$cb}}" class="custom-control-input sibling_radio" value="Yes">
                            <label class="custom-control-label" for="customRadioInline{{$cb}}">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline{{$cb}}-" name="customRadioInline{{$cb}}" class="custom-control-input sibling_radio" checked="" value="No">
                            <label class="custom-control-label" for="customRadioInline{{$cb}}-">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="display: none;" id="{{$cb}}_sibling">
                    <label class="col-12 col-lg-4">Sibling State ID# : </label>
                    <div class="col-12 col-lg-6">
                        <input type="text" class="form-control" name="{{$cb}}_sibling" id="{{$cb}}_sibling_field" onblur="checkSibling(this)"><span class="hidden">
                        Checking Sibing State ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>
                        <span class="{{$cb}}_sibling_label"></span>
                    </div>
                     <div class="col-12 col-md-2 col-xl-2">
                        <span class="help" data-toggle="tooltip" data-html="true" title="If you do not know the sibling's 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office.">
                            <i class="fas fa-question"></i>
                        </span>
                    </div>
                </div>
            </div>
            

            @endfor
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $(".sibling_radio").change(function()
        {
            var div_id = $(this).parent().parent().parent().attr("id").replace("_sibling_part_1", "");
            if($(this).val() == "Yes")
            {
                $("#"+div_id+"_sibling").show();
            }
            else
            {
                $("#"+div_id+"_sibling").hide();
            }
        })
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