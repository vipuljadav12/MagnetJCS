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
        <div class="page-title mt-5 mb-5">Upload School Address</div>
        <div>
            <a href="{{url('/admin/ZonedSchool/import',$master_address_id)}}" title="Import" class="btn btn-secondary">Import</a>
            <a href="{{url('/admin/ZonedSchool/')}}" title="Go Back" class="btn btn-secondary">Go Back</a>
            {{-- <a href="javascript::void(0)" onclick="export_zone_address()" title="Export Zoned School Addresses" class="btn btn-info"><i class="fa fa-download"></i> Export Addresses</a> --}}
        </div>
    </div>
</div>

    {{-- <div class="raw"> --}}
        <div class="card shadow">
            <div class="card-body">
                @include("layouts.admin.common.alerts")
                <div class="pt-20 pb-20">
                    <div class="table-responsive">
                        <table id="zonedSchoolList" class="table table-striped mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-middle">Building/House No</th>
                                    <th class="text-middle">Prefix Direction</th>
                                    <th class="text-middle">Street Name</th>
                                    <th class="text-middle">Street Type</th>
                                    <th class="text-middle">Unit Info</th>
                                    <th class="text-middle">Suffix Direction</th>
                                    <th class="text-middle">City</th>
                                    <th class="text-middle">ZIP Code</th>
                                    <th class="text-middle">Elmentary School</th>
                                    <th class="text-middle">Intermediate School</th>
                                    <th class="text-middle">Middle School</th>
                                    <th class="text-middle">High School</th>
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
                    {
                      "targets": "8",
                      "className": 'text-center'
                    },
                    {"className": "dt-center", "targets": "_all"}
                    
                ],
            'ajax': {
                url: "{{url('admin/ZonedSchool/getzonedschool',$master_address_id)}}",
            },
            // dom: 'Bfrtip',
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         title: 'Submissions',
            //         text:'Export to Excel',
            //         //Columns to export
            //         exportOptions: {
            //             modifier: {
            //                 search: 'applied',
            //                 order: 'applied'
            //             }
            //         }
            //     }
            // ],
                
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

