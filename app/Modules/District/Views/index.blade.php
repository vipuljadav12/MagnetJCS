@extends('layouts.admin.app')
@section('title')District | {{config('APP_NAME',env("APP_NAME"))}} @endsection
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
            <div class="page-title mt-5 mb-5">District</div>
            <div class="">
                <a href="{{url('admin/District/create')}}" class="btn btn-sm btn-secondary" title="">Add District</a>
                <a href="{{url('admin/District/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a>
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
                        <th class="align-middle w-120 text-center">District Logo</th>
                        <th class="align-middle">District Name</th>
                        <th class="align-middle">District Slug</th>
                        {{-- <th class="align-middle">Phone</th> --}}
                        <th class="align-middle text-center">Status</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($districts as $key=>$district)
                        <tr>
                            <td class="align-middle text-center">
                                <img src="{{url('/resources/filebrowser/').'/'.$district->district_slug.'/logo/'.$district->district_logo}}" alt="img" title="" width="70" id="img" class="img-thumbnail mr-3">
                            </td>
                            <td>{{$district->name}}</td>
                            {{-- <td>
                                <a href="{{url('/').'/'.$district->district_slug.'.magent.com'}}" target="_blank">{{$district->district_slug}}.magent.com</a>
                            </td> --}}
                            {{-- <td>{{$district->phone}}</td> --}}
                            <td>
                                <a href="{{"http://".'/'.$district->district_slug.'.'.Request::getHost()}}" target="_blank">{{$district->district_slug}}.{{Request::getHost()}}</a>
                            </td>
                            <td class="text-center">
                                <input id="{{$district->id}}" type="checkbox"  class="js-switch js-switch-1 js-switch-xs status" data-size="Small" {{$district->status=='Y'?'checked':''}} />
                            </td>
                            <td class="text-center">
                                <a href="{{url('admin/District/edit',$district->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="deletefunction({{$district->id}})" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
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
                        'targets': [3,4], // column index (start from 0)
                        'orderable': false, // set orderable false for selected columns
                    }]
                });
                //Buttons examples
                var table = $('#datatable-buttons').DataTable({
                    lengthChange: false,
                    buttons: ['copy', 'excel', 'pdf', 'colvis'],
                });
                table.buttons().container()
                    .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
            });
        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '{{url('admin/District/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this District to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/District/delete/'+id;
            });
        };
    </script>
@endsection