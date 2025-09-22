@extends('layouts.admin.app')
@section('title') Trash School @endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Trash School </div>
        <div class="">
            <a href="{{url('admin/School')}}" class="btn btn-sm btn-danger" title="">Back</a>

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
                    <th class="align-middle text-center">Action</th>
                </tr>
            </thead>
            <tbody>
               @if(isset($schools))
               @foreach($schools as $key=>$school)
               <tr>
                <td class="">{{$school->name}}</td>
                <td class="">{{$school->grade}}</td>
                <td class="">{{$school->magnet}}</td>
                <td class="">{{$school->zoning_api_name}}</td>
                <td class="">{{$school->sis_name}}</td>

                <td class="text-center">

                    <a href="{{url('admin/School/restore',$school->id)}}" class="font-18 ml-5 mr-5" title="Restore"><i class="fas fa-undo"></i>
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
<!-- InstanceEndEditable -->
@section('scripts')

<script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
<!-- Sweet Alert -->
<script src="{{url('/resources/assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{url('/resources/assets/plugins/sweet-alert2/jquery.sweet-alert.init.js')}}"></script>
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".alert").delay(2000).fadeOut(1000);
        $('#datatable').DataTable({
            'columnDefs': [ {
                    'targets': [5], // column index (start from 0)
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
</script> 
@endsection