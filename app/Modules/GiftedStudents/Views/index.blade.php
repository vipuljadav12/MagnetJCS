@extends('layouts.admin.app')
@section('title')Gifted Students | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
    <!-- DataTables -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('resources/assets/admin/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Gifted Students</div>
            <div class="">
                <a href="{{url('admin/GiftedStudents/import')}}" class="btn btn-sm btn-success" title="Import Gifted Students">Import Gifted Students</a>
                <a href="{{url('admin/GiftedStudents/create')}}" class="btn btn-sm btn-secondary" title="">Add Gifted Student</a>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table id="datatable" class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="align-middle w-120 text-center">Student ID</th>
                        <th class="align-middle">First Name</th>
                        <th class="align-middle">Last Name</th>
                        <th class="align-middle">Admin</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key=>$value)
                        <tr>
                            <td class="align-middle text-center">{{ $value->stateID }}</td>
                            <td>{{$value->first_name ?? '-'}}</td>
                            <td>{{$value->last_name ?? '-'}}</td>
                            <td>{{$value->admin ?? '-'}}</td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="deletefunction({{$value->id}})" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                     @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {{-- @include('layouts.admin.common.datatable') --}}
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
                $(".alert").delay(2000).fadeOut(1000);
                $('#datatable').DataTable({
                    'columnDefs': [ {
                        'targets': [4], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    }]
                });
                //Buttons examples
                // var table = $('#datatable-buttons').DataTable({
                //     lengthChange: false,
                //     buttons: ['copy', 'excel', 'pdf', 'colvis'],
                // });
                // table.buttons().container()
                //     .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
            });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to Destroy this record?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/GiftedStudents/delete/'+id;
            });
        };
    </script>
@endsection