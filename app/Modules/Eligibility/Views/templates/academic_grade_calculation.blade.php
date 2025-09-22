@php 
    if(isset($eligibilityContent))
    {
        $content = json_decode($eligibilityContent->content) ?? null;
        $scoring = json_decode($eligibilityContent->content)->scoring ?? null;
        // print_r($scoring);
        // print_r($content->GPA);
    }
@endphp
<div class="form-group">
    <label class="control-label">Name of Academic Grade Calculation</label>
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
       
<div class="form-group  template-option-4">
    <label class="control-label">What calculation method will be used for the grade calculation? :</label>
    <div class="">
        <select class="form-control custom-select " id="selectScoreTypeAGC" name="extra[scoring][type]">
            @php 
                $types = array(
                    "GA"=>"Grade Average",
                    "GPA"=>"GPA",
                    "CLSG" => "Grade Avg By Subject", //"Count of Letter/ Standards Grades",
                    "DD" => "Data Display"
                );//array ends
            @endphp
            <option value="">Select Option</option>
            @foreach($types as $t=>$type)
                <option value="{{$t}}" @if(isset($scoring->type) && $scoring->type == $t) selected @endif>{{$type}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="ifGPA   @if(isset($scoring->type) && $scoring->type == "GPA") @else d-none @endif">
    <div class="form-group">
        <label class="">If GPA is used, what is the scale for : </label>
        <div class="d-flex flex-wrap">
                <div class="w-90 mb-10 mr-10">
                    <label class="">A (4)</label>
                    <input class="form-control" type="text" name="extra[GPA][A]" value="{{$content->GPA->A ?? ""}}">
                </div>
                <div class="w-90 mb-10 mr-10">
                    <label class="">B (3)</label>
                    <input class="form-control" type="text" name="extra[GPA][B]" value="{{$content->GPA->B ?? ""}}">
                </div>
                <div class="w-90 mb-10 mr-10">
                    <label class="">C (2)</label>
                    <input class="form-control" type="text" name="extra[GPA][C]" value="{{$content->GPA->C ?? ""}}">
                </div>
                <div class="w-90 mb-10 mr-10">
                    <label class="">D (1)</label>
                    <input class="form-control" type="text" name="extra[GPA][D]" value="{{$content->GPA->D ?? ""}}">
                </div>
        </div>
    </div>    
</div>   
<div class="template-type-0011 ifNotDD @if(isset($scoring->type) && $scoring->type != "DD") @else d-none @endif">
    <div class="form-group">
        <label class="control-label">What else will be assigned to the grade calculation?</label>
        <div class="">
            <select class="form-control custom-select template-type-select-new selectScoreMethodAGC" id="selectScoreMethodAGC" name="extra[scoring][method]">
                @php 
                    $methods = array(
                        "YN"=>"Yes/No",
                        "NR"=>"Numeric Ranking",
                        "DD" => "Data Display"
                    );//array ends
                @endphp
                <option value="">Select Option</option>
                @foreach($methods as $m=>$method)
                    <option value="{{$m}}" @if(isset($scoring->method) && $scoring->method == $m) selected @endif>{{$method}}</option>
                @endforeach
            </select>            
        </div>
    </div>    
</div>
<div class="@if(isset($scoring->method) && $scoring->method == "YN") @else  d-none @endif scoreTypeDiv scoreTypeYN">
    <div class="form-group">
        <label class="">Option 1 : </label>
        <div class=""><input type="text" class="form-control" name="extra[scoring][YN][]" @if(isset($scoring->method) && $scoring->method == "YN") value="{{$scoring->YN[0] ?? ""}}" @endif></div>
    </div>
    <div class="form-group">
        <label class="">Option 2 : </label>
        <div class=""><input type="text" class="form-control" name="extra[scoring][YN][]" @if(isset($scoring->method) && $scoring->method == "YN") value="{{$scoring->YN[1] ?? ""}}" @endif></div>
    </div>
</div>
<div class=" @if(isset($scoring->method) && $scoring->method == "NR") @else  d-none @endif scoreTypeDiv scoreTypeNR">
    @if(isset($scoring))
        @foreach($scoring->NR as $k=>$n)
            <div class="form-group">
                <label class="">Numeric Ranking {{$k+1}} : </label>
                <div class="">
                    <input type="text" class="form-control" name="extra[scoring][NR][]" value="{{$n}}">
                </div>
            </div>
        @endforeach
    @else
        <div class="form-group">
            <label class="">Numeric Ranking 1 : </label>
            <div class=""><input type="tex  t" class="form-control" name="extra[scoring][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 2 : </label>
            <div class=""><input type="text" class="form-control" name="extra[scoring][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 3 : </label>
            <div class=""><input type="text" class="form-control" name="extra[scoring][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 4 : </label>
            <div class=""><input type="text" class="form-control" name="extra[scoring][NR][]"></div>
        </div>
    @endif
    <div class="mb-20"><a href="javascript:void(0);" class="font-18 add-more-numeric-ranking-st" title=""><i class="far fa-plus-square"></i></a></div>
</div>
<div class="@if(isset($scoring->method) && $scoring->method == "YN") @else  d-none @endif scoreTypeDiv scoreTypeDD"></div>

<div class="form-group ifDD @if(isset($scoring->type) && ($scoring->type == "DD" || $scoring->type == "GA")) @else d-none @endif">
    <label class="control-label">What Academic Year Need to Use for Grades Calculation ?</label>
    <div class="d-flex flex-wrap">
        @php 
            $academic_year_ary = [];
            $current_year = date('Y');
            for ($i=0; $i < 5; $i++) { 
                $tmp_year = $current_year .'-'. substr( $current_year+1, 2);
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
</div>

<div class="form-group ifDD @if(isset($scoring->type) && ($scoring->type == "DD" || $scoring->type == "GA")) @else d-none @endif">
    <label class="control-label">Which Academic Term Need to Use for Grades Calculation ?</label>
    <div class="d-flex flex-wrap">
        {{-- @php $array = array('Q1.1 Average','Q1.2 Exam','Q1.3 Qtr Grade','Q2.1 Average','Q2.2 Exam','Q2.3 Qtr Grade','Q3.1 Average','Q3.2 Exam','Q3.3 Qtr Grade','Q4.1 Average','Q4.2 Exam','Q4.3 Qtr Grade','Q4.4 Final Grade') @endphp --}}
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
</div>
<div class="form-group ifDD @if(isset($scoring->type) && $scoring->type == "DD") @else d-none @endif">
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
