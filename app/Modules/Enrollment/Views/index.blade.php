@extends('layouts.admin.app')

@section('title', "Enrollment Periods")

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
            <div class="page-title mt-5 mb-5">New Enrollment Period</div>
            <div class="">
                @if((checkPermission(Auth::user()->role_id,'Enrollment/create') == 1))
                    @if(Session::get('district_id') != 0)
                        <a href="{{url('admin/Enrollment/create')}}" class="btn btn-sm btn-secondary" title="Add">Add Enrollment</a>
                    @endif
                @endif
                <a href="{{url('admin/Enrollment/trash')}}" class="btn btn-sm btn-danger d-none" title="Trash">Trash</a>
            </div> 
        </div>
    </div>
    @include('layouts.admin.common.alerts')
    <div class="card shadow">
        {{-- <div class="card-header">Open Enrollment</div> --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">School Year</th>
                            <th class="text-center">Confirmation Style</th>
                            <th class="text-center">Beginning Date</th>
                            <th class="text-center">Ending Date</th>
                            <th class="text-center w-120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($enrollments)
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td class="text-center">{{$enrollment->school_year}}</td>
                                    <td class="text-center">{{$enrollment->confirmation_style}}</td>
                                    <td class="text-center">{{getDateFormat($enrollment->begning_date)}}</td>
                                    <td class="text-center">{{getDateFormat($enrollment->ending_date)}}</td>
                                    <td class="text-center">
                                        @if((checkPermission(Auth::user()->role_id,'Enrollment/edit') == 1))
                                            <a href="{{url('admin/Enrollment/edit/'.$enrollment->id)}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
                                        @endif
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
            // 'order': [],
            'columnDefs': [{
                'targets': [2,3],
                'orderable': false
            }]
        });

        $(document).on('change', '#status', function(){
            $.ajax({
                type: 'post',
                url: '{{url('admin/Enrollment/update_status')}}',
                data: {
                    '_token': "{{csrf_token()}}",
                    'id': $(this).val(),
                    'status': $(this).prop('checked')==true?'Y':'N'
                }
            });
        });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Enrollment Period to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Enrollment/move_to_trash/'+id;
            });
        };

    </script>
@stop   