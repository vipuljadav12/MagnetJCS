@extends('layouts.front.app')
@section('title')
    <title>Jefferson Co. Country Schools Program</title>
@endsection
@section('content')
        <div class="mt-20">
        <div class="card bg-light p-20">
            <div class="text-center font-20 b-600 mb-10">
            {{-- {{dd(getConfig()[$msg_type.'_title'])}} --}}
            		@if(isset(getConfig()[$msg_type.'_title']))
	            		@php
	            			$msg_title = getConfig()[$msg_type.'_title'];
	            			$msg_title = str_replace("###CONFIRMATION_NO###", (isset($confirmation_no) ? $confirmation_no : ""), $msg_title);
	            			$msg_title = str_replace("###STARTOVER###", "<a hrer='".url('/')."' class='btn btn-primary'>START OVER</a>", $msg_title);

	            		@endphp
	            		{!! $msg_title !!}
	            	@endif


        		</div>
            <div class="">
            	@if(isset(getConfig()[$msg_type]))
            		@php
            			$msg = getConfig()[$msg_type];
            			$msg = str_replace("###CONFIRMATION_NO###", (isset($confirmation_no) ? $confirmation_no : ""), $msg);
            			$msg = str_replace("###STARTOVER###", "<a hrer='".url('/')."' class='btn btn-primary'>START OVER</a>", $msg);

            		@endphp
            		{!! $msg !!}
            	@endif

                @if($msg_type != "before_application_open_text" && $msg_type != "after_application_open_text" && $msg_type != "no_grade_info" && $msg_type != "writingprompt_success" && $msg_type != "recommendation_success" && $msg_type != "return_invalid" && $msg_type != "return_thankyou" && $msg_type != "return_no_student")
                    @if(Session::has("from_admin"))
                        <a href="{{url('/phone/submission')}}" class="btn btn-info">START OVER</a>
                    @else
                        <a href="{{url('/')}}" class="btn btn-info">START OVER</a>
                    @endif
                @elseif($msg_type == "no_grade_info")
                    <a href="{{url('/')}}" class="btn btn-info">EXIT</a>
                @endif
            </div>
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