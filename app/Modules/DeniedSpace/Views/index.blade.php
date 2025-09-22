@extends('layouts.admin.app')
@section('title')Edit Communication | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
    
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Date Setting For Expire Waitlist</div>
    </div>
</div>
    @include("layouts.admin.common.alerts")

        <ul class="nav nav-tabs" id="myTab1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active maintab" id="communication-tab" data-toggle="tab" href="#datesettings" role="tab" aria-controls="datesettings" aria-selected="true">Date Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link maintab" href="{{url('/admin/DeniedSpace/form/communication/'.$form_id)}}">Communication Settings</a>
                </li>
        </ul>
   
        <div class="tab-content bordered" id="myTab1Content">
            <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                <form class="" action="{{url('admin/DeniedSpace/form/date/store/'.$form_id)}}" method="POST" onsubmit="return validateForm()">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="">Select Date</label>
                        <div class="">
                            <input class="form-control datetimepicker" name="expire_waitlist_date" id="expire_waitlist_date"  value="{{((isset($data->waitlist_end_date) && $data->waitlist_end_date != '') ? date('m/d/Y', strtotime($data->waitlist_end_date)) : '')}}" data-date-format="mm/dd/yyyy">
                        </div>
                    </div>
                    
                    <div class="form-group"> <input type="submit" class="btn btn-secondary" value="Save Dates"> <input type="submit" class="btn btn-warning" value="Expire Waitlist Now" name="expire_now" style="float:right !important"></div>
                    </form>
            </div>
        </div>
     </form>
     
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">
        $("#expire_waitlist_date").datepicker({
            numberOfMonths: 1,
            dateFormat: 'mm/dd/yyyy',
            autoclose: true
        })

        function validateForm()
        {
            if($("#expire_waitlist_date").val() == "")
            {
                alert("Please select expire waitlist date");
                return false;
            }
        }
</script>
@endsection