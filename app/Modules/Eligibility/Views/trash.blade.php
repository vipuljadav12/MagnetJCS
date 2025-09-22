    @extends('layouts.admin.app')
@section('title') Trash Eligibility  @endsection
@section('styles')
    <!-- DataTables -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('resources/assets/admin/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert -->
    <link href="{{url('resources/assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Trash Eligibility</div>
            <div class="">
                <a href="{{url('admin/Eligibility')}}" class="btn btn-sm btn-secondary" title="">Back</a>
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
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($eligibilities))
                        @foreach($eligibilities as $key=>$eligibility)
                            <tr>
                                <td class="">{{$eligibility->name}}</td>
                                <td class="text-center">
                                    <a href="{{url('admin/Eligibility/restore',$eligibility->id)}}" class="font-18 ml-5 mr-5" title=""><i class=" fas fa-undo"></i></a>
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
                    'targets': [1], // column index (start from 0)
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
    </script>
@endsection