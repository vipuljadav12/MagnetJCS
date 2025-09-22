@extends('layouts.admin.app')
@section('title')
    Submissions Result
@endsection
@section('styles')
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
    .custom-select2{
    margin: 5px !important;
}
.dt-buttons {position: absolute !important; padding-top: 5px !important;}
</style>
@endsection
@section('content')

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Submission Results</div>
        <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/process/logs')}}" title="Go Back">Go Back</a></div>
       </div> 

</div>

 <div class="card shadow">
                            <div class="card-body">
                                {{-- <div class="" align="right">
                                    <a class="btn btn-secondary btn-lg" href="javascript:void(0)"
                                     data-toggle="modal" data-target="#exportfieldsModal">Export to Excel</a>
                                </div> --}}
                                <div class="row col-md-12 pull-left" id="submission_filters"></div>

                                @if(!empty($final_data))
                                
                                        <div class="table-responsive">
                                            <div class="row col-md-12" id="submission_filters"></div>
                                        <div style="height: 704px; overflow-y: auto;">

                                    <table class="table table-striped" id="tbl_submission_results">
                                        <thead>
                                            <tr>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Submission ID</th>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Student Name</th>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Next Grade</th>
                                                <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                                                <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important">Outcome</th>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Race</th>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">School</th>
                                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                                                <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Choice</th>
                                                <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Outcome</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($final_data as $key=>$value)
                                                <tr>
                                                    <td class="">{{$value['id']}}</td>
                                                    <td class="">{{$value['name']}}</td>
                                                    <td class="text-center">{{$value['grade']}}</td>
                                                    <td class="">{{$value['program_name']}}</td>
                                                    <td class="">{{$value['offered_status']}}</td>
                                                    <td class="">{{$value['race']}}</td>
                                                    <td class="">{{$value['school']}}</td>
                                                    <td class="">{{$value['program']}}</td>
                                                    <td class="text-center">{{$value['choice']}}</td>
                                                    <td class="">{!! $value['outcome'] !!}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                @endif
                        </div>
                    </div>

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


        function showReport()
        {
            if($("#enrollment").val() == "")
            {
                alert("Please select enrollment year");
            }
            else if($("#reporttype").val() == "")
            {
                alert("Please select report type");
            }
            else
            {
                var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment").val()+"/"+$("#reporttype").val();
                document.location.href = link;
            }
        }

    </script>
@endsection