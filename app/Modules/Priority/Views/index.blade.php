@extends('layouts.admin.app')

@section('title')Priorities @stop

@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
    <!-- DataTables -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('resources/assets/admin/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Priority Master</div>
        <div class="">
            @if(Session::get('district_id') != 0)
                <a href="{{url('admin/Priority/create')}}" class="btn btn-sm btn-secondary" title="">Add Priority</a>                
            @endif
                <a href="{{url('admin/Priority/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a>
        </div>
    </div>
</div>

<!-- Show Error messsages -->
{{-- @parent  --}}

<div class="card shadow">
    <div class="card-body">
        @include("layouts.admin.common.alerts")
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="priority">
                <thead>
                    <tr>
                        <th class="align-middle">Template Name</th>
                        <th class="align-middle text-center w-120">Status</th>
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($priorities)
                        @foreach($priorities as $priority)
                        <tr>
                            <td class="">{{$priority->name}}</td>
                            <td class="text-center"><input id="chk_00" type="checkbox" class="js-switch js-switch-1 js-switch-xs" value="{{$priority->id}}" data-size="Small" @if($priority->status=="Y") checked @endif /></td>
                            <td class="text-center">
                                <a href="{{url('admin/Priority/edit/'.$priority->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="deletefunction({{$priority->id}})" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
                        </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- InstanceEndEditable --> </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $("#priority").DataTable({
                'order': [],
                'columnDefs': [{
                    'targets': [1, 2],
                    'orderable': false
                }]
            });
        });

        $(document).on('change', '#chk_00', function(){
            $.ajax({
                type: "post",
                url: "{{url('admin/Priority/updatestatus')}}",
                data: {   
                    "_token": "{{csrf_token()}}",                 
                    "id": $(this).attr('value'),
                    "status": $(this).prop('checked')==true?"Y":"N"
                }
            });
        });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Priority to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Priority/delete/'+id;
            });
        };
    </script>
@stop