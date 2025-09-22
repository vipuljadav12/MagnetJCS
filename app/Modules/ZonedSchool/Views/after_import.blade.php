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
        <div class="page-title mt-5 mb-5">Imported School Address</div>
        <div>
            <a href="{{url('/admin/ZonedSchool')}}" title="Go Back" class="btn btn-secondary">Go Back</a>
            {{-- <a href="javascript::void(0)" onclick="export_zone_address()" title="Export Zoned School Addresses" class="btn btn-info"><i class="fa fa-download"></i> Export Addresses</a> --}}
        </div>
    </div>
</div>

    {{-- <div class="raw"> --}}
        <div class="card shadow">
            <div class="card-body">
                @include("layouts.admin.common.alerts")
                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="active-screen-tab" data-toggle="tab" href="#active-screen" role="tab" aria-controls="active-screen" aria-selected="true">Imported Addresses</a></li>
                    <li class="nav-item"><a class="nav-link" id="active2-email-tab" data-toggle="tab" href="#active2-email" role="tab" aria-controls="active2-email" aria-selected="false">Addresses with Error</a></li>
                </ul>
                 <div class="tab-content bordered" id="myTab2Content">
                    <div class="tab-pane fade show active" id="active-screen" role="tabpanel" aria-labelledby="active-screen-tab">
                        <div class="">
                            <div class="table-responsive">
                                <table id="zonedSchoolList_1" class="table table-striped mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-middle">Bldg No</th>
                                            <th class="text-middle">Street Address</th>
                                            <th class="text-middle">Street Type</th>
                                            <th class="text-middle">Unit Info</th>
                                            <th class="text-middle">City</th>
                                            <th class="text-middle">Zip</th>
                                            <th class="text-middle">Elmentary School</th>
                                            <th class="text-middle">Intermediate School</th>
                                            <th class="text-middle">Middle School</th>
                                            <th class="text-middle">High School</th>
                                        </tr>
                                    </thead>
                                    @foreach($addedArr as $key=>$value)
                                        <tr>
                                            <td class="text-middle">{{$value['bldg_num']}}</td>
                                            <td class="text-middle">{{$value['street_name']}}</td>
                                            <td class="text-middle">{{$value['street_type']}}</td>
                                            <td class="text-middle">{{$value['unit_info']}}</td>
                                            <td class="text-middle">{{$value['city']}}</td>
                                            <td class="text-middle">{{$value['zip']}}</td>
                                            <td class="text-middle">{{$value['elementary_school']}}</td>
                                            <td class="text-middle">{{$value['intermediate_school']}}</td>
                                            <td class="text-middle">{{$value['middle_school']}}</td>
                                            <td class="text-middle">{{$value['high_school']}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="active2-email" role="tabpanel" aria-labelledby="active2-email-tab">
                        <div class="">
                            <div class="table-responsive">
                                    <table id="zonedSchoolList_2" class="table table-striped mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-middle">Bldg No</th>
                                                <th class="text-middle">Street Address</th>
                                                <th class="text-middle">Street Type</th>
                                                <th class="text-middle">Unit Info</th>
                                                <th class="text-middle">City</th>
                                                <th class="text-middle">Zip</th>
                                                <th class="text-middle">Elmentary School</th>
                                                <th class="text-middle">Intermediate School</th>
                                                <th class="text-middle">Middle School</th>
                                                <th class="text-middle">High School</th>
                                            </tr>
                                        </thead>
                                        @foreach($invalidArr as $key=>$value)
                                            <tr>
                                                <td class="text-middle">{{$value['bldg_num']}}</td>
                                                <td class="text-middle">{{$value['street_name']}}</td>
                                                <td class="text-middle">{{$value['street_type']}}</td>
                                                <td class="text-middle">{{$value['unit_info']}}</td>
                                                <td class="text-middle">{{$value['city']}}</td>
                                                <td class="text-middle">{{$value['zip']}}</td>
                                                <td class="text-middle">{{$value['elementary_school']}}</td>
                                                <td class="text-middle">{{$value['intermediate_school']}}</td>
                                                <td class="text-middle">{{$value['middle_school']}}</td>
                                                <td class="text-middle">{{$value['high_school']}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-20 pb-20">
                    
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
        $("#zonedSchoolList_1").DataTable();

        $("#zonedSchoolList_2").DataTable({
            "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                ],
             dom: 'Bfrtip',
             buttons: [
                 {
                     extend: 'excelHtml5',
                     title: 'Invalid Address',
                     text:'Export to Excel',
            //         //Columns to export
            //         exportOptions: {
            //             modifier: {
            //                 search: 'applied',
            //                 order: 'applied'
            //             }
            //         }
                 }
             ],
                
        });

       
    </script>
@stop

