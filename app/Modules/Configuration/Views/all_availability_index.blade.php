@extends('layouts.admin.app')
@section('title')Process Late Submission | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<style type="text/css">
    .buttons-excel{display: none !important;}
    table .header-fixed {
  position: fixed;
  top: 0px;
  z-index: 1020; /* 10 less than .navbar-fixed to prevent any overlap */

  -webkit-border-radius: 0;
     -moz-border-radius: 0;
          border-radius: 0;
  -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 5px rgba(0,0,0,.1);
     -moz-box-shadow: inset 0 1px 0 #fff, 0 1px 5px rgba(0,0,0,.1);
          box-shadow: inset 0 1px 0 #fff, 0 1px 5px rgba(0,0,0,.1);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false); /* IE6-9 */
}
input[type="checkbox"]:after {
    width: 17px;
    height: 17px;
    margin-top: -2px;
    font-size: 14px;
    line-height: 1.2;
}
input[type="checkbox"]:checked:after {
    font-family: 'Font Awesome 5 Free';
    color: #00346b;
    font-weight: 900;
    width: 17px;
    height: 17px;
}
      #table-wrap {
  margin: 10px;
  height: 400px;
  overflow: auto;
}
    /* EOS */
  </style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Late Submission</div>
        </div>
    </div>
    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview03-tab" data-toggle="tab" href="#preview03" role="tab" aria-controls="preview03" aria-selected="true">All Programs</a></li>
            @if($displayother > 0)
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Population/'.$application_id)}}">Population Changes</a></li>
                <li class="nav-item"><a class="nav-link" href="{{url('/admin/LateSubmission/Submission/Result/'.$application_id)}}">Submission Results</a></li>
            @endif
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            @include('LateSubmission::Template.all_availability')
        </div>

@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
    <script type="text/javascript">
    	   $('#process_selection').submit(function(event) {
	       	   event.preventDefault();
             var selected = false;
              $(".check_selector").each(function(){
                  if(this.checked == true)
                    selected = true;
              })
              if(!selected)
              {
                  alert("Select atleast one progrm");
                  return false;
              }

              if($("#process_event").val() != "saveonly")
              {
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
              }
              $("#wrapperloading").show();
	            $.ajax({
	                url:'{{ url('/admin/LateSubmission/Process/Selection/'.$application_id.'/store')}}',
	                type:"POST",
	                data: $('#process_selection').serialize(),
	                success:function(response){
	                   $("#wrapperloading").hide();
                       if($("#process_event").val() != "saveonly")
                            document.location.href = "{{url('/admin/LateSubmission/Population/'.$application_id)}}";
                        $("#process_event").val("");


                      
	                }
	            })

     	});
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
                {
                    $("#WS-"+str).val(0);
                }
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
                $("#process_event").val("saveonly");
                $('#process_selection').submit();
           }
 

        $("#last_date_online_acceptance").datetimepicker({
            numberOfMonths: 1,
            autoclose: true,
             startDate:new Date(),
            dateFormat: 'mm/dd/yy hh:ii',
            pickerPosition: 'top-right'
        })

        $("#last_date_offline_acceptance").datetimepicker({
            numberOfMonths: 1,
            autoclose: true,
             startDate:new Date(),
            dateFormat: 'mm/dd/yy hh:ii',
            pickerPosition: 'top-right'
        })

        function enableDisableWithdrawn(id, val)
        {
            if(val == "No")
            {
              $("#black"+id).attr("disabled", "disabled");
              $("#white"+id).attr("disabled", "disabled");
              $("#other"+id).attr("disabled", "disabled");

              $("#black"+id).val(0);
              $("#white"+id).val(0);
              $("#other"+id).val(0);
              updateAwardSlot(id);
            }
            else
            {
              $("#black"+id).removeAttr("disabled");
              $("#white"+id).removeAttr("disabled");
              $("#other"+id).removeAttr("disabled");
            }

        }

        function checkUncheckAll(obj)
        {
            if(obj.prop("checked"))
            {
                $(".check_selector").prop("checked", true);
                $("span.hide-table").removeClass("hide-table").addClass("show-table");
                $("tr.disable-row").removeClass("disable-row").addClass("enable-row");
            }
            else
            {
                $(".check_selector").prop("checked", false);
                $("span.show-table").removeClass("show-table").addClass("hide-table");
                $("tr.enable-row").removeClass("enable-row").addClass("disable-row");
            }
        }

        function showHideRow(id, obj)
        {
            if(obj.prop("checked"))
            {
                $("#row"+id).removeClass("disable-row");
                $("#row"+id).addClass("enable-row");
                $("#row"+id).find("td").each(function() {
                      $(this).find("span").each(function()
                      {
                        $(this).removeClass("hide-table").addClass("show-table");
                      })
                 });
            }
            else
            {
                $("#row"+id).removeClass("enable-row");
                $("#row"+id).addClass("disable-row");
                 $("#row"+id).find("td").each(function() {
                      $(this).find("span").each(function()
                      {
                        $(this).removeClass("show-table").addClass("hide-table");
                      })
                 });
            }
        }

        function updateAwardSlot(id)
        {
          if($("#black"+id).val() == "")
            $("#black"+id).val(0);

          if($("#white"+id).val() == "")
            $("#white"+id).val(0);

          if($("#other"+id).val() == "")
            $("#other"+id).val(0);

          if($("#additional_seats"+id).val() == "")
            $("#additional_seats"+id).val(0);

          var withdrawn = parseInt($("#black"+id).val()) + parseInt($("#white"+id).val()) + parseInt($("#other"+id).val());
          var additional = parseInt($("#additional_seats"+id).val());

          console.log("Additional " + additional);
          console.log("Withdrawn " + withdrawn);
          console.log("Additional " + parseInt($("#available_slot"+id).html()) + withdrawn + additional);

          $("#awardslot"+id).val(parseInt($("#available_slot"+id).html()) + withdrawn + additional);
          $("#awardslot_span"+id).html(parseInt($("#available_slot"+id).html()) + withdrawn + additional);

        }

      


    </script>
@endsection