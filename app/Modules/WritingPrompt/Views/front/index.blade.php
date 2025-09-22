@extends('layouts.front.app')

@section('content')

  @php
    $total_minutes = $duration;
    $res = ($total_minutes/60);
    $hours = intval($res);
    $minutes = ($res-$hours) * 60;
    $extra = '';

    if ($hours > 0) {
      $extra = $hours . ' hour(s)';
      $extra .= ($minutes > 0) ? ' & ' : '';
    }
    if ($minutes > 0) {
      $extra .= $minutes . ' minute(s) ';
    }
    // $extra = '2 hours';
  @endphp

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
      @if($intro_txt != '')
        {!! $intro_txt !!}
      @else

        <div class="text-left font-16 b-600 mt-30 mb-10">Please remember:</div>
        <div class="text-left">
          <ul>
              <li>You will have <strong>{{$extra}}</strong> to complete the essay.  The timer begins once you access the writing prompt.  Once the <strong>{{$extra}}</strong> ends the essay will automatically be submitted.</li>
              <li>Please be sure to not hit submit before you are ready for the essay to be submitted.</li>
          </ul>
        </div>
      @endif
      <div class="p-20 mt-20 mb-10">
        <div class="box-0">
          <div class="col-12 col-md-6 col-xl-12 text-center">
            <form method="post" action="{{url('/WritingPrompt/exam')}}">
            {{csrf_field()}}
              {{-- <input type="hidden" name="req_url" value="{{$req_url}}"> --}}
              <button type="submit" class="btn btn-secondary btn-xxl" title="" style="height: 55px; width: 180px;">Start Now</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection