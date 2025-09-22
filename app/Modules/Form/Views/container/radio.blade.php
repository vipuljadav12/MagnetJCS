<div class='form-group form-group-input row buildBox{{$formContent->id}}' data-build-id="{{$formContent->id}}" id="{{$formContent->id}}" >
    <label class='control-label col-12 col-md-5' id="label{{$formContent->id}}">{{getContentValue($formContent->id,"label") ?? "Label :"}} </label>
    <div class='col-12 col-md-6 col-xl-6' id="input{{$formContent->id}}">
    	@php
			$required = getContentValue($formContent->id,"required");
			$required = isset($required) ?  "required" : "";
		@endphp
            @php
                $currentOptions = getContentRadio($formContent->id);
            @endphp
            @if(isset($currentOptions) && !empty($currentOptions))
                @foreach($currentOptions as $c=>$currentOption) 
                    <input type="radio" name="inputc{{$formContent->id}}"{{--  {{$required}} --}} value="{{$currentOption->field_value}}" class="radioissue1">&nbsp;{{$currentOption->field_value}}&nbsp; 
                @endforeach
            @endif
    </div>
    <div>
    	<a class="btn text-danger removeField"><i class="fa fa-times"></i></a>
    </div>
</div>
<style type="text/css">
    .radioissue
    {
          all: unset;
    }
</style>