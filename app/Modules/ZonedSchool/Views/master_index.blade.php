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
        <div class="page-title mt-5 mb-5">Zoned Master Addresses</div>
        <div>
            <a href="{{url('/admin/ZonedSchool/import/0')}}" title="Import" class="btn btn-secondary">Import</a>
        </div>

    </div>
</div>

    {{-- <div class="raw"> --}}
        <div class="card shadow">
            <div class="card-body">
                @include("layouts.admin.common.alerts")
                <div class="pt-20 pb-20">
                    <div class="table-responsive">
                        <table id="masterAddressList" class="table table-striped mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-middle">Master Name</th>
                                    <th class="text-middle">Created By</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-middle">Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masterAddress as $key=>$address)
                                    <tr>
                                        <td class="text-middle">{{$address->group_name ?? ""}}</td>
                                        <td class="text-middle">{{getUserName($address->user_id) ?? ""}}</td>
                                        <td class="text-center">
                                            <input id="{{$address->id}}" type="checkbox" class="status js-switch js-switch-1 js-switch-xs" data-size="Small" {{isset($address->status) && $address->status=='Y'?'checked disabled':''}} />
                                        </td>
                                        {{-- <td class="text-middle">{{$address->status ?? ""}}</td> --}}
                                        <td class="text-middle">{{getDateTimeFormat($address->created_at) ?? ""}}</td>
                                        <td class="text-center"><a href="{{url('admin/ZonedSchool/master',$address->id)}}" title='Show Addresses' class='font-18'><i class='fa fa-eye'></i></a></td>
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
        var dtbl_zoned_list = $("#masterAddressList").DataTable({
            "columnDefs": [
                    {"className": "dt-center", "targets": "_all"}
                ],
        });

        $('.status').change(function() {
            var status=$(this).prop('checked')==true ? 'Y' : 'N' ;
            var master_id = $(this).attr('id');

            if(status == 'Y')
            {
                // $('.status').toggle('click');
                location.href = "{{url('admin/ZonedSchool/master/changeStatus/')}}/" + master_id;
            }
        });
    </script>
@stop

