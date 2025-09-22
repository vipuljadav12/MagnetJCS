@extends('layouts.admin.app')

@section('title', "Enrollment Periods Trash")

@section('styles')
    {{-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> --}}
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Enrollment Periods Trash</div>
            <div class="">
                <a href="{{url('admin/Enrollment')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a>                
            </div> 
        </div>
    </div>
    @include('layouts.admin.common.alerts')
    <div class="card shadow">
        {{-- <div class="card-header">Trashed Enrollments</div> --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="">School Year</th>
                            <th class="">Confirmation Style</th>
                            <th class="text-center w-120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($enrollments)
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td class="">{{$enrollment->school_year}}</td>
                                    <td class="">{{$enrollment->confirmation_style}}</td>
                                    <td class="text-center">
                                        <a href="{{url('admin/Enrollment/restore/'.$enrollment->id)}}" class="font-18 ml-5 mr-5" title="Restore"><i class=" fas fa-undo"></i></a>
                                        <a href="javascript:void(0)" onclick="deletefunction({{$enrollment->id}})" class="font-18 ml-5 mr-5 text-danger d-none" title="Delete"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        
        $('.table').DataTable({
            'order': [],
            'columnDefs': [{
                'targets': [2],
                'orderable': false
            }]
        });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to permanent delete this Enrollment Period?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Enrollment/delete/'+id;
            });
        };

    </script>
@stop   