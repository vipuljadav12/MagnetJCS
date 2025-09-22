<div class='form-group form-group-input row buildBox{{$formContent->id}}' data-build-id="{{$formContent->id}}" id="{{$formContent->id}}">
   
    <div class='col-12 col-md-11 col-xl-11'>
    	<div class="alert1 {{getContentValue($formContent->id,"alert-class") ?? "alert-danger"}} text-center">
            <p>{!! getContentValue($formContent->id,"value") ?? "" !!}</p>
        </div>
    </div>
    <div>
    	<a class="btn text-danger removeField"><i class="fa fa-times"></i></a>
    </div>
</div>