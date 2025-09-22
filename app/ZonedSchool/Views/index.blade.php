@extends('layouts.admin.app')


@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Zoned School Search</div>
        <div>
            <a href="{{url('/admin/ZonedSchool/import')}}" title="Import Zoned School Addresses" class="btn btn-info"><i class="fa fa-upload"></i> Import  Addresses</a>
            <a href="javascript::void(0)" onclick="export_zone_address()" title="Export Zoned School Addresses" class="btn btn-info"><i class="fa fa-download"></i> Export Addresses</a>
        </div>
    </div>
</div>

    {{-- <div class="raw"> --}}
        <div class="card shadow">
            <div class="card-body">
                @include("layouts.admin.common.alerts")
                <div class="pt-20 pb-20">
                    <div class="table-responsive">
                        <table id="zonedSchoolList" class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-middle">Bldg No</th>
                                    <th class="text-middle">Street Address</th>
                                    <th class="text-middle">Street Type</th>
                                    <th class="text-middle">City</th>
                                    <th class="text-middle">Zip</th>
                                    <th class="text-middle">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {{-- </div> --}}
</form>
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script> --}}
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var dtbl_zoned_list = $("#zonedSchoolList").DataTable({
            'serverSide': true,
            "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                ],
            'ajax': {
                url: "{{url('admin/ZonedSchool/getzonedschool')}}",
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Submissions',
                    text:'Export to Excel'
                    //Columns to export
                    //exportOptions: {
                   //     columns: [0, 1, 2, 3,4,5,6]
                   // }
                }
            ],
                
        });

        function fetch_zoned_school()
        {
            $.ajax({
                url:'{{url('/admin/ZonedSchool/search')}}',
                dataSrc: "Data",
                type:"post",
                data:{_token:'{{csrf_token()}}',address:$("#address").val(),grade: $("#grade").val(),zip: $("#zip").val(),city: $("#city").val()},
                success:function(response){
                    $("#response").html(response);
                }

            }).done( function(data) {
                $('#zoneList').dataTable({
                    "searching": false,
                    'columnDefs': [
                    {
                        "targets": "_all", // your case first column
                        "className": "text-center",
                    }],
                    // dom: 'Bfrtip',
                    // buttons: [
                    //     {
                    //         extend: 'excelHtml5',
                    //         title: 'Excel',
                    //         text:'Export to excel'
                    //     }
                    // ]
                });
            });
        }
        /*-- Description field validation end --*/

        function export_zone_address(){
            location.href = '{{url('/admin/ZonedSchool/export')}}?address='+$("#address").val()+'&grade='+$("#grade").val()+'&zip='+$("#zip").val()+'&city='+$("#city").val();
        }
    </script>
@stop

