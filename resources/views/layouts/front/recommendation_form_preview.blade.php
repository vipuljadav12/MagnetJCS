@extends('layouts.front.app')

@section('title')
    <title>{{$program_name}}</title>
@endsection
@section('content')
<style type="text/css">
    input[type="checkbox"]:after {
    width: 17px;
    height: 17px;
    margin-top: -2px;
    font-size: 14px;
    line-height: 1.2;
}
input[type="checkbox"]:checked:after {
    font-family: 'Font Awesome 5 Free';
    color: #00346b;
    font-weight: 900;
    width: 17px;
    height: 17px;
}
</style>
    {{-- @include("layouts.front.common.district_header") --}}
    <div class="mt-20">
      <div class="card bg-light p-20">
        <div class="row">
          <div class="col-sm-12 col-xs-12 d-flex justify-content-between">
            <div class="text-left font-20 b-600">{{$program_name}}</div>
            <div class="text-right font-20 b-600 text-danger">Preview</div>
          </div>
        </div>
      </div>
    </div>
    <form id="recommendationForm">
        <div class="mt-20">
          <div class="card bg-light p-20">
            <div class="row">
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Confirmation No: <span>AGT-0001-0001</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Student: <span> Ansley Visone </span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">School: <span> Blossomwood ES </span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Title: <span>{{config('variables.recommendation_subject')[$subject]}}</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Teacher: 
                    <span class="d-inline-block">
                        <input type="text" class="form-control max-250" name="teacher_name" placeholder="Teacher Name">
                    </span>
                </div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Email: <span class="d-inline-block">
                  <input type="text" class="form-control max-250" name="teacher_email" placeholder="Email ID">
                  </span></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="p-20 border mt-20 mb-20">
            @if($header_text != "")
                {!! $header_text !!}
            @else
                <div class="h6 mb-10">Dear Staff:</div>
                <div class="h6 mb-20">Your recommendation is an important consideration in the decision process of the screening committee for acceptance into the college Academy.</div>
            @endif
            @if(isset($content->header) && !empty($content->header))
            @foreach($content->header as $key=>$header)
                <div class="h4 mb-20">{{$header->name}}</div>
                <div class="box-0">
                    {{-- <input type="hidden" name="extra[answer][{{$key}}][name]" value="{{$header->name}}"> --}}
                    @foreach($header->questions as $q=>$question)
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-3 col-xl-3 b-600 text-right mt-1">{{$question}}</label>
                            <div class="col-12 col-md-8 col-xl-8">
                                <select class="form-control custom-select recommQuestion" name="extra[answer][{{$key}}][answers][{{$question}}]">
                                    <option value="0">Choose an option</option>
                                    @foreach($header->options as $o => $option)
                                        @if($option != '')
                                            <option value="{{ $header->points->{$o} }}">{{$header->points->{$o} . '. ' . $option}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- {{dd($header, $question)}} --}}
                    @foreach($header->options as $ko => $option)
                        @if($option != '')
                            <input type="hidden" name="extra[answer][{{$key}}][options][]" value="{{$option}}">
                            <input type="hidden" name="extra[answer][{{$key}}][points][]" value="{{$header->points->{$ko} }}">
                        @endif
                    @endforeach
                </div>
            @endforeach
            @endif
            <div class="form-group row">
                <label class="control-label col-12 col-md-3 col-xl-3 b-600 text-right mt-1" for="qry02">Additional comment</label>
                <div class="col-12 col-md-8 col-xl-8">
                    <textarea class="form-control" name="comment" rows=8></textarea>    
                </div>
            </div>
            <div class="form-group row mb-0 d-none">
                <div class="col-12 col-md-11 col-xl-11 text-right">
                    <label class="mr-10">Average Score</label>
                    <span class="d-inline-block">
                        <input type="hidden" class="average_score" name="avg_score">
                        <input class="form-control max-250 average_score" id="average_score" value="0.00" disabled>
                    </span> 
                </div>
            </div>
        </div>
        @if(isset($content->description))
        <div class="mt-20">
            <div class="card bg-light p-20">
                <div class="row">
                    @foreach($content->description as $k=>$value)
                        <div class="col-sm-12 col-xs-12 mb-10">
                            <div class="text-left font-16 b-600"><input type="checkbox" class="" name="extra[description][]" value="{{$value}}"> <span>{{$value}}</span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <div class="mt-20">
            <div class="p-20 pb-0">
                <div class="text-center font-20 b-600 mb-10">This Recommendation is confidential</div>
                <div class="col-12 col-md-6 col-xl-12 text-center mb-30"> 
                    <input type="button" class="btn btn-secondary btn-xxl" onclick="alert('This functionality will not work in preview mode.')" value="Submit Recommendation">
                    {{-- <a href="javascript:void(0);" class="btn btn-secondary btn-xxl" title="">Submit Recommendation</a> --}}
                </div>
                <div class="col-12 col-md-6 col-xl-12 text-center">
                  <p>This electronic form must be completed by {{getDateTimeFormat($recommendation_due_date ?? '')}}.</p>
                </div>
            </div>
        </div>

        @if(strip_tags($footer_text) != '')
        <div class="mt-0 mb-20">
            <div class="card bg-light p-20">
                <div class="text-center">
                    <p class="m-0">{!! $footer_text !!}</p>
                </div>
            </div>
        </div>
        @endif
    </form>
@endsection
@section('scripts')

<script type="text/javascript">

</script>

@endsection