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
        <div class=""><a href="{{url('admin/Priority')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
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
                        <th class="align-middle">Priority Name</th>
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($priorities)
                        @foreach($priorities as $priority)
                        <tr>
                            <td class="">{{$priority->name}}</td>
                            <td class="text-center">
                                <a href="{{url('admin/Priority/restore/'.$priority->id)}}" class="font-18 ml-5 mr-5" title=""><i class=" fas fa-undo"></i></a>
                            </td>
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
                // "scrollX": true
                'order': [],
                'columnDefs': [{
                    'targets': [1],
                    'orderable': false
                }]
            });
        });
    </script>
@stop