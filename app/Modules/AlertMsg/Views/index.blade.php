@extends('layouts.admin.app')
@section('title')Configuration | {{config('APP_NAME',env("APP_NAME"))}} @endsection
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
            <div class="page-title mt-5 mb-5">Text</div>
            <div class="">
                @if((checkPermission(Auth::user()->role_id,'Configuration/create') == 1))
                <a href="{{url('admin/Configuration/create')}}" class="btn btn-sm btn-secondary" title="Add Text">Add Text</a>
                @endif
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
                        <th class="align-middle text-center">ID</th>
                        <th class="align-middle">Msg Title</th>
                        <th class="align-middle text-center">Msg Text</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($msgs as $key=>$value)
                        <tr>
                            <td class="text-center">{{$key+1}}</td>
                            <td>{{$value->msg_title}}</td>
                            <td>{{$value->msg_txt}}</td>
                        
                            <td class="text-center">
                                <a href="{{url('admin/AlertMsg/edit',$value->id)}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
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
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
                $(".alert").delay(2000).fadeOut(1000);
                $('#datatable').DataTable({
                    
                    
                });
                //Buttons examples
                
            });
        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '{{url('admin/Configuration/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });

      
    </script>
@endsection