@extends('layouts.front.app')

@section('styles')
<link rel="stylesheet" href="{{url('/')}}/resources/assets/front/css/jquery.countdownTimer.css">
@endsection

@section('content')
  <div class="mt-20">
    <div class="card bg-light p-20">
      <div class="row">
          <div class="col-sm-6 col-xs-12 font-16 b-600">Student Name: <span>{{$data['submission']->first_name." ".$data['submission']->last_name}}</span></div>
          <div class="col-sm-6 text-right font-16 col-xs-12 b-600">Student's Submission ID: <span>{{$data['submission']->confirmation_no}}</span></div>
      </div>
      <div class="row">
          <div class="col-sm-6 col-xs-12 font-16 b-600 mt-10">Program Name: <span>{{getProgramName($data['program_id'])}}</span></div>
          <div class="col-sm-6 text-right font-16 col-xs-12 b-600 mt-10">Next Grade: <span>{{$data['submission']->next_grade}}</span></div>
      </div>

      <div class="row">
        <div class="col-sm-6 col-xs-12">
          <div class="text-left font-20 b-600"></div>
        </div>
        <div class="col-sm-6 col-xs-12 text-sm-right text-xs-center mt-20 font-16 b-600">Time Remaining: <span id="hs_timer" class="text-danger"></span></div>
      </div>
      
    </div>
  </div>
  <form class="p-20 border mt-20 mb-20" id="frm_wp_exam_store" method="post" action="{{url('WritingPrompt/store/exam')}}">
  {{csrf_field()}}
    <div class="row pt-20">
        <div class="col-12">
        <p>{!! $data['intro_txt'] or '' !!}</p>
      </div>
      </div>
    <div class="box-0">

      @if(!empty($data['wp_question']))
        @foreach($data['wp_question'] as $key => $wp)
          <div class="form-group row">
            <input type="hidden" name="writing_prompt[{{$key}}]" value="{{$wp}}">
            <label class="control-label col-12 col-md-12 col-xl-12 b-600" for="qry01">{{$wp}} : </label>
            <div class="col-12 col-md-12 col-xl-12">
              <textarea class="form-control" name="writing_sample[{{$key}}]" rows="7" id="qry01"></textarea>
            </div>
          </div>
        @endforeach
      @endif
      
      <div class="form-group row">
        <div class="col-12 col-md-12 col-xl-12"> <button type="submit" class="btn btn-secondary btn-xxl" title="" style="height: 55px; width: 140px;">Submit</a> </div>
      </div>
    </div>
  </form>
@endsection

@section('scripts')
<script type="text/javascript" src="{{url('/resources/assets/front/js/jquery.countdownTimer.js')}}"></script> 
<script type="text/javascript">

  @php
    $total_minutes = $data['duration'] ?? 0; 
    $res = ($total_minutes/60);
    $hours = intval($res);
    $minutes = ($res-$hours) * 60;
  @endphp

  var hours = "{{$hours or '0'}}";
  var minutes = "{{$minutes or '0'}}";
  // var time_duration = "{{$data['wp_config']->duration or '0'}}";

  $(function(){
    $('#hs_timer').countdowntimer({
      hours : hours,
      minutes : minutes,
      seconds : 0,
      size : "sm",
      // tickInterval : 5‚
      // timeSeparator : "-"‚
      timeUp : timeisUp
    });

    function timeisUp() {
      $('#frm_wp_exam_store').submit();
    }

  });
</script>
@endsection