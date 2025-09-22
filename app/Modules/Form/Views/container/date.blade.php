<div class='form-group form-group-input row buildBox{{$formContent->id}}' data-build-id="{{$formContent->id}}" id="{{$formContent->id}}">
    <label class='control-label col-12 col-md-5' id="label{{$formContent->id}}">{{getContentValue($formContent->id,"label") ?? "Label :"}} </label>
    <div class='col-12 col-md-6 col-xl-6'>
        @php
            $required = getContentValue($formContent->id,"required");
            $required = isset($required) ?  "required" : "";
        @endphp
        <input type='date' class='form-control' id="input{{$formContent->id}}" placeholder="{{getContentValue($formContent->id,"placeholder") ?? ""}}" {{-- {{$required}} --}}>
    </div>
    <div>
        <a class="btn text-danger removeField"><i class="fa fa-times"></i></a>
    </div>
</div>