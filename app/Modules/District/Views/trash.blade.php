@extends('layouts.admin.app')
@section('title')Trash District | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Trash District</div>
            <div class=""><a href="{{url('admin/District')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="align-middle w-120 text-center">District Logo</th>
                        <th class="align-middle">District Name</th>
                        <th class="align-middle">District URL</th>
{{--                        <th class="align-middle">Phone</th>--}}
                        <th class="align-middle text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($districts as $key=>$district)
                        <tr class="text-center">
                            <td>
                                <img src="{{url('/resources/filebrowser/').'/'.$district->district_slug.'/logo/'.$district->district_logo}}" alt="img" title="" width="70" id="img" class="img-thumbnail mr-3">
                            </td>
                            <td>{{$district->name}}</td>
                            <td>
                                <a href="{{"https://".'/'.$district->district_slug.'.'.Request::getHost()}}" target="_blank">{{$district->district_slug}}.{{Request::getHost()}}</a>
                            </td>
{{--                            <td>{{$district->phone}}</td>--}}
                            <td class="text-center">
                                <a href="{{url('admin/District/restore',$district->id)}}" class="font-18 ml-5 mr-5" title=""><i class=" fas fa-undo"></i></a>
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
{{--    @include('layouts.admin.common.datatable')--}}
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".alert").delay(2000).fadeOut(1000);
            $('#datatable').DataTable({
                'columnDefs': [ {
                    'targets': [3], // column index (start from 0)
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
    </script>
@endsection