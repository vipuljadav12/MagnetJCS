@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection Settings</div>
        </div>
    </div>
    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/Process/Selection/Population')}}">Population Changes</a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/Process/Selection/Results/Form')}}">Submissions Result</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include('ProcessSelection::Template.processing')
        </div>
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>

<script type="text/javascript">
	/* DataTables start */
	$('#tbl_population_changes').DataTable();
	/* DataTables end */

    $("#form_field").change(function()
    {
        if($(this).val() != "")
        {
            $("#programs_select").val("");
        }
    })

    $("#programs_select").change(function()
    {
        if($(this).val() != "")
        {
            $("#form_field").val("");
        }
    })

     $('#process_selection').submit(function(event) {
        event.preventDefault();
            if($("#last_date_online_acceptance").val() == "")
            {
                alert("Please select Last date of online acceptance");
                return false;
            }

            if($("#last_date_offline_acceptance").val() == "")
            {
                alert("Please select Last date of offline acceptance");
                return false;
            }

            @if($display_outcome == 0)
                if($("#form_field").val() == "" && $("#programs_select").val() == "")
                {
                    alert("Please select Program or Form to proceed");
                    return false;
                }
            @endif
            $("#wrapperloading").show();
            $.ajax({
                url:'{{ url('admin/Process/Selection/store')}}',
                type:"POST",
                data: {"_token": "{{csrf_token()}}", "form_field": $("#form_field").val(), "programs_select": $("#programs_select").val(), "last_date_online_acceptance": $("#last_date_online_acceptance").val(), "last_date_offline_acceptance": $("#last_date_offline_acceptance").val()},
                success:function(response){
                    $("#wrapperloading").hide();
                    @if($display_outcome == 0)
                        document.location.href = "{{url('/admin/Process/Selection/Population/Form/')}}/" + $("#form_field").val();
                    @else
                        document.location.href = "{{url('/admin/Process/Selection/Population/Form/')}}";
                    @endif

                }
            })

     });

    $("#last_date_online_acceptance").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })

    $("#last_date_offline_acceptance").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })

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