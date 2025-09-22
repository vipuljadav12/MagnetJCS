    @php global $hidefurther  @endphp
    @foreach($data as $key=>$row)
        @if($hidefurther == "Yes")
        	@php $classdisp = "d-custom-hide" @endphp
        @else
	        @php $classdisp = "" @endphp
        @endif
        <div class="row {{$classdisp}}">
            <div class="col-12">
                {!! getFieldByTypeandId($row->type,$row->id,$row->form_id, $page_id); !!}
            </div>
        </div>
        
    @endforeach
