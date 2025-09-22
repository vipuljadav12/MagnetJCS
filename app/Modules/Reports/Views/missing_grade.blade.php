@extends('layouts.admin.app')
@section('title')
	Missing Grade Report
@endsection
@section('content')
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
.dt-buttons {position: absolute !important; padding-top: 5px !important;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Missing Grade Report</div>
        </div>
    </div>

    <div class="card shadow">
        @include("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id])
    </div>


    <div class="">
            
            <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                            <div class="">
                                
                                <div class="card shadow" id="response">
                                    



                                </div>
                            </div>
                        </div>
        </div>
@endsection
@section('scripts')
        <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 2 minutes to finish. </div></div>

	<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

    <script type="text/javascript">

        function loadSubmissionData(val)
        {
            $("#wrapperloading").show();
            var  url = "{{url('admin/Reports/missing/'.$enrollment_id.'/')}}/grade/response";

            
            $.ajax({
                type: 'get',
                dataType: 'JSON',
                url: url,
                success: function(response) {

                        $("#response").html(response.html);
                        $("#wrapperloading").hide();
                        var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
                         /*dom: 'Bfrtip',
                         buttons: [
                                {
                                    extend: 'excelHtml5',
                                    title: 'Missing-Grade',
                                    text:'Export to Excel',

                                    //Columns to export
                                    exportOptions: {
                                        columns: ':not(.notexport)'
                                    }
                                }
                            ]*/
                        });

                      // Each column dropdown filter
                    $("#datatable thead th").each( function ( i ) {
                        // Disable dropdown filter for disalble_dropdown_ary (index=0)
                        var disalble_dropdown_ary = [2];//13
                        if ($.inArray(i, disalble_dropdown_ary) >= 0) {
                            var column_title = $(this).text();
                            
                            var select = $('<select class="form-control custom-select2 submission_filters col-md-4" id="filter_option"><option value="">Select Student Type</option></select>')
                                .appendTo( $('#submission_filters') )
                                .on( 'change', function () {
                                    if($(this).val() != '')
                                    {
                                        dtbl_submission_list.column( i )
                                            .search("^"+$(this).val()+"$",true,false)
                                            .draw();
                                    }
                                    else
                                    {
                                        dtbl_submission_list.column( i )
                                            .search('')
                                            .draw();
                                    }
                                } );
                     
                            dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' )
                            } );
                        }
                    } );
                    // Hide Columns
                    dtbl_submission_list.columns([2]).visible(false);
       
                }
            });

        }
        loadSubmissionData({{$late_submission}});

        function showImportSection()
        {
            $("#importmissinggrade").removeClass("d-none");
        }
        function changeMissingReport(id)
        {
            if(id == "")
            {
                document.location.href = "{{url('/admin/Reports/missing/grade')}}";
            }
            else
            {
                document.location.href = "{{url('/admin/Reports/missing/grade/')}}/"+id;
            }
        }

        function editRow(id)
        {
            /*if($display_outcome == 0)*/
                $("#edit"+id).addClass("d-none");
                $("#save"+id).removeClass("d-none");
                $("#cancel"+id).removeClass("d-none");

                $("#row"+id).find("span.scorelabel").addClass('d-none');
                $("#row"+id).find("input.scoreinput").removeClass('d-none');
            /*else
                alert("Process Selection Completed");
            endif*/

        }

        function hideEditRow(id)
        {
            $("#edit"+id).removeClass("d-none");
            $("#save"+id).addClass("d-none");
            $("#cancel"+id).addClass("d-none");

            $("#row"+id).find("span.scorelabel").removeClass('d-none');
            $("#row"+id).find("input.scoreinput").addClass('d-none');
        }

        function saveScore(id)
        {

            var data = {};
            var keyArr = new Array();
            var valid = true;
            var zeroInclude = false;
            $("#row"+id).find("input.scoreinput").each(function(e)
            {
                if($.trim($(this).val()) != "" && $.trim($(this).val()) != "0")
                {
                    if(parseInt($.trim($(this).val())) > 100 && parseInt($.trim($(this).val())) <= 0)
                    {
                        alert("Value allowed between 0-100");
                        valid = false;
                    }
                    data[$(this).attr("id")] = $(this).val();
                    $(this).parent().find(".scorelabel").html($(this).val());
                    keyArr[keyArr.length] = $(this).attr("id");
                }
                else if($.trim($(this).val()) == "0")
                {
                    zeroInclude = true;
                }

            })

            if (!$.isEmptyObject(data) && valid == true) { 
                data['_token'] = "{{csrf_token()}}";
                $.ajax({
                    url : "{{url('/admin/Reports/missing/grade/save/')}}/"+id,
                    type: "POST",
                    data : data,
                    success: function(data)
                    {
                        $("#edit"+id).removeClass("d-none");
                        $("#save"+id).addClass("d-none");
                        $("#cancel"+id).addClass("d-none");

                        $("#row"+id).find("span.scorelabel").removeClass('d-none');
                        $("#row"+id).find("input.scoreinput").addClass('d-none');

                        alert("Grade updated successfully");
                        if(!zeroInclude)
                        {
                                $("#row"+id).remove();
                        }
//
                       // $("#row"+id).find("span.scorelabel").html($("#row"+id).find("input.scoreinput").val());

                       
                        //data - response from server
                    }
                });
            }    
        }

        function exportMissing()
        {
            if($("#filter_option").val() == "")
                document.location.href="{{url('/admin/Reports/export/missinggrade')}}/{{Session::get("enrollment_id")}}/{{$late_submission}}";
            else
                document.location.href="{{url('/admin/Reports/export/missinggrade')}}/"+$("#filter_option").val() + "/{{$late_submission}}";

        }
	</script>

@endsection