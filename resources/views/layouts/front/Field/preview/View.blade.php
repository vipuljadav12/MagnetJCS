<div class="table-responsive">
    @php $displayArr = getViewEnableFields($form_id) @endphp
	     <table class="table table-striped table-bordered">
            <tbody>
            	@foreach($displayArr as $key1=>$value1)
                       
                        
                       
                         <tr>
                            <td class="b-600 w-75">{{$value1->field_value}} :</td>
                            <td class="">
                            </td>
                        </tr>
                        
                    @endforeach
            </tbody>
        </table>
    </div>
                            