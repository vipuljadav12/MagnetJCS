@extends('layouts.admin.app')
@section('title') School Master @endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">School Master</div>
        <div class="">
            <a href="{{url('admin/School/create')}}" class="btn btn-sm btn-secondary" title="Add">Add School</a>
            <a href="{{url('admin/School/trash')}}" class="btn btn-sm btn-danger" title="Trash">Trash</a>

        </div>
    </div>
</div>
<div class="card shadow">
   @include("layouts.admin.common.alerts")
   <div class="card-body">
    <div class="table-responsive">
        <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                    <th class="align-middle">School Name</th>
                    <th class="align-middle">Grade</th>
                    <th class="align-middle">Magnet</th>
                    <th class="align-middle">Zoning API Name</th>
                    <th class="align-middle">SIS Name</th>
                    <th class="align-middle text-center">Status</th>
                    <th class="align-middle text-center">Action</th>
                </tr>
            </thead>
            <tbody>
             @if(isset($schools))
             @foreach($schools as $key=>$school)
             <tr>
                <td class="">{{$school->name}}</td>
                <td class="">{{$school->grade_id}}</td>
                <td class="">{{$school->magnet}}</td>
                <td class="">{{$school->zoning_api_name}}</td>
                <td class="">{{$school->sis_name}}</td>
                <td class="text-center">
                    <input id="{{$school->id}}" type="checkbox" class=" status js-switch js-switch-1 js-switch-xs" data-size="Small" {{isset($school->status)&&$school->status=='Y'?'checked':''}} />
                </td>
                <td class="text-center">
                    <a href="{{url('admin/School/edit',$school->id)}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deletefunction({{$school->id}})" class="font-18 ml-5 mr-5 text-danger" title="Trash"><i class="far fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>
</div>
</div>

@endsection
@section('scripts')
<!-- Sweet Alert -->
<script src="{{url('/resources/assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{url('/resources/assets/plugins/sweet-alert2/jquery.sweet-alert.init.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".alert").delay(2000).fadeOut(1000);
        $('#datatable').DataTable({
            'columnDefs': [ {
                    'targets': [5,6], // column index (start from 0)
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
        var status=$(this).prop('checked')==true ? 'Y' : 'N' ;
        $.ajax({
            type: "get",
            url: '{{url('admin/School/changestatus')}}',
            data: {
                id:$(this).attr('id'),
                status:status
            },
            complete: function(data) {
                console.log('success');
            }
        });
    });

    var deletefunction = function(id){
        swal({
            title: "Are you sure you would like to move this School to trash?",
            text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/School/delete/'+id;
            });
        };
    </script> 
    @endsection