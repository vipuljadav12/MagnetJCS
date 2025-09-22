@extends("layouts.admin.app")
@section('title')
    Trash Users | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Trash User</div>
            <div class="">
                <a href="{{url('admin/Users')}}" class="btn btn-sm btn-secondary" title="">Back</a>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table class="table table-striped mb-0 mt-10" id="userTable">
                    <thead>
                    <tr>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Email</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $u=>$user)
                        <tr>
                            <td class="">{{$user->full_name}}</td>
                            <td class="">{{$user->email}}</td>
                            <td class="text-center">
                                <a href="{{url('admin/Users/trash/restore').'/'.$user->id}}" class="font-18 ml-5 mr-5" title=""><i class=" fas fa-undo"></i></a>
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
@section("scripts")

    <script type="text/javascript">
        $('#userTable').DataTable({
            'columnDefs': [ {
                'targets': [2], // column index (start from 0)
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
    </script>
@endsection
