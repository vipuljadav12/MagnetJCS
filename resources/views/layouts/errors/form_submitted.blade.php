@extends('layouts.front.app')
@section('title')
<title>HCS Magnet</title>
@endsection
@section('content')
        <div class="mt-20 pt-50">
        <div class=""  id="printmsg">
          <div class="col-12 text-center text-danger"><h2>Form already submitted.</h2></div>
              
        <div class="card alert-danger p-20">
           
            <div class="">
              Your form is already submitted. You have tried to re-submit form by pressing "Back" button of browser.
            </div>
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