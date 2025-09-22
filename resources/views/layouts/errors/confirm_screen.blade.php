@extends('layouts.front.app')

@section('content')
        <div class="mt-20 pt-50">
        <div class=""  id="printmsg">
          <div class="col-12 text-center"><h2>{!! $confirm_title !!}</h2></div>
              @if($student_type=="active")
                @php $class = "alert-success" @endphp
              @else
                @php $class = "alert-danger" @endphp
              @endif
              <div class="alert {{$class}} text-center mt-20"><strong>{!! $confirm_subject !!}</strong></div>

              @if(isset($instructions) && $instructions != '')
                   <div class="card bg-light p-20 mb-20">
                        <div class="">
                          {!! $instructions !!}
                        </div>
                      </div>

              @endif
        <div class="card bg-light p-20">
           
            <div class="">
              

            	@if(isset($confirm_msg))
            		@php
            			$confirm_msg = str_replace("###CONFIRMATION_NO###", (isset($confirmation_no) ? $confirmation_no : ""), $confirm_msg);
            			$confirm_msg = str_replace("###STARTOVER###", "<a hrer='".url('/')."' class='btn btn-primary'>START OVER</a>", $confirm_msg);

            		@endphp
            		{!! $confirm_msg !!}
            	@endif
            </div>
        </div>
      </div>

         <div class="form-group d-flex flex-wrap justify-content-between pt-20">
              <label class="control-label col-12 col-md-4 col-xl-3 pl-0">
                @if(Session::has("from_admin"))
                  <button type="button" class="btn btn-secondary" title="" value="Exit Application" onclick="document.location.href='{{url('/phone/submission')}}'"><i class="fa fa-backward"></i>  Exit Application</button>
                @else
                  <button type="button" class="btn btn-secondary" title="" value="Exit Application" onclick="document.location.href='{{url('/')}}'"><i class="fa fa-backward"></i>  Exit Application</button>
                @endif
              </label>
              <button onclick="document.location.href='{{url('/print/application/'.$confirmation_no)}}'" class="btn btn-secondary step-2-2-btn" value="Print Application"><i class="fa fa-print"></i> Print</button>
          </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    function disableBack() {window.history.forward()}

    window.onload = disableBack();
    window.onpageshow = function (evt) {if (evt.persisted) disableBack()}
});

</script>
@endsection