@extends('layouts.admin.app')
@section('title')Eligibility Master @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Eligibility Master</div>
            <div class="">
                <a href="{{url('admin/Eligibility/create')}}" class="btn btn-sm btn-secondary" title="">Add Eligibility</a>
                <a href="{{url('admin/Eligibility/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="align-middle">Eligibility Type</th>
                        <th class="align-middle text-center w-120">Status</th>
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($eligibilityTemplates))
                       @foreach($eligibilityTemplates as $key=>$eligibilityTemplate)
                           <tr>
                               <td class="">{{$eligibilityTemplate->name}}</td>
                               <td class="text-center"><input id="{{$eligibilityTemplate->id}}" type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" {{isset($eligibilityTemplate->status)&&$eligibilityTemplate->status=='Y'?'checked':''}} /></td>
                               <td class="text-center">
                                   <a href="{{url('admin/Eligibility/edit',$eligibilityTemplate->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                   {{-- <a href="view-eligibility-master1.html" class="font-18 ml-5 mr-5" title=""><i class="far fa-eye"></i></a> --}}
                                   <a href="javascript:void(0);" onclick="deletefunction({{$eligibilityTemplate->id}})" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a></td>
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
    {{-- @include('layouts.admin.common.datatable') --}}
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <!-- Sweet Alert -->
    <script src="{{url('/resources/assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
    <script src="{{url('/resources/assets/plugins/sweet-alert2/jquery.sweet-alert.init.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".alert").delay(2000).fadeOut(1000);
            $('#datatable').DataTable({
                'columnDefs': [ {
                    'targets': [1,2], // column index (start from 0)
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
                url: '{{url('admin/Eligibility/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Eligibility to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Eligibility/delete/'+id;
            });
        };
    </script>
@endsection