@extends('layouts.admin.app')
@section('title')Population Changes | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<style type="text/css">
    .buttons-excel{display: none !important;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Population Changes</div>
        </div>
    </div>
    
    <form class="">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link " href="{{url('/admin/LateSubmission/Process/Selection/'.$application_id)}}">All Programs</a></li>
            <li class="nav-item"><a class="nav-link active" id="preview03-tab" data-toggle="tab" href="#preview03" role="tab" aria-controls="preview03" aria-selected="true">Population Changes</a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Submission/Result/'.$application_id)}}">Submission Result</a></li>

        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include('LateSubmission::Template.population_changes')
        </div>
    </form>    
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var dtbl_submission_list = $("#tbl_population_changes").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
             bPaginate: false,
             bSort: false,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'PopulationChanges',
                        text:'Export to Excel'
                    }
                ]
            });

            $("#ExportReporttoExcel").on("click", function() {
                dtbl_submission_list.button( '.buttons-excel' ).trigger();
            });

            
            function updateFinalStatus()
            {
                $("#wrapperloading").show();
                $.ajax({
                    url:'{{url('/admin/LateSubmission/Accept/list/'.$application_id)}}',
                    type:"post",
                    data: {"_token": "{{csrf_token()}}"},
                    success:function(response){
                        alert("Status Allocation Done.");
                        $("#wrapperloading").hide();
                        document.location.reload();

                    }
                })
            }


    </script>
@endsection