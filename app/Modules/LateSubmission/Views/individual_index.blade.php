@extends('layouts.admin.app')
@section('title') Individual Program Process Selection @endsection
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
#waitingprograms, #waitingprograms1 { width: 97% important, margin: 0 auto !important; padding-top: 10px; padding-bottom: 10px; margin-top:10px; margin-bottom: 10px; border-top: 2px dashed #000; border-bottom: 2px dashed #000; }
    /* EOS */
  </style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Individual Program Process Selection</div>
        </div>
    </div>
    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link " href="{{url('/admin/LateSubmission')}}">Form Type</a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Availability/Show')}}">All Programs</a></li>
            <li class="nav-item"><a class="nav-link active" id="preview03-tab" data-toggle="tab" href="#preview03" role="tab" aria-controls="preview03" aria-selected="true">Individual Program</a></li>
            @if($displayother > 0)
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Population/Form')}}">Population Changes</a></li>
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Submission/Result/1')}}">Submission Results</a></li>
            @endif
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include('LateSubmission::Template.individual_programs')
        </div>

@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is running.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://rawgit.com/Danielku15/FixedHeader/master/js/dataTables.fixedHeader.js"></script>
    <script type="text/javascript">
    	  
       /* var dtbl_submission_list = $("#tbl_population_changes").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
              fixedHeader: {
            relativeScroll: true
        },
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

         */   
        function updateFinalStatus()
        {
            $("#wrapperloading").show();
            $.ajax({
                url:'{{url('/admin/Process/Selection/Accept/list')}}',
                type:"post",
                data: {"_token": "{{csrf_token()}}"},
                success:function(response){
                    alert("Status Allocation Done.");
                    $("#wrapperloading").hide();
                    document.location.reload();

                }
            })
        }

        function updateProcessSeats(str)
        {
            var available_seats = parseInt($("#available_seats-"+str).html());
            var offer_count = parseInt($("#offer_count-"+str).html());
            var waitlist_count = parseInt($("#waitlist_count-"+str).html());
            if($("#WS-"+str).val() == "")
                $("#WS-"+str).val(0);
            var withdraw_student = parseInt($("#WS-"+str).val());
            $("#WS-"+str).val(withdraw_student);
            $(".process_seats-"+str).html(available_seats-offer_count+withdraw_student);
            $(".updated_seats-"+str).val(available_seats+withdraw_student);
        }

        function onlyNumberKey(evt) { 
                  
                // Only ASCII charactar in that range allowed 
                var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
                if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) 
                    return false; 
                return true; 
        } 

       function saveData()
       {
            var grade = $("#grade").val();
            var program_id = $("#program_id").val();
            var seats = $("#WS-"+program_id+"-"+grade).val();

            $("#wrapperloading").show();
            $.ajax({
                type: 'get',
                url: "{{url('admin/LateSubmission/Individual/Save/')}}/"+program_id+"/"+grade+"/"+seats,
                success: function(response) {
                        $("#waitingprograms").html(response.html);
                        $("#waitingprograms1").html(response.html);
                        $("#waitingprograms1").addClass("d-none");
                        $("#wrapperloading").hide();
                        $("#waitingprograms").removeClass("d-none");


                    }
                });

       }

        function showProgramsWaitlist(val)
        {
            if(val == "")
            {
                $("#responseprogram").html("");
                $("#waitingprograms1").removeClass("d-none");
                return false;
            }
            $("#wrapperloading").show();
            $.ajax({
                type: 'get',
                dataType: 'JSON',
                url: "{{url('admin/LateSubmission/Individual/Show/Response/')}}/"+val,
                success: function(response) {

                        $("#responseprogram").html(response.html);
                        if($("#waitingprograms1").html() != "")
                        {
                            $("#waitingprograms").removeClass("d-none");
                            $("#waitingprograms").html($("#waitingprograms1").html());
                        }

                        $("#wrapperloading").hide();

                         $("#last_date_late_submission_online_acceptance").datetimepicker({
                            numberOfMonths: 1,
                            autoclose: true,
                             startDate:new Date(),
                            dateFormat: 'mm/dd/yy hh:ii',
                            pickerPosition: 'top-right'
                        })

                        $("#last_date_late_submission_offline_acceptance").datetimepicker({
                            numberOfMonths: 1,
                            autoclose: true,
                             startDate:new Date(),
                            dateFormat: 'mm/dd/yy hh:ii',
                            pickerPosition: 'top-right'
                        })

                         $('#process_selection').submit(function(event) {
                             event.preventDefault();

                            if($("#last_date_late_submission_online_acceptance").val() == "")
                            {
                                alert("Please select Last date of online acceptance");
                                return false;
                            }

                            if($("#last_date_late_submission_offline_acceptance").val() == "")
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
                                url:'{{ url('admin/LateSubmission/Individual/store')}}',
                                type:"POST",
                                data: $('#process_selection').serialize(),
                                success:function(response){
                                    $("#wrapperloading").hide();
                                    //alert(response);
                                    document.location.href = "{{url('/admin/LateSubmission/Population/Form')}}";

                                }
                            })

                    });

                    }
                });

        }

      


    </script>
@endsection