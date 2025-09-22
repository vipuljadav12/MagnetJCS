@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection</div><div class="text-right"><a href="javascript:void(0)" class="btn btn-secondary d-none" onclick="rollBackStatus();">Roll Back Status</a></div></div>
        </div>
    </div>
    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Processing</a></li>
            
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include('ProcessSelection::Template.processing')
        </div>
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>

<script type="text/javascript">
    $("#form_field").change(function()
    {
        if($(this).val() != "")
        {
            $("#wrapperloading").show();
            $.ajax({
                url:'{{ url('admin/Process/Selection/validate/application/')}}/'+$(this).val(),
                type:"GET",
                success:function(response){
                    $("#wrapperloading").hide();
                    if(response != "OK")
                    {
                        $("#error_msg").html('<div class="alert1 alert-danger pl-20 pt-20"><ul>'+response+'</ul></div>');
                        $("#submit_btn").addClass("d-none");
                    }
                    else
                    {
                        $("#submit_btn").removeClass("d-none");
                        $("#error_msg").html("");
                    }
                    
                }
            })
        }
        else
        {
            $("#submit_btn").addClass("d-none");  
        }
    })

    $('#process_selection').submit(function(event) {
        event.preventDefault();
                if($("#form_field").val() == "")
                {
                    alert("Please select Form to proceed");
                    return false;
                }

                document.location.href = "{{url('/admin/Process/Selection/step2/')}}/"+$("#form_field").val();


     });

    function rollBackStatus()
    {
        $("#wrapperloading").show();
        $.ajax({
            url:'{{url('/admin/Process/Selection/Revert/list')}}',
            type:"post",
            data: {"_token": "{{csrf_token()}}"},
            success:function(response){
                alert("All Statuses Reverted.");
                document.location.href = "{{url('/admin/Process/Selection')}}";
                $("#wrapperloading").hide();

            }
        })
    }


</script>
@endsection