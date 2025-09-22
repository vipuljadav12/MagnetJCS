@php 
    if(isset($eligibilityContent))
    {
        $content = json_decode($eligibilityContent->content) ?? null;
        // print_r($eligibilityContent->content);
    }
@endphp
<div class="form-group">
    <label class="control-label">Name of Academic Grades</label>
    <div class="">
        <input type="text" class="form-control" value="{{$eligibility->name ?? old('name')}}" name="name">
        @if($errors->first('name'))
            <div class="mb-1 text-danger">
                {{-- {{ $errors->first('name')}} --}}
                Name is required.
            </div>
        @endif
    </div>
</div>
<div class="form-group">
    <label class="control-label">How are academic grades reported?</label>
    <div class="">
        <select class="form-control custom-select" name="extra[academic_grade]">
            @php 
                $grades = array(
                    "STD"=>"Standard Based",
                    "NUM"=>"1-100"
                );//array ends
            @endphp
            <option value="">Select Option</option>
            @foreach($grades as $g=>$grade)
                <option value="{{$g}}" @if(isset($content->academic_grade) && $content->academic_grade == $g) selected @endif>{{$grade}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label">What academic terms will be used?</label>
    <div class="">
        <select class="form-control custom-select" name="extra[academic_term]">
            @php 
                $terms = array(
                    "SEM"=>"Semesters",
                    "9W"=>"9 weeks / Quarter",
                    "YE"=>"Year End",
                    
                );//array ends
            @endphp
            <option value="">Select Option</option>
            @foreach($terms as $t=>$term)
                <option value="{{$t}}" @if(isset($content->academic_term) && $content->academic_term == $t) selected @endif>{{$term}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group d-none">
    <label class="control-label">How many academic terms will be pulled?</label>
    <div class="d-flex flex-wrap">
        @for($i = 1 ; $i <= 4 ; $i++)
            <div class="mr-20">
                <div class="custom-control"><!-- custom-checkbox-->
                    <input type="radio" class="custom-control-input" id="checkbox_terms_{{$i}}" value="{{$i}}" name="extra[terms_pulled][]" @if(isset($content->terms_pulled) && in_array($i, $content->terms_pulled)) checked @endif>
                <label for="checkbox_terms_{{$i}}" class="custom-control-label">{{$i}}</label></div>
            </div>
        @endfor
    </div>
</div>

{{-- <div class="form-group ifDD ">
    <label class="control-label">Which Academic Year Grades Need to Display ?</label>
    <div class="d-flex flex-wrap">
        @php 
            $academic_year_ary = [];
            $current_year = date('Y');
            for ($i=0; $i < 5; $i++) { 
                $tmp_year = $current_year .'-'. ($current_year+1);
                array_push($academic_year_ary, $tmp_year);
                $current_year--;
            }
            $i = 0;
        @endphp
        @foreach($academic_year_ary as $value)
            <div class="mr-20 col-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="academic_year_checkbox_calc_{{$i}}" value="{{$value}}" name="extra[academic_year_calc][]" @if(isset($content->academic_year_calc) && in_array($value, $content->academic_year_calc)) checked @endif>
                <label for="academic_year_checkbox_calc_{{$i}}" class="custom-control-label">{{$value}}</label></div>
            </div>
            @php $i++ @endphp
        @endforeach
    </div>
</div> --}}

<div class="form-group ifDD ">
    <label class="control-label">Which Academic Year Grades Need to Display ?</label>
    <div class="row">
        @php 
            $academic_year_ary = [];
            $current_year = date('Y');
            for ($i=0; $i < 5; $i++) { 
                $tmp_year = $current_year .'-'. substr( $current_year+1, 2);
                array_push($academic_year_ary, $tmp_year);
                $current_year--;
            }
            $i = 0;
            $j = 0;

        $array = array();
        $array = array('Sem 1 Avg'=>'Semester 1 Avgrage', 'Sem 2 Avg'=>'Semester 2 Avgrage', "Yearly Avg"=>'Year End', 'Q1 Grade'=>'Q1 Grade','F1 Grade'=>'F1 Grade', 'Q2 Grade' => 'Q2 Grade', 'Q3 Grade' => 'Q3 Grade', 'Q4 Grade' => 'Q4 Grade' )  
        // dd($content);
        @endphp

        @foreach($academic_year_ary as $value)
            <div class="col-12 col-lg-3 mb-30">
                <div class="custom-outer-box">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input academic_year_checkbox_calc" id="academic_year_checkbox_calc_{{$i}}" value="{{$value}}" name="extra[academic_year_calc][]" @if(isset($content->academic_year_calc) && in_array($value, $content->academic_year_calc)) checked @endif>
                    <label for="academic_year_checkbox_calc_{{$i}}" class="custom-control-label">{{$value}}</label></div>

                    <div class="custom-sub-box academic_year_checkbox_calc_{{$i}}" @if(isset($content->academic_year_calc) && !in_array($value, $content->academic_year_calc)) style="display: none;" @endif>
                        @foreach($array as $tkey=>$term)
                        {{-- {{dd($content->terms_calc->$value)}} --}}
                            <div class="pl-20 custom-sub-child">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="checkbox_calc_{{$j}}" value="{{$tkey}}" name="extra[terms_calc][{{$value}}][]" @if(isset($content->terms_calc->$value) && in_array($tkey, $content->terms_calc->$value)) checked @endif>
                                <label for="checkbox_calc_{{$j}}" class="custom-control-label">{{$term}}</label></div>
                            </div>
                            @php $j++ @endphp
                        @endforeach
                    </div>
                </div>
            </div>
            @php $i++ @endphp
        @endforeach
    </div>
</div>

{{-- <div class="form-group ifDD ">
    <label class="control-label">Which Academic Term Grades Needs to Display ?</label>
    <div class="d-flex flex-wrap">
        @php $array = array('Semester 1 Avgrage','Semester 2 Avgrage','Year End') @endphp
        @php $i = 0 @endphp
            @foreach($array as $value)
                <div class="mr-20 col-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkbox_calc_{{$i}}" value="{{$value}}" name="extra[terms_calc][]" @if(isset($content->terms_calc) && in_array($value, $content->terms_calc)) checked @endif>
                    <label for="checkbox_calc_{{$i}}" class="custom-control-label">{{$value}}</label></div>
                </div>
                @php $i++ @endphp
            @endforeach
    </div>
</div> --}}


<div class="form-group">
    <label class="control-label">What course types will be used?</label>
    <div class="d-flex flex-wrap">
        @php 
            $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");
        @endphp
        @foreach($subjects as $s=>$subject)
            <div class="mr-20">
                <div class="custom-control custom-checkbox">
                    <input  value="{{$s}}" type="checkbox" class="custom-control-input" id="checkbox{{$s}}" name="extra[subjects][]" @if(isset($content->subjects) && in_array($s, $content->subjects)) checked @endif >
                    <label for="checkbox{{$s}}" class="custom-control-label">{{$subject}}</label></div>
            </div>
        @endforeach        
    </div>
</div>
<style>
.custom-sub-box {position: relative}
.custom-sub-box:before {content: ""; position: absolute; left: 7px; top: -4px; background: #ccc; width: 2px; bottom: 11px;}
.custom-sub-child {position: relative}
.custom-sub-child:before {content: ""; position: absolute; left: 7px; top: 11px; background: #ccc; height: 2px; width: 20px;}
.custom-checkbox .custom-control-input:checked~.custom-control-label::before {background-color: #00346b;}
</style>
