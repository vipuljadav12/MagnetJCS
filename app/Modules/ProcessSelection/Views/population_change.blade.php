@extends('layouts.admin.app')
@section('title')Population Changes of of {{getApplicationName($application_id)}} @endsection
@section('content')
<style type="text/css">
    .buttons-excel{display: none !important;}
</style>
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
  <style id="compiled-css" type="text/css">
      #table-wrap {
  margin: 10px;
  height: 400px;
  overflow: auto;
}
    /* EOS */
  </style>
  @if($display_outcome == 0)
    <form action="{{ url('admin/Process/Selection/store')}}" method="post" name="process_selection" id="process_selection">
    {{csrf_field()}}
        <input type="hidden" name="application_id" value="{{$application_id}}" id="application_id">
    @endif
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Population Changes - <span class="text-danger">{{getApplicationName($application_id)}}</span></div>
            <div class="text-right"><a href="{{url('/admin/Process/Selection')}}" class="btn btn-secondary">Change Application</a></div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist"> 
                <li class="nav-item"><a class="nav-link" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Settings</a></li> 
                <li class="nav-item"><a class="nav-link active" id="preview04-tab" data-toggle="tab" href="#preview04" role="tab" aria-controls="preview04" aria-selected="true">Population Changes</a></li> 
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/Process/Selection/Results/Application/'.$application_id)}}">Submissions Result</a></li> 
            </ul> 
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                    @include('ProcessSelection::Template.acceptance_window') 
                </div> 
                
                @include('ProcessSelection::Template.population_changes') 
            </div> 
        
            
        </div> 
    </div> 
    @if($display_outcome == 0) 
        </form>  
    @endif   
@endsection 

@section('scripts') 
    <div id="wrapperloading" style="display:none; z-index: 9999 !important;">
        <div id="loading">
            <i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. 
        </div>
    </div> 
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
    @include("ProcessSelection::common_js") 
    <script type="text/javascript"> 
        var dtbl_submission_list = $("#tbl_population_changes").DataTable(
                                    {   "aaSorting": [], 
                                        dom: 'Bfrtip', 
                                        fixedHeader: { relativeScroll: true }, 
                                        bPaginate: false,
                                        bSort: false, 
                                        buttons: [ { 
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
                    url:'{{url('/admin/Process/Selection/Accept/list')}}',
                    type:"post",
                    data: {"_token": "{{csrf_token()}}", "application_id": $("#application_id").val()},
                    success:function(response){
                        alert("Status Allocation Done.");
                        $("#wrapperloading").hide();
                        document.location.reload();

                    }
                })
            }


    </script>
@endsection