@extends("layouts.admin.app")
@section('title')
	Users
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">User</div>
        <div class="">
            <a href="{{url('admin/Users/create')}}" class="btn btn-sm btn-secondary" title="">Add User</a>
            <a href="{{url('admin/Users/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        @include("layouts.admin.common.alerts")
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="userTable">
                <thead>
                    <tr>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Email Address</th>
                        <th class="align-middle text-center">Status</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($users as $u=>$user)
	                    <tr>
	                        <td class="">{{$user->full_name}}</td>
	                        <td class="">{{$user->email}}</td>
	                        <td class="text-center">
                                <input  type="checkbox" data-plugin="switchery"  class=" js-switch js-switch-1 js-switch-xs  userStatus" data-size="Small"  @if(isset($user->status) && $user->status == "Y") checked @endif data-id="{{$user->id}}"/>
                            </td>
	                        <td class="text-center">
                                <a href="{{url('admin/Users/edit').'/'.$user->id}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                {{-- <a href="javascript:void(0);" class="font-18 ml-5 mr-5" title=""><i class="far fa-eye"></i></a> --}}
                                <a href="javascript:void(0);" onclick="deletefunction({{$user->id}})"  class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
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
    $(document).ready(function() {
        $(".alert").delay(2000).fadeOut(1000);
        $('#userTable').DataTable({
            'columnDefs': [ {
                'targets': [2,3], // column index (start from 0)
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

    //status change
    $(document).on("change",".userStatus",function()
    {
        // alert();
        var user_id = $(this).attr("data-id");
        $.ajax({
            url: '{{url('admin/Users/status/')}}',
            type: 'POST',
            data: {
                _token : "{{csrf_token()}}",
                user_id : user_id
            },
            success: function(data) {
                return data;
            },
        });
    });

    //delete confermation
    var deletefunction = function(id){
        swal({ 
          title: "Are you sure you would like to move this User to trash?",
            text: "",
            // type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
          }).then(function() {
            window.location.href = '{{url('/')}}/admin/Users/trash/'+id;
        });
    };





</script>
@endsection
