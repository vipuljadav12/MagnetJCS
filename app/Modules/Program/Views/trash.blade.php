@extends('layouts.admin.app')
@section('title')Trash Program  | {{config('APP_NAME',env("APP_NAME"))}}  @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Trash Program</div>
            <div class=""><a href="{{url('admin/Program')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table id="datatable" class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="align-middle">Program Name</th>
                        <th class="align-middle">Grade Levels Available</th>
                        <th class="align-middle">Parent Submission Form</th>
                        <th class="align-middle">Ranking System</th>
                        <th class="align-middle">Selection Method</th>
                        <th class="align-middle">Select Priority</th>
                        <th class="align-middle text-center w-90">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($programs as $key=>$program)
                        <tr>
                            <td class="">{{$program->name}}</td>
                            <td class="">{{$program->grade_lavel}}</td>
                            <td class="">{{$program->parent_submission_form}}</td>
                            <td class="">Committee Score</td>
                            <td class="">Lottery Method</td>
                            <td class="">2</td>
                            <td class="text-center">
                                <a href="{{url('admin/Program/restore',$program->id)}}" class="font-18 ml-5 mr-5" title="Restore"><i class="fas fa-undo"></i></a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".alert").delay(2000).fadeOut(1000);
            $('#datatable').DataTable({
                'columnDefs': [ {
                    'targets': [6], // column index (start from 0)
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
        {{--$('.status').change(function() {--}}
        {{--    var click=$(this).prop('checked')==true ? 'Y' : 'N' ;--}}
        {{--    $.ajax({--}}
        {{--        type: "get",--}}
        {{--        url: '{{url('admin/Eligibility/status')}}',--}}
        {{--        data: {--}}
        {{--            id:$(this).attr('id'),--}}
        {{--            status:click--}}
        {{--        },--}}
        {{--        complete: function(data) {--}}
        {{--            console.log('success');--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
        {{--var deletefunction = function(id){--}}
        {{--    swal({--}}
        {{--        title: "Are you sure you would like to delete this Eligibility?",--}}
        {{--        text: "",--}}
        {{--        // type: "warning",--}}
        {{--        showCancelButton: true,--}}
        {{--        confirmButtonColor: "#DD6B55",--}}
        {{--        confirmButtonText: "Yes",--}}
        {{--        closeOnConfirm: false--}}
        {{--    }).then(function() {--}}
        {{--        window.location.href = '{{url('/')}}/admin/Eligibility/delete/'+id;--}}
        {{--    });--}}
        {{--};--}}
    </script>
@endsection