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
        <div class="page-title mt-5 mb-5">Address Override</div>
        <div>
            {{-- <a href="{{url('/admin/ZonedSchool/import')}}" title="Import" class="btn btn-secondary">Import</a> --}}
            <a href="{{url('/admin/ZonedSchool/create')}}" title="Import" class="btn btn-secondary">Add Address</a>
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
                        <table id="zonedAddressList" class="table table-striped mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-middle">Building/House No</th>
                                    <th class="text-middle">Prefix Direction</th>
                                    <th class="text-middle">Street Name</th>
                                    <th class="text-middle">Street Type</th>
                                    <th class="text-middle">Unit Info</th>
                                    <th class="text-middle">Suffix Direction</th>
                                    <th class="text-middle">City</th>
                                    <th class="text-middle">State</th>
                                    <th class="text-middle">ZIP Code</th>
                                    <th class="text-middle">Elementary School</th>
                                    <th class="text-middle">Intermediate School</th>
                                    <th class="text-middle">Middle School</th>
                                    <th class="text-middle">High School</th>
                                    <th class="text-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zonedSchool as $key=>$address)
                                    <tr>
                                        <td class="text-middle">{{$address->bldg_num ?? ""}}</td>
                                        <td class="text-middle">{{$address->prefix_dir ?? ""}}</td>
                                        <td class="text-middle">{{$address->street_name ?? ""}}</td>
                                        <td class="text-middle">{{$address->street_type ?? ""}}</td>
                                        <td class="text-middle">{{$address->unit_info ?? ""}}</td>
                                        <td class="text-middle">{{$address->suffix_dir ?? ""}}</td>
                                        <td class="text-middle">{{$address->city ?? ""}}</td>
                                        <td class="text-middle">{{$address->state ?? ""}}</td>
                                        <td class="text-middle">{{$address->zip ?? ""}}</td>
                                        <td class="text-middle">{{$address->elementary_school ?? ""}}</td>
                                        <td class="text-middle">{{$address->intermediate_school ?? ""}}</td>
                                        <td class="text-middle">{{$address->middle_school ?? ""}}</td>
                                        <td class="text-middle">{{$address->high_school ?? ""}}</td>
                                        <td class="text-center"><a href="{{url('admin/ZonedSchool/edit',$address->id)}}" title='Edit' class='font-18'><i class='far fa-edit'></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
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
        var dtbl_zoned_list = $("#zonedAddressList").DataTable({
            "columnDefs": [
                    {"className": "dt-center", "targets": "_all"}
                ],
        });
    </script>
@stop

