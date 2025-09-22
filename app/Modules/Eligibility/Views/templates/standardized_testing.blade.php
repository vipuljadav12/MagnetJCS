@php 
    if(isset($eligibilityContent))
    {
        $subjectsContent = json_decode($eligibilityContent->content)->subjects ?? null;
        $scoring = json_decode($eligibilityContent->content)->scoring ?? null;
    }
@endphp
<div class="form-group template-option-8">
    <label class="control-label">Name of Standardized Testing</label>
    <div class="">
		<input class="form-control" type="text" value="{{$eligibility->name ?? old('name')}}" name="name">
		@if($errors->first('name'))
            <div class="mb-1 text-danger">
                {{-- {{ $errors->first('name')}} --}}
                Name is required.
            </div>
        @endif
	</div>
</div>
<div class="form-group template-option-8">
    <label class="control-label">Is there a scoring method or data displayed?</label>
    <div class="">
		<select class="form-control custom-select" id="selectDataType" name="extra[scoring][type]">
            <option value="">Select Option</option>
            <option value="SC" @if(isset($scoring->type) && $scoring->type == "SC") selected @endif >Scoring</option>
            <option value="DD" @if(isset($scoring->type) && $scoring->type == "DD") selected @endif >Data Display</option>
        </select>
	</div>
</div>
<div class="form-group template-option-8">
    <label class="control-label">How will score method work?</label>
    <div class="">
		<select class="form-control custom-select " id="selectScoreMethod" name="extra[scoring][method]">
            <option value="">Select Option</option>
            <option value="YN" @if(isset($scoring->method) && $scoring->method == "YN") selected @endif >Yes/No</option>
            <option value="NR" @if(isset($scoring->method) && $scoring->method == "NR") selected @endif >Numeric Ranking</option>
            <option value="CO" @if(isset($scoring->method) && $scoring->method == "CO") selected @endif >Conversion</option>
            <option value="NA" @if(isset($scoring->method) && $scoring->method == "NA") selected @endif >N/A (display only)</option>
        </select>
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
	        <div class=""><input type="tex	t" class="form-control" name="extra[scoring][NR][]"></div>
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
<div class="form-group  @if(isset($scoring->method) && $scoring->method == "CO") @else  d-none @endif scoreTypeDiv scoreTypeCO">
    <label class="control-label">What is the conversion method?</label>
    <div class="">
		<select class="form-control custom-select" name="extra[scoring][CO]" >
            <option value="">Select Option</option>
            <option value="NP" @if(isset($scoring->CO) && $scoring->CO == "NP") selected @endif>National Percentage</option>
            <option value="RS" @if(isset($scoring->CO) && $scoring->CO == "RS") selected @endif>Raw Score</option>
        </select>
	</div>
</div>
<div class="form-group template-option-8">
    <label class="control-label">What standardized test scores will be used?</label>
	<div class="d-flex flex-wrap">
		@php 
            $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");
        @endphp
        @foreach($subjects as $s=>$subject)
            <div class="mr-20">
                <div class="custom-control custom-checkbox">
                    <input  value="{{$s}}" type="checkbox" class="custom-control-input" id="checkbox{{$s}}" name="extra[subjects][]" @if(isset($subjectsContent) && in_array($s, $subjectsContent)) checked @endif >
                    <label for="checkbox{{$s}}" class="custom-control-label">{{$subject}}</label></div>
            </div>
        @endforeach
    </div>
</div>		