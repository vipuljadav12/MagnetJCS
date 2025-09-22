@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection</div></div>
        </div>
    </div>
    
      <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">  
        <div class="card shadow">
            <div class="card-body">
                    <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                        <div class="">
                            
                            <div class="form-group">
                                <label for="">Select Application Form : </label>
                                <div class="">
                                    <select class="form-control custom-select" id="form_field" name="form_field">
                                        <option value="">Select</option>
                                        @foreach($applications as $key=>$value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            
                            
                            <div class="text-right"><input type="button" class="btn btn-success" title="Select Form" value="Select Form" id="selectform_settings"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>

<script type="text/javascript">

    $("#selectform_settings").click(function()
    {
        if($("#form_field").val() == "")
        {
            alert("Please select application type to process.")
            $("#form_field").focus();
            return false;
        }
        document.location.href = "{{url('/admin/Process/Selection/test/')}}/"+$("#form_field").val();
    })
     
</script>
@endsection