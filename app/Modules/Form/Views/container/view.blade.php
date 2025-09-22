<div class='form-group form-group-input row col- buildBox{{$formContent->id}}' data-build-id="{{$formContent->id}}" id="{{$formContent->id}}">
    <div class="card-body table-responsive">
                @php $disp_fields = getViewEnableFields($formContent->form_id) @endphp
                <table class="table table-striped table-bordered">
            <tbody>
                @foreach($disp_fields as $key1=>$value1)
                        <tr>
                            <td class="b-600 w-75">{{$value1->field_value}} :</td>
                            <td class="">
                            </td>
                        </tr>
                @endforeach
            </tbody></table>
                

            </div>
            <div>
        <a class="btn text-danger removeField"><i class="fa fa-times"></i></a>
    </div>
</div>