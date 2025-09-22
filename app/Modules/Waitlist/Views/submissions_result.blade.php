@extends('layouts.admin.app')
@section('title')Submission Result | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<style type="text/css">
    .buttons-excel{display: none !important;}
    .custom-select{
        margin: 5px !important;
    }
</style>

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Submission Result</div>
        </div>
    </div>
    
    <form class="">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link " href="{{url('/admin/Waitlist/Process/Selection/'.$application_id)}}">All Programs</a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/admin/Waitlist/Population/'.$application_id)}}">Population Change</a></li>
            <li class="nav-item"><a class="nav-link active" id="preview04-tab" data-toggle="tab" href="#preview04" role="tab" aria-controls="preview04" aria-selected="true">Submission Result</a></li>
        </ul>

        <div class="tab-content bordered" id="myTabContent">
            @include('Waitlist::Template.submissions_result')
        </div>
    </form>    
@endsection

@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>

<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var dtbl_submission_list = $("#tbl_submission_results").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
             bPaginate: false,
             bSort: false,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Submissions-Results',
                        text:'Export to Excel',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }
                ]
            });

            $("#ExportReporttoExcel").on("click", function() {
                dtbl_submission_list.button( '.buttons-excel' ).trigger();
            });

             $("#tbl_submission_results thead th").each( function ( i ) {
            // Disable dropdown filter for disalble_dropdown_ary (index=0)
            var disalble_dropdown_ary = [0, 1, 5, 6, 7, 8, 9];//13



                if ($.inArray(i, disalble_dropdown_ary) == -1) {
                    var column_title = $(this).text();
                    
                    var select = $('<select class="form-control col-md-3 custom-select custom-select2"><option value="">Select '+column_title+'</option></select>')
                        .appendTo( $('#submission_filters') )
                        .on( 'change', function () {
                            dtbl_submission_list.column( i )
                                .search($(this).val())
                                .draw();
                        } );
                    
                    if(i == 2)
                    {
                        var gArr = new Array('PreK', 'K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
                        var kArr = new Array();
                        dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                            kArr[kArr.length] = d;
                        });
                        for(m=0; m < gArr.length; m++)
                        {
                            if($.inArray(gArr[m], kArr) >= 0)
                            {
                                 select.append( '<option value="'+gArr[m]+'">'+gArr[m]+'</option>' )
                            }
                        }


                    }
                    else
                    {
                        dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {

                            str = d.replace('<div class="alert1 alert-success p-10 text-center d-block">', "");
                            str = str.replace('<div class="alert1 alert-danger p-10 text-center d-block">', "");
                            str = str.replace('<div class="alert1 alert-warning p-10 text-center d-block">', "");
                            str = str.replace('</div>', "");
                            select.append( '<option value="'+str+'">'+str+'</option>' )
                        } );
                    }
                }
            } );
            // Hide Columns
            dtbl_submission_list.columns([3, 4]).visible(false);

            function updateFinalStatus()
            {
                $("#wrapperloading").show();
                $.ajax({
                    url:'{{url('/admin/Waitlist/Accept/list/'.$application_id)}}',
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