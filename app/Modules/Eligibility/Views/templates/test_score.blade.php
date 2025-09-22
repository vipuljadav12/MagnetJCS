@php 
    if(isset($eligibilityContent))
    {
        $allow_spreadsheet = json_decode($eligibilityContent->content)->allow_spreadsheet ?? null;
        $content = json_decode($eligibilityContent->content)->eligibility_type;
    }
@endphp
<div class="form-group template-option-1">
    <label class="control-label">Name of Test Score : </label>
    <div class="">
    	<input type="text" class="form-control" value="{{$eligibility->name ?? old('name')}}" name="name">
    </div>
</div>
<div class="form-group">
    <label class="control-label">How Many Textbox for Scores Required : </label>
    <div class="">
        <input type="text" class="form-control" name="extra[eligibility_type][box_count]" @if(isset($content->box_count)) value="{{$content->box_count[0]}}" @endif>
    </div>
</div>

<div class="form-group custom-none">
    <label class="control-label">Eligibility Type : </label>
    <div class="">
        <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
            <option value="">Select Option</option>
            <option value="NR" @if(isset($content->type) && $content->type == "NR") selected @endif>Numeric Ranking</option>
        </select>   
    </div>
</div>
<div class="template-type-2 @if(isset($content->type) && $content->type == "NR") @else  d-none @endif">
    @if(isset($content) && isset($content->NR))
        @foreach($content->NR as $k=>$n)
            <div class="form-group">
                <label class="">Numeric Ranking {{$k+1}} : </label>
                <div class="">
                    <input type="text" class="form-control" name="extra[eligibility_type][NR][]" value="{{$n}}">
                </div>
            </div>
        @endforeach
    @else
        <div class="form-group">
            <label class="">Numeric Ranking 1 : </label>
            <div class=""><input type="text" class="form-control" name="extra[eligibility_type][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 2 : </label>
            <div class=""><input type="text" class="form-control" name="extra[eligibility_type][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 3 : </label>
            <div class=""><input type="text" class="form-control" name="extra[eligibility_type][NR][]"></div>
        </div>
        <div class="form-group">
            <label class="">Numeric Ranking 4 : </label>
            <div class=""><input type="text" class="form-control" name="extra[eligibility_type][NR][]"></div>
        </div>
    @endif
    <div class="mb-20"><a href="javascript:void(0);" class="font-18 add-ranking-13" title=""><i class="far fa-plus-square"></i></a></div>
</div>

