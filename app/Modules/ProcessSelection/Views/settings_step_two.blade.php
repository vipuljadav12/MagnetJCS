@php
    if (isset($swingData->swing_percentage) && ($swingData->swing_percentage!='')) {
        $swing_data = explode(',', $swingData->swing_percentage);
    } else {
        $swing_data = [];
    }
@endphp

@extends('layouts.admin.app')
@section('title')Processing Max Percent Swing | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    @include("layouts.admin.common.alerts")
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Processing Max Percent Swing</div><div class=""><a href="{{url('admin/Process/Selection/settings')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div> 
        </div>
    </div>
    
    <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
        
        <form method="post" action="{{url('admin/Process/Selection/settings/store/'.$application_id)}}">
            {{csrf_field()}}
            <div class="card shadow">
                <div class="card-header">Processing Max Percent Swing</div>
                <div class="card-body">
                    <div class="table-responsive" style="height: auto; overflow-y: auto;">
                        <table class="table tbl_adm">
                            <thead>
                                <tr>
                                    <th class="" style=" top: 0; background-color: #fff !important; z-index: 200 !important">Selection Round</th>
                                    <th style="  top: 0; background-color: #fff !important; z-index: 200 !important">% Swing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i=0; $i<3; $i++)
                                    <tr>
                                        <td class="">*Round {{($i+1)}}</td>
                                        <td class=""><input type="text" name="swing_value[]" class="form-control adm_value digit_float" value="{{$swing_data[$i] ?? ''}}" maxlength="5"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right pt-20"><input type="submit" class="btn btn-success" title="Save" value="Save"></div>
                     
                </div>
            </div>
        </form>


    </div>

@endsection

@section('scripts')
<script type="text/javascript">
    // float digits validation
    $(document).on('keypress', '.digit_float', function() {
        digitValidation(event, this, 'float');
    });
    function digitValidation(evt, elm, type='') {
        if (type=='float') {
            var float_valid = ($(elm).val().indexOf('.') != -1);
        } else {
            var float_valid = true;
        }
        if ((evt.which != 46 || float_valid) &&
           ((evt.which < 48 || evt.which > 57) &&
           (evt.which != 0 && evt.which != 8))) {
               evt.preventDefault();
        }
    }
</script>
@endsection